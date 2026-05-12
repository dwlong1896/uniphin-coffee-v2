<?php

class Router
{

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
        $requestPath = parse_url($uri, PHP_URL_PATH) ?? '/';

        $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($basePath !== '' && $basePath !== '/' && strpos($requestPath, $basePath) === 0) {
            $requestPath = substr($requestPath, strlen($basePath));
            $requestPath = $requestPath === false || $requestPath === '' ? '/' : $requestPath;
        }

        $requestPath = rtrim($requestPath, '/');
        $requestPath = $requestPath === '' ? '/' : $requestPath;

    
        $matched = $this->matchRoute($method, $requestPath);

        if ($matched === null) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        $handler = $matched['handler'];
        $params  = $matched['params'];   

        if (is_callable($handler)) {
            $handler($params);   
            return;
        }

        if (is_array($handler) && count($handler) === 2) {
            [$controllerClass, $action] = $handler;
            $controller = new $controllerClass();
            $controller->$action($params);   
            return;
        }

        http_response_code(500);
        echo 'Invalid route handler';
    }


    private function matchRoute(string $method, string $requestPath): array|null
    {
        foreach ($this->routes[$method] as $pattern => $handler) {
         
            $regex = preg_replace('/\{[^\/]+\}/', '([^/]+)', $pattern);
            $regex = '#^' . $regex . '$#';

            if (preg_match($regex, $requestPath, $matches)) {
               
                array_shift($matches);

               
                preg_match_all('/\{([^\/]+)\}/', $pattern, $paramNames);

            
    
                $params = array_combine($paramNames[1], $matches);

                return ['handler' => $handler, 'params' => $params];
            }
        }

        return null;
    }
    private function addRoute(string $method, string $path, $handler): void
    {
        // Luu route voi path da chuan hoa de dong nhat khi tim kiem.
        $normalizedPath = rtrim($path, '/');
        $normalizedPath = $normalizedPath === '' ? '/' : $normalizedPath;
        $this->routes[$method][$normalizedPath] = $handler;
    }
}