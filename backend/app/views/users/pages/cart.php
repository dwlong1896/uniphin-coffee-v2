<?php
$cartItems = is_array($cartItems ?? null) ? $cartItems : [];
$cartTotal = (float) ($cartTotal ?? 0);
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$upload = static fn(?string $file): string => $publicBase . '/uploads/' . ltrim((string) $file, '/');
$fallbackImage = 'https://minio.thecoffeehouse.com/image/admin/1751598833_matcha-latte-tay-bac-nong_400x400.png';
$itemCount = count($cartItems);
?>

<div class="cart-page-shell">
    <div class="cart-page-wrap">
        <nav class="cart-breadcrumb" aria-label="Breadcrumb">
            <a href="<?= htmlspecialchars($publicBase . '/', ENT_QUOTES, 'UTF-8') ?>">TRANG CHỦ</a>
            <span>&gt;</span>
            <strong>GIỎ HÀNG</strong>
        </nav>

        <?php if (empty($cartItems)): ?>
        <section class="cart-empty-state">
            <h1>Giỏ hàng đang trống</h1>
            <p>Bạn chưa có món nào trong giỏ. Hãy quay lại trang sản phẩm để chọn món.</p>
            <a class="cart-continue-link" href="<?= htmlspecialchars($publicBase . '/san-pham', ENT_QUOTES, 'UTF-8') ?>">
                TIẾP TỤC MUA HÀNG
            </a>
        </section>
        <?php else: ?>
        <div class="cart-layout" id="cartPageRoot"
            data-update-url="<?= htmlspecialchars($publicBase . '/cart/update', ENT_QUOTES, 'UTF-8') ?>"
            data-remove-url="<?= htmlspecialchars($publicBase . '/cart/remove', ENT_QUOTES, 'UTF-8') ?>"
            data-login-url="<?= htmlspecialchars($publicBase . '/login', ENT_QUOTES, 'UTF-8') ?>"
            data-checkout-url="<?= htmlspecialchars($publicBase . '/checkout', ENT_QUOTES, 'UTF-8') ?>">
            <section class="cart-main-panel">
                <label class="cart-select-all">
                    <input type="checkbox" id="cartSelectAll" checked>
                    <span>Chọn tất cả</span>
                </label>

                <div class="cart-list-divider"></div>

                <div class="cart-list" id="cartItemList">
                    <?php foreach ($cartItems as $item): ?>
                    <article class="cart-line-item" data-cart-item
                        data-product-id="<?= (int) ($item['Product_ID'] ?? 0) ?>"
                        data-price="<?= htmlspecialchars((string) ($item['price'] ?? 0), ENT_QUOTES, 'UTF-8') ?>">
                        <label class="cart-line-item__check">
                            <input type="checkbox" class="cart-item-checkbox" checked>
                            <span></span>
                        </label>

                        <div class="cart-line-item__media">
                            <img src="<?= htmlspecialchars($upload((string) ($item['image'] ?? '')), ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars((string) ($item['name'] ?? 'Sản phẩm'), ENT_QUOTES, 'UTF-8') ?>"
                                onerror="this.src='<?= htmlspecialchars($fallbackImage, ENT_QUOTES, 'UTF-8') ?>'">
                        </div>

                        <div class="cart-line-item__info">
                            <h2><?= htmlspecialchars((string) ($item['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h2>
                            <p class="cart-line-item__category">
                                <?= htmlspecialchars((string) ($item['category_name'] ?? 'Đồ uống'), ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <div class="cart-line-item__qty">
                                <span>Số lượng</span>
                                <input type="number" class="cart-quantity-input" min="1"
                                    value="<?= (int) ($item['quantity'] ?? 0) ?>"
                                    aria-label="Số lượng sản phẩm">
                            </div>

                            <div class="cart-line-item__price" data-item-subtotal>
                                <?= htmlspecialchars(number_format((float) ($item['subtotal'] ?? 0), 0, ',', '.') . ' đ', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="cart-line-item__actions">
                            <button type="button" class="cart-remove-btn" data-remove-item>Xóa</button>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <div class="cart-main-footer">
                    <a class="cart-continue-link" href="<?= htmlspecialchars($publicBase . '/san-pham', ENT_QUOTES, 'UTF-8') ?>">
                        TIẾP TỤC MUA HÀNG
                    </a>
                </div>
            </section>

            <aside class="cart-summary-panel">
                <div class="cart-summary-card">
                    <h2>THÔNG TIN ĐƠN HÀNG</h2>

                    <div class="cart-summary-row">
                        <span>Tạm tính</span>
                        <strong id="cartSummarySubtotal"><?= htmlspecialchars(number_format($cartTotal, 0, ',', '.') . ' đ', ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>

                    <div class="cart-summary-row">
                        <span>Phí vận chuyển</span>
                        <strong>-</strong>
                    </div>

                    <div class="cart-summary-divider"></div>

                    <div class="cart-summary-row cart-summary-row--total">
                        <span>Tổng đơn hàng</span>
                        <strong id="cartSummaryTotal"><?= htmlspecialchars(number_format($cartTotal, 0, ',', '.') . ' đ', ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>

                    <button type="button" class="cart-checkout-btn" id="cartCheckoutButton">THANH TOÁN</button>
                </div>

                <p class="cart-summary-note" id="cartSummaryNote"><?= $itemCount ?> món đang có trong giỏ hàng của bạn.</p>
            </aside>
        </div>
        <?php endif; ?>
    </div>
</div>
