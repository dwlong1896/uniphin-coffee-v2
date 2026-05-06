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
            <a href="<?= htmlspecialchars($publicBase . '/', ENT_QUOTES, 'UTF-8') ?>">TRANG CHU</a>
            <span>&gt;</span>
            <strong>GIO HANG</strong>
        </nav>

        <?php if (empty($cartItems)): ?>
        <section class="cart-empty-state">
            <h1>Gio hang dang trong</h1>
            <p>Ban chua co mon nao trong gio. Hay quay lai trang san pham de chon mon.</p>
            <a class="cart-continue-link" href="<?= htmlspecialchars($publicBase . '/san-pham', ENT_QUOTES, 'UTF-8') ?>">
                TIEP TUC MUA HANG
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
                    <span>Chon tat ca</span>
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
                                alt="<?= htmlspecialchars((string) ($item['name'] ?? 'San pham'), ENT_QUOTES, 'UTF-8') ?>"
                                onerror="this.src='<?= htmlspecialchars($fallbackImage, ENT_QUOTES, 'UTF-8') ?>'">
                        </div>

                        <div class="cart-line-item__info">
                            <h2><?= htmlspecialchars((string) ($item['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h2>
                            <p class="cart-line-item__category">
                                <?= htmlspecialchars((string) ($item['category_name'] ?? 'Do uong'), ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <div class="cart-line-item__qty">
                                <span>So luong</span>
                                <input type="number" class="cart-quantity-input" min="1"
                                    value="<?= (int) ($item['quantity'] ?? 0) ?>"
                                    aria-label="So luong san pham">
                            </div>

                            <div class="cart-line-item__price" data-item-subtotal>
                                <?= htmlspecialchars(number_format((float) ($item['subtotal'] ?? 0), 0, ',', '.') . ' d', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="cart-line-item__actions">
                            <button type="button" class="cart-remove-btn" data-remove-item>Xoa</button>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <div class="cart-main-footer">
                    <a class="cart-continue-link" href="<?= htmlspecialchars($publicBase . '/san-pham', ENT_QUOTES, 'UTF-8') ?>">
                        TIEP TUC MUA HANG
                    </a>
                </div>
            </section>

            <aside class="cart-summary-panel">
                <div class="cart-summary-card">
                    <h2>THONG TIN DON HANG</h2>

                    <div class="cart-summary-row">
                        <span>Tam tinh</span>
                        <strong id="cartSummarySubtotal"><?= htmlspecialchars(number_format($cartTotal, 0, ',', '.') . ' d', ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>

                    <div class="cart-summary-row">
                        <span>Phi van chuyen</span>
                        <strong>-</strong>
                    </div>

                    <div class="cart-summary-divider"></div>

                    <div class="cart-summary-row cart-summary-row--total">
                        <span>Tong don hang</span>
                        <strong id="cartSummaryTotal"><?= htmlspecialchars(number_format($cartTotal, 0, ',', '.') . ' d', ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>

                    <button type="button" class="cart-checkout-btn" id="cartCheckoutButton">THANH TOAN</button>
                </div>

                <p class="cart-summary-note" id="cartSummaryNote"><?= $itemCount ?> mon dang co trong gio hang cua ban.</p>
            </aside>
        </div>
        <?php endif; ?>
    </div>
</div>
