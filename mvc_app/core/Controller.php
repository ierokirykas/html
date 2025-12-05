<?php
namespace Core;

abstract class Controller {
    protected $request;
    protected $response;
    
    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }
    
    // Метод для проверки CSRF токена
    protected function verifyCsrfToken() {
        $token = $this->request->post('csrf_token');
        if (!$token || $token !== ($_SESSION['csrf_token'] ?? '')) {
            $this->response->json(['error' => 'Invalid CSRF token'], 403);
        }
    }
    
    // Метод для генерации CSRF токена
    protected function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
?>