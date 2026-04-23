<?php

// Tao doi tuong router va dang ky cac route giao dien.
$router = new Router();
$pageController = new PageController();

$pages = [
    '/' => 'Trang chủ',
    '/gioi-thieu' => 'Giới thiệu',
    '/tin-tuc' => 'Tin tức',
    '/san-pham' => 'Sản phẩm',
    '/lien-he' => 'Liên hệ',
    '/faqs' => 'FAQs',
    '/register' => 'Đăng ký',
    '/login' => 'Đăng nhập',
    '/account' => 'Tài khoản',
    '/terms' => 'Điều khoản sử dụng',
];

foreach ($pages as $path => $title) {
    $router->get($path, static function () use ($pageController, $title): void {
        $pageController->show($title);
    });
}
