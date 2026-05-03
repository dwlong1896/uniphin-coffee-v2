<?php

$router = new Router();
$pageController = new PageController();
$userController = new UserController();
$authController = new AuthController();
$adminController = new AdminController();
$newsController = new NewsController();

// show(ten-file-trong-view, Tittle)

// user
$router->get('/', static function () use ($pageController): void {$pageController->show('trang-chu', 'Trang chủ');});
$router->get('/gioi-thieu', static function () use ($pageController): void {$pageController->show('gioi-thieu', 'Giới thiệu');});
$router->get('/tin-tuc', static function () use ($newsController): void {$newsController->index();});
$router->get('/tin-tuc/:slug', static function ($slug) use ($newsController): void {$newsController->detail($slug);});
$router->get('/san-pham', static function () use ($pageController): void {$pageController->show('san-pham', 'Sản phẩm');});
$router->get('/lien-he', static function () use ($pageController): void {$pageController->show('lien-he', 'Liên hệ');});
$router->get('/faqs', static function () use ($pageController): void {$pageController->show('faqs', 'FAQs');});
$router->get('/tai-khoan', static function () use ($userController): void {$userController->profile();});
$router->post('/tai-khoan', static function () use ($userController): void {$userController->updateProfile();});

// Trang auth hien thuc rieng theo giao dien yeu cau.
$router->get('/login', static function () use ($authController): void {$authController->login();});
$router->get('/register', static function () use ($authController): void {$authController->register();});

// Auth - xử lý form
$router->post('/login', static function () use ($authController): void {$authController->handleLogin();});
$router->post('/register', static function () use ($authController): void {$authController->handleRegister();});
$router->post('/logout', static function () use ($authController): void {$authController->handleLogout();});

// Admin routes - chỉ admin mới vào được
$router->get('/admin/dashboard', static function () use ($adminController): void {$adminController->dashboard();});
$router->get('/admin/users', static function () use ($adminController): void {$adminController->users();});
$router->get('/admin/products', static function () use ($adminController): void {$adminController->products();});
$router->get('/admin/orders', static function () use ($adminController): void {$adminController->orders();});
$router->get('/admin/posts', static function () use ($adminController): void {$adminController->posts();});
$router->post('/admin/posts/create', static function () use ($adminController): void { $adminController->handleCreatePost(); });
$router->post('/admin/posts/edit/{id}', static function ($id) use ($adminController): void { $adminController->handleUpdatePost($id); });
$router->post('/admin/posts/delete', static function () use ($adminController): void { $adminController->handleDeletePost(); });

$router->get('/admin/comments', static function () use ($adminController): void {$adminController->comments();});
$router->get('/admin/contacts', static function () use ($adminController): void {$adminController->contacts();});
$router->get('/admin/qa', static function () use ($adminController): void {$adminController->qa();});
$router->get('/admin/profile', static function () use ($adminController): void {$adminController->profile();});
$router->get('/admin/homepage', static function () use ($adminController): void {$adminController->homepage();});
$router->get('/admin/faqpage', static function () use ($adminController): void {$adminController->faqspage();});
$router->get('/admin/contactpage', static function () use ($adminController): void {$adminController->contactpage();});
$router->get('/admin/aboutpage', static function () use ($adminController): void {$adminController->aboutpage();});

$router->post('/admin/profile', static function () use ($adminController): void {$adminController->updateProfile();});
