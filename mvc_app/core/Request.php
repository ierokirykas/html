<?php
namespace Core;

class Request {
    private $method;
    private $uri;
    private $params;
    private $data;
    
    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->params = $_GET;
        $this->data = $this->getInputData();
    }
    
    public function getMethod() {
        return $this->method;
    }
    
    public function getUri() {
        return $this->uri;
    }
    
    public function get($key = null, $default = null) {
        if ($key === null) {
            return $this->params;
        }
        return $this->params[$key] ?? $default;
    }
    
    public function post($key = null, $default = null) {
        if ($key === null) {
            return $this->data;
        }
        return $this->data[$key] ?? $default;
    }
    
    public function input($key = null, $default = null) {
        if ($this->method === 'GET') {
            return $this->get($key, $default);
        }
        return $this->post($key, $default);
    }
    
    public function isMethod($method) {
        return strtoupper($method) === $this->method;
    }
    
    private function getInputData() {
        $data = [];
        
        if ($this->method === 'POST' || $this->method === 'PUT') {
            if (!empty($_POST)) {
                $data = $_POST;
            } else {
                $input = file_get_contents('php://input');
                $data = json_decode($input, true) ?? [];
            }
        }
        
        return $data;
    }
    
    public function isJson() {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return strpos($contentType, 'application/json') !== false;
    }
}
?>