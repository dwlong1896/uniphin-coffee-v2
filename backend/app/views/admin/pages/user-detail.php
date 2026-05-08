<?php
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

$toUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/' . ltrim($path, '/');
};

$user = is_array($user ?? null) ? $user : [];
$userFilters = is_array($userFilters ?? null) ? $userFilters : [];

$fullName = trim((string) (($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')));
$firstName = htmlspecialchars((string) ($user['first_name'] ?? ''), ENT_QUOTES, 'UTF-8');
$lastName = htmlspecialchars((string) ($user['last_name'] ?? ''), ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars((string) ($user['email'] ?? ''), ENT_QUOTES, 'UTF-8');
$phone = htmlspecialchars((string) ($user['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
$address = htmlspecialchars((string) ($user['address'] ?? ''), ENT_QUOTES, 'UTF-8');
$gender = htmlspecialchars((string) ($user['gender'] ?? ''), ENT_QUOTES, 'UTF-8');
$birthDate = htmlspecialchars((string) ($user['birth_date'] ?? ''), ENT_QUOTES, 'UTF-8');
$role = (string) ($user['role'] ?? 'customer');
$status = (string) ($user['status'] ?? 'active');
$createdAt = htmlspecialchars((string) ($user['created_at'] ?? ''), ENT_QUOTES, 'UTF-8');
$updatedAt = htmlspecialchars((string) ($user['updated_at'] ?? ''), ENT_QUOTES, 'UTF-8');
$avatar = !empty($user['image'])
    ? $toUrl('uploads/' . $user['image'])
    : $toUrl('assets/admin/images/author/avatar.png');

$roleLabels = [
    'admin' => 'Quản trị viên',
    'customer' => 'Khách hàng',
];

$statusLabels = [
    'active' => 'Đang hoạt động',
    'banned' => 'Đã khóa',
    'pending' => 'Chờ duyệt',
    'inactive' => 'Tạm ẩn',
];

$roleClass = $role === 'admin' ? 'bg-primary' : 'bg-info text-dark';
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
                                <span class="badge <?php echo htmlspecialchars($roleClass, ENT_QUOTES, 'UTF-8'); ?>">
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
                <h4 class="header-title mb-0">Thông tin tài khoản</h4>
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
                        <span><i class="fa-solid fa-user-tag me-2 text-muted"></i> Vai trò</span>
                        <strong><?php echo htmlspecialchars($roleLabels[$role] ?? $role, ENT_QUOTES, 'UTF-8'); ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-shield-halved me-2 text-muted"></i> Trạng thái</span>
                        <strong><?php echo htmlspecialchars($statusLabels[$status] ?? $status, ENT_QUOTES, 'UTF-8'); ?></strong>
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
                <h4 class="header-title mb-0">Chỉnh sửa hồ sơ</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($toUrl('admin/users/update?id=' . urlencode((string) ($user['ID'] ?? ''))), ENT_QUOTES, 'UTF-8'); ?>"
                    method="post" enctype="multipart/form-data">
                    <input type="hidden" name="filter_keyword"
                        value="<?php echo htmlspecialchars((string) ($userFilters['keyword'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="filter_role"
                        value="<?php echo htmlspecialchars((string) ($userFilters['role'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="filter_status"
                        value="<?php echo htmlspecialchars((string) ($userFilters['status'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tên</label>
                            <input type="text" name="first_name" class="form-control" value="<?php echo $firstName; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Họ và tên đệm</label>
                            <input type="text" name="last_name" class="form-control" value="<?php echo $lastName; ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo $phone; ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" name="address" class="form-control" value="<?php echo $address; ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Giới tính</label>
                            <select name="gender" class="form-control">
                                <option value="">-- Chọn giới tính --</option>
                                <option value="male" <?php echo $gender === 'male' ? 'selected' : ''; ?>>Nam</option>
                                <option value="female" <?php echo $gender === 'female' ? 'selected' : ''; ?>>Nữ</option>
                                <option value="other" <?php echo $gender === 'other' ? 'selected' : ''; ?>>Khác</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" name="birth_date" class="form-control" value="<?php echo $birthDate; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ảnh đại diện</label>
                            <input type="file" name="avatar" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Vai trò</label>
                            <select name="role" class="form-control">
                                <?php foreach ($roleLabels as $roleValue => $roleLabel): ?>
                                <option value="<?php echo htmlspecialchars($roleValue, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo $role === $roleValue ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái</label>
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

                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>

                <hr class="my-4">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h6 class="mb-1 text-danger">Xóa tài khoản</h6>
                        <p class="mb-0 text-muted">Chỉ nên xóa khi tài khoản không còn dữ liệu ràng buộc như đơn hàng, bình luận hoặc nội dung quản trị.</p>
                    </div>
                    <form action="<?php echo htmlspecialchars($toUrl('admin/users/delete?id=' . urlencode((string) ($user['ID'] ?? ''))), ENT_QUOTES, 'UTF-8'); ?>"
                        method="post"
                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này không? Hành động này không thể hoàn tác.');">
                        <input type="hidden" name="filter_keyword"
                            value="<?php echo htmlspecialchars((string) ($userFilters['keyword'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="filter_role"
                            value="<?php echo htmlspecialchars((string) ($userFilters['role'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="filter_status"
                            value="<?php echo htmlspecialchars((string) ($userFilters['status'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="ti-trash"></i> Xóa tài khoản
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
