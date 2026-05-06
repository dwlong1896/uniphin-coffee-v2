<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php echo isset($pageTitle) ? htmlspecialchars((string) $pageTitle, ENT_QUOTES, 'UTF-8') : 'UNIPHIN COFFEE'; ?>
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;400;600;800&display=swap"
        rel="stylesheet">
    <?php
    // Base URL cua thu muc public, hoat dong ca khi dat project trong subfolder.
    $publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    $asset = static function (string $path) use ($publicBase): string {
        return ($publicBase === '' ? '' : $publicBase) . '/assets/' . ltrim($path, '/');
    };
    ?>
    <!-- CSS nội bộ của bạn -->
    <link rel="stylesheet"
        href="<?php echo htmlspecialchars($asset('css/user/main-layout.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($asset('css/user/header.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($asset('css/user/sidebar.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($asset('css/user/pages.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($asset('css/user/footer.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet"
        href="<?php echo htmlspecialchars($asset('css/user/tai-khoan.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($asset('css/user/san-pham.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($asset('css/user/chi-tiet.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <!-- CSS của Slick Slider (ĐÃ SỬA THÀNH LINK CDN TRỰC TIẾP) -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />

    <!-- CSS CỦA AOS (THÊM VÀO ĐÂY) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <?php require __DIR__ . '/partials/header.php'; ?>
        <?php require __DIR__ . '/partials/sidebar.php'; ?>

        <main class="content">
            <?php if (!empty($flashSuccess)): ?>
            <div class="flash-message flash-message-success">
                <?php echo htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($flashError)): ?>
            <div class="flash-message flash-message-error">
                <?php echo htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <?php endif; ?>
            <?php echo $content; ?>
        </main>

        <?php require __DIR__ . '/partials/footer.php'; ?>
    </div>
    <script src="<?php echo htmlspecialchars($asset('js/user/sidebar.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>

    <!-- JS của jQuery và Slick Slider (ĐÃ SỬA THÀNH LINK CDN TRỰC TIẾP VÀ CHUẨN HTTPS) -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    <!-- JS CỦA AOS (THÊM VÀO ĐÂY) -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Ép trình duyệt tải lại file JS mới nhất của bạn -->
    <script
        src="<?php echo htmlspecialchars($asset('js/user/san-pham.js'), ENT_QUOTES, 'UTF-8'); ?>?v=<?php echo time(); ?>">
    </script>
</body>

</html>