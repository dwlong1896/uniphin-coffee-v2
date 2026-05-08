<div class="faq-page-wrapper">
    <!-- Hero Section -->
    <section class="faq-hero">
        <div class="faq-hero-content">
            <h1>CHÚNG TÔI Ở ĐÂY ĐỂ GIÚP ĐỠ BẠN</h1>
            <div class="faq-search-box">
                <input type="text" id="faqSearch" placeholder="Mô tả vấn đề của bạn">
                <button class="faq-search-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <div class="faq-main-container">
        <!-- FAQ Title -->
        <h2 class="faq-section-title">CÁC CÂU HỎI THƯỜNG GẶP</h2>

        <!-- FAQ Accordion -->
        <div class="faq-list">
            <?php if (!empty($faqs)): ?>
                <?php foreach ($faqs as $faq): ?>
                    <div class="faq-item">
                        <div class="faq-question">
                            <span><?php echo htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <div class="faq-chevron">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </div>
                        <div class="faq-answer">
                            <div class="faq-answer-inner">
                                <div class="faq-answer-content">
                                    <?php echo nl2br(htmlspecialchars($faq['answer'], ENT_QUOTES, 'UTF-8')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="faq-empty">
                    <p>Hiện chưa có câu hỏi nào.</p>
                </div>
            <?php endif; ?>

            <div id="noResults" class="faq-empty" style="display: none;">
                <p>Không tìm thấy kết quả phù hợp.</p>
            </div>
        </div>

        <!-- Support Section -->
        <section class="faq-support-box">
            <h3>Bạn cần thêm hỗ trợ?</h3>
            <p class="support-subtitle">Hãy liên hệ với chúng tôi</p>
            <div class="support-info">
                <p>Email: uniphincoffee@gmail.com</p>
                <p>Hotline: 19006067</p>
            </div>
        </section>
    </div>
</div>

<script src="<?php echo htmlspecialchars($asset('js/user/faqs.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
