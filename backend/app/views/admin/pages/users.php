<?php
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$toUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/' . ltrim($path, '/');
};

$users = is_array($users ?? null) ? $users : [];
$userFilters = is_array($userFilters ?? null) ? $userFilters : [];

$buildUserDetailUrl = static function (int $userId) use ($toUrl, $userFilters): string {
    $params = array_filter([
        'id' => $userId,
        'keyword' => (string) ($userFilters['keyword'] ?? ''),
        'role' => (string) ($userFilters['role'] ?? ''),
        'status' => (string) ($userFilters['status'] ?? ''),
    ], static fn($value): bool => $value !== '');

    $query = http_build_query($params);

    return $toUrl('admin/users/viewdetail' . ($query !== '' ? '?' . $query : ''));
};

$formatDateTime = static function (?string $value): string {
    if ($value === null || trim($value) === '') {
        return '--';
    }

    $timestamp = strtotime($value);

    return $timestamp ? date('d/m/Y H:i', $timestamp) : $value;
};

$roleLabels = [
    'admin' => 'Quản trị viên',
    'customer' => 'Khách hàng',
];

$roleClasses = [
    'admin' => 'bg-primary',
    'customer' => 'bg-info text-dark',
];

$statusLabels = [
    'active' => 'Đang hoạt động',
    'banned' => 'Đã khóa',
    'pending' => 'Chờ duyệt',
    'inactive' => 'Tạm ẩn',
];

$statusClasses = [
    'active' => 'bg-success',
    'banned' => 'bg-danger',
    'pending' => 'bg-warning text-dark',
    'inactive' => 'bg-secondary',
];
?>

<div class="row">
    <div class="col-12 mt-4">
        <?php if (!empty($flashSuccess)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!empty($flashError)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-body">
                <div class="d-lg-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="header-title mb-1">Quản lý tài khoản</h4>
                        <p class="text-muted mb-0">Theo dõi danh sách người dùng và chỉnh sửa hồ sơ khi cần.</p>
                    </div>
                    <a href="<?php echo htmlspecialchars($toUrl('admin/profile'), ENT_QUOTES, 'UTF-8'); ?>"
                        class="btn btn-outline-primary mt-3 mt-lg-0">
                        <i class="ti-user"></i> Hồ sơ của tôi
                    </a>
                </div>

                <form action="<?php echo htmlspecialchars($toUrl('admin/users'), ENT_QUOTES, 'UTF-8'); ?>" method="get"
                    class="row g-3 align-items-end">
                    <div class="col-lg-5">
                        <label class="form-label">Tìm kiếm</label>
                        <input type="text" name="keyword" class="form-control"
                            placeholder="Họ tên, email hoặc số điện thoại"
                            value="<?php echo htmlspecialchars((string) ($userFilters['keyword'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">Vai trò</label>
                        <select name="role" class="form-control">
                            <option value="">Tất cả</option>
                            <?php foreach ($roleLabels as $roleValue => $roleLabel): ?>
                            <option value="<?php echo htmlspecialchars($roleValue, ENT_QUOTES, 'UTF-8'); ?>"
                                <?php echo (($userFilters['role'] ?? '') === $roleValue) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-control">
                            <option value="">Tất cả</option>
                            <?php foreach ($statusLabels as $statusValue => $statusLabel): ?>
                            <option value="<?php echo htmlspecialchars($statusValue, ENT_QUOTES, 'UTF-8'); ?>"
                                <?php echo (($userFilters['status'] ?? '') === $statusValue) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">Lọc</button>
                            <a href="<?php echo htmlspecialchars($toUrl('admin/users'), ENT_QUOTES, 'UTF-8'); ?>"
                                class="btn btn-outline-secondary w-100">Xóa</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-body">
                <div class="d-sm-flex justify-content-between align-items-center mb-4">
                    <h4 class="header-title mb-0">Danh sách tài khoản</h4>
                    <span class="text-muted">Tổng cộng: <?php echo htmlspecialchars((string) count($users), ENT_QUOTES, 'UTF-8'); ?> tài khoản</span>
                </div>

                <div class="data-tables">
                    <table id="dataTable" class="text-center">
                        <thead class="bg-light text-capitalize">
                            <tr>
                                <th class="text-center">Mã</th>
                                <th class="text-center">Người dùng</th>
                                <th class="text-center">Liên hệ</th>
                                <th class="text-center">Vai trò</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Ngày tạo</th>
                                <th class="text-center">Cập nhật</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($users !== []): ?>
                            <?php foreach ($users as $user): ?>
                            <?php
                                $userId = (int) ($user['ID'] ?? 0);
                                $fullName = trim((string) (($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')));
                                $role = (string) ($user['role'] ?? '');
                                $status = (string) ($user['status'] ?? '');
                                $avatar = !empty($user['image'])
                                    ? $toUrl('uploads/' . $user['image'])
                                    : $toUrl('assets/admin/images/author/avatar.png');
                            ?>
                            <tr>
                                <td class="fw-semibold">#<?php echo htmlspecialchars((string) $userId, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-start">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?php echo htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8'); ?>" alt="avatar"
                                            style="width:44px;height:44px;border-radius:50%;object-fit:cover;">
                                        <div>
                                            <div class="fw-semibold">
                                                <?php echo htmlspecialchars($fullName !== '' ? $fullName : 'Chưa cập nhật', ENT_QUOTES, 'UTF-8'); ?>
                                            </div>
                                            <small class="text-muted"><?php echo htmlspecialchars((string) ($user['gender'] ?? '--'), ENT_QUOTES, 'UTF-8'); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-start">
                                    <div><?php echo htmlspecialchars((string) ($user['email'] ?? '--'), ENT_QUOTES, 'UTF-8'); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars((string) ($user['phone'] ?? '--'), ENT_QUOTES, 'UTF-8'); ?></small>
                                </td>
                                <td>
                                    <span class="badge <?php echo htmlspecialchars($roleClasses[$role] ?? 'bg-secondary', ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($roleLabels[$role] ?? $role, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo htmlspecialchars($statusClasses[$status] ?? 'bg-secondary', ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($statusLabels[$status] ?? $status, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($formatDateTime((string) ($user['created_at'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($formatDateTime((string) ($user['updated_at'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <a href="<?php echo htmlspecialchars($buildUserDetailUrl($userId), ENT_QUOTES, 'UTF-8'); ?>"
                                        class="btn btn-sm btn-primary">
                                        <i class="ti-pencil-alt"></i> Chỉnh sửa
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="8">Chưa có tài khoản nào phù hợp với bộ lọc hiện tại.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
