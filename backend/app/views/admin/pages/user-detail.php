<?php
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

$toUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/' . ltrim($path, '/');
};

$user = is_array($user ?? null) ? $user : [];
$userFilters = is_array($userFilters ?? null) ? $userFilters : [];

$fullName = trim((string) (($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')));
$email = htmlspecialchars((string) ($user['email'] ?? ''), ENT_QUOTES, 'UTF-8');
$phone = htmlspecialchars((string) ($user['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
$address = htmlspecialchars((string) ($user['address'] ?? ''), ENT_QUOTES, 'UTF-8');
$gender = htmlspecialchars((string) ($user['gender'] ?? ''), ENT_QUOTES, 'UTF-8');
$birthDate = htmlspecialchars((string) ($user['birth_date'] ?? ''), ENT_QUOTES, 'UTF-8');
$status = (string) ($user['status'] ?? 'active');
$createdAt = htmlspecialchars((string) ($user['created_at'] ?? ''), ENT_QUOTES, 'UTF-8');
$updatedAt = htmlspecialchars((string) ($user['updated_at'] ?? ''), ENT_QUOTES, 'UTF-8');
$avatar = !empty($user['image'])
    ? $toUrl('uploads/' . $user['image'])
    : $toUrl('assets/admin/images/author/avatar.png');
$role = (string) ($user['role'] ?? 'customer');

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

$statusClass = match ($status) {
    'active' => 'bg-success',
    'banned' => 'bg-danger',
    'pending' => 'bg-warning text-dark',
    'inactive' => 'bg-secondary',
    default => 'bg-secondary',
};

$filterQuery = http_build_query(array_filter([
    'keyword' => (string) ($userFilters['keyword'] ?? ''),
    'role' => (string) ($userFilters['role'] ?? ''),
    'status' => (string) ($userFilters['status'] ?? ''),
], static fn($value): bool => $value !== ''));

$backUrl = $toUrl('admin/users' . ($filterQuery !== '' ? '?' . $filterQuery : ''));
?>

<div class="row mt-4">
    <div class="col-12">
        <?php if (!empty($flashSuccess)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!empty($flashError)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body text-white p-4"
                style="background: linear-gradient(135deg, #198754, #0d6efd); border-radius: 0.375rem;">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <img src="<?php echo htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8'); ?>" alt="avatar"
                            style="width:96px;height:96px;border-radius:50%;border:3px solid rgba(255,255,255,0.25);object-fit:cover;">
                        <div>
                            <h3 class="mb-1"><?php echo htmlspecialchars($fullName !== '' ? $fullName : 'Chưa cập nhật', ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="mb-2" style="opacity:0.9;">Mã tài khoản: #<?php echo htmlspecialchars((string) ($user['ID'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge <?php echo htmlspecialchars($roleClasses[$role] ?? 'bg-secondary', ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($roleLabels[$role] ?? $role, ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                                <span class="badge <?php echo htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($statusLabels[$status] ?? $status, ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="<?php echo htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">
                            <i class="ti-arrow-left"></i> Quay lại danh sách
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title mb-0">Thông tin khách hàng</h4>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-user me-2 text-muted"></i> Họ và tên</span>
                        <strong><?php echo htmlspecialchars($fullName !== '' ? $fullName : 'Chưa cập nhật', ENT_QUOTES, 'UTF-8'); ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-envelope me-2 text-muted"></i> Email</span>
                        <strong><?php echo $email !== '' ? $email : '—'; ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-phone me-2 text-muted"></i> Số điện thoại</span>
                        <strong><?php echo $phone !== '' ? $phone : '—'; ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-location-dot me-2 text-muted"></i> Địa chỉ</span>
                        <strong><?php echo $address !== '' ? $address : '—'; ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-person me-2 text-muted"></i> Giới tính</span>
                        <strong><?php echo $gender !== '' ? $gender : '—'; ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-calendar-days me-2 text-muted"></i> Ngày sinh</span>
                        <strong><?php echo $birthDate !== '' ? $birthDate : '—'; ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-user-tag me-2 text-muted"></i> Vai trò</span>
                        <strong><?php echo htmlspecialchars($roleLabels[$role] ?? $role, ENT_QUOTES, 'UTF-8'); ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-calendar-plus me-2 text-muted"></i> Ngày tạo</span>
                        <strong><?php echo $createdAt !== '' ? $createdAt : '—'; ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-clock-rotate-left me-2 text-muted"></i> Cập nhật</span>
                        <strong><?php echo $updatedAt !== '' ? $updatedAt : '—'; ?></strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title mb-0">Quản trị tài khoản</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($toUrl('admin/users/update?id=' . urlencode((string) ($user['ID'] ?? ''))), ENT_QUOTES, 'UTF-8'); ?>"
                    method="post">
                    <input type="hidden" name="filter_keyword"
                        value="<?php echo htmlspecialchars((string) ($userFilters['keyword'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="filter_status"
                        value="<?php echo htmlspecialchars((string) ($userFilters['status'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái tài khoản</label>
                            <select name="status" class="form-control">
                                <?php foreach ($statusLabels as $statusValue => $statusLabel): ?>
                                <option value="<?php echo htmlspecialchars($statusValue, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo $status === $statusValue ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" name="new_password" class="form-control"
                                placeholder="Nhập mật khẩu mới nếu muốn cấp lại">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" name="confirm_password" class="form-control"
                                placeholder="Nhập lại mật khẩu mới">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </form>
            </div>
        </div>
    </div>
</div>
