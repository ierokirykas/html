<?php
namespace Controllers;

use Core\Controller;
use Models\UserModel;

class AuthController extends Controller {
    private $userModel;
    
    public function __construct($request, $response) {
        parent::__construct($request, $response);
        $this->userModel = new UserModel();
    }
    
    public function showLoginForm() {
        if ($this->userModel->isLoggedIn()) {
            $this->response->redirect('/dashboard');
        }
        
        $this->response->view('auth/login');
    }
    
    public function login() {
        if ($this->request->isMethod('GET')) {
            return $this->showLoginForm();
        }
        
        // POST запрос
        $username = $this->request->post('username');
        $password = $this->request->post('password');
        
        if ($this->userModel->authenticate($username, $password)) {
            $this->response->redirect('/dashboard');
        } else {
            $this->response->view('auth/login', [
                'error' => 'Неверные учетные данные'
            ]);
        }
    }
    
    public function logout() {
        $this->userModel->logout();
        $this->response->redirect('/');
    }
}
?>