<?php

require_once 'app/Controllers/HomeController.php';
require_once 'app/Controllers/AuthController.php';

$homeController = new HomeController();
$authController = new AuthController();

$routes = [
    '/' => ['controller' => $homeController, 'method' => 'index'],
    '/register' => ['controller' => $authController, 'method' => 'register'],
    '/login' => ['controller' => $authController, 'method' => 'login'],
];

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = str_replace('/event-management-system', '', $requestUri);
$requestMethod = $_SERVER['REQUEST_METHOD'];

if (isset($routes[$requestUri])) {
    $route = $routes[$requestUri];
    if (method_exists($route['controller'], $route['method'])) {
        if ($requestMethod === 'POST') {
            $route['controller']->{$route['method']}($_POST);
        } else {
            $route['controller']->{$route['method']}($_GET);
        }
    } else {
        http_response_code(500);
        echo "Method {$route['method']} not found in the controller.";
    }
} else {
    http_response_code(404);
    echo "404 Not Found";
}
