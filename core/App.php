<?php

namespace Core;

class App
{
    public function __construct() {
        $routes = require __DIR__ . '/../web.php';
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestUri = str_replace('/event-management-system/public', '', $requestUri);

        if (array_key_exists($requestUri, $routes)) {
            [$controllerClass, $method] = $routes[$requestUri];

            if (class_exists($controllerClass)) {
                if (method_exists($controllerClass, $method)) {
                    $controller = new $controllerClass();
                    $controller->$method();
                } else {
                    http_response_code(500);
                    die('Method not found');
                }
            } else {
                http_response_code(500);
                die('Class not found');
            }
        } else {
            http_response_code(404);
            die("404 - Page Not Found");
        }
    }
}
