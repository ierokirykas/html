<?php
// index.php
require_once 'config/database.php';
require_once 'models/Model.php';
require_once 'controllers/Controller.php';
require_once 'views/View.php';

// error_reporting(E_WARNING);
// ini_set('display_errors', '1');

// try {

// Инициализация компонентов
$model = new Model();
$controller = new Controller($model);
$view = new View($controller, $model);

// Обработка действий
if (isset($_GET['action']) && !empty($_GET['action'])) {
    $action = $_GET['action'];
    if (method_exists($controller, $action)) {
        $controller->$action();
    }
}
// Обработка POST действий (данные из формы)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    if (method_exists($controller, $action)) {
        $controller->$action();
    }
}
// Вывод результата
echo $view->output();

/* } catch (Throwable $ex) {
    echo $ex->getMessage();
} */
?>
