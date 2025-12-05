<?php
namespace Models;

class UserModel {
    private $db;
    private $table = "users";
    
    public function __construct() {
        $database = new \Database();
        $this->db = $database->getConnection();
        session_start();
    }
    
    public function authenticate($username, $password) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE username = :username";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['loggedin'] = true;
                    $_SESSION['login_time'] = time();
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            error_log("Ошибка аутентификации: " . $e->getMessage());
            return false;
        }
    }
    
    public function register($username, $password, $role = 'user') {
        try {
            // Проверяем, существует ли пользователь
            $checkQuery = "SELECT id FROM " . $this->table . " WHERE username = :username";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(":username", $username);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                return ['success' => false, 'error' => 'Пользователь уже существует'];
            }
            
            // Хэшируем пароль
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            // Создаем пользователя
            $query = "INSERT INTO " . $this->table . " (username, password, role) VALUES (:username, :password, :role)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":password", $passwordHash);
            $stmt->bindParam(":role", $role);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'user_id' => $this->db->lastInsertId(),
                    'message' => 'Пользователь успешно создан'
                ];
            }
            
            return ['success' => false, 'error' => 'Ошибка создания пользователя'];
        } catch (\Exception $e) {
            error_log("Ошибка регистрации: " . $e->getMessage());
            return ['success' => false, 'error' => 'Системная ошибка'];
        }
    }
    
    public function isLoggedIn() {
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            return false;
        }
        
        // Проверка времени сессии (30 минут)
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > 1800)) {
            $this->logout();
            return false;
        }
        
        // Обновляем время сессии
        $_SESSION['login_time'] = time();
        return true;
    }
    
    public function isAdmin() {
        return $this->isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    public function getUserRole() {
        return $this->isLoggedIn() ? $_SESSION['role'] : 'guest';
    }
    
    public function logout() {
        session_unset();
        session_destroy();
        session_start(); // Начинаем новую сессию
    }
    
    public function getAllUsers() {
        try {
            $query = "SELECT id, username, role, created_at FROM " . $this->table . " ORDER BY created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Ошибка получения пользователей: " . $e->getMessage());
            return [];
        }
    }
    
    public function getUserById($id) {
        try {
            $query = "SELECT id, username, role, created_at FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Ошибка получения пользователя: " . $e->getMessage());
            return null;
        }
    }
    
    public function updateUser($id, $data) {
        try {
            $updates = [];
            $params = [':id' => $id];
            
            if (isset($data['username'])) {
                $updates[] = "username = :username";
                $params[':username'] = $data['username'];
            }
            
            if (isset($data['role'])) {
                $updates[] = "role = :role";
                $params[':role'] = $data['role'];
            }
            
            if (isset($data['password']) && !empty($data['password'])) {
                $updates[] = "password = :password";
                $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if (empty($updates)) {
                return false;
            }
            
            $query = "UPDATE " . $this->table . " SET " . implode(', ', $updates) . " WHERE id = :id";
            $stmt = $this->db->prepare($query);
            return $stmt->execute($params);
        } catch (\Exception $e) {
            error_log("Ошибка обновления пользователя: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteUser($id) {
        try {
            // Нельзя удалить самого себя
            if ($this->isLoggedIn() && $_SESSION['user_id'] == $id) {
                return false;
            }
            
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Ошибка удаления пользователя: " . $e->getMessage());
            return false;
        }
    }
}
?>