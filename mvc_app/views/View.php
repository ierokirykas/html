<?php
// views/View.php
class View {
    private $model;
    private $controller;

    public function __construct($controller, $model) {
        $this->controller = $controller;
        $this->model = $model;
    }

    public function output() {
        $data = $this->model->getString();
        $userRole = $this->model->getUserRole(); // Сохраняем роль в переменную
        
        // Блок авторизации
        $authSection = '';
        if ($this->model->isLoggedIn()) {
        $username = $_SESSION['username'];
        $role = $_SESSION['role'];
        $authSection = "
        <div style='background: #f0f0f0; padding: 10px; margin-bottom: 20px;'>
            <p>Вы вошли как: <strong>$username</strong> ($role) 
            <a href='index.php?action=logout' style='float: right;'>Выйти</a></p>
        </div>";
        } else {
        $authSection = "
        <div style='background: #f0f0f0; padding: 10px; margin-bottom: 20px;'>
            <p>Вы не авторизованы. 
            <a href='index.php?action=loginForm'>Войти</a></p>
        </div>";
        }

            // Форма для админов
        $form = '';
        if ($this->model->isAdmin()) {
            $form = '
            <div style="border: 2px solid green; padding: 15px; margin-bottom: 20px;">
                <h3>Панель администратора</h3>
                <form method="POST" action="index.php">
                    <div style="margin-bottom: 10px;">
                        <label for="id">ID записи (оставьте пустым для новой записи):</label><br>
                        <input type="number" name="id" id="id">
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label for="data_string">Текст записи:</label><br>
                        <input type="text" name="data_string" id="data_string" required style="width: 300px;">
                    </div>
                    <input type="submit" name="submit" value="Сохранить">
                    <input type="hidden" name="action" value="saveData">
                </form>
            </div>';
        }
        // Форма входа
        $loginForm = '';
        if (isset($_GET['action']) && $_GET['action'] === 'loginForm' && !$this->model->isLoggedIn()) {
            $loginForm = '
            <div style="border: 2px solid blue; padding: 15px; margin-bottom: 20px;">
                <h3>Вход в систему</h3>
                <form method="POST" action="index.php">
                    <div style="margin-bottom: 10px;">
                        <label for="username">Имя пользователя:</label><br>
                        <input type="text" name="username" id="username" required style="width: 200px;">
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label for="password">Пароль:</label><br>
                        <input type="password" name="password" id="password" required style="width: 200px;">
                    </div>
                    <input type="submit" value="Войти">
                    <input type="hidden" name="action" value="login">
                </form>
                <p><small>Тестовые пользователи:<br>
                admin / admin123<br>
                user / user123</small></p>
            </div>';
        }

        // Сообщения
        $message = '';
        if (isset($_SESSION['message'])) {
            $message = '<div style="color: green; padding: 10px; border: 1px solid green; margin-bottom: 20px;">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
        }
        if (isset($_SESSION['error'])) {
            $message = '<div style="color: red; padding: 10px; border: 1px solid red; margin-bottom: 20px;">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }

        return '<!DOCTYPE html>
        <html>
        <head>
            <title>MVC Demo</title>
            <meta charset="UTF-8">
            <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .container { max-width: 800px; margin: 0 auto; }
            .role-badge { 
                background: #007bff; 
                color: white; 
                padding: 2px 8px; 
                border-radius: 10px; 
                font-size: 12px;
            }
            .admin { background: #dc3545; }
            .user { background: #28a745; }
            .guest { background: #6c757d; }
        </style>
        </head>
        <body>
            <div class="container">
            <h1>MVC Приложение с авторизацией</h1>
            ' . $authSection . '
            ' . $message . '
            ' . $loginForm . '
            ' . $form . '

            <div class="data-block">
                <h2>Данные из базы:</h2>
                ' . $data . '
            </div>
            
            <div style="margin-top: 20px; padding: 10px; background: #f8f9fa;">
                <h3>Информация о правах доступа:</h3>
                <ul>
                    <li><strong>Гость:</strong> Может только просматривать эту страницу</li>
                    <li><strong>Пользователь:</strong> Может просматривать данные из базы</li>
                    <li><strong>Администратор:</strong> Может добавлять и редактировать данные</li>
                </ul>
                <p>Текущая роль: <span class="role-badge ' . $userRole . '">' . $userRole . '</span></p>
            </div>
            
            <p><a href="index.php">Обновить страницу</a></p>
            <p>Приложение работает на WSL Ubuntu 20.04!</p>
        </body>
        </html>';
    }
}
?>
