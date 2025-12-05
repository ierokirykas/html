<?php
namespace Core;

class Router {
    private $routes = [];
    
    public function __construct($routes) {
        $this->routes = $routes;
    }
    
    public function dispatch(Request $request, Response $response) {
        $method = $request->getMethod();
        $uri = $request->getUri();
        
        foreach ($this->routes as $route) {
            if ($this->match($route, $method, $uri)) {
                $this->execute($route, $request, $response);
                return;
            }
        }
        
        // 404 если маршрут не найден
        $response->setStatusCode(404);
        $response->json(['error' => 'Route not found']);
    }
    
    private function match($route, $method, $uri) {
        // Проверка метода
        if (!in_array($method, $route['methods'])) {
            return false;
        }
        
        // Проверка URI (простой вариант, можно добавить regex)
        if ($route['uri'] === $uri) {
            return true;
        }
        
        // Проверка с параметрами
        $pattern = preg_replace('/\{(\w+)\}/', '(\w+)', $route['uri']);
        $pattern = str_replace('/', '\/', $pattern);
        
        if (preg_match('/^' . $pattern . '$/', $uri, $matches)) {
            return true;
        }
        
        return false;
    }
    
    private function execute($route, $request, $response) {
        list($controllerName, $methodName) = explode('@', $route['handler']);
        
        $controllerClass = "Controllers\\" . $controllerName;
        
        if (!class_exists($controllerClass)) {
            throw new \Exception("Controller $controllerClass not found");
        }
        
        $controller = new $controllerClass($request, $response);
        
        if (!method_exists($controller, $methodName)) {
            throw new \Exception("Method $methodName not found in $controllerClass");
        }
        
        $controller->$methodName();
    }
}
?>