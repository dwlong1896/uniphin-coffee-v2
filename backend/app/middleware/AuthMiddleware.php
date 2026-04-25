<?php

class AuthMiddleware
{
    // Chỉ admin mới được vào, không phải admin → 403
    public static function requireAdmin(): void
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            echo '403 Forbidden - Chỉ dành cho quản trị viên';
            exit;
        }
    }
}