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
            $this->setFlash('error', 'Không tìm thấy tài khoản người dùng.');
            $this->redirect($this->buildUsersRedirectUrl($filters));
        }

        $this->view('admin/pages/user-detail', [
            'title' => 'Quản lý tài khoản',
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

        $user = $this->userModel->findById($userId);

        if ($user === null) {
            $this->setFlash('error', 'Không tìm thấy tài khoản người dùng.');
            $this->redirect($this->buildUsersRedirectUrl($filters));
        }

        $status = trim((string) ($_POST['status'] ?? 'active'));
        $newPassword = trim((string) ($_POST['new_password'] ?? ''));
        $confirmPassword = trim((string) ($_POST['confirm_password'] ?? ''));

        if (!in_array($status, ['active', 'banned', 'pending', 'inactive'], true)) {
            $this->setFlash('error', 'Trạng thái không hợp lệ.');
            $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
        }

        if ($userId === (int) ($_SESSION['user_id'] ?? 0) && $status !== 'active') {
            $this->setFlash('error', 'Bạn không thể tự chuyển trạng thái tài khoản hiện tại khỏi hoạt động.');
            $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
        }

        $passwordHash = null;

        if ($newPassword !== '' || $confirmPassword !== '') {
            if (strlen($newPassword) < 6) {
                $this->setFlash('error', 'Mật khẩu mới phải có ít nhất 6 ký tự.');
                $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
            }

            if ($newPassword !== $confirmPassword) {
                $this->setFlash('error', 'Xác nhận mật khẩu mới không khớp.');
                $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
            }

            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        if (!$this->userModel->updateAdminControls($userId, $status, $passwordHash)) {
            $this->setFlash('error', 'Không thể cập nhật tài khoản người dùng. Vui lòng thử lại.');
            $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
        }

        $successMessage = $passwordHash !== null
            ? 'Đã cập nhật trạng thái và cấp mật khẩu mới cho tài khoản người dùng.'
            : 'Đã cập nhật trạng thái tài khoản người dùng.';

        $this->setFlash('success', $successMessage);
        $this->redirect($this->buildUserDetailRedirectUrl($userId, $filters));
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