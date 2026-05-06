<?php
// ==========================================
// KHỞI TẠO HÀM $asset()
// Tạo đường dẫn tuyệt đối từ thư mục public.
// Dùng kiểm tra isset() để tránh khai báo lại
// khi file này được include nhiều lần (hoặc từ Controller).
// ==========================================
if (!isset($asset) || !is_callable($asset)) {
    $publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    $asset = static fn(string $path): string => $publicBase . '/assets/' . ltrim($path, '/');
}
$products = is_array($products ?? null) ? $products : [];
$upload = static fn(?string $file): string => $publicBase . '/uploads/' . ltrim((string) $file, '/');
$buildDescription = static function (string $name, string $categoryName, string $description): string {
    $description = trim($description);

    if ($description !== '') {
        return $description;
    }

    return sprintf(
        '%s thuộc dòng %s với hương vị cân bằng, dễ uống và phù hợp để thưởng thức bất kỳ lúc nào trong ngày.',
        $name,
        strtolower($categoryName)
    );
};

$grouped_products = [];
foreach ($products as $product) {
    $categoryName = (string) ($product['category_name'] ?? 'KHÁC');

    if (strtoupper($categoryName) === 'FPRAPE') {
        $categoryName = 'FRAPPE';
    }

    $grouped_products[$categoryName][] = [
        'id' => (int) ($product['ID'] ?? 0),
        'name' => (string) ($product['name'] ?? ''),
        'sub' => ucfirst(strtolower($categoryName)),
        'price' => number_format((float) ($product['price'] ?? 0), 0, ',', '.') . ' đ',
        'img' => $upload((string) ($product['image'] ?? '')),
        'image' => (string) ($product['image'] ?? ''),
        'description' => $buildDescription(
            (string) ($product['name'] ?? ''),
            $categoryName,
            (string) ($product['description'] ?? '')
        ),
        'slug' => (string) ($product['slug'] ?? ''),
    ];
}

$bestseller_items = array_slice($products, 0, 5);
?>

<div class="uniphin-menu-wrapper">
    <!-- ── BANNER SLIDER ── -->
    <div class="uniphin-banner-slider">
        <?php
        // Fallback URL dùng chung cho tất cả banner khi ảnh local bị lỗi
        $bannerFallback = 'https://minio.thecoffeehouse.com/image/admin/1777871117_collect-banner-912x456px.jpg';
        $banners = [
            ['src' => $asset('images/banners/banner1.jpg'), 'alt' => 'Banner 1'],
            ['src' => $asset('images/banners/banner2.jpg'), 'alt' => 'Banner 2'],
            ['src' => $asset('images/banners/banner3.jpg'), 'alt' => 'Banner 3'],
        ];
        foreach ($banners as $banner): ?>
        <div class="banner-item">
            <img src="<?= htmlspecialchars($banner['src']) ?>" alt="<?= htmlspecialchars($banner['alt']) ?>"
                onerror="this.src='<?= $bannerFallback ?>'">
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ── HEADER ── -->
    <div class="uniphin-menu-header">
        <div class="uniphin-menu-logo">
            <span class="uniphin-logo-top">HÔM NAY</span>
            <span class="uniphin-logo-bottom">UỐNG GÌ?</span>
        </div>
        <div class="uniphin-menu-search">
            <input type="text" placeholder="Tìm kiếm">
            <button class="uniphin-search-btn" aria-label="Tìm kiếm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>
        </div>
    </div>



    <!-- ── BODY: SIDEBAR + SẢN PHẨM ── -->
    <div class="uniphin-menu-body">

        <!-- Sidebar điều hướng danh mục (render động từ $grouped_products) -->
        <aside class="uniphin-menu-categories">
            <ul>
                <?php
                $isFirst = true;
                $delaySidebar = 0; // Khởi tạo biến trễ cho AOS
                
                foreach (array_keys($grouped_products) as $categoryName):
                    // 1. Sửa lỗi chính tả từ FPRAPE thành FRAPPE nếu có

                    // 2. Chuyển tên danh mục thành ID hợp lệ cho HTML anchor
                    $anchorId = htmlspecialchars(str_replace(' ', '_', $categoryName));
                ?>
                <!-- Thêm hiệu ứng trượt từ trái sang và tăng dần độ trễ -->
                <li data-aos="fade-right" data-aos-delay="<?= $delaySidebar ?>">
                    <a href="#<?= $anchorId ?>" <?= $isFirst ? 'class="menu-active"' : '' ?>>
                        <?= strtoupper(htmlspecialchars($categoryName)) ?>
                    </a>
                </li>
                <?php
                    $isFirst = false;
                    $delaySidebar += 100; // Mỗi danh mục hiện ra sau mục trước 0.1 giây
                endforeach;
                ?>
            </ul>
        </aside>

        <!-- Danh sách sản phẩm nhóm theo danh mục -->
        <div class="uniphin-menu-products">
            <?php foreach ($grouped_products as $category => $items):
                $anchorId = htmlspecialchars(str_replace(' ', '_', $category));
            ?>
            <section id="<?= $anchorId ?>" class="uniphin-category-section">
                <h2 class="uniphin-category-title" data-aos="fade-right"><?= htmlspecialchars($category) ?></h2>
                <div class="uniphin-product-grid">
                    <?php 
                    $delay = 0; // Khởi tạo biến đếm delay
                    foreach ($items as $product): 
                    ?>
                    <!-- Thêm data-aos="fade-up" và gài biến delay vào đây -->
                    <article class="uniphin-product-card" data-aos="fade-up" data-aos-delay="<?= $delay ?>"
                        data-name="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>"
                        data-price="<?= htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8') ?>"
                        data-description="<?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?>"
                        data-image="<?= htmlspecialchars($product['img'], ENT_QUOTES, 'UTF-8') ?>"
                        data-category="<?= htmlspecialchars($product['sub'], ENT_QUOTES, 'UTF-8') ?>" tabindex="0"
                        role="button" aria-haspopup="dialog"
                        aria-label="Xem chi tiết sản phẩm <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
                        <div class="uniphin-image-wrapper">
                            <img src="<?= htmlspecialchars($product['img']) ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>"
                                onerror="this.src='https://minio.thecoffeehouse.com/image/admin/1751598833_matcha-latte-tay-bac-nong_400x400.png'">
                        </div>
                        <div class="uniphin-product-sub"><?= htmlspecialchars($product['sub']) ?></div>
                        <div class="uniphin-product-name"><?= htmlspecialchars($product['name']) ?></div>
                        <div class="uniphin-product-price"><?= htmlspecialchars($product['price']) ?></div>
                    </article>
                    <?php 
                    // Sau mỗi ly, tăng thời gian chờ lên 200 mili-giây
                    $delay += 150; 
                    endforeach; 
                    ?>
                </div>
            </section>
            <?php endforeach; ?>
        </div>

    </div><!-- /.uniphin-menu-body -->

    <!-- ── BEST SELLER ── -->
    <div class="uniphin-bestseller-section" data-aos="zoom-in-up" data-aos-duration="1000">
        <div class="bestseller-header">
            <h2>BEST SELLER</h2>
            <div class="bestseller-line"></div>
        </div>

        <!-- Slider — tên và giá truyền qua data-* để JS cập nhật info-wrapper khi đổi slide -->
        <div class="bestseller-slider">
            <?php foreach ($bestseller_items as $item):
                $itemCategoryName = (string) ($item['category_name'] ?? 'BEST SELLER');
                if (strtoupper($itemCategoryName) === 'FPRAPE') {
                    $itemCategoryName = 'FRAPPE';
                }
                $itemDescription = $buildDescription(
                    (string) ($item['name'] ?? ''),
                    $itemCategoryName,
                    (string) ($item['description'] ?? '')
                );
                $formattedPrice = number_format($item['price'], 0, ',', '.') . ' đ';
            ?>
            <div class="bestseller-item" data-name="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>"
                data-price="<?= htmlspecialchars($formattedPrice, ENT_QUOTES, 'UTF-8') ?>"
                data-description="<?= htmlspecialchars($itemDescription, ENT_QUOTES, 'UTF-8') ?>"
                data-image="<?= htmlspecialchars($upload((string) ($item['image'] ?? '')), ENT_QUOTES, 'UTF-8') ?>"
                data-category="<?= htmlspecialchars(ucfirst(strtolower($itemCategoryName)), ENT_QUOTES, 'UTF-8') ?>"
                tabindex="0" role="button" aria-haspopup="dialog"
                aria-label="Xem chi tiết best seller <?= htmlspecialchars((string) ($item['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <div class="bestseller-img-wrapper">
                    <img src="<?= htmlspecialchars($upload((string) ($item['image'] ?? ''))) ?>"
                        alt="<?= htmlspecialchars($item['name']) ?>"
                        onerror="this.src='https://minio.thecoffeehouse.com/image/admin/1751598833_matcha-latte-tay-bac-nong_400x400.png'">
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Hiển thị thông tin của slide đang active — JS sẽ cập nhật khi người dùng chuyển slide -->
        <div class="bestseller-info-wrapper">
            <div class="bestseller-active-name">
                <?= htmlspecialchars($bestseller_items[0]['name']) ?>
            </div>
            <div class="bestseller-active-price">
                <?= htmlspecialchars(number_format($bestseller_items[0]['price'], 0, ',', '.') . ' đ') ?>
            </div>
        </div>
    </div><!-- /.uniphin-bestseller-section -->
    <div class="uniphin-flashsale-wrapper" data-aos="fade-up">
        <div class="flashsale-header">
            <div class="flashsale-title">
                Khuyến mãi hấp dẫn
            </div>
            <div class="flashsale-countdown">
                <div class="countdown-item"><span id="days">01</span><small>Ngày</small></div>
                <div class="countdown-sep">:</div>
                <div class="countdown-item"><span id="hours">00</span><small>Giờ</small></div>
                <div class="countdown-sep">:</div>
                <div class="countdown-item"><span id="mins">00</span><small>Phút</small></div>
                <div class="countdown-sep">:</div>
                <div class="countdown-item"><span id="secs">00</span><small>Giây</small></div>
            </div>
        </div>

        <div class="flashsale-slider">
            <?php foreach ($bestseller_items as $item):
                $saleCategoryName = (string) ($item['category_name'] ?? 'FLASH SALE');
                if (strtoupper($saleCategoryName) === 'FPRAPE') {
                    $saleCategoryName = 'FRAPPE';
                }
                $saleDescription = $buildDescription(
                    (string) ($item['name'] ?? ''),
                    $saleCategoryName,
                    (string) ($item['description'] ?? '')
                );
                $saleCurrentPrice = number_format($item['price'] * 0.9, 0, ',', '.') . ' Ä‘';
            ?>
            <div class="sale-card" data-name="<?= htmlspecialchars((string) ($item['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                data-price="<?= htmlspecialchars($saleCurrentPrice, ENT_QUOTES, 'UTF-8') ?>"
                data-description="<?= htmlspecialchars($saleDescription, ENT_QUOTES, 'UTF-8') ?>"
                data-image="<?= htmlspecialchars($upload((string) ($item['image'] ?? '')), ENT_QUOTES, 'UTF-8') ?>"
                data-category="<?= htmlspecialchars('Flash Sale', ENT_QUOTES, 'UTF-8') ?>" tabindex="0" role="button"
                aria-haspopup="dialog"
                aria-label="Xem chi tiết flash sale <?= htmlspecialchars((string) ($item['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <div class="sale-img">
                    <span class="sale-badge">-10%</span>
                    <img src="<?= htmlspecialchars($upload((string) ($item['image'] ?? ''))) ?>" alt=""
                        onerror="this.src='https://minio.thecoffeehouse.com/image/admin/1751598833_matcha-latte-tay-bac-nong_400x400.png'">
                </div>
                <div class="sale-info">
                    <h3 class="sale-name"><?= $item['name'] ?></h3>
                    <div class="sale-price">
                        <span class="price-current"><?= number_format($item['price'] * 0.9 , 0, ',', '.') ?> đ</span>
                        <span class="price-old"><?= number_format($item['price'], 0, ',', '.') ?> đ</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ==========================================
     MỤC: BỘ SƯU TẬP MỚI (EDITORIAL LAYOUT)
     ========================================== -->
    <div class="uniphin-collection-section">
        <div class="collection-container">

            <!-- NỬA BÊN TRÁI: POSTER LỚN -->
            <div class="collection-poster" data-aos="fade-right" data-aos-duration="800">
                <!-- Thay đường dẫn ảnh poster của bạn vào đây -->
                <img src="https://minio.thecoffeehouse.com/image/admin/1751598833_matcha-latte-tay-bac-nong_400x400.png"
                    alt="Summer Collection">
                <div class="poster-content">
                    <span class="poster-badge">MỚI RA MẮT</span>
                    <h2>Mùa Hè Rực Rỡ</h2>
                    <p>Khám phá bộ sưu tập trái cây nhiệt đới tươi mát, đánh bay cái nóng mùa hè cùng Uniphin.</p>
                    <a href="#" class="btn-discover">Khám phá ngay <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <!-- NỬA BÊN PHẢI: DANH SÁCH MÓN XẾP DỌC -->
            <!-- NỬA BÊN PHẢI: DANH SÁCH MÓN LẬT 3D -->
            <!-- NỬA BÊN PHẢI: DANH SÁCH MÓN LẬT 180 ĐỘ (TRUE 3D FLIP) -->
            <div class="collection-list-3d">
                <?php 
            $new_collection = array_slice($products, 0, 4); // Lấy 4 món
            $delay = 0;
            foreach ($new_collection as $item): 
            ?>
                <div class="uniphin-flip-card" data-aos="zoom-in" data-aos-delay="<?= $delay ?>">
                    <div class="flip-card-inner">

                        <!-- Mặt trước: Ly nước lơ lửng và tên món mờ ảo -->
                        <div class="flip-card-front">
                            <div class="front-glow"></div> <!-- Quầng sáng phía sau ly -->
                            <img class="floating-cup"
                                src="<?= htmlspecialchars($upload((string) ($item['image'] ?? ''))) ?>"
                                alt="<?= htmlspecialchars($item['name']) ?>"
                                onerror="this.src='https://minio.thecoffeehouse.com/image/admin/1751598833_matcha-latte-tay-bac-nong_400x400.png'">
                            <h3 class="front-teaser"><?= htmlspecialchars($item['name']) ?></h3>
                        </div>

                        <!-- Mặt sau: Lật ra thông tin chốt đơn -->
                        <div class="flip-card-back">
                            <h3 class="back-title"><?= htmlspecialchars($item['name']) ?></h3>
                            <div class="back-divider"></div>
                            <p class="back-price"><?= number_format($item['price'], 0, ',', '.') ?> đ</p>
                            <p class="back-desc">Tuyệt tác giải nhiệt mùa hè, đậm đà khó cưỡng. Công thức độc bản từ
                                Uniphin.</p>
                            <button class="btn-flip-add"><i class="fas fa-shopping-cart"></i> Đặt ngay</button>
                        </div>

                    </div>
                </div>
                <?php 
            $delay += 150; 
            endforeach; 
            ?>
            </div>

        </div>
    </div>



</div><!-- /.uniphin-menu-wrapper -->

<div class="uniphin-product-modal" id="uniphinProductModal" hidden aria-hidden="true">
    <div class="uniphin-product-modal__dialog" role="dialog" aria-modal="true"
        aria-labelledby="uniphinProductModalTitle">
        <button type="button" class="uniphin-product-modal__close" data-modal-close
            aria-label="Đóng chi tiết sản phẩm">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M6 6L18 18M18 6L6 18"></path>
            </svg>
        </button>

        <div class="uniphin-product-modal__media">
            <span class="uniphin-product-modal__bean bean-one" aria-hidden="true"></span>
            <span class="uniphin-product-modal__bean bean-two" aria-hidden="true"></span>
            <span class="uniphin-product-modal__bean bean-three" aria-hidden="true"></span>
            <div class="uniphin-product-modal__halo"></div>
            <img id="uniphinProductModalImage" src="" alt="">
        </div>

        <div class="uniphin-product-modal__content">
            <div class="uniphin-product-modal__eyebrow" id="uniphinProductModalCategory"></div>
            <h3 class="uniphin-product-modal__title" id="uniphinProductModalTitle"></h3>
            <div class="uniphin-product-modal__price" id="uniphinProductModalPrice"></div>
            <p class="uniphin-product-modal__description" id="uniphinProductModalDescription"></p>
            <div class="uniphin-product-modal__actions">
                <div class="uniphin-product-modal__quantity" aria-label="Chọn số lượng">
                    <button type="button" class="uniphin-product-modal__qty-btn" data-qty-action="decrease"
                        aria-label="Giảm số lượng">-</button>
                    <input type="text" class="uniphin-product-modal__qty-value" id="uniphinProductModalQty" value="1"
                        inputmode="numeric" readonly aria-label="Số lượng sản phẩm">
                    <button type="button" class="uniphin-product-modal__qty-btn" data-qty-action="increase"
                        aria-label="Tăng số lượng">+</button>
                </div>
                <button type="button" class="uniphin-product-modal__cta">THÊM VÀO GIỎ</button>
            </div>
        </div>
    </div>
</div>
