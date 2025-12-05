<?php
class Controller {
    private $model;

    public function __construct($model) {
        $this->model = $model;
    }

    public function clicked() {
        $this->model->change();
    }

    public function saveData() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : null;
            $data_string = isset($_POST['data_string']) ? trim($_POST['data_string']) : '';
            
            if (!empty($data_string)) {
                $success = $this->model->saveFormData($id, $data_string);
                
                if ($success) {
                    $_SESSION['message'] = $id ? "Запись с ID $id успешно обновлена!" : "Новая запись успешно создана!";
                } else {
                    $_SESSION['error'] = "Ошибка: недостаточно прав для выполнения операции!";
                }
            }
            
            header('Location: index.php');
            exit;
        }
    }

    // Методы для авторизации
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            
            if ($this->model->authenticate($username, $password)) {
                $_SESSION['message'] = "Добро пожаловать, " . $_SESSION['username'] . "!";
                header('Location: index.php');
                exit;
            } else {
                $_SESSION['error'] = "Неверное имя пользователя или пароль!";
                header('Location: index.php?action=loginForm');
                exit;
            }
        }
    }

    public function logout() {
        $this->model->logout();
        $_SESSION['message'] = "Вы успешно вышли из системы!";
        header('Location: index.php');
        exit;
    }

    public function loginForm() {
        // Просто отображаем форму входа
    }
}
?>