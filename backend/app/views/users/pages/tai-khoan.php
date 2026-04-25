<?php
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

$toUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/' . ltrim($path, '/');
};

$firstNameRaw = trim((string) ($user['first_name'] ?? ''));
$lastNameRaw = trim((string) ($user['last_name'] ?? ''));
$fullName = trim($firstNameRaw . ' ' . $lastNameRaw);
$phone = (string) ($user['phone'] ?? '');
$email = (string) ($user['email'] ?? '');
$birth = (string) ($user['birth_date'] ?? '');
$gender = (string) ($user['gender'] ?? '');
$address = (string) ($user['address'] ?? '');

$birthFormatted = $birth ? date('d/m/Y', strtotime($birth)) : '—';
$genderText = match ($gender) {
    'male' => 'Nam',
    'female' => 'Nữ',
    default => '—',
};

$avatar = !empty($user['image'])
    ? $toUrl('uploads/' . $user['image'])
    : $toUrl('assets/image/user.png');
?>

<section class="account-page">
    <div class="account-shell">
        <aside class="account-sidebar">
            <h1 class="account-sidebar-title">Tài khoản</h1>
            <nav aria-label="Tài khoản">
                <ul class="account-sidebar-menu">
                    <li class="is-active"><a href="#">Thông tin của tôi</a></li>
                    <li><a href="#">Đổi mật khẩu</a></li>
                </ul>
            </nav>
        </aside>

        <div class="account-main">
            <div class="account-breadcrumbs">TRANG CHỦ &gt; TÀI KHOẢN &gt; THÔNG TIN</div>

            <h2 class="account-heading">TÀI KHOẢN ĐĂNG NHẬP</h2>

            <div class="account-avatar-wrap">
                <img src="<?php echo htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8'); ?>" alt="Ảnh đại diện"
                    class="account-avatar">
            </div>

            <div class="account-details">
                <div class="account-row">
                    <span class="account-label">Họ và tên</span>
                    <span class="account-value"><?php echo htmlspecialchars($fullName !== '' ? $fullName : '—', ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="account-row">
                    <span class="account-label">Số điện thoại</span>
                    <span class="account-value"><?php echo htmlspecialchars($phone !== '' ? $phone : '—', ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="account-row">
                    <span class="account-label">Email</span>
                    <span class="account-value"><?php echo htmlspecialchars($email !== '' ? $email : '—', ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="account-row">
                    <span class="account-label">Ngày sinh</span>
                    <span class="account-value"><?php echo htmlspecialchars($birthFormatted, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="account-row">
                    <span class="account-label">Giới tính</span>
                    <span class="account-value"><?php echo htmlspecialchars($genderText, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="account-row">
                    <span class="account-label">Địa chỉ</span>
                    <span class="account-value"><?php echo htmlspecialchars($address !== '' ? $address : '—', ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </div>

            <button type="button" class="account-update-btn" id="accountToggleBtn">Cập nhật thông tin</button>
        </div>
    </div>
</section>

<div class="account-modal is-hidden" id="accountModal" aria-hidden="true">
    <div class="account-modal-backdrop" id="accountModalBackdrop"></div>
    <div class="account-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="accountModalTitle">
        <div class="account-modal-header">
            <h3 class="account-modal-title" id="accountModalTitle">Cập nhật thông tin</h3>
            <button type="button" class="account-modal-close" id="accountCloseBtn" aria-label="Đóng">&times;</button>
        </div>

        <form action="<?php echo htmlspecialchars($toUrl('tai-khoan'), ENT_QUOTES, 'UTF-8'); ?>" method="post"
            enctype="multipart/form-data" class="account-edit-form" id="accountEditForm">
            <div class="account-form-grid">
                <div class="account-field">
                    <label for="first_name">Tên</label>
                    <input id="first_name" name="first_name" type="text"
                        value="<?php echo htmlspecialchars($firstNameRaw, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="account-field">
                    <label for="last_name">Họ</label>
                    <input id="last_name" name="last_name" type="text"
                        value="<?php echo htmlspecialchars($lastNameRaw, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="account-field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email"
                        value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="account-field">
                    <label for="phone">Số điện thoại</label>
                    <input id="phone" name="phone" type="tel"
                        value="<?php echo htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="account-field">
                    <label for="birth_date">Ngày sinh</label>
                    <input id="birth_date" name="birth_date" type="date"
                        value="<?php echo htmlspecialchars($birth, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="account-field">
                    <label for="gender">Giới tính</label>
                    <select id="gender" name="gender">
                        <option value="">Chọn giới tính</option>
                        <option value="male" <?php echo $gender === 'male' ? 'selected' : ''; ?>>Nam</option>
                        <option value="female" <?php echo $gender === 'female' ? 'selected' : ''; ?>>Nữ</option>
                    </select>
                </div>
                <div class="account-field account-field-full">
                    <label for="address">Địa chỉ</label>
                    <input id="address" name="address" type="text"
                        value="<?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="account-field account-field-full">
                    <label for="avatar">Ảnh đại diện</label>
                    <input id="avatar" name="avatar" type="file" accept=".jpg,.jpeg,.png,.webp">
                </div>
            </div>

            <div class="account-form-actions">
                <button type="button" class="account-cancel-btn" id="accountCancelBtn">Hủy</button>
                <button type="submit" class="account-save-btn">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<script>
(() => {
    const toggleBtn = document.getElementById('accountToggleBtn');
    const cancelBtn = document.getElementById('accountCancelBtn');
    const closeBtn = document.getElementById('accountCloseBtn');
    const modal = document.getElementById('accountModal');
    const backdrop = document.getElementById('accountModalBackdrop');

    if (!toggleBtn || !cancelBtn || !closeBtn || !modal || !backdrop) {
        return;
    }

    const openModal = () => {
        modal.classList.remove('is-hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('account-modal-open');
    };

    const closeModal = () => {
        modal.classList.add('is-hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('account-modal-open');
    };

    toggleBtn.addEventListener('click', openModal);
    cancelBtn.addEventListener('click', closeModal);
    closeBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('is-hidden')) {
            closeModal();
        }
    });
})();
</script>
