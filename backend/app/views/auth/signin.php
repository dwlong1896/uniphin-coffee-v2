<?php
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$assetUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/assets/' . ltrim($path, '/');
};
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng Nhập</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetUrl('css/user/signin.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body>
<div class="signin-page-container">
    <div class="logo-header">
        <a href="<?php echo htmlspecialchars(($publicBase === '' ? '' : $publicBase) . '/', ENT_QUOTES, 'UTF-8'); ?>">
            <img src="<?php echo htmlspecialchars($assetUrl('image/rmbgblack1.png'), ENT_QUOTES, 'UTF-8'); ?>" alt="UNIPHIN COFFEE Logo" class="logo-img">
        </a>
    </div>

    <div class="signin-content-wrapper">
        <h1 class="signin-title">Đăng Nhập</h1>
        <div class="signin-card">
            <form action="#" method="post" class="signin-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Email" required>
                </div>

                <div class="form-group password-group">
                    <label for="password">Mật khẩu</label>
                    <div class="input-with-icon">
                        <input type="password" id="password" name="password" placeholder="Mật khẩu" required>
                        <button type="button" class="toggle-password-btn" data-target="password" aria-label="Hiển thị mật khẩu">👁</button>
                    </div>
                </div>

                <div class="forgot-password-container">
                    <a href="#">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="btn-signin-submit">Đăng nhập</button>
            </form>

            <div class="signup-redirect">
                Bạn chưa có tài khoản? <a href="<?php echo htmlspecialchars(($publicBase === '' ? '' : $publicBase) . '/register', ENT_QUOTES, 'UTF-8'); ?>">Đăng ký</a>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo htmlspecialchars($assetUrl('js/user/auth.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
