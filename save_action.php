<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000"); // Замени на свой фронтенд
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Пользователь не авторизован']);
    exit;
}

// Настройки подключения к БД
$host = 'localhost';
$db   = 'cl82604_bluz';
$user = 'cl82604_bluz';
$pass = '160e210359_GU';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    file_put_contents('errors.log', date('[Y-m-d H:i:s] ') . 'DB Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    echo json_encode(['success' => false, 'error' => 'Ошибка подключения к БД']);
    exit;
}

// Получаем данные из запроса
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['task_type'], $data['block'], $data['task_details'], $data['is_correct'])) {
    echo json_encode(['success' => false, 'error' => 'Не все обязательные поля заполнены']);
    exit;
}

$task_type = htmlspecialchars(trim($data['task_type']));
$block = htmlspecialchars(trim($data['block']));
$task_details = htmlspecialchars(trim($data['task_details'])) ?: null;
$is_correct = intval($data['is_correct']);
$user_id = $_SESSION['user_id'];

// Проверка is_correct
if (!in_array($is_correct, [0, 1])) {
    echo json_encode(['success' => false, 'error' => 'Недопустимое значение is_correct']);
    exit;
}

// Проверка на пустые обязательные поля
if (empty($task_type) || empty($block)) {
    echo json_encode(['success' => false, 'error' => 'Поля task_type и block обязательны']);
    exit;
}

// Запрос к БД
$stmt = $pdo->prepare("INSERT INTO user_actions (user_id, task_type, block, task_details, is_correct) VALUES (?, ?, ?, ?, ?)");

try {
    $stmt->execute([$user_id, $task_type, $block, $task_details, $is_correct]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    file_put_contents('errors.log', date('[Y-m-d H:i:s] ') . 'Query Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    echo json_encode(['success' => false, 'error' => 'Ошибка сохранения данных']);
}
?>