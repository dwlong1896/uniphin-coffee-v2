<?php

class NewsController extends Controller
{
    private $newsModel;
    private $categoryModel;
    private $commentModel;
    public function __construct()
    {
        $this->newsModel = new NewsModel();
        $this->categoryModel = new NewsCategoryModel();
        $this->commentModel = new CommentModel();
    }

    public function index()
    {
        $filters = [
            'search' => $_GET['search'] ?? '',
            'category' => $_GET['category'] ?? '',
            'sort' => $_GET['sort'] ?? 'newest'
        ];

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = 9;
        $offset = ($page - 1) * $limit;

        $newsList = $this->newsModel->getNews($filters, $limit, $offset,false);
        $totalNews = $this->newsModel->countNews($filters,false);
        $categories = $this->categoryModel->getAll();
        $data = [
            'news' => $newsList,
            'categories' => $categories, // Đưa vào mảng data
            'totalPages' => ceil($totalNews / $limit),
            'currentPage' => $page,
            'filters' => $filters
        ];


        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($data);
            exit;
        }

        $this->view('users/pages/tin-tuc', $data);
    }

    public function detail($params)
    {
        $slug = is_array($params) ? ($params['slug'] ?? '') : $params;
        $newsItem = $this->newsModel->findBySlug((string) $slug);

        if (!$newsItem) {
            $this->redirect($this->baseUrl('tin-tuc'));
        }

        $newsId = $newsItem['ID'];
        $categoryId = $newsItem['N_Cate_ID']; // Lấy ID danh mục để tìm bài liên quan


        $relatedNews = $this->newsModel->getRelatedNews((int) $categoryId, (int) $newsId);

        $sort = $_GET['comment_sort'] ?? 'newest';
        $page = isset($_GET['comment_page']) ? (int) $_GET['comment_page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $comments = $this->commentModel->getCommentsByNewsId($newsId, false, $sort, $limit, $offset);
        $totalRootComments = $this->commentModel->countRootComments($newsId);


        $this->view('users/pages/news_detail', [
            'newsItem' => $newsItem,
            'comments' => $comments,
            'relatedNews' => $relatedNews,
            'currentSort' => $sort,
            'currentPage' => $page,
            'totalPages' => ceil($totalRootComments / $limit)
        ]);
    }
    public function postComment()
    {

        if (!isset($_SESSION['user_id'])) {
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập!']);
                exit;
            }
            header("Location: " . $this->baseUrl('login'));
            exit;
        }

        // Lấy dữ liệu từ Form
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';
        $newsId = isset($_POST['news_id']) ? (int) $_POST['news_id'] : 0;
        $parentId = !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null;


        // Validation - Kiểm tra tính hợp lệ (Server-side)
        $errors = [];

        if (empty($content)) {
            $errors[] = "Nội dung bình luận không được để trống.";
        } elseif (mb_strlen($content) > 1000) {
            $errors[] = "Bình luận quá dài (tối đa 1000 ký tự)";
        }

        if ($newsId <= 0) {
            $errors[] = "Bài viết không hợp lệ.";
        }

        if (!empty($errors)) {
            $this->jsonResponse('error', implode(' ', $errors));
        }

        // Ở đây ta dùng htmlspecialchars trước khi lưu để chống XSS
        $cleanContent = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        $result = $this->commentModel->addComment($newsId, $_SESSION['user_id'], $cleanContent, $parentId);
        if ($result) {
            // Xử lý phản hồi dựa trên loại yêu cầu (AJAX hay Normal)
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'success',
                    'comment' => [
                        'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'],
                        'content' => htmlspecialchars($content),
                        'created_at' => 'Vừa xong',
                        'image' => $_SESSION['image'] ?? 'news_default.png'
                    ]
                ]);
                exit;
            } else {
                $_SESSION['success_msg'] = "Gửi bình luận thành công!";
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }
        } else {
            $this->jsonResponse('error', 'Lỗi hệ thống, không thể lưu bình luận.');
        }
    }

    private function jsonResponse($status, $message, $data = [])
    {
        if ($this->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(array_merge(['status' => $status, 'message' => $message], $data));
            exit;
        }
        // Nếu không phải AJAX thì dùng Session để báo lỗi
        $_SESSION[$status . '_msg'] = $message;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    private function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    public function handleCommentAction()
    {
        // Tắt hiển thị lỗi trực tiếp để không làm hỏng JSON response
        error_reporting(E_ALL);
        ini_set('display_errors', 0);

        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse('error', 'Hết phiên đăng nhập! Vui lòng đăng nhập lại.');
        }

        $action = $_POST['action'] ?? '';
        $commentId = (int) ($_POST['comment_id'] ?? 0);

        // 2. Validate ID bình luận
        if ($commentId <= 0) {
            $this->jsonResponse('error', 'Mã bình luận không hợp lệ.');
        }

        $success = false;

        try {
            if ($action === 'delete') {
                $isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
                $success = $this->commentModel->deleteComment($commentId, $_SESSION['user_id'], $isAdmin);

                if (!$success) {
                    $this->jsonResponse('error', 'Bạn không có quyền xóa bình luận này hoặc bình luận không tồn tại.');
                }

            } elseif ($action === 'edit') {
                $content = isset($_POST['content']) ? trim($_POST['content']) : '';

                // 3. Validate nội dung khi Sửa (Giống Admin: không rỗng, max 1000)
                if (empty($content)) {
                    $this->jsonResponse('error', 'Nội dung bình luận không được để trống.');
                }
                if (mb_strlen($content) > 1000) {
                    $this->jsonResponse('error', 'Nội dung quá dài (tối đa 1000 ký tự).');
                }

                // Chống XSS trước khi lưu
                $cleanContent = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
                $success = $this->commentModel->updateComment($commentId, $_SESSION['user_id'], $cleanContent);

                if (!$success) {
                    $this->jsonResponse('error', 'Không thể cập nhật. Có thể bạn không phải chủ sở hữu bình luận này.');
                }

            } else {
                $this->jsonResponse('error', 'Hành động không được hỗ trợ.');
            }
        } catch (Exception $e) {
            $this->jsonResponse('error', 'Sự cố hệ thống: ' . $e->getMessage());
        }

        // 4. Phản hồi thành công
        if ($this->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success']);
            exit;
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}