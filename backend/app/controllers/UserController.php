<?php
class UserController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function profile(): void
    {
        AuthMiddleware::requireLogin();

        $user = $this->userModel->findById($_SESSION['user_id']);

        $this->view('users/pages/tai-khoan', [
            'user' => $user
        ], 'users/layouts/main');
    }

    public function updateProfile(): void
    {
        AuthMiddleware::requireLogin();

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

        // 🔥 lấy avatar cũ
        $currentUser = $this->userModel->findById($userId);
        if ($currentUser === null) {
            $this->setFlash('error', 'Không tìm thấy tài khoản người dùng.');
            $this->redirect($this->baseUrl('tai-khoan'));
        }

        $existingUser = $this->userModel->findByEmail($data['email']);
        if (
            $data['email'] !== ''
            && $existingUser !== null
            && (int) ($existingUser['ID'] ?? 0) !== (int) $userId
        ) {
            $this->setFlash('error', 'Email đã tồn tại. Vui lòng chọn email khác.');
            $this->redirect($this->baseUrl('tai-khoan'));
        }

        $oldImage = $currentUser['image'] ?? null;

        if ($file && $file['error'] === UPLOAD_ERR_OK) {

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $allowed)) {
                $this->setFlash('error', 'Chỉ chấp nhận file jpg, jpeg, png hoặc webp.');
                $this->redirect($this->baseUrl('tai-khoan'));
            }

            $fileName = 'avatar_' . $userId . '_' . time() . '.' . $ext;

            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uniphin2/backend/public/uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {

                $imageName = $fileName;

                // 🔥 xóa ảnh cũ
                if ($oldImage && $oldImage !== 'default-avatar.png') {
                    $oldFile = $uploadDir . $oldImage;
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
            }
        }

        $this->userModel->updateProfile($userId, $data, $imageName);

        $_SESSION['name'] = $data['first_name'] . ' ' . $data['last_name'];
        $_SESSION['email'] = $data['email'];

        $this->setFlash('success', 'Cập nhật thông tin thành công.');
        $this->redirect($this->baseUrl('tai-khoan'));
    }
}
