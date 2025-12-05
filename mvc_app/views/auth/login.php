<h2>Вход в систему</h2>

<form method="POST" action="/auth/login">
    <div>
        <label>Имя пользователя:</label>
        <input type="text" name="username" required>
    </div>
    
    <div>
        <label>Пароль:</label>
        <input type="password" name="password" required>
    </div>
    
    <button type="submit">Войти</button>
</form>

<div style="margin-top: 20px; background: #f5f5f5; padding: 10px;">
    <strong>Тестовые данные:</strong><br>
    • Админ: admin / admin123<br>
    • Пользователь: user / user123
</div>