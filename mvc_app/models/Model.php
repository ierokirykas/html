<?php
class Model {
    private $db;
    private $table = "New_Data";
    private $user_table = "users";

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        session_start(); // Запускаем сессию в модели
    }

    // Методы для работы с пользователями
    public function authenticate($username, $password) {
        try {
            $query = "SELECT * FROM " . $this->user_table . " WHERE username = :username";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['loggedin'] = true;
                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            error_log("Ошибка аутентификации: " . $e->getMessage());
            return false;
        }
    }

    public function isLoggedIn() {
        return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
    }

    public function isAdmin() {
        return $this->isLoggedIn() && $_SESSION['role'] === 'admin';
    }

    public function getUserRole() {
        return $this->isLoggedIn() ? $_SESSION['role'] : 'guest';
    }

    public function logout() {
        session_destroy();
        $_SESSION = array();
    }

    // Обновляем методы для работы с данными с проверкой прав
    public function getDataFromDB() {
        try {
            $query = "SELECT * FROM " . $this->table . " ORDER BY id";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Ошибка получения данных: " . $e->getMessage());
            return array();
        }
    }

    public function getString() {
        if (!$this->isLoggedIn()) {
            return "Пожалуйста, войдите в систему для просмотра данных.";
        }

        $data = $this->getDataFromDB();
        
        if (empty($data)) {
            return "База данных пуста.";
        }
        
        $result = "<h3>Последние записи из базы данных:</h3>";
        $count = count($data);
        
        for ($i = 0; $i < $count; $i++) {
            $record = $data[$i];
            $result .= "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px;'>";
            $result .= "Запись " . implode(" | ", $record);
            $result .= "</div>";
        }
        
        return $result;
    }

    public function saveFormData($id = null, $data_string) {
        // Проверяем права доступа
        if (!$this->isAdmin()) {
            return false;
        }

        try {
            if ($id) {
                $query = "UPDATE " . $this->table . " SET data_string = :data_string, updated_at = NOW() WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id", $id);
                $stmt->bindParam(":data_string", $data_string);
            } else {
                $query = "INSERT INTO " . $this->table . " (data_string) VALUES (:data_string)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":data_string", $data_string);
            }
            
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            error_log("Ошибка сохранения данных формы: " . $e->getMessage());
            return false;
        }
    }
}
?>