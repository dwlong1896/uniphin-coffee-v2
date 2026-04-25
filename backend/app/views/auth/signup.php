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
    <title>Đăng Ký</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;400;600;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet"
        href="<?php echo htmlspecialchars($assetUrl('css/auth/signup.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>

<body>
    <div class="signup-page">
        <div class="logo-header">
            <a
                href="<?php echo htmlspecialchars(($publicBase === '' ? '' : $publicBase) . '/', ENT_QUOTES, 'UTF-8'); ?>">
                <img src="<?php echo htmlspecialchars($assetUrl('image/rmbgblack1.png'), ENT_QUOTES, 'UTF-8'); ?>"
                    alt="UNIPHIN COFFEE Logo" class="logo-img">
            </a>
        </div>

        <div class="signup-wrapper">
            <h1 class="signup-title">Đăng Ký</h1>

            <div class="signup-card">
                <form action="<?php echo htmlspecialchars($publicBase . '/register', ENT_QUOTES, 'UTF-8'); ?>"
                    method="post">
                    <div class="form-group">
                        <label for="fullName">Họ và tên</label>
                        <input type="text" id="fullName" name="fullName" placeholder="Họ và tên" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Email" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="tel" id="phone" name="phone" placeholder="Số điện thoại" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Mật khẩu</label>
                        <div class="password-input-wrapper">
                            <input type="password" id="password" name="password" placeholder="Mật khẩu" required>
                            <button type="button" class="eye-btn" data-target="password"
                                aria-label="Hiển thị mật khẩu">👁</button>
                        </div>
                    </div>

                    <button type="submit" class="btn-signup-submit">Đăng ký</button>
                </form>

                <div class="signin-redirect">
                    Bạn đã có tài khoản? <a
                        href="<?php echo htmlspecialchars(($publicBase === '' ? '' : $publicBase) . '/login', ENT_QUOTES, 'UTF-8'); ?>">Đăng
                        nhập</a>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo htmlspecialchars($assetUrl('js/user/auth.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>

</html>