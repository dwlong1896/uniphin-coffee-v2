<?php

$router = new Router();
$pageController = new PageController();
$userController = new UserController();
$authController = new AuthController();
$adminController = new AdminController();
$productController = new ProductController();

// show(ten-file-trong-view, title)

// User routes
$router->get('/chi-tiet', static function () use ($pageController): void {$pageController->show('chi-tiet', 'Chi tiet san pham');});
$router->get('/', static function () use ($pageController): void {$pageController->show('trang-chu', 'Trang chu');});
$router->get('/gioi-thieu', static function () use ($pageController): void {$pageController->show('gioi-thieu', 'Gioi thieu');});
$router->get('/tin-tuc', static function () use ($pageController): void {$pageController->show('tin-tuc', 'Tin tuc');});
$router->get('/san-pham', static function () use ($productController): void {$productController->menu();});
$router->get('/lien-he', static function () use ($pageController): void {$pageController->show('lien-he', 'Lien he');});
$router->get('/faqs', static function () use ($pageController): void {$pageController->show('faqs', 'FAQs');});
$router->get('/tai-khoan', static function () use ($userController): void {$userController->profile();});
$router->post('/tai-khoan', static function () use ($userController): void {$userController->updateProfile();});

// Auth routes
$router->get('/login', static function () use ($authController): void {$authController->login();});
$router->get('/register', static function () use ($authController): void {$authController->register();});
$router->post('/login', static function () use ($authController): void {$authController->handleLogin();});
$router->post('/register', static function () use ($authController): void {$authController->handleRegister();});
$router->post('/logout', static function () use ($authController): void {$authController->handleLogout();});

// Admin routes
$router->get('/admin/dashboard', static function () use ($adminController): void {$adminController->dashboard();});
$router->get('/admin/users', static function () use ($adminController): void {$adminController->users();});
$router->get('/admin/products/viewdetail', static function () use ($productController): void {$productController->viewdetail();});
$router->get('/admin/products', static function () use ($productController): void {$productController->index();});
$router->post('/admin/categories/create', static function () use ($productController): void {$productController->createCategory();});
$router->post('/admin/categories/update', static function () use ($productController): void {$productController->updateCategory();});
$router->post('/admin/categories/delete', static function () use ($productController): void {$productController->deleteCategory();});
$router->post('/admin/products/create', static function () use ($productController): void {$productController->create();});
$router->post('/admin/products/update', static function () use ($productController): void {$productController->update();});
$router->post('/admin/products/delete', static function () use ($productController): void {$productController->delete();});

$router->get('/admin/orders', static function () use ($adminController): void {$adminController->orders();});
$router->get('/admin/posts', static function () use ($adminController): void {$adminController->posts();});
$router->get('/admin/comments', static function () use ($adminController): void {$adminController->comments();});
$router->get('/admin/contacts', static function () use ($adminController): void {$adminController->contacts();});
$router->get('/admin/qa', static function () use ($adminController): void {$adminController->qa();});
$router->get('/admin/profile', static function () use ($adminController): void {$adminController->profile();});
$router->get('/admin/homepage', static function () use ($adminController): void {$adminController->homepage();});
$router->get('/admin/faqpage', static function () use ($adminController): void {$adminController->faqspage();});
$router->get('/admin/contactpage', static function () use ($adminController): void {$adminController->contactpage();});
$router->get('/admin/aboutpage', static function () use ($adminController): void {$adminController->aboutpage();});
$router->post('/admin/profile', static function () use ($adminController): void {$adminController->updateProfile();});
