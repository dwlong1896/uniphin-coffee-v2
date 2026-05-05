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

// ==========================================
// 1. BẢNG CATEGORY (Giả lập Database)
// ==========================================
$categories = [
    ['ID' => 1, 'Name' => 'EXPRESSO'],
    ['ID' => 2, 'Name' => 'AMERICANO'],
    ['ID' => 3, 'Name' => 'LATTE'],
    ['ID' => 4, 'Name' => 'FPRAPE'],
    ['ID' => 5, 'Name' => 'PHIN'],
    ['ID' => 6, 'Name' => 'COLD BREW'],
];

// ==========================================
// 2. BẢNG PRODUCTS (Giả lập Database)
// Cấu trúc: ID, description, image, status,
//           price, stock_quantity, name,
//           P_Cate_ID, updated_at, slug
// ==========================================
$products_db = [

    // --- EXPRESSO (P_Cate_ID = 1) ---
    ['ID' =>  1, 'P_Cate_ID' => 1, 'status' => 'active',       'price' => 30000.00, 'stock_quantity' =>  50, 'name' => 'Expresso Nóng',           'image' => 'expresso_nong.png', 'description' => 'Cà phê nguyên chất pha máy',    'slug' => 'expresso-nong',           'updated_at' => '2026-05-05 08:00:00'],
    ['ID' =>  2, 'P_Cate_ID' => 1, 'status' => 'active',       'price' => 30000.00, 'stock_quantity' =>  45, 'name' => 'Expresso Đá',             'image' => 'expresso_da.png',   'description' => 'Expresso kết hợp đá lạnh',     'slug' => 'expresso-da',             'updated_at' => '2026-05-05 08:05:00'],
    ['ID' =>  3, 'P_Cate_ID' => 1, 'status' => 'active',       'price' => 35000.00, 'stock_quantity' =>  30, 'name' => 'Cappuccino Đá',           'image' => 'cap_da.png',        'description' => 'Cappuccino truyền thống với đá','slug' => 'cappuccino-da',           'updated_at' => '2026-05-05 08:10:00'],
    ['ID' =>  4, 'P_Cate_ID' => 1, 'status' => 'active',       'price' => 35000.00, 'stock_quantity' =>  25, 'name' => 'Cappuccino Nóng',         'image' => 'cap_nong.png',      'description' => 'Lớp bọt sữa mềm mịn',          'slug' => 'cappuccino-nong',         'updated_at' => '2026-05-05 08:15:00'],
    ['ID' =>  5, 'P_Cate_ID' => 1, 'status' => 'active',       'price' => 35000.00, 'stock_quantity' =>  40, 'name' => 'Caramel Macchiato Đá',    'image' => 'car_da.png',        'description' => 'Vị ngọt ngào của Caramel',     'slug' => 'caramel-macchiato-da',    'updated_at' => '2026-05-05 08:20:00'],
    ['ID' =>  6, 'P_Cate_ID' => 1, 'status' => 'active',       'price' => 35000.00, 'stock_quantity' =>  15, 'name' => 'Caramel Macchiato Nóng',  'image' => 'car_nong.png',      'description' => 'Macchiato nóng ấm áp',          'slug' => 'caramel-macchiato-nong',  'updated_at' => '2026-05-05 08:25:00'],

    // --- AMERICANO (P_Cate_ID = 2) ---
    ['ID' =>  7, 'P_Cate_ID' => 2, 'status' => 'active',       'price' => 28000.00, 'stock_quantity' => 100, 'name' => 'A-Mê Classic',            'image' => 'ame_classic.png',   'description' => 'Americano đậm đà chuẩn vị',    'slug' => 'ame-classic',             'updated_at' => '2026-05-05 09:00:00'],
    ['ID' =>  8, 'P_Cate_ID' => 2, 'status' => 'active',       'price' => 28000.00, 'stock_quantity' =>  60, 'name' => 'A-Mê Đào',               'image' => 'ame_dao.png',       'description' => 'Hương đào thơm mát',            'slug' => 'ame-dao',                 'updated_at' => '2026-05-05 09:05:00'],
    ['ID' =>  9, 'P_Cate_ID' => 2, 'status' => 'active',       'price' => 28000.00, 'stock_quantity' =>  40, 'name' => 'A-Mê Mơ',               'image' => 'ame_mo.png',        'description' => 'Vị mơ thanh chua nhẹ',          'slug' => 'ame-mo',                  'updated_at' => '2026-05-05 09:10:00'],
    ['ID' => 10, 'P_Cate_ID' => 2, 'status' => 'out_of_stock', 'price' => 28000.00, 'stock_quantity' =>   0, 'name' => 'A-Mê Yuzu',              'image' => 'ame_yuzu.png',      'description' => 'Yuzu Nhật Bản cực fresh',       'slug' => 'ame-yuzu',                'updated_at' => '2026-05-05 09:15:00'],
    // ^ out_of_stock — sẽ bị lọc bỏ, không hiển thị trên menu

    // --- LATTE (P_Cate_ID = 3) ---
    ['ID' => 11, 'P_Cate_ID' => 3, 'status' => 'active',       'price' => 35000.00, 'stock_quantity' =>  30, 'name' => 'Latte Đá',               'image' => 'latte_da.png',           'description' => 'Cà phê hòa quyện sữa tươi', 'slug' => 'latte-da',            'updated_at' => '2026-05-05 10:00:00'],
    ['ID' => 12, 'P_Cate_ID' => 3, 'status' => 'active',       'price' => 35000.00, 'stock_quantity' =>  20, 'name' => 'Latte Nóng',             'image' => 'latte_nong.png',         'description' => 'Latte nóng thơm béo',        'slug' => 'latte-nong',          'updated_at' => '2026-05-05 10:05:00'],
    ['ID' => 13, 'P_Cate_ID' => 3, 'status' => 'active',       'price' => 40000.00, 'stock_quantity' =>  50, 'name' => 'Matcha Latte Đá',         'image' => 'matcha_latte_da.png',    'description' => 'Bột Matcha Nhật Bản',        'slug' => 'matcha-latte-da',     'updated_at' => '2026-05-05 10:10:00'],
    ['ID' => 14, 'P_Cate_ID' => 3, 'status' => 'active',       'price' => 40000.00, 'stock_quantity' =>  25, 'name' => 'Matcha Latte Nóng',       'image' => 'matcha_latte_nong.png',  'description' => 'Trà xanh ấm áp',             'slug' => 'matcha-latte-nong',   'updated_at' => '2026-05-05 10:15:00'],

    // --- FPRAPE (P_Cate_ID = 4) ---
    ['ID' => 15, 'P_Cate_ID' => 4, 'status' => 'active',       'price' => 45000.00, 'stock_quantity' =>  40, 'name' => 'Frappe Chocolate',        'image' => 'frappe_choc.png',    'description' => 'Chocolate xay viên đá mát lạnh', 'slug' => 'frappe-chocolate', 'updated_at' => '2026-05-05 11:00:00'],
    ['ID' => 16, 'P_Cate_ID' => 4, 'status' => 'active',       'price' => 45000.00, 'stock_quantity' =>  35, 'name' => 'Frappe Caramel',          'image' => 'frappe_caramel.png', 'description' => 'Đá xay phủ Caramel',             'slug' => 'frappe-caramel',   'updated_at' => '2026-05-05 11:05:00'],
    ['ID' => 17, 'P_Cate_ID' => 4, 'status' => 'active',       'price' => 45000.00, 'stock_quantity' =>  20, 'name' => 'Frappe Matcha',           'image' => 'frappe_matcha.png',  'description' => 'Matcha đá xay phủ kem',          'slug' => 'frappe-matcha',    'updated_at' => '2026-05-05 11:10:00'],
    ['ID' => 18, 'P_Cate_ID' => 4, 'status' => 'inactive',     'price' => 45000.00, 'stock_quantity' =>   0, 'name' => 'Frappe Vanilla',          'image' => 'frappe_vanilla.png', 'description' => 'Đá xay hương Vani',              'slug' => 'frappe-vanilla',   'updated_at' => '2026-05-05 11:15:00'],
    // ^ inactive — ngừng bán, bị lọc bỏ

    // --- PHIN (P_Cate_ID = 5) ---
    ['ID' => 19, 'P_Cate_ID' => 5, 'status' => 'active',       'price' => 25000.00, 'stock_quantity' => 100, 'name' => 'Cà Phê Đen Đá',          'image' => 'phin_den_da.png',  'description' => 'Cà phê đen truyền thống',  'slug' => 'ca-phe-den-da',   'updated_at' => '2026-05-05 12:00:00'],
    ['ID' => 20, 'P_Cate_ID' => 5, 'status' => 'active',       'price' => 25000.00, 'stock_quantity' =>  80, 'name' => 'Cà Phê Đen Nóng',        'image' => 'phin_den_nong.png','description' => 'Đen nóng đậm đà',           'slug' => 'ca-phe-den-nong', 'updated_at' => '2026-05-05 12:05:00'],
    ['ID' => 21, 'P_Cate_ID' => 5, 'status' => 'active',       'price' => 29000.00, 'stock_quantity' => 150, 'name' => 'Cà Phê Sữa Đá',          'image' => 'phin_sua_da.png',  'description' => 'Cà phê sữa thân quen',     'slug' => 'ca-phe-sua-da',   'updated_at' => '2026-05-05 12:10:00'],
    ['ID' => 22, 'P_Cate_ID' => 5, 'status' => 'active',       'price' => 29000.00, 'stock_quantity' =>  90, 'name' => 'Bạc Xỉu Đá',             'image' => 'bac_xiu.png',      'description' => 'Nhiều sữa ít cà phê',      'slug' => 'bac-xiu-da',      'updated_at' => '2026-05-05 12:15:00'],

    // --- COLD BREW (P_Cate_ID = 6) ---
    ['ID' => 23, 'P_Cate_ID' => 6, 'status' => 'active',       'price' => 40000.00, 'stock_quantity' =>  15, 'name' => 'Cold Brew Truyền Thống',  'image' => 'coldbrew_truyenthong.png', 'description' => 'Ủ lạnh 24h tinh khiết',      'slug' => 'coldbrew-truyenthong', 'updated_at' => '2026-05-05 13:00:00'],
    ['ID' => 24, 'P_Cate_ID' => 6, 'status' => 'active',       'price' => 45000.00, 'stock_quantity' =>  20, 'name' => 'Cold Brew Sữa Tươi',      'image' => 'coldbrew_sua.png',         'description' => 'Cold Brew thêm chút sữa',     'slug' => 'coldbrew-sua',         'updated_at' => '2026-05-05 13:05:00'],
    ['ID' => 25, 'P_Cate_ID' => 6, 'status' => 'active',       'price' => 45000.00, 'stock_quantity' =>  25, 'name' => 'Cold Brew Cam Sả',        'image' => 'coldbrew_camsa.png',       'description' => 'Thanh mát vị cam sả',         'slug' => 'coldbrew-cam-sa',      'updated_at' => '2026-05-05 13:10:00'],
    ['ID' => 26, 'P_Cate_ID' => 6, 'status' => 'active',       'price' => 45000.00, 'stock_quantity' =>  10, 'name' => 'Cold Brew Tonic',         'image' => 'coldbrew_tonic.png',       'description' => 'Nước Tonic và Cold Brew',     'slug' => 'coldbrew-tonic',       'updated_at' => '2026-05-05 13:15:00'],
];

// ==========================================
// 3. LOGIC XỬ LÝ: Ghép dữ liệu & Nhóm theo Category
// ==========================================

// Tạo map ID => Name để tra cứu tên danh mục trong O(1) thay vì lặp lồng nhau
$category_map = array_column($categories, 'Name', 'ID');

$grouped_products = [];

foreach ($products_db as $product) {
    // Chỉ hiển thị sản phẩm đang bán (status = 'active')
    // out_of_stock và inactive đều bị bỏ qua
    if ($product['status'] !== 'active') {
        continue;
    }

    $categoryName = $category_map[$product['P_Cate_ID']] ?? 'KHÁC';

    // Format dữ liệu cho UI — tách riêng khỏi raw data
    $grouped_products[$categoryName][] = [
        'name'  => $product['name'],
        'sub'   => ucfirst(strtolower($categoryName)),
        'price' => number_format($product['price'], 0, ',', '.') . ' đ', // 30000.00 → "30.000 đ"
        'img'   => $asset('images/products/' . $product['image']),        // Ghép đường dẫn tuyệt đối
    ];
}

// Lấy 5 sản phẩm đầu làm Best Seller (hoặc thay bằng query riêng sau)
$bestseller_items = array_slice($products_db, 0, 5);
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
                // Dùng array_keys để chỉ lấy tên danh mục, không cần value
                foreach (array_keys($grouped_products) as $categoryName):
                    // Chuyển tên danh mục thành ID hợp lệ cho HTML anchor (ví dụ: "COLD BREW" → "COLD_BREW")
                    $anchorId = htmlspecialchars(str_replace(' ', '_', $categoryName));
                ?>
                <li>
                    <a href="#<?= $anchorId ?>" <?= $isFirst ? 'class="menu-active"' : '' ?>>
                        <?= htmlspecialchars($categoryName) ?>
                    </a>
                </li>
                <?php
                    $isFirst = false;
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
                    <div class="uniphin-product-card" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                        <div class="uniphin-image-wrapper">
                            <img src="<?= htmlspecialchars($product['img']) ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>"
                                onerror="this.src='https://minio.thecoffeehouse.com/image/admin/1751598833_matcha-latte-tay-bac-nong_400x400.png'">
                        </div>
                        <div class="uniphin-product-sub"><?= htmlspecialchars($product['sub']) ?></div>
                        <div class="uniphin-product-name"><?= htmlspecialchars($product['name']) ?></div>
                        <div class="uniphin-product-price"><?= htmlspecialchars($product['price']) ?></div>
                    </div>
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
    <div class="uniphin-bestseller-section" data-aos="zoom-in" data-aos-duration="1000">
        <div class="bestseller-header">
            <h2>BEST SELLER</h2>
            <div class="bestseller-line"></div>
        </div>

        <!-- Slider — tên và giá truyền qua data-* để JS cập nhật info-wrapper khi đổi slide -->
        <div class="bestseller-slider">
            <?php foreach ($bestseller_items as $item):
                $formattedPrice = number_format($item['price'], 0, ',', '.') . ' đ';
            ?>
            <div class="bestseller-item" data-name="<?= htmlspecialchars($item['name']) ?>"
                data-price="<?= htmlspecialchars($formattedPrice) ?>">
                <div class="bestseller-img-wrapper">
                    <img src="<?= $asset('images/products/' . $item['image']) ?>"
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

</div><!-- /.uniphin-menu-wrapper -->