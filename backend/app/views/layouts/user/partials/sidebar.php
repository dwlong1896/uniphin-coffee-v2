<?php
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$toUrl = static function (string $path = '/') use ($publicBase): string {
    $normalizedPath = '/' . ltrim($path, '/');
    return ($publicBase === '' ? '' : $publicBase) . $normalizedPath;
};
$assetUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/assets/' . ltrim($path, '/');
};
?>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar-container" id="userSidebar">
    <div class="sidebar-header">
        <img src="<?php echo htmlspecialchars($assetUrl('image/rmbgblack1.png'), ENT_QUOTES, 'UTF-8'); ?>" alt="UNIPHIN COFFEE" class="sidebar-logo">
        <button class="close-btn" id="sidebarCloseBtn" aria-label="Dong menu">×</button>
    </div>

    <nav class="sidebar-nav">
        <ul>
            <li><a href="<?php echo htmlspecialchars($toUrl('/'), ENT_QUOTES, 'UTF-8'); ?>">TRANG CHỦ</a></li>
            <li><a href="<?php echo htmlspecialchars($toUrl('/gioi-thieu'), ENT_QUOTES, 'UTF-8'); ?>">GIỚI THIỆU</a></li>
            <li><a href="<?php echo htmlspecialchars($toUrl('/tin-tuc'), ENT_QUOTES, 'UTF-8'); ?>">TIN TỨC</a></li>
            <li><a href="<?php echo htmlspecialchars($toUrl('/san-pham'), ENT_QUOTES, 'UTF-8'); ?>">SẢN PHẨM</a></li>
            <li><a href="<?php echo htmlspecialchars($toUrl('/lien-he'), ENT_QUOTES, 'UTF-8'); ?>">LIÊN HỆ</a></li>
            <li><a href="<?php echo htmlspecialchars($toUrl('/faqs'), ENT_QUOTES, 'UTF-8'); ?>">FAQs</a></li>
        </ul>
    </nav>

    <div class="sidebar-auth">
        <a href="<?php echo htmlspecialchars($toUrl('/register'), ENT_QUOTES, 'UTF-8'); ?>">
            <button class="btn-register-side">ĐĂNG KÝ</button>
        </a>
        <a href="<?php echo htmlspecialchars($toUrl('/login'), ENT_QUOTES, 'UTF-8'); ?>">
            <button class="btn-login-side">ĐĂNG NHẬP</button>
        </a>
    </div>
</aside>
