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
    <link rel="stylesheet"
        href="<?php echo htmlspecialchars($asset('css/user/main-layout.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($asset('css/user/header.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($asset('css/user/sidebar.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($asset('css/user/pages.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($asset('css/user/footer.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet"
        href="<?php echo htmlspecialchars($asset('css/user/tai-khoan.css'), ENT_QUOTES, 'UTF-8'); ?>">
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
            <?php echo $content; ?>
        </main>

        <?php require __DIR__ . '/partials/footer.php'; ?>
    </div>
    <script src="<?php echo htmlspecialchars($asset('js/user/sidebar.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>

</html>