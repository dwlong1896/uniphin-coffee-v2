<?php

class AdminController extends Controller
{
    private UserModel $userModel;
    private NewsModel $newsModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->newsModel = new NewsModel();
    }


    public function dashboard(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/index', ['title' => 'Dashboard'], 'admin/layouts/main');
    }

    public function users(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/users', ['title' => 'Quản lý tài khoản'], 'admin/layouts/main');
    }

    public function products(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/products', ['title' => 'Quản lý sản phẩm'], 'admin/layouts/main');
    }

    public function orders(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/orders', ['title' => 'Quản lý đơn hàng'], 'admin/layouts/main');
    }

    public function posts(): void {
    AuthMiddleware::requireAdmin();
    
    // Lấy filter từ URL để Admin cũng có thể tìm kiếm bài viết
    $filters = [
        'search'   => $_GET['search'] ?? '',
        'category' => $_GET['category'] ?? '',
        'sort'     => $_GET['sort'] ?? 'newest'
    ];

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $newsList = $this->newsModel->getNews($filters, $limit, $offset);
    $totalNews = $this->newsModel->countNews($filters);

    $this->view('admin/pages/posts', [
        'title' => 'Quản lý tin tức',
        'news'  => $newsList,
        'totalPages' => ceil($totalNews / $limit),
        'currentPage' => $page
    ], 'admin/layouts/main');
}
    public function deletePost(): void {
        AuthMiddleware::requireAdmin();
        header('Content-Type: application/json');
        
        $id = $_POST['id'] ?? null;
        if ($id) {
            $result = $this->newsModel->deletePost((int)$id);
            echo json_encode(['status' => $result ? 'success' : 'error', 'message' => 'Đã xóa bài viết']);
        }
        exit;
    }
    public function handleCreatePost(): void {
        AuthMiddleware::requireAdmin();
        header('Content-Type: application/json');

        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        // --- THÊM SEO ---
        $meta_keywords = trim($_POST['meta_keywords'] ?? '');
        $meta_description = trim($_POST['meta_description'] ?? '');

        if (empty($title) || empty($content)) {
            echo json_encode(['status' => 'error', 'message' => 'Tiêu đề và nội dung không được trống!']);
            exit;
        }

        if (empty($slug)) { $slug = $this->newsModel->slugify($title); }

        // --- XỬ LÝ UPLOAD HÌNH ẢNH (THUMBNAIL) ---
        $file = $_FILES['thumbnail'] ?? null;
        $imageName = 'default-news.png'; // Ảnh mặc định nếu không upload

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($ext, $allowed)) {
                $imageName = 'news_' . time() . '_' . rand(100, 999) . '.' . $ext;
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uniphin2/backend/public/uploads/news/';
                
                if (!is_dir($uploadDir)) { mkdir($uploadDir, 0755, true); }
                move_uploaded_file($file['tmp_name'], $uploadDir . $imageName);
            }
        }

        $data = [
            'title' => $title,
            'content' => $content,
            'slug' => $slug,
            'image' => $imageName,
            'meta_keywords' => $meta_keywords,
            'meta_description' => $meta_description,
            'admin_id' => $_SESSION['user_id']
        ];

        $result = $this->newsModel->createPost($data);
        echo json_encode(['status' => $result ? 'success' : 'error']);
        exit;
    }
    public function getPostJson(): void {
    AuthMiddleware::requireAdmin();
    header('Content-Type: application/json');

    $id = $_GET['id'] ?? null;
    if ($id) {
        $post = $this->newsModel->getPostById((int)$id);
        echo json_encode($post);
    }
    exit;
}
    public function handleUpdatePost(): void {
        AuthMiddleware::requireAdmin();
        header('Content-Type: application/json');

        $id = (int)$_POST['id'];
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $meta_keywords = trim($_POST['meta_keywords'] ?? '');
        $meta_description = trim($_POST['meta_description'] ?? '');

        // Lấy bài viết cũ để biết ảnh cũ là gì
        $oldPost = $this->newsModel->getPostById($id);
        $imageName = $oldPost['image']; 

        // --- XỬ LÝ UPLOAD HÌNH ẢNH MỚI ---
        $file = $_FILES['thumbnail'] ?? null;
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $newImageName = 'news_' . time() . '.' . $ext;
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uniphin2/backend/public/uploads/news/';
                
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $newImageName)) {
                    // Xóa ảnh cũ (nếu không phải ảnh mặc định)
                    if ($imageName && $imageName !== 'default-news.png') {
                        $oldPath = $uploadDir . $imageName;
                        if (file_exists($oldPath)) { unlink($oldPath); }
                    }
                    $imageName = $newImageName;
                }
            }
        }

        $data = [
            'title' => $title,
            'content' => $content,
            'slug' => $slug,
            'image' => $imageName,
            'meta_keywords' => $meta_keywords,
            'meta_description' => $meta_description
        ];

        $result = $this->newsModel->updatePost($id, $data);
        echo json_encode(['status' => $result ? 'success' : 'error']);
        exit;
    }
// Hàm xử lý Ẩn/Hiện bình luận (AJAX cho mượt như Hiền muốn)
    public function toggleCommentStatus(): void {
    AuthMiddleware::requireAdmin();
    header('Content-Type: application/json');
    
    $commentId = $_POST['id'] ?? null;
    if ($commentId) {
        $result = $this->newsModel->toggleCommentStatus((int)$commentId);
        echo json_encode(['status' => $result ? 'success' : 'error']);
    }
    exit;
}
    public function getCommentJson(): void {
        AuthMiddleware::requireAdmin();
        header('Content-Type: application/json');

        $id = $_GET['id'] ?? null;
        if ($id) {
            // Hiền cần viết thêm hàm getCommentById trong Model nhé
            $comment = $this->newsModel->getCommentById((int)$id);
            echo json_encode($comment);
        }
        exit;
    }
    public function comments(): void
    {
        AuthMiddleware::requireAdmin();
        $newsId = $_GET['news_id'] ?? null;
        $title = 'Quản lý tất cả bình luận';
        
        if ($newsId) {
            $article = $this->newsModel->getPostById((int)$newsId);
            $title = "Bình luận cho bài: " . ($article['title'] ?? 'N/A');
            $comments = $this->newsModel->getAllCommentsForAdmin((int)$newsId);
        } else {
            $comments = $this->newsModel->getAllCommentsForAdmin();
        }

        $this->view('admin/pages/comments', [
            'title'    => $title,
            'comments' => $comments,
            'newsId'   => $newsId // Để view biết đang lọc hay xem tất cả
        ], 'admin/layouts/main');
    }

    public function deleteComment(): void {
        AuthMiddleware::requireAdmin();
        header('Content-Type: application/json');

        $id = $_POST['id'] ?? null;
        if ($id) {
            // Admin xóa nên truyền ID người dùng là 0 và isAdmin là true
            $result = $this->newsModel->deleteComment((int)$id, 0, true);
            echo json_encode(['status' => $result ? 'success' : 'error']);
        }
        exit;
    }
    public function handlePostComment(): void {
        AuthMiddleware::requireAdmin();
        header('Content-Type: application/json');

        $newsId   = (int)$_POST['news_id'];
        $content  = trim($_POST['content'] ?? '');
        $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

        if (empty($content)) {
            echo json_encode(['status' => 'error', 'message' => 'Nội dung không được để trống!']);
            exit;
        }

        // Sử dụng ID của Admin đang đăng nhập
        $result = $this->newsModel->addComment($newsId, $_SESSION['user_id'], $content, $parentId);
        
        echo json_encode(['status' => $result ? 'success' : 'error']);
        exit;
    }
    public function handleUpdateComment(): void {
        AuthMiddleware::requireAdmin();
        header('Content-Type: application/json');

        $commentId = (int)$_POST['id'];
        $content = trim($_POST['content'] ?? '');

        if (empty($content)) {
            echo json_encode(['status' => 'error', 'message' => 'Nội dung không được để trống!']);
            exit;
        }

        // Gọi lại hàm updateComment mà tụi mình đã thống nhất (chỉ chủ sở hữu mới sửa được)
        $result = $this->newsModel->updateComment($commentId, $_SESSION['user_id'], $content);
        
        echo json_encode(['status' => $result ? 'success' : 'error']);
        exit;
    }
    public function contacts(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/contacts', ['title' => 'Quản lý liên hệ'], 'admin/layouts/main');
    }

    public function qa(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/qa', ['title' => 'Quản lý hỏi đáp'], 'admin/layouts/main');
    }

    public function profile(): void
    {
        AuthMiddleware::requireAdmin();

        $user = $this->userModel->findById($_SESSION['user_id']);
        
        $this->view('admin/pages/profile', [
        'title' => 'My Profile',
        'user'  => $user,
    ], 'admin/layouts/main');
    }


    public function updateProfile(): void
    {
        AuthMiddleware::requireAdmin();

        $userId = $_SESSION['user_id'];

        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name'  => trim($_POST['last_name']  ?? ''),
            'email'      => trim($_POST['email']      ?? ''),
            'phone'      => trim($_POST['phone']      ?? ''),
            'address'    => trim($_POST['address']    ?? ''),
            'gender'     => trim($_POST['gender']     ?? ''),
            'birth_date' => trim($_POST['birth_date'] ?? ''),
        ];

        $file = $_FILES['avatar'] ?? null;
        $imageName = null;

        // lấy user hiện tại
        $currentUser = $this->userModel->findById($userId);
        $oldImage = $currentUser['image'] ?? null;

        if ($file && $file['error'] === UPLOAD_ERR_OK) {

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $allowed)) {
                $this->setFlash('error', 'File không hợp lệ');
                $this->redirect($this->baseUrl('admin/profile'));
            }

            $fileName = 'avatar_' . $userId . '_' . time() . '.' . $ext;

            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uniphin2/backend/public/uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {

                $imageName = $fileName;

                if ($oldImage) {
                    $oldFile = $uploadDir . $oldImage;
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
            }
        }

        // 👉 Gọi model
        $this->userModel->updateProfile($userId, $data, $imageName);

        $_SESSION['name'] = $data['first_name'] . ' ' . $data['last_name'];

        $this->setFlash('success', 'Cập nhật hồ sơ thành công!');
        $this->redirect($this->baseUrl('admin/profile'));
    }
    
    public function homepage(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/homepage', ['title' => 'Home Page'], 'admin/layouts/main');
    }

    public function contactpage(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/contactpage', ['title' => 'Contact Page'], 'admin/layouts/main');
    }

    public function aboutpage(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/aboutpage', ['title' => 'About Page'], 'admin/layouts/main');
    }

    public function faqspage(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/faqpage', ['title' => 'FAQ Page'], 'admin/layouts/main');
    }
}