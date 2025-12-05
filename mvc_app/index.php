<?php
// index.php
session_start();

// Автозагрузка классов
spl_autoload_register(function ($class) {
    $prefixes = [
        'Core\\' => 'core/',
        'Controllers\\' => 'controllers/',
        'Models\\' => 'models/',
        'Views\\' => 'views/'
    ];
    
    foreach ($prefixes as $prefix => $base_dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }
        
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        
        if (file_exists($file)) {
            require $file;
        }
    }
});

require_once 'config/database.php';
$routes = include 'config/routes.php';

// Инициализация
$request = new Core\Request();
$response = new Core\Response();
$router = new Core\Router($routes);

// Обработка запроса
$router->dispatch($request, $response);
?>