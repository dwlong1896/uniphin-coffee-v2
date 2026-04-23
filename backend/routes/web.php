<?php

// Tao doi tuong router va dang ky cac route giao dien.
$router = new Router();
$pageController = new PageController();
$authController = new AuthController();

$pages = [
    '/' => 'Trang chủ',
    '/gioi-thieu' => 'Giới thiệu',
    '/tin-tuc' => 'Tin tức',
    '/san-pham' => 'Sản phẩm',
    '/lien-he' => 'Liên hệ',
    '/faqs' => 'FAQs',
    '/account' => 'Tài khoản',
    '/terms' => 'Điều khoản sử dụng',
];

foreach ($pages as $path => $title) {
    $router->get($path, static function () use ($pageController, $title): void {
        $pageController->show($title);
    });
}

// Trang auth hien thuc rieng theo giao dien yeu cau.
$router->get('/login', static function () use ($authController): void {
    $authController->login();
});

$router->get('/register', static function () use ($authController): void {
    $authController->register();
});
