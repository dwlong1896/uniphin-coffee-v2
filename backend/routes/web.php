<?php

$router = new Router();
$pageController = new PageController();
$authController = new AuthController();
$adminController = new AdminController();



// show(ten-file-trong-view, Tittle)

// user
$router->get('/', static function () use ($pageController): void {$pageController->show('trang-chu', 'Trang chủ');});
$router->get('/gioi-thieu', static function () use ($pageController): void {$pageController->show('gioi-thieu', 'Giới thiệu');});
$router->get('/tin-tuc', static function () use ($pageController): void {$pageController->show('tin-tuc', 'Tin tức');});
$router->get('/san-pham', static function () use ($pageController): void {$pageController->show('san-pham', 'Sản phẩm');});
$router->get('/lien-he', static function () use ($pageController): void {$pageController->show('lien-he', 'Liên hệ');});
$router->get('/faqs', static function () use ($pageController): void {$pageController->show('faqs', 'FAQs');});
$router->get('/account', static function () use ($pageController): void {$pageController->show('tai-khoan', 'Tài khoản');});

// Trang auth hien thuc rieng theo giao dien yeu cau.
$router->get('/login', static function () use ($authController): void {$authController->login();});
$router->get('/register', static function () use ($authController): void {$authController->register();});


// Auth - xử lý form
$router->post('/login', static function () use ($authController): void {$authController->handleLogin();});
$router->post('/logout', static function () use ($authController): void {$authController->handleLogout();});


// Admin routes - chỉ admin mới vào được
$router->get('/admin/dashboard',  static function () use ($adminController): void { $adminController->dashboard(); });
$router->get('/admin/users',      static function () use ($adminController): void { $adminController->users(); });
$router->get('/admin/products',   static function () use ($adminController): void { $adminController->products(); });
$router->get('/admin/orders',     static function () use ($adminController): void { $adminController->orders(); });
$router->get('/admin/posts',      static function () use ($adminController): void { $adminController->posts(); });
$router->get('/admin/comments',   static function () use ($adminController): void { $adminController->comments(); });
$router->get('/admin/contacts',   static function () use ($adminController): void { $adminController->contacts(); });
$router->get('/admin/qa',         static function () use ($adminController): void { $adminController->qa(); });
$router->get('/admin/profile',    static function () use ($adminController): void { $adminController->profile(); });
$router->get('/admin/homepage',   static function () use ($adminController): void { $adminController->homepage(); });
$router->get('/admin/faqpage',    static function () use ($adminController): void { $adminController->faqspage(); });
$router->get('/admin/contactpage',    static function () use ($adminController): void { $adminController->contactpage(); });
$router->get('/admin/aboutpage',    static function () use ($adminController): void { $adminController->aboutpage(); });