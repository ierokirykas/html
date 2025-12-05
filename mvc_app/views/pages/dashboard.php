<h2>Панель управления</h2>
<p>Добро пожаловать, <?= $_SESSION['username'] ?>!</p>
<p>Ваша роль: <strong><?= $_SESSION['role'] ?></strong></p>

<?php if ($_SESSION['role'] === 'admin'): ?>
<h3>Управление данными</h3>

<!-- Форма для создания данных -->
<form id="createForm">
    <input type="text" id="dataInput" placeholder="Введите данные" required>
    <button type="submit">Добавить</button>
</form>

<!-- Список данных с кнопками удаления -->
<div id="dataList">
    <h4>Текущие данные:</h4>
    <?php foreach ($data as $item): ?>
    <div style="border: 1px solid #ccc; padding: 10px; margin: 5px;">
        <?= htmlspecialchars($item['data_string']) ?>
        <button onclick="updateData(<?= $item['id'] ?>)">Изменить</button>
        <button onclick="deleteData(<?= $item['id'] ?>)">Удалить</button>
    </div>
    <?php endforeach; ?>
</div>

<script>
document.getElementById('createForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const input = document.getElementById('dataInput').value;
    
    try {
        const response = await fetch('/api/data', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ data_string: input })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Данные добавлены!');
            location.reload();
        } else {
            alert('Ошибка: ' + result.error);
        }
    } catch (error) {
        alert('Ошибка сети');
    }
});
</script>
<?php endif; ?>