<?php
namespace Controllers;

use Core\Controller;
use Models\Model;
use Models\UserModel;

class PageController extends Controller {
    private $model;
    private $userModel;
    
    public function __construct($request, $response) {
        parent::__construct($request, $response);
        $this->model = new Model();
        $this->userModel = new UserModel();
    }
    
    // GET /
    public function home() {
        $data = [
            'title' => 'Главная страница',
            'user' => $this->userModel->isLoggedIn() ? $_SESSION['username'] : null,
            'role' => $this->userModel->getUserRole()
        ];
        
        $this->response->view('pages/home', $data);
    }
    
    // GET /dashboard
    public function dashboard() {
        if (!$this->userModel->isLoggedIn()) {
            $this->response->redirect('/login');
        }
        
        $data = [
            'title' => 'Панель управления',
            'user' => $_SESSION['username'],
            'role' => $_SESSION['role'],
            'data' => $this->model->getAll()
        ];
        
        $this->response->view('pages/dashboard', $data);
    }
    
    // GET /admin
    public function admin() {
        if (!$this->userModel->isLoggedIn() || !$this->userModel->isAdmin()) {
            $this->response->redirect('/dashboard');
        }
        
        // Получаем всех пользователей (только для админа)
        $users = $this->userModel->getAllUsers();
        
        $data = [
            'title' => 'Административная панель',
            'user' => $_SESSION['username'],
            'role' => $_SESSION['role'],
            'users' => $users,
            'totalUsers' => count($users),
            'systemInfo' => [
                'php_version' => PHP_VERSION,
                'mysql_version' => $this->getMysqlVersion(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
            ]
        ];
        
        $this->response->view('pages/admin', $data);
    }
    
    // GET /user/{id}/profile
    public function userProfile() {
        $userId = $this->request->get('id');
        
        if (!$this->userModel->isLoggedIn()) {
            $this->response->redirect('/login');
        }
        
        // Проверяем, что пользователь смотрит свой профиль или это админ
        if ($_SESSION['user_id'] != $userId && !$this->userModel->isAdmin()) {
            $this->response->redirect('/dashboard');
        }
        
        $userData = $this->userModel->getUserById($userId);
        
        if (!$userData) {
            $this->response->view('pages/404', ['message' => 'Пользователь не найден']);
            return;
        }
        
        $data = [
            'title' => 'Профиль пользователя',
            'profile_user' => $userData,
            'current_user' => [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role']
            ],
            'user_data' => $this->model->getUserData($userId) // если есть данные пользователя
        ];
        
        $this->response->view('pages/profile', $data);
    }
    
    private function getMysqlVersion() {
        try {
            $database = new \Database();
            $db = $database->getConnection();
            $stmt = $db->query("SELECT VERSION() as version");
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['version'] ?? 'Unknown';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}
?>