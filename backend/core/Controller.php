<?php

class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = 'users/layouts/main'): void
    {
        // Tao duong dan tuyet doi toi file view.
        // Neu truyen 'users/index' thi file se la app/views/users/index.php
        $basePath = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__);
        $viewPath = $basePath . '/app/views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            $this->abort(404, 'View not found: ' . $view);
        }

        // Bien doi mang du lieu thanh bien de dung truc tiep trong view.
        // Vi du ['users' => $users] se tro thanh bien $users.
        extract($data, EXTR_SKIP);

        // Render noi dung view truoc, sau do chen vao layout tong.
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
        // Sau khi redirect can dung ngay de tranh render tiep noi dung cu.
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
