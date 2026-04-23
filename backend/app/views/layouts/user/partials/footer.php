<?php
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$toUrl = static function (string $path = '/') use ($publicBase): string {
    $normalizedPath = '/' . ltrim($path, '/');
    return ($publicBase === '' ? '' : $publicBase) . $normalizedPath;
};
$assetUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/assets/' . ltrim($path, '/');
};
?>
<footer class="footer-container">
    <div class="footer-content">
        <div class="footer-column">
            <h3 class="footer-title">Giới thiệu</h3>
            <div class="footer-links-grid">
                <a href="<?php echo htmlspecialchars($toUrl('/'), ENT_QUOTES, 'UTF-8'); ?>">Home</a>
                <a href="<?php echo htmlspecialchars($toUrl('/tin-tuc'), ENT_QUOTES, 'UTF-8'); ?>">News</a>
                <a href="<?php echo htmlspecialchars($toUrl('/gioi-thieu'), ENT_QUOTES, 'UTF-8'); ?>">About us</a>
                <a href="<?php echo htmlspecialchars($toUrl('/faqs'), ENT_QUOTES, 'UTF-8'); ?>">FAQs</a>
                <a href="<?php echo htmlspecialchars($toUrl('/account'), ENT_QUOTES, 'UTF-8'); ?>">Account</a>
                <a href="<?php echo htmlspecialchars($toUrl('/lien-he'), ENT_QUOTES, 'UTF-8'); ?>">Contact us</a>
            </div>
        </div>

        <div class="footer-column center-column">
            <img src="<?php echo htmlspecialchars($assetUrl('image/rmbgwhite.png'), ENT_QUOTES, 'UTF-8'); ?>" alt="UNIPHIN COFFEE Logo" class="footer-logo">
            <h3 class="footer-title cyan-text">Follow us</h3>
            <div class="social-icons">
                <a href="#" aria-label="Facebook">
                    <img src="<?php echo htmlspecialchars($assetUrl('image/facebook.png'), ENT_QUOTES, 'UTF-8'); ?>" alt="Facebook">
                </a>
                <a href="#" aria-label="Twitter">
                    <img src="<?php echo htmlspecialchars($assetUrl('image/twiter.png'), ENT_QUOTES, 'UTF-8'); ?>" alt="Twitter">
                </a>
                <a href="#" aria-label="Instagram">
                    <img src="<?php echo htmlspecialchars($assetUrl('image/instargram.png'), ENT_QUOTES, 'UTF-8'); ?>" alt="Instagram">
                </a>
            </div>
        </div>

        <div class="footer-column cl2">
            <h3 class="footer-title">Locations</h3>
            <div class="location-info">
                <strong>SAI GON LOCATION</strong>
                <p>60 Bui Thi Xuan</p>
                <p>Phuong Pham Ngu Lao, TP.HCM</p>
                <p>(028) 3863 2345</p>
            </div>
        </div>
    </div>

    <hr class="footer-divider" />

    <div class="footer-bottom">
        <div class="contact-info">
            <p><span class="cyan-text">Hotline:</span> 1900 6067</p>
            <p><span class="cyan-text">Email:</span> uniphincoffee@gmail.com</p>
        </div>
        <div class="legal-links">
            <a href="<?php echo htmlspecialchars($toUrl('/terms'), ENT_QUOTES, 'UTF-8'); ?>">Terms of Use - Private Policy</a>
        </div>
    </div>
</footer>
