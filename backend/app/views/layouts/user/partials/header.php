<?php
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

$toUrl = static function (string $path = '/') use ($publicBase): string {
    $normalizedPath = '/' . ltrim($path, '/');
    return ($publicBase === '' ? '' : $publicBase) . $normalizedPath;
};
$assetUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/assets/' . ltrim($path, '/');
};

$isActive = static function (string $path) use ($requestPath, $toUrl): bool {
    $target = rtrim(parse_url($toUrl($path), PHP_URL_PATH) ?? '/', '/');
    $current = rtrim($requestPath, '/');
    return ($target === '' ? '/' : $target) === ($current === '' ? '/' : $current);
};

$navItems = [
    ['path' => '/', 'label' => 'TRANG CHỦ'],
    ['path' => '/gioi-thieu', 'label' => 'GIỚI THIỆU'],
    ['path' => '/tin-tuc', 'label' => 'TIN TỨC'],
    ['path' => '/san-pham', 'label' => 'SẢN PHẨM'],
    ['path' => '/lien-he', 'label' => 'LIÊN HỆ'],
    ['path' => '/faqs', 'label' => 'FAQS'],
];
?>
<header class="header-container">
    <div class="header-content">
        <button class="menu-toggle-btn" id="menuToggleBtn" aria-label="Mo menu">☰</button>
        <div class="logo-section">
            <a href="<?php echo htmlspecialchars($toUrl('/'), ENT_QUOTES, 'UTF-8'); ?>" class="logo-link">
                <img src="<?php echo htmlspecialchars($assetUrl('image/rmbgblack1.png'), ENT_QUOTES, 'UTF-8'); ?>" alt="UNIPHIN COFFEE Logo" class="logo-img">
            </a>
        </div>

        <nav class="nav-section desktop-menu">
            <ul class="nav-links">
                <?php foreach ($navItems as $navItem): ?>
                    <li>
                        <a
                            class="<?php echo $isActive($navItem['path']) ? 'active' : ''; ?>"
                            href="<?php echo htmlspecialchars($toUrl($navItem['path']), ENT_QUOTES, 'UTF-8'); ?>"
                        >
                            <?php echo htmlspecialchars($navItem['label'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="auth-section">
            <a href="<?php echo htmlspecialchars($toUrl('/register'), ENT_QUOTES, 'UTF-8'); ?>" class="auth-link"><button class="btn-register">ĐĂNG KÝ</button></a>
            <a href="<?php echo htmlspecialchars($toUrl('/login'), ENT_QUOTES, 'UTF-8'); ?>" class="auth-link"><button class="btn-login">ĐĂNG NHẬP</button></a>
        </div>
    </div>
</header>
