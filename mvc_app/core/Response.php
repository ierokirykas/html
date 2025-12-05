<?php
namespace Core;

class Response {
    private $statusCode = 200;
    private $headers = [];
    private $body;
    
    public function setStatusCode($code) {
        $this->statusCode = $code;
        http_response_code($code);
    }
    
    public function setHeader($name, $value) {
        $this->headers[$name] = $value;
        header("$name: $value");
    }
    
    public function json($data, $statusCode = 200) {
        $this->setStatusCode($statusCode);
        $this->setHeader('Content-Type', 'application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
    
    public function view($template, $data = []) {
        extract($data);
        
        // Определяем путь к шаблону
        $templatePath = "views/$template.php";
        
        if (!file_exists($templatePath)) {
            throw new \Exception("View template not found: $templatePath");
        }
        
        // Буферизация вывода
        ob_start();
        include $templatePath;
        $content = ob_get_clean();
        
        // Включаем лейаут
        include 'views/layouts/main.php';
        exit;
    }
    
    public function redirect($url, $statusCode = 302) {
        $this->setStatusCode($statusCode);
        $this->setHeader('Location', $url);
        exit;
    }
}
?>