<?php
// views/pages/admin.php
$csrf_token = $_SESSION['csrf_token'] ?? '';
?>

<h2>Административная панель</h2>
<p>Добро пожаловать, администратор <strong><?= htmlspecialchars($user) ?></strong>!</p>

<div class="admin-sections">
    
    <!-- Секция 1: Системная информация -->
    <section class="admin-section">
        <h3>Системная информация</h3>
        <div class="system-info">
            <div class="info-item">
                <strong>Версия PHP:</strong> <?= htmlspecialchars($systemInfo['php_version']) ?>
            </div>
            <div class="info-item">
                <strong>Версия MySQL:</strong> <?= htmlspecialchars($systemInfo['mysql_version']) ?>
            </div>
            <div class="info-item">
                <strong>Сервер:</strong> <?= htmlspecialchars($systemInfo['server_software']) ?>
            </div>
            <div class="info-item">
                <strong>Всего пользователей:</strong> <?= $totalUsers ?>
            </div>
        </div>
    </section>
    
    <!-- Секция 2: Управление пользователями -->
    <section class="admin-section">
        <h3>Управление пользователями</h3>
        
        <!-- Форма создания нового пользователя -->
        <div class="card">
            <h4>Создать нового пользователя</h4>
            <form method="POST" action="/api/users" id="createUserForm">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Имя пользователя" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Пароль" required>
                </div>
                <div class="form-group">
                    <select name="role">
                        <option value="user">Пользователь</option>
                        <option value="admin">Администратор</option>
                    </select>
                </div>
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <button type="submit" class="btn btn-success">Создать</button>
            </form>
        </div>
        
        <!-- Таблица пользователей -->
        <div class="card">
            <h4>Список пользователей</h4>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя пользователя</th>
                        <th>Роль</th>
                        <th>Дата регистрации</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td>
                            <span class="role-badge role-<?= $user['role'] ?>">
                                <?= $user['role'] ?>
                            </span>
                        </td>
                        <td><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></td>
                        <td class="actions">
                            <button onclick="editUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>', '<?= $user['role'] ?>')"
                                    class="btn btn-sm btn-warning">Изменить</button>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <button onclick="deleteUser(<?= $user['id'] ?>)"
                                    class="btn btn-sm btn-danger">Удалить</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    
    <!-- Секция 3: Быстрые действия -->
    <section class="admin-section">
        <h3>Быстрые действия</h3>
        <div class="quick-actions">
            <button onclick="clearOldData()" class="btn btn-info">Очистить старые данные</button>
            <button onclick="exportData()" class="btn btn-info">Экспорт данных</button>
            <button onclick="showSystemLogs()" class="btn btn-info">Показать логи</button>
            <a href="/api/data" target="_blank" class="btn btn-info">API Endpoints</a>
        </div>
    </section>
</div>

<!-- Модальное окно для редактирования пользователя -->
<div id="editUserModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Редактирование пользователя</h3>
        <form id="editUserForm">
            <input type="hidden" id="editUserId" name="id">
            <div class="form-group">
                <label>Имя пользователя:</label>
                <input type="text" id="editUsername" name="username" required>
            </div>
            <div class="form-group">
                <label>Новый пароль (оставьте пустым, чтобы не менять):</label>
                <input type="password" id="editPassword" name="password">
            </div>
            <div class="form-group">
                <label>Роль:</label>
                <select id="editRole" name="role">
                    <option value="user">Пользователь</option>
                    <option value="admin">Администратор</option>
                </select>
            </div>
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
</div>

<style>
.admin-sections {
    display: grid;
    gap: 20px;
}
.admin-section {
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 20px;
    background: #f9f9f9;
}
.card {
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 15px;
    margin: 15px 0;
    background: white;
}
.users-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
.users-table th, .users-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}
.users-table th {
    background-color: #f2f2f2;
}
.role-badge {
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 12px;
    color: white;
}
.role-admin { background: #dc3545; }
.role-user { background: #28a745; }
.actions { white-space: nowrap; }
.btn {
    padding: 5px 10px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    margin: 2px;
}
.btn-sm { font-size: 12px; padding: 3px 8px; }
.btn-success { background: #28a745; color: white; }
.btn-warning { background: #ffc107; color: black; }
.btn-danger { background: #dc3545; color: white; }
.btn-info { background: #17a2b8; color: white; }
.quick-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}
.modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 20px;
    border-radius: 5px;
    width: 400px;
    max-width: 90%;
}
.close {
    float: right;
    font-size: 28px;
    cursor: pointer;
}
</style>

<script>
// Функции для работы с пользователями
function editUser(id, username, role) {
    document.getElementById('editUserId').value = id;
    document.getElementById('editUsername').value = username;
    document.getElementById('editRole').value = role;
    document.getElementById('editUserModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('editUserModal').style.display = 'none';
}

// Обработка формы редактирования пользователя
document.getElementById('editUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    try {
        const response = await fetch(`/api/users/${data.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Пользователь обновлен!');
            location.reload();
        } else {
            alert('Ошибка: ' + result.error);
        }
    } catch (error) {
        alert('Ошибка сети');
    }
});

// Удаление пользователя
async function deleteUser(id) {
    if (!confirm('Вы уверены, что хотите удалить этого пользователя?')) return;
    
    try {
        const response = await fetch(`/api/users/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                csrf_token: '<?= $csrf_token ?>'
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Пользователь удален!');
            location.reload();
        } else {
            alert('Ошибка: ' + result.error);
        }
    } catch (error) {
        alert('Ошибка сети');
    }
}

// Создание пользователя
document.getElementById('createUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    try {
        const response = await fetch('/api/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Пользователь создан!');
            location.reload();
        } else {
            alert('Ошибка: ' + result.error);
        }
    } catch (error) {
        alert('Ошибка сети');
    }
});

// Быстрые действия
async function clearOldData() {
    if (!confirm('Очистить данные старше 30 дней?')) return;
    
    try {
        const response = await fetch('/api/data/cleanup', {
            method: 'POST'
        });
        
        const result = await response.json();
        alert(result.message || 'Операция выполнена');
    } catch (error) {
        alert('Ошибка сети');
    }
}

function exportData() {
    window.open('/api/data/export', '_blank');
}

function showSystemLogs() {
    window.open('/api/logs', '_blank');
}

// Закрытие модального окна при клике вне его
window.onclick = function(event) {
    const modal = document.getElementById('editUserModal');
    if (event.target === modal) {
        closeModal();
    }
}
</script>