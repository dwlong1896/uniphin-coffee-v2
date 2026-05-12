<?php

class AuthMiddleware
{
    public static function requireAdmin(): void
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            echo '403 Forbidden - Chỉ dành cho quản trị viên';
            exit;
        }
    }

    public static function requireLogin(): void
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
            http_response_code(403);
            echo '403 Forbidden - Chỉ dành cho người dùng đăng nhập';
            exit;
        }
    }
}