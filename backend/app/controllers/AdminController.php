<?php

class AdminController extends Controller
{
    
    public function dashboard(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/index', ['title' => 'Dashboard'], 'admin/layouts/main');
    }

    public function users(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/users', ['title' => 'Quản lý tài khoản'], 'admin/layouts/main');
    }

    public function products(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/products', ['title' => 'Quản lý sản phẩm'], 'admin/layouts/main');
    }

    public function orders(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/orders', ['title' => 'Quản lý đơn hàng'], 'admin/layouts/main');
    }

    public function posts(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/posts', ['title' => 'Quản lý tin tức'], 'admin/layouts/main');
    }

    public function comments(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/comments', ['title' => 'Quản lý bình luận'], 'admin/layouts/main');
    }

    public function contacts(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/contacts', ['title' => 'Quản lý liên hệ'], 'admin/layouts/main');
    }

    public function qa(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/qa', ['title' => 'Quản lý hỏi đáp'], 'admin/layouts/main');
    }

    public function profile(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/profile', ['title' => 'My Profile'], 'admin/layouts/main');
    }
    public function homepage(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/homepage', ['title' => 'Home Page'], 'admin/layouts/main');
    }

    public function contactpage(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/contactpage', ['title' => 'Contact Page'], 'admin/layouts/main');
    }

    public function aboutpage(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/aboutpage', ['title' => 'About Page'], 'admin/layouts/main');
    }

    public function faqspage(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/faqpage', ['title' => 'FAQ Page'], 'admin/layouts/main');
    }
}