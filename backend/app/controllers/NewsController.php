<?php

class NewsController extends Controller
{
    private $newsModel;

    public function __construct() {
        $this->newsModel = new NewsModel();
    }

    // 1. Danh sách tin tức
    public function index() {
        $filters = [
            'search'   => $_GET['search'] ?? '',
            'category' => $_GET['category'] ?? '',
            'sort'     => $_GET['sort'] ?? 'newest'
        ];

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 9;
        $offset = ($page - 1) * $limit;

        $newsList = $this->newsModel->getNews($filters, $limit, $offset);
        $totalNews = $this->newsModel->countNews($filters);
        
        $data = [
            'news'        => $newsList,
            'totalPages'  => ceil($totalNews / $limit),
            'currentPage' => $page,
            'filters'     => $filters
        ];

        // Nếu là yêu cầu AJAX (ví dụ khi khách bấm lọc hoặc chuyển trang)
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($data);
            exit;
        }

        $this->view('users/pages/news_list', $data);
    }

    // 2. Chi tiết bài viết & Bình luận
    public function detail($slug) {
        $article = $this->newsModel->findBySlug($slug);
        if (!$article) { $this->redirect($this->baseUrl('tin-tuc')); return; }

        $sort = $_GET['comment_sort'] ?? 'newest';
        $isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
        $comments = $this->newsModel->getCommentsByNewsId($article['ID'], $isAdmin, $sort);

        $this->view('users/pages/news_detail', [
            'article' => $article,
            'comments' => $comments,
            'currentSort' => $sort,
            'isLoggedIn' => isset($_SESSION['user_id'])
        ]);
    }

    // 3. Gửi bình luận (Hỗ trợ AJAX để không load lại trang)
    public function postComment() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập!']);
            exit;
        }

        $content = trim($_POST['content'] ?? '');
        $newsId = $_POST['news_id'];
        $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

        if (empty($content)) {
            echo json_encode(['status' => 'error', 'message' => 'Nội dung không được để trống!']);
            exit;
        }

        $result = $this->newsModel->addComment($newsId, $_SESSION['user_id'], $content, $parentId);

        if ($result) {
            echo json_encode([
                'status' => 'success',
                'comment' => [
                    'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'],
                    'content' => htmlspecialchars($content),
                    'created_at' => 'Vừa xong',
                    'image' => $_SESSION['image'] ?? 'default-avatar.png'
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống!']);
        }
        exit;
    }

    // 4. Xử lý xóa/sửa (AJAX)
    public function handleCommentAction() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) exit;

        $action = $_POST['action'] ?? '';
        $commentId = (int)$_POST['comment_id'];
        $isAdmin = ($_SESSION['role'] === 'admin');

        $success = false;
        if ($action === 'delete') {
            $success = $this->newsModel->deleteComment($commentId, $_SESSION['user_id'], $isAdmin);
        } elseif ($action === 'edit') {
            $content = trim($_POST['content']);
            $success = $this->newsModel->updateComment($commentId, $_SESSION['user_id'], $content);
        }

        echo json_encode(['status' => $success ? 'success' : 'error']);
        exit;
    }
}