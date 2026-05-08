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
        require_once __DIR__ . '/../models/FaqModel.php';
        $faqModel = new FaqModel();
        $this->view('admin/pages/qa', [
            'title' => 'Quản lý hỏi đáp',
            'faqs'  => $faqModel->getAllAdmin(),
        ], 'admin/layouts/main');
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
        require_once __DIR__ . '/../models/AboutSectionModel.php';
        $model = new AboutSectionModel();
        $this->view('admin/pages/aboutpage', [
            'title'    => 'About Page',
            'sections' => $model->getAll(),
        ], 'admin/layouts/main');
    }

    public function faqspage(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/faqpage', ['title' => 'FAQ Page'], 'admin/layouts/main');
    }

    // --- FAQ CRUD ---

    public function faqSave(): void
    {
        AuthMiddleware::requireAdmin();
        require_once __DIR__ . '/../models/FaqModel.php';
        $faqModel = new FaqModel();

        $id   = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $data = [
            'question'   => trim($_POST['question'] ?? ''),
            'answer'     => trim($_POST['answer'] ?? ''),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active'  => (int)($_POST['is_active'] ?? 1),
        ];

        if ($id) {
            $faqModel->update($id, $data);
            $this->setFlash('success', 'Cập nhật câu hỏi thành công!');
        } else {
            $faqModel->create($data);
            $this->setFlash('success', 'Thêm câu hỏi thành công!');
        }
        $this->redirect($this->baseUrl('admin/qa'));
    }

    public function faqDelete(): void
    {
        AuthMiddleware::requireAdmin();
        require_once __DIR__ . '/../models/FaqModel.php';
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            (new FaqModel())->delete($id);
            $this->setFlash('success', 'Đã xóa câu hỏi!');
        }
        $this->redirect($this->baseUrl('admin/qa'));
    }

    // --- About Section Save ---

    public function aboutSave(): void
    {
        AuthMiddleware::requireAdmin();
        require_once __DIR__ . '/../models/AboutSectionModel.php';
        $model = new AboutSectionModel();

        $id   = (int)($_POST['id'] ?? 0);
        $data = [
            'title'     => trim($_POST['title'] ?? ''),
            'content'   => trim($_POST['content'] ?? ''),
            'image_url' => trim($_POST['image_url'] ?? ''),
        ];

        if ($id) {
            $model->update($id, $data);
            $this->setFlash('success', 'Cập nhật nội dung thành công!');
        }
        $this->redirect($this->baseUrl('admin/aboutpage'));
    }
}