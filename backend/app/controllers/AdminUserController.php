<?php

class AdminUserController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index(): void
    {
        AuthMiddleware::requireAdmin();

        $filters = [
            'keyword' => trim((string) ($_GET['keyword'] ?? '')),
            'role' => trim((string) ($_GET['role'] ?? '')),
            'status' => trim((string) ($_GET['status'] ?? '')),
        ];

        $this->view('admin/pages/users', [
            'title' => 'Quản lý tài khoản',
            'users' => $this->userModel->getAdminUsers($filters),
            'userFilters' => $filters,
        ], 'admin/layouts/main');
    }

    public function viewDetail(): void
    {
        AuthMiddleware::requireAdmin();

        $userId = (int) ($_GET['id'] ?? 0);
        $filters = [
            'keyword' => trim((string) ($_GET['keyword'] ?? '')),
            'role' => trim((string) ($_GET['role'] ?? '')),
            'status' => trim((string) ($_GET['status'] ?? '')),
        ];

        if ($userId <= 0) {
            $this->setFlash('error', 'Tài khoản không hợp lệ.');
            $this->redirect($this->buildUsersRedirectUrl($filters));
        }

        $user = $this->userModel->findById($userId);

        if ($user === null) {
            $this->setFlash('error', 'Không tìm thấy tài khoản.');
            $this->redirect($this->buildUsersRedirectUrl($filters));
        }

        $this->view('admin/pages/user-detail', [
            'title' => 'Chỉnh sửa tài khoản',
            'user' => $user,
            'userFilters' => $filters,
        ], 'admin/layouts/main');
    }

    public function update(): void
    {
        AuthMiddleware::requireAdmin();

        $userId = (int) ($_GET['id'] ?? 0);
        $filters = [
            'keyword' => trim((string) ($_POST['filter_keyword'] ?? '')),
            'role' => trim((string) ($_POST['filter_role'] ?? '')),
            'status' => trim((string) ($_POST['filter_status'] ?? '')),
        ];

        if ($userId <= 0) {
            $this->setFlash('error', 'Tài khoản không hợp lệ.');
            $this->redirect($this->buildUsersRedirectUrl($filters));
        }

        $currentUser = $this->userModel->findById($userId);

        if ($currentUser === null) {
            $this->setFlash('error', 'Không tìm thấy tài khoản.');
            $this->redirect($this->buildUsersRedirectUrl($filters));
        }

        $data = [
            'first_name' => trim((string) ($_POST['first_name'] ?? '')),
            'last_name' => trim((string) ($_POST['last_name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'address' => trim((string) ($_POST['address'] ?? '')),
            'gender' => trim((string) ($_POST['gender'] ?? '')),
            'birth_date' => trim((string) ($_POST['birth_date'] ?? '')),
            'role' => trim((string) ($_POST['role'] ?? 'customer')),
            'status' => trim((string) ($_POST['status'] ?? 'active')),
        ];

        if ($data['first_name'] === '' || $data['last_name'] === '' || $data['email'] === '') {
            $this->setFlash('error', 'Vui lòng nhập đầy đủ họ, tên và email.');
            $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'Email không hợp lệ.');
            $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
        }

        if ($this->userModel->emailExists($data['email'], $userId)) {
            $this->setFlash('error', 'Email đã được sử dụng bởi tài khoản khác.');
            $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
        }

        if (!in_array($data['role'], ['admin', 'customer'], true)) {
            $this->setFlash('error', 'Vai trò không hợp lệ.');
            $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
        }

        if (!in_array($data['status'], ['active', 'banned', 'pending', 'inactive'], true)) {
            $this->setFlash('error', 'Trạng thái không hợp lệ.');
            $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
        }

        if ($userId === (int) ($_SESSION['user_id'] ?? 0)) {
            if ($data['role'] !== 'admin') {
                $this->setFlash('error', 'Bạn không thể tự thay đổi vai trò của chính mình.');
                $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
            }

            if ($data['status'] !== 'active') {
                $this->setFlash('error', 'Bạn không thể tự chuyển trạng thái tài khoản hiện tại khỏi hoạt động.');
                $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
            }
        }

        $imageName = null;
        $file = $_FILES['avatar'] ?? null;
        $oldImage = $currentUser['image'] ?? null;

        if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $this->setFlash('error', 'Tải ảnh đại diện thất bại.');
                $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
            }

            $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $allowed, true)) {
                $this->setFlash('error', 'Chỉ chấp nhận file jpg, jpeg, png hoặc webp.');
                $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
            }

            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uniphin2/backend/public/uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $imageName = 'user_' . $userId . '_' . time() . '.' . $ext;

            if (!move_uploaded_file($file['tmp_name'], $uploadDir . $imageName)) {
                $this->setFlash('error', 'Không thể lưu ảnh đại diện.');
                $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
            }

            if ($oldImage && $oldImage !== $imageName) {
                $oldFile = $uploadDir . $oldImage;
                if (is_file($oldFile)) {
                    unlink($oldFile);
                }
            }
        }

        if (!$this->userModel->updateUserByAdmin($userId, $data, $imageName)) {
            $this->setFlash('error', 'Không thể cập nhật tài khoản. Vui lòng thử lại.');
            $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
        }

        if ($userId === (int) ($_SESSION['user_id'] ?? 0)) {
            $_SESSION['name'] = trim($data['first_name'] . ' ' . $data['last_name']);
            $_SESSION['email'] = $data['email'];
        }

        $this->setFlash('success', 'Cập nhật tài khoản thành công.');
        $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
    }

    public function delete(): void
    {
        AuthMiddleware::requireAdmin();

        $userId = (int) ($_GET['id'] ?? 0);
        $filters = [
            'keyword' => trim((string) ($_POST['filter_keyword'] ?? '')),
            'role' => trim((string) ($_POST['filter_role'] ?? '')),
            'status' => trim((string) ($_POST['filter_status'] ?? '')),
        ];

        if ($userId <= 0) {
            $this->setFlash('error', 'Tài khoản không hợp lệ.');
            $this->redirect($this->buildUsersRedirectUrl($filters));
        }

        if ($userId === (int) ($_SESSION['user_id'] ?? 0)) {
            $this->setFlash('error', 'Bạn không thể tự xóa tài khoản đang đăng nhập.');
            $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
        }

        $user = $this->userModel->findById($userId);

        if ($user === null) {
            $this->setFlash('error', 'Không tìm thấy tài khoản.');
            $this->redirect($this->buildUsersRedirectUrl($filters));
        }

        $blockReason = $this->userModel->getDeletionBlockReason($userId, (string) ($user['role'] ?? ''));

        if ($blockReason !== null) {
            $this->setFlash('error', $blockReason);
            $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
        }

        $oldImage = (string) ($user['image'] ?? '');
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uniphin2/backend/public/uploads/';

        if (!$this->userModel->deleteUserByAdmin($userId)) {
            $this->setFlash('error', 'Không thể xóa tài khoản. Vui lòng thử lại.');
            $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
        }

        if ($oldImage !== '') {
            $oldFile = $uploadDir . $oldImage;
            if (is_file($oldFile)) {
                unlink($oldFile);
            }
        }

        $this->setFlash('success', 'Đã xóa tài khoản thành công.');
        $this->redirect($this->buildUsersRedirectUrl($filters));
    }

    private function buildUsersRedirectUrl(array $filters = []): string
    {
        $query = http_build_query(array_filter($filters, static fn($value): bool => $value !== ''));
        $path = 'admin/users';

        if ($query !== '') {
            $path .= '?' . $query;
        }

        return $this->baseUrl($path);
    }

    private function buildUserDetailRedirectUrl(int $userId, array $filters = []): string
    {
        $queryData = array_filter($filters, static fn($value): bool => $value !== '');
        $queryData['id'] = $userId;
        $query = http_build_query($queryData);

        return $this->baseUrl('admin/users/viewdetail' . ($query !== '' ? '?' . $query : ''));
    }
}
