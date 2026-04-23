<?php

// Tao doi tuong router va dang ky cac route giao dien.
$router = new Router();
$pageController = new PageController();
$authController = new AuthController();

$pages = [
    '/' => ['view' => 'trang-chu', 'title' => 'Trang chủ'],
    '/gioi-thieu' => ['view' => 'gioi-thieu', 'title' => 'Giới thiệu'],
    '/tin-tuc' => ['view' => 'tin-tuc', 'title' => 'Tin tức'],
    '/san-pham' => ['view' => 'san-pham', 'title' => 'Sản phẩm'],
    '/lien-he' => ['view' => 'lien-he', 'title' => 'Liên hệ'],
    '/faqs' => ['view' => 'faqs', 'title' => 'FAQs'],
    '/account' => ['view' => 'tai-khoan', 'title' => 'Tài khoản'],
    '/terms' => ['view' => 'dieu-khoan', 'title' => 'Điều khoản sử dụng'],
];

foreach ($pages as $path => $page) {
    $router->get($path, static function () use ($pageController, $page): void {
        $pageController->show($page['view'], $page['title']);
    });
}

// Trang auth hien thuc rieng theo giao dien yeu cau.
$router->get('/login', static function () use ($authController): void {
    $authController->login();
});

$router->get('/register', static function () use ($authController): void {
    $authController->register();
});
