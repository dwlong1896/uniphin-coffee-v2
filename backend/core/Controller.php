<?php

class Controller
{
    protected function baseUrl(string $path = ''): string
    {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        return $base . '/' . ltrim($path, '/');
    }

    protected function setFlash(string $key, string $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }

    protected function getFlash(string $key): ?string
    {
        if (!isset($_SESSION['_flash'][$key])) {
            return null;
        }
        $message = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);
        if (empty($_SESSION['_flash'])) {
            unset($_SESSION['_flash']);
        }
        return is_string($message) ? $message : null;
    }

    protected function view(string $view, array $data = [], ?string $layout = 'users/layouts/main'): void
    {
        $basePath = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__);
        $viewPath = $basePath . '/app/views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            $this->abort(404, 'View not found: ' . $view);
        }

        $data['flashSuccess'] ??= $this->getFlash('success');
        $data['flashError']   ??= $this->getFlash('error');

        // $asset helper – available in cả view và layout
        $publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        $asset = static function (string $path) use ($publicBase): string {
            return ($publicBase === '' ? '' : $publicBase) . '/assets/' . ltrim($path, '/');
        };

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutPath = $basePath . '/app/views/' . $layout . '.php';
        if (!file_exists($layoutPath)) {
            $this->abort(404, 'Layout not found: ' . $layout);
        }

        require $layoutPath;
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function abort(int $statusCode = 500, string $message = 'Internal Server Error'): void
    {
        http_response_code($statusCode);
        echo $message;
        exit;
    }
}