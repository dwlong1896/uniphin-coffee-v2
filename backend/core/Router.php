<?php

class Router
{
    // Bang luu route theo tung HTTP method.
    // Vi du: $routes['GET']['/users'] = [UserController::class, 'index']
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function dispatch(string $method, string $uri): void
    {
        // Lay path tu URI day du.
        $requestPath = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Cat tien to thu muc chay app (vi du /uniphin2/backend/public)
        // de route khai bao ngan gon (/users) van match duoc.
        $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($basePath !== '' && $basePath !== '/' && strpos($requestPath, $basePath) === 0) {
            $requestPath = substr($requestPath, strlen($basePath));
            $requestPath = $requestPath === false || $requestPath === '' ? '/' : $requestPath;
        }

        // Chuan hoa URL de tranh trung route:
        // - /users/ va /users duoc xem la mot
        // - duong dan rong se tro thanh /
        $requestPath = rtrim($requestPath, '/');
        $requestPath = $requestPath === '' ? '/' : $requestPath;

        $handler = $this->routes[$method][$requestPath] ?? null;
        if ($handler === null) {
            // Khong tim thay route phu hop -> tra 404.
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        if (is_callable($handler)) {
            // Ho tro handler dang closure/function.
            $handler();
            return;
        }

        if (is_array($handler) && count($handler) === 2) {
            [$controllerClass, $action] = $handler;
            $controller = new $controllerClass();
            // Goi action trong controller, vi du [UserController::class, 'index'].
            $controller->$action();
            return;
        }

        // Handler ton tai nhung sai dinh dang -> loi cau hinh route.
        http_response_code(500);
        echo 'Invalid route handler';
    }

    private function addRoute(string $method, string $path, $handler): void
    {
        // Luu route voi path da chuan hoa de dong nhat khi tim kiem.
        $normalizedPath = rtrim($path, '/');
        $normalizedPath = $normalizedPath === '' ? '/' : $normalizedPath;
        $this->routes[$method][$normalizedPath] = $handler;
    }
}
