<?php
// views/auth/register.php
$csrf_token = $_SESSION['csrf_token'] ?? '';
?>

<div class="auth-container">
    <h2>Регистрация нового пользователя</h2>
    
    <?php if (isset($error) && $error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if (isset($success) && $success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <form method="POST" action="/auth/register">
        <div class="form-group">
            <label for="username">Имя пользователя:</label>
            <input type="text" id="username" name="username" required 
                   minlength="3" maxlength="50"
                   value="<?= htmlspecialchars($username ?? '') ?>">
            <small>От 3 до 50 символов</small>
        </div>
        
        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required
                   minlength="6">
            <small>Не менее 6 символов</small>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Подтвердите пароль:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <div class="form-group">
            <label for="role">Роль:</label>
            <select id="role" name="role">
                <option value="user">Пользователь</option>
                <option value="admin">Администратор</option>
            </select>
        </div>
        <?php else: ?>
            <input type="hidden" name="role" value="user">
        <?php endif; ?>
        
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        
        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
        
        <div style="margin-top: 15px;">
            <a href="/login">Уже есть аккаунт? Войти</a>
        </div>
    </form>
</div>

<style>
.auth-container {
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background: #fff;
}
.form-group {
    margin-bottom: 15px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}
.form-group input,
.form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.form-group small {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 12px;
}
.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}
.btn-primary {
    background: #007bff;
    color: white;
}
.btn-primary:hover {
    background: #0056b3;
}
</style>

<script>
// Проверка совпадения паролей
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Пароли не совпадают!');
        return false;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('Пароль должен быть не менее 6 символов!');
        return false;
    }
    
    return true;
});
</script>