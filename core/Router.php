<?php
// Clase Router: utilidad para enrutamiento centralizado y limpio.

class Router {
    private static $routes = [];

    private static function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($data);
        exit;
    }

    public static function add($method, $uri, $action) {
        self::$routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action
        ];
    }

    public static function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach (self::$routes as $route) {
            if ($route['method'] === $requestMethod && preg_match($route['uri'], $requestUri, $matches)) {
                array_shift($matches);
                call_user_func_array($route['action'], $matches);
                return;
            }
        }
        self::jsonResponse(["error" => "Ruta no encontrada"], 404);
    }
}
