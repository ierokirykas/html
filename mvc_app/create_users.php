<?php
// create_users.php
session_start();

require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Создаем таблицу если ее нет
    $create_table = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $db->exec($create_table);
    echo "Таблица 'users' создана или уже существует.\n\n";

    // Генерируем безопасные хэши
    $admin_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $user_hash = password_hash('user123', PASSWORD_DEFAULT);
    
    echo "Сгенерированные хэши:\n";
    echo "admin123: " . $admin_hash . "\n";
    echo "user123: " . $user_hash . "\n\n";

    // Вставляем в базу с проверкой на дубликаты
    $users = [
        ['username' => 'admin', 'password' => $admin_hash, 'role' => 'admin'],
        ['username' => 'user', 'password' => $user_hash, 'role' => 'user']
    ];

    foreach ($users as $user) {
        // Проверяем, существует ли пользователь
        $check_sql = "SELECT id FROM users WHERE username = :username";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bindParam(':username', $user['username']);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() == 0) {
            // Добавляем нового пользователя
            $sql = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':username', $user['username']);
            $stmt->bindParam(':password', $user['password']);
            $stmt->bindParam(':role', $user['role']);
            $stmt->execute();
            
            echo "✅ Создан пользователь: " . $user['username'] . " (роль: " . $user['role'] . ")\n";
        } else {
            // Обновляем существующего
            $sql = "UPDATE users SET password = :password, role = :role WHERE username = :username";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':username', $user['username']);
            $stmt->bindParam(':password', $user['password']);
            $stmt->bindParam(':role', $user['role']);
            $stmt->execute();
            
            echo "↻ Обновлен пользователь: " . $user['username'] . " (роль: " . $user['role'] . ")\n";
        }
    }

    echo "\n✅ Пользователи успешно созданы!\n\n";
    echo "Данные для входа:\n";
    echo "Администратор: admin / admin123\n";
    echo "Пользователь: user / user123\n";
    
    // Показываем всех пользователей
    echo "\nСписок всех пользователей в базе:\n";
    $select_sql = "SELECT id, username, role, created_at FROM users ORDER BY id";
    $select_stmt = $db->query($select_sql);
    
    echo "+----+----------+--------+---------------------+\n";
    echo "| ID | Username | Role   | Created At          |\n";
    echo "+----+----------+--------+---------------------+\n";
    
    while ($row = $select_stmt->fetch(PDO::FETCH_ASSOC)) {
        printf("| %2d | %-8s | %-6s | %-19s |\n", 
            $row['id'], 
            $row['username'], 
            $row['role'], 
            $row['created_at']
        );
    }
    echo "+----+----------+--------+---------------------+\n";

} catch (PDOException $e) {
    die("❌ Ошибка подключения к базе данных: " . $e->getMessage());
}
?>