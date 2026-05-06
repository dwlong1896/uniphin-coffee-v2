<?php
// Bắt tham số name từ URL

$products_db = [
    [
        'id' => 1,
        'name' => 'Espresso', // Đã sửa chuẩn từ Expresso
        'sub' => 'Cà phê pha máy',
        'price' => 30000,
        'img' => 'https://minio.thecoffeehouse.com/image/admin/1751598833_matcha-latte-tay-bac-nong_400x400.png', // Đường dẫn ảnh thực tế của bạn
        'image' => '1751598833_matcha-latte-tay-bac-nong_400x400.png', // Tên file ảnh (nếu code cũ của bạn có gọi biến này)
        'desc' => 'Một tách Espresso nguyên bản được bắt đầu bởi những hạt Arabica chất lượng, phối trộn với tỉ lệ cân đối hạt Robusta, cho ra vị ngọt caramel, vị chua dịu và sánh đặc.',
        'category' => 'Cà phê'
    ],
    [
        'id' => 2,
        'name' => 'Cappuccino Đá',
        'sub' => 'Cà phê pha máy',
        'price' => 35000,
        'img' => 'https://minio.thecoffeehouse.com/image/admin/1751598833_matcha-latte-tay-bac-nong_400x400.png',
        'image' => '1751598833_matcha-latte-tay-bac-nong_400x400.png',
        'desc' => 'Tuyệt tác giải nhiệt mùa hè, đậm đà khó cưỡng. Công thức độc bản từ Uniphin hòa quyện cùng bọt sữa béo ngậy.',
        'category' => 'Cà phê'
    ],
    [
        'id' => 3,
        'name' => 'FRAPPE Mocha', // Đã sửa chuẩn từ FPRAPE
        'sub' => 'Đá xay',
        'price' => 45000,
        'img' => 'https://minio.thecoffeehouse.com/image/admin/1751598833_matcha-latte-tay-bac-nong_400x400.png',
        'image' => '1751598833_matcha-latte-tay-bac-nong_400x400.png',
        'desc' => 'Sự kết hợp hoàn hảo giữa cà phê espresso, sốt chocolate đậm đặc và lớp kem tươi mát lạnh.',
        'category' => 'Đá xay'
    ]
    // Thêm các sản phẩm khác vào đây...
];
$productName = isset($_GET['name']) ? $_GET['name'] : '';

// --- ĐOẠN NÀY LÀ LOGIC TÌM SẢN PHẨM ---
// Giả định bạn đang dùng mảng $products_db, thực tế bạn sẽ dùng câu lệnh SQL: 
// SELECT * FROM products WHERE name = '$productName'
$currentProduct = null;
foreach ($products_db as $item) {
    if ($item['name'] === $productName) {
        $currentProduct = $item;
        break;
    }
}

// Nếu ai đó nhập bậy URL, báo lỗi hoặc cho quay về trang chủ
if (!$currentProduct) {
    die("<h2>Sản phẩm không tồn tại! Vui lòng quay lại.</h2>");
}
?>

<!-- Gọi Header của trang web vào đây (nếu bạn có chia file) -->
<?php // include 'header.php'; ?>

<!-- KHU VỰC HIỂN THỊ CHI TIẾT SẢN PHẨM -->
<div class="uniphin-detail-page">
    <div class="uniphin-detail-container">

        <!-- Nửa trái: Ảnh và Hạt cà phê -->
        <div class="detail-left">
            <div class="detail-img-bg"></div>
            <img class="detail-main-img" src="<?= htmlspecialchars($currentProduct['image']) ?>"
                alt="<?= htmlspecialchars($currentProduct['name']) ?>">

            <!-- 3 hạt cà phê trang trí -->
            <img class="bean bean-1" src="https://minio.thecoffeehouse.com/image/admin/1715012574_coffee-beans-1.png"
                alt="bean">
            <img class="bean bean-2" src="https://minio.thecoffeehouse.com/image/admin/1715012574_coffee-beans-1.png"
                alt="bean">
            <img class="bean bean-3" src="https://minio.thecoffeehouse.com/image/admin/1715012574_coffee-beans-1.png"
                alt="bean">
        </div>

        <!-- Nửa phải: Thông tin -->
        <div class="detail-right">
            <h1><?= htmlspecialchars($currentProduct['name']) ?></h1>
            <p class="detail-price"><?= number_format($currentProduct['price'], 0, ',', '.') ?> đ</p>

            <!-- Bạn có thể thay bằng trường mô tả từ Database -->
            <p class="detail-desc">Một tách Espresso nguyên bản được bắt đầu bởi những hạt Arabica chất lượng, phối trộn
                với tỉ lệ cân đối hạt Robusta, cho ra vị ngọt caramel, vị chua dịu và sánh đặc.</p>

            <button class="detail-add-btn">THÊM VÀO GIỎ</button>
        </div>

    </div>
</div>

<!-- Gọi Footer của trang web vào đây -->
<?php // include 'footer.php'; ?>