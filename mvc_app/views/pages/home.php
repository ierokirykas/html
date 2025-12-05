<?php
// views/pages/home.php
?>

<div class="hero-section">
    <h1>Добро пожаловать в MVC Приложение!</h1>
    <p class="lead">Простое и эффективное приложение на PHP с использованием паттерна MVC</p>
    
    <?php if (!$user): ?>
        <div class="auth-buttons">
            <a href="/login" class="btn btn-primary btn-lg">Войти в систему</a>
            <a href="/auth/register" class="btn btn-secondary btn-lg">Зарегистрироваться</a>
        </div>
    <?php else: ?>
        <div class="welcome-message">
            <p>Вы вошли как <strong><?= htmlspecialchars($user) ?></strong> (<?= $role ?>)</p>
            <a href="/dashboard" class="btn btn-success btn-lg">Перейти в панель управления</a>
        </div>
    <?php endif; ?>
</div>

<div class="features">
    <div class="feature-card">
        <h3>📱 MVC Архитектура</h3>
        <p>Чистое разделение на Model, View и Controller для лучшей поддержки кода</p>
    </div>
    
    <div class="feature-card">
        <h3>🔐 Безопасность</h3>
        <p>Хэширование паролей, CSRF защита, сессии, ролевая модель доступа</p>
    </div>
    
    <div class="feature-card">
        <h3>🔄 REST API</h3>
        <p>Полноценное API для интеграции с другими системами</p>
    </div>
    
    <div class="feature-card">
        <h3>📊 Админ-панель</h3>
        <p>Удобное управление пользователями и данными для администраторов</p>
    </div>
</div>

<div class="api-demo">
    <h2>Пример работы с API</h2>
    <div class="api-examples">
        <div class="api-example">
            <h4>Получить все данные:</h4>
            <code>GET /api/data</code>
            <button onclick="testApi('GET', '/api/data')" class="btn btn-sm btn-outline">Тест</button>
        </div>
        
        <div class="api-example">
            <h4>Создать данные:</h4>
            <code>POST /api/data</code>
            <button onclick="testApi('POST', '/api/data', {data_string: 'Тестовые данные'})" 
                    class="btn btn-sm btn-outline">Тест</button>
        </div>
    </div>
    
    <div id="api-result" style="margin-top: 20px; display: none;">
        <h4>Результат:</h4>
        <pre id="api-result-content" style="background: #f5f5f5; padding: 10px; border-radius: 5px;"></pre>
    </div>
</div>

<style>
.hero-section {
    text-align: center;
    padding: 50px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    margin-bottom: 40px;
}
.hero-section h1 {
    font-size: 2.5rem;
    margin-bottom: 20px;
}
.lead {
    font-size: 1.2rem;
    margin-bottom: 30px;
    opacity: 0.9;
}
.auth-buttons, .welcome-message {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}
.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-lg { padding: 15px 30px; font-size: 18px; }
.btn-primary { background: #007bff; color: white; }
.btn-primary:hover { background: #0056b3; }
.btn-secondary { background: #6c757d; color: white; }
.btn-secondary:hover { background: #545b62; }
.btn-success { background: #28a745; color: white; }
.btn-success:hover { background: #1e7e34; }
.btn-outline { background: transparent; border: 1px solid #007bff; color: #007bff; }
.btn-outline:hover { background: #007bff; color: white; }
.btn-sm { padding: 5px 10px; font-size: 14px; }

.features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}
.feature-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}
.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}
.feature-card h3 {
    margin-top: 0;
    color: #333;
}

.api-demo {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    background: #f8f9fa;
}
.api-examples {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}
.api-example {
    background: white;
    padding: 15px;
    border-radius: 5px;
    border-left: 4px solid #007bff;
}
.api-example h4 {
    margin-top: 0;
}
.api-example code {
    display: block;
    background: #f5f5f5;
    padding: 8px;
    border-radius: 3px;
    margin: 10px 0;
    font-family: 'Courier New', monospace;
}
</style>

<script>
async function testApi(method, url, data = null) {
    const apiResult = document.getElementById('api-result');
    const apiContent = document.getElementById('api-result-content');
    
    apiResult.style.display = 'block';
    apiContent.textContent = 'Выполняется запрос...';
    
    try {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            }
        };
        
        if (data) {
            options.body = JSON.stringify(data);
        }
        
        const response = await fetch(url, options);
        const result = await response.json();
        
        apiContent.textContent = JSON.stringify(result, null, 2);
    } catch (error) {
        apiContent.textContent = 'Ошибка: ' + error.message;
    }
}

// Пример динамического обновления без перезагрузки
if (<?= $user ? 'true' : 'false' ?>) {
    // Если пользователь авторизован, можно загрузить данные через API
    fetch('/api/data')
        .then(response => response.json())
        .then(data => {
            // Можно отобразить данные на главной странице
            console.log('Загружено записей:', data.count);
        })
        .catch(error => console.error('Ошибка загрузки данных:', error));
}
</script>