<?php
$checkoutItems = is_array($checkoutItems ?? null) ? $checkoutItems : [];
$checkoutTotal = (float) ($checkoutTotal ?? 0);
$checkoutFormData = is_array($checkoutFormData ?? null) ? $checkoutFormData : [];
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$upload = static fn(?string $file): string => $publicBase . '/uploads/' . ltrim((string) $file, '/');
$fallbackImage = 'https://minio.thecoffeehouse.com/image/admin/1751598833_matcha-latte-tay-bac-nong_400x400.png';
?>

<div class="checkout-page-shell">
    <div class="checkout-page-wrap">
        <nav class="checkout-breadcrumb" aria-label="Breadcrumb">
            <a href="<?= htmlspecialchars($publicBase . '/', ENT_QUOTES, 'UTF-8') ?>">TRANG CHỦ</a>
            <span>&gt;</span>
            <a href="<?= htmlspecialchars($publicBase . '/cart', ENT_QUOTES, 'UTF-8') ?>">GIỎ HÀNG</a>
            <span>&gt;</span>
            <strong>THANH TOÁN</strong>
        </nav>

        <div class="checkout-layout">
            <section class="checkout-form-panel">
                <h1>THÔNG TIN GIAO HÀNG</h1>

                <form method="post" action="<?= htmlspecialchars($publicBase . '/checkout', ENT_QUOTES, 'UTF-8') ?>"
                    class="checkout-form">
                    <input type="hidden" name="selected_items"
                        value="<?= htmlspecialchars((string) ($checkoutFormData['selected_items'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

                    <label class="checkout-field">
                        <span>ĐỊA CHỈ</span>
                        <input type="text" name="address" placeholder="Nhập địa chỉ"
                            value="<?= htmlspecialchars((string) ($checkoutFormData['address'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                            required>
                    </label>

                    <label class="checkout-field">
                        <span>HỌ VÀ TÊN</span>
                        <input type="text" name="full_name" placeholder="Nhập họ và tên"
                            value="<?= htmlspecialchars((string) ($checkoutFormData['full_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                            required>
                    </label>

                    <label class="checkout-field">
                        <span>SỐ ĐIỆN THOẠI</span>
                        <input type="text" name="phone" placeholder="Nhập số điện thoại"
                            value="<?= htmlspecialchars((string) ($checkoutFormData['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                            required>
                    </label>

                    <fieldset class="checkout-payment-group">
                        <legend>PHƯƠNG THỨC THANH TOÁN</legend>

                        <label class="checkout-payment-option">
                            <input type="radio" name="payment_method" value="COD"
                                <?= (($checkoutFormData['payment_method'] ?? 'COD') === 'COD') ? 'checked' : '' ?>>
                            <span>Thanh toán khi nhận hàng (COD)</span>
                        </label>

                        <label class="checkout-payment-option">
                            <input type="radio" name="payment_method" value="Bank_Transfer"
                                <?= (($checkoutFormData['payment_method'] ?? '') === 'Bank_Transfer') ? 'checked' : '' ?>>
                            <span>Thanh toán qua chuyển khoản</span>
                        </label>
                    </fieldset>

                    <div class="checkout-submit-wrap">
                        <button type="submit" class="checkout-submit-btn">HOÀN TẤT ĐƠN HÀNG</button>
                    </div>
                </form>
            </section>

            <aside class="checkout-summary-panel">
                <div class="checkout-summary-card">
                    <h2>TÓM TẮT ĐƠN HÀNG</h2>

                    <div class="checkout-summary-items">
                        <?php foreach ($checkoutItems as $item): ?>
                        <article class="checkout-summary-item">
                            <div class="checkout-summary-item__media">
                                <img src="<?= htmlspecialchars($upload((string) ($item['image'] ?? '')), ENT_QUOTES, 'UTF-8') ?>"
                                    alt="<?= htmlspecialchars((string) ($item['name'] ?? 'Sản phẩm'), ENT_QUOTES, 'UTF-8') ?>"
                                    onerror="this.src='<?= htmlspecialchars($fallbackImage, ENT_QUOTES, 'UTF-8') ?>'">
                            </div>
                            <div class="checkout-summary-item__content">
                                <h3><?= htmlspecialchars((string) ($item['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h3>
                                <p><?= htmlspecialchars((string) ($item['category_name'] ?? 'Đồ uống'), ENT_QUOTES, 'UTF-8') ?></p>
                                <p>Số lượng: <?= (int) ($item['quantity'] ?? 0) ?></p>
                                <strong><?= htmlspecialchars(number_format((float) ($item['subtotal'] ?? 0), 0, ',', '.') . ' đ', ENT_QUOTES, 'UTF-8') ?></strong>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>

                    <div class="checkout-summary-row">
                        <span>Tạm tính</span>
                        <strong><?= htmlspecialchars(number_format($checkoutTotal, 0, ',', '.') . ' đ', ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>

                    <div class="checkout-summary-row">
                        <span>Phí vận chuyển</span>
                        <strong>-</strong>
                    </div>

                    <div class="checkout-summary-divider"></div>

                    <div class="checkout-summary-row checkout-summary-row--total">
                        <span>Tổng đơn hàng</span>
                        <strong><?= htmlspecialchars(number_format($checkoutTotal, 0, ',', '.') . ' đ', ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
