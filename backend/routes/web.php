<?php

$router = new Router();
$pageController = new PageController();
$userController = new UserController();
$authController = new AuthController();
$adminController = new AdminController();
$adminUserController = new AdminUserController();
$orderController = new OrderController();
$productController = new ProductController();
$cartController = new CartController();
$newsController = new NewsController();

// show(ten-file-trong-view, title)

// user
$router->get('/', static function () use ($pageController): void {$pageController->show('trang-chu', 'Trang chủ');});
$router->get('/gioi-thieu', static function () use ($pageController): void {$pageController->show('gioi-thieu', 'Giới thiệu');});
$router->get('/tin-tuc', static function () use ($newsController): void {$newsController->index();});
$router->get('/tin-tuc/{slug}', static function ($slug) use ($newsController): void {$newsController->detail($slug);});
$router->post('/post-comment', static function () use ($newsController): void {$newsController->postComment();}); // AJAX Post Comment
$router->post('/comment-action', static function () use ($newsController): void {$newsController->handleCommentAction();}); // AJAX Edit/Delete Comment
$router->get('/san-pham', static function () use ($productController): void {$productController->menu();});
$router->get('/cart', static function () use ($cartController): void {$cartController->index();});
$router->get('/gio-hang', static function () use ($cartController): void {$cartController->index();});
$router->get('/checkout', static function () use ($cartController): void {$cartController->checkout();});
$router->get('/thanh-toan', static function () use ($cartController): void {$cartController->checkout();});
$router->get('/lien-he', static function () use ($pageController): void {$pageController->show('lien-he', 'Liên hệ');});
$router->get('/faqs', static function () use ($pageController): void {$pageController->show('faqs', 'FAQs');});
$router->get('/dieu-khoan', static function () use ($pageController): void {$pageController->show('dieu-khoan', 'Dieu khoan');});
$router->get('/terms', static function () use ($pageController): void {$pageController->show('dieu-khoan', 'Dieu khoan');});
$router->get('/tai-khoan', static function () use ($userController): void {$userController->profile();});
$router->post('/tai-khoan', static function () use ($userController): void {$userController->updateProfile();});
$router->post('/cart/add', static function () use ($cartController): void {$cartController->add();});
$router->post('/cart/update', static function () use ($cartController): void {$cartController->update();});
$router->post('/cart/remove', static function () use ($cartController): void {$cartController->remove();});
$router->post('/checkout', static function () use ($cartController): void {$cartController->placeOrder();});
$router->post('/thanh-toan', static function () use ($cartController): void {$cartController->placeOrder();});
$router->post('/gio-hang/them', static function () use ($cartController): void {$cartController->add();});
$router->post('/gio-hang/cap-nhat', static function () use ($cartController): void {$cartController->update();});
$router->post('/gio-hang/xoa', static function () use ($cartController): void {$cartController->remove();});

// Auth routes
$router->get('/login', static function () use ($authController): void {$authController->login();});
$router->get('/register', static function () use ($authController): void {$authController->register();});
$router->post('/login', static function () use ($authController): void {$authController->handleLogin();});
$router->post('/register', static function () use ($authController): void {$authController->handleRegister();});
$router->post('/logout', static function () use ($authController): void {$authController->handleLogout();});

// Admin routes
$router->get('/admin/users', static function () use ($adminUserController): void {$adminUserController->index();});
$router->get('/admin/users/viewdetail', static function () use ($adminUserController): void {$adminUserController->viewDetail();});
$router->post('/admin/users/update', static function () use ($adminUserController): void {$adminUserController->update();});
$router->get('/admin/products/viewdetail', static function () use ($productController): void {$productController->viewdetail();});
$router->get('/admin/products', static function () use ($productController): void {$productController->index();});
$router->post('/admin/product-categories/create', static function () use ($productController): void {$productController->createCategory();});
$router->post('/admin/product-categories/update', static function () use ($productController): void {$productController->updateCategory();});
$router->post('/admin/product-categories/delete', static function () use ($productController): void {$productController->deleteCategory();});
$router->post('/admin/products/create', static function () use ($productController): void {$productController->create();});
$router->post('/admin/products/update', static function () use ($productController): void {$productController->update();});
$router->post('/admin/products/delete', static function () use ($productController): void {$productController->delete();});

$router->get('/admin/orders', static function () use ($orderController): void {$orderController->index();});
$router->get('/admin/orders/viewdetail', static function () use ($orderController): void {$orderController->viewDetail();});
$router->post('/admin/orders/update', static function () use ($orderController): void {$orderController->update();});
$router->get('/admin/posts', static function () use ($adminController): void {$adminController->posts();});
$router->post('/admin/posts/create', static function () use ($adminController): void {$adminController->handleCreatePost();});
$router->post('/admin/posts/update', static function () use ($adminController): void {$adminController->handleUpdatePost();});
$router->post('/admin/posts/delete', static function () use ($adminController): void {$adminController->deletePost();}); // Khớp với tên hàm deletePost
$router->get('/admin/posts/get-json', static function () use ($adminController): void {$adminController->getPostJson();}); // Lấy data đổ vào Modal
$router->get('/admin/posts/get-post-detail-full', [AdminController::class, 'getPostDetailFull']);
// --- QUẢN LÝ BÌNH LUẬN (ADMIN - AJAX) ---
$router->get('/admin/comments', static function () use ($adminController): void {$adminController->comments();});
$router->post('/admin/comments/toggle', static function () use ($adminController): void {$adminController->toggleCommentStatus();});
$router->post('/admin/comments/delete', static function () use ($adminController): void {$adminController->deleteComment();});
$router->post('/admin/comments/post-admin', static function () use ($adminController): void {$adminController->handlePostComment();}); // Admin tự comment/reply
$router->get('/admin/comments/get-json', static function () use ($adminController): void {$adminController->getCommentJson();}); // Lấy data comment cho Modal
$router->post('/admin/comments/update', static function () use ($adminController): void {$adminController->handleUpdateComment();});
// --- QUẢN LÝ DANH MỤC TIN TỨC (ADMIN - AJAX) ---
$router->get('/admin/categories', static function () use ($adminController): void {
    $adminController->categories();
});
$router->post('/admin/categories/create', static function () use ($adminController): void {
    $adminController->createCategory();
});
$router->post('/admin/categories/update', static function () use ($adminController): void {
    $adminController->handleUpdateCategory();
});
$router->post('/admin/categories/delete', static function () use ($adminController): void {
    $adminController->deleteCategory();
});
$router->get('/admin/categories/get-json', static function () use ($adminController): void {
    $adminController->getCategoryJson();
});
$router->get('/admin/contacts', static function () use ($adminController): void {$adminController->contacts();});
$router->get('/admin/qa', static function () use ($adminController): void {$adminController->qa();});
$router->get('/admin/profile', static function () use ($adminController): void {$adminController->profile();});
$router->get('/admin/homepage', static function () use ($adminController): void {$adminController->homepage();});
$router->get('/admin/faqpage', static function () use ($adminController): void {$adminController->faqspage();});
$router->get('/admin/contactpage', static function () use ($adminController): void {$adminController->contactpage();});
$router->get('/admin/aboutpage', static function () use ($adminController): void {$adminController->aboutpage();});

$router->post('/admin/profile', static function () use ($adminController): void {$adminController->updateProfile();});

$router->post('/admin/faq/save', static function () use ($adminController): void {$adminController->faqSave();});
$router->post('/admin/faq/delete', static function () use ($adminController): void {$adminController->faqDelete();});
$router->post('/admin/about/save', static function () use ($adminController): void {$adminController->aboutSave();});
