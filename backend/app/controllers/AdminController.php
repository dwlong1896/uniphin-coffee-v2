<?php

class AdminController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function dashboard(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/index', ['title' => 'Dashboard'], 'admin/layouts/main');
    }

    public function users(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/users', ['title' => 'Quản lý người dùng'], 'admin/layouts/main');
    }

    public function products(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/products', ['title' => 'Quản lý sản phẩm'], 'admin/layouts/main');
    }

    public function posts(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/posts', ['title' => 'Quản lý tin tức'], 'admin/layouts/main');
    }

    public function comments(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/comments', ['title' => 'Quản lý bình luận'], 'admin/layouts/main');
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
            'user' => $user,
        ], 'admin/layouts/main');
    }

    public function updateProfile(): void
    {
        AuthMiddleware::requireAdmin();

        $userId = $_SESSION['user_id'];

        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'gender' => trim($_POST['gender'] ?? ''),
            'birth_date' => trim($_POST['birth_date'] ?? ''),
        ];

        $file = $_FILES['avatar'] ?? null;
        $imageName = null;

        $currentUser = $this->userModel->findById($userId);
        $oldImage = $currentUser['image'] ?? null;

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $allowed, true)) {
                $this->setFlash('error', 'File khong hop le');
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

        $this->userModel->updateProfile($userId, $data, $imageName);

        $_SESSION['name'] = $data['first_name'] . ' ' . $data['last_name'];

        $this->setFlash('success', 'Cap nhat ho so thanh cong!');
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