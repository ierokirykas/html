<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MVC Application</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        nav { background: #333; padding: 10px; margin-bottom: 20px; }
        nav a { color: white; margin-right: 15px; text-decoration: none; }
        nav a:hover { text-decoration: underline; }
        .container { max-width: 1200px; margin: 0 auto; }
        .alert { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <nav>
        <a href="/">Главная</a>
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
            <a href="/dashboard">Дашборд</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="/admin">Админка</a>
            <?php endif; ?>
            <a href="/auth/logout">Выйти (<?= $_SESSION['username'] ?>)</a>
        <?php else: ?>
            <a href="/login">Войти</a>
        <?php endif; ?>
        <a href="/api/data" target="_blank">API</a>
    </nav>
    
    <div class="container">
        <?php if (isset($success) && $success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if (isset($error) && $error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <?= $content ?? '' ?>
    </div>
    
    <script>
        // JavaScript для работы с API
        async function deleteData(id) {
            if (!confirm('Удалить запись?')) return;
            
            try {
                const response = await fetch(`/api/data/${id}`, {
                    method: 'DELETE'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Запись удалена');
                    location.reload();
                } else {
                    alert('Ошибка: ' + result.error);
                }
            } catch (error) {
                alert('Ошибка сети');
            }
        }
        
        async function updateData(id) {
            const newData = prompt('Введите новые данные:');
            if (!newData) return;
            
            try {
                const response = await fetch(`/api/data/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ data_string: newData })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Запись обновлена');
                    location.reload();
                } else {
                    alert('Ошибка: ' + result.error);
                }
            } catch (error) {
                alert('Ошибка сети');
            }
        }
    </script>
</body>
</html>