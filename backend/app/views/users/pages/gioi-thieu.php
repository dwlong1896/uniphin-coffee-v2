<?php
/**
 * Trang Giới thiệu — dữ liệu từ $sections (AboutSectionModel)
 * Fallback nếu chưa có dữ liệu trong DB.
 */
$s = function (string $key, string $field, string $default = '') use ($sections) {
    return htmlspecialchars($sections[$key][$field] ?? $default, ENT_QUOTES, 'UTF-8');
};
?>

<div class="about-page">
    <!-- Header -->
    <section class="about-header">
        <h1>UNIPHINCOFFEE</h1>
        <h2><?php echo $s('hero', 'title', 'NƠI BẮT ĐẦU NHỮNG NGÀY HIỆU QUẢ'); ?></h2>
        <p><?php echo nl2br($s('hero', 'content', 'Một không gian yên tĩnh, thoải mái cùng ly cà phê chất lượng giúp bạn khởi đầu ngày mới với sự tập trung và năng lượng, để học tập và làm việc hiệu quả hơn.')); ?></p>
    </section>

    <!-- Hero Banner -->
    <section class="about-hero">
        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1400&q=80" alt="Sinh viên UniPhin Coffee">
    </section>

    <!-- Nguồn gốc -->
    <section class="about-section">
        <div class="about-section-container">
            <div class="about-section-image">
                <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=800&q=80" alt="Nguồn gốc">
            </div>
            <div class="about-section-content">
                <h3><?php echo $s('origin', 'title', 'Nguồn gốc'); ?></h3>
                <p><?php echo nl2br($s('origin', 'content', 'UniPhin Coffee bắt đầu từ một ý tưởng rất đơn giản – tạo ra một không gian dành riêng cho sinh viên.')); ?></p>
            </div>
        </div>
    </section>

    <!-- Sứ mệnh -->
    <section class="about-section about-mission-bg reverse">
        <div class="about-section-container">
            <div class="about-section-image">
                <img src="https://images.unsplash.com/photo-1521017432531-fbd92d768814?w=800&q=80" alt="Sứ mệnh">
            </div>
            <div class="about-section-content">
                <h3><?php echo $s('mission', 'title', 'Sứ mệnh'); ?></h3>
                <p><?php echo nl2br($s('mission', 'content', 'UniPhin Coffee hướng đến việc mang lại những ly cà phê chất lượng với mức giá phù hợp cho sinh viên.')); ?></p>
            </div>
        </div>
    </section>

    <!-- Chất lượng -->
    <section class="about-section">
        <div class="about-section-container">
            <div class="about-section-image">
                <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=800&q=80" alt="Chất lượng" class="image-glow">
            </div>
            <div class="about-section-content">
                <h3><?php echo $s('quality', 'title', 'Từ hạt cà phê ngon đến ly cà phê trọn vị'); ?></h3>
                <p><?php echo nl2br($s('quality', 'content', 'Chúng mình lựa chọn nguồn cà phê từ những vùng trồng tại Việt Nam.')); ?></p>
            </div>
        </div>
    </section>

    <!-- Feedback -->
    <section class="about-feedback">
        <button class="carousel-btn prev" onclick="document.querySelector('.feedback-container').scrollBy({left:-240,behavior:'smooth'})">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </button>
        <div class="feedback-container">
            <div class="feedback-card" style="background-color:#f0f9ff;">
                <div class="feedback-avatar"><img src="https://i.pravatar.cc/150?u=pa" alt="Phương Anh"></div>
                <h4>Phương Anh</h4>
                <p>"Cứ tới deadline là mình chạy qua UniPhin ngồi. Không gian dễ chịu, ngồi 15 phút thôi mà thấy đầu óc nhẹ hẳn."</p>
            </div>
            <div class="feedback-card" style="background-color:#fffbeb;">
                <div class="feedback-avatar"><img src="https://i.pravatar.cc/150?u=tk" alt="Tuấn Khang"></div>
                <h4>Tuấn Khang</h4>
                <p>"Mình mê vibe ở đây cực, nhạc vừa đủ nghe, không ồn ào. Cà phê không quá đắng, kiểu uống là thấy relax liền."</p>
            </div>
            <div class="feedback-card" style="background-color:#f5f3ff;">
                <div class="feedback-avatar"><img src="https://i.pravatar.cc/150?u=ml" alt="Minh Lan"></div>
                <h4>Minh Lan</h4>
                <p>"Giá cả hợp lý, không gian rộng rãi. Mình hay rủ nhóm bạn đến đây ôn thi, ngồi cả buổi cũng không bị đuổi."</p>
            </div>
            <div class="feedback-card" style="background-color:#fdf2f8;">
                <div class="feedback-avatar"><img src="https://i.pravatar.cc/150?u=hd" alt="Hoàng Dũng"></div>
                <h4>Hoàng Dũng</h4>
                <p>"Cà phê ở đây ngon thiệt sự, uống xong tỉnh táo hẳn. Wifi mạnh nữa, làm việc rất hiệu quả."</p>
            </div>
        </div>
        <button class="carousel-btn next" onclick="document.querySelector('.feedback-container').scrollBy({left:240,behavior:'smooth'})">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </button>
    </section>
</div>
