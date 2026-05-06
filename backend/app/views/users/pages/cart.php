<?php
$cartItems = is_array($cartItems ?? null) ? $cartItems : [];
$cartTotal = (float) ($cartTotal ?? 0);
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$upload = static fn(?string $file): string => $publicBase . '/uploads/' . ltrim((string) $file, '/');
$fallbackImage = 'https://minio.thecoffeehouse.com/image/admin/1751598833_matcha-latte-tay-bac-nong_400x400.png';
?>

<div class="uniphin-cart-page">
    <div class="uniphin-cart-shell">
        <div class="uniphin-cart-head">
            <p class="uniphin-cart-kicker">Giỏ hàng</p>
            <h1 class="uniphin-cart-title">Các món bạn đã chọn</h1>
        </div>

        <?php if (empty($cartItems)): ?>
        <div class="uniphin-cart-empty">
            <h2>Giỏ hàng đang trống</h2>
            <p>Chọn món ở trang sản phẩm để bắt đầu đơn hàng của bạn.</p>
            <a class="uniphin-cart-back" href="<?= htmlspecialchars($publicBase . '/san-pham', ENT_QUOTES, 'UTF-8') ?>">
                Quay lại trang sản phẩm
            </a>
        </div>
        <?php else: ?>
        <div class="uniphin-cart-grid">
            <div class="uniphin-cart-list">
                <?php foreach ($cartItems as $item): ?>
                <article class="uniphin-cart-item">
                    <div class="uniphin-cart-item__media">
                        <img src="<?= htmlspecialchars($upload((string) ($item['image'] ?? '')), ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars((string) ($item['name'] ?? 'Sản phẩm'), ENT_QUOTES, 'UTF-8') ?>"
                            onerror="this.src='<?= htmlspecialchars($fallbackImage, ENT_QUOTES, 'UTF-8') ?>'">
                    </div>
                    <div class="uniphin-cart-item__body">
                        <h2><?= htmlspecialchars((string) ($item['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h2>
                        <p>Số lượng: <?= (int) ($item['quantity'] ?? 0) ?></p>
                    </div>
                    <div class="uniphin-cart-item__price">
                        <?= htmlspecialchars(number_format(((float) ($item['price'] ?? 0)) * ((int) ($item['quantity'] ?? 0)), 0, ',', '.') . ' đ', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <aside class="uniphin-cart-summary">
                <p class="uniphin-cart-summary__label">Tạm tính</p>
                <p class="uniphin-cart-summary__total">
                    <?= htmlspecialchars(number_format($cartTotal, 0, ',', '.') . ' đ', ENT_QUOTES, 'UTF-8') ?>
                </p>
                <p class="uniphin-cart-summary__note">
                    Giỏ hàng hiện được lưu trong session của trình duyệt.
                </p>
            </aside>
        </div>
        <?php endif; ?>
    </div>
</div>
