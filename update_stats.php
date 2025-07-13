<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'error' => 'Пользователь не авторизован']);
    exit;
}

// Подключение к БД
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

$data = json_decode(file_get_contents('php://input'), true);

// Добавлено обязательное поле task_details
if (!isset($data['task_type'], $data['block'], $data['is_correct'], $data['task_details'])) {
    echo json_encode(['success' => false, 'error' => 'Не все обязательные поля заполнены']);
    exit;
}

$taskType = htmlspecialchars(trim($data['task_type']));
$block = htmlspecialchars(trim($data['block']));
$isCorrect = intval($data['is_correct']);
$taskDetails = htmlspecialchars(trim($data['task_details'])); // Новое поле
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];

// Проверка task_type
if (!in_array($taskType, ['basic', 'advanced'])) {
    echo json_encode(['success' => false, 'error' => 'Недопустимый тип задачи']);
    exit;
}

// Начало транзакции
$pdo->beginTransaction();

try {
    // 1. Запись в user_actions
    $stmtAction = $pdo->prepare("
        INSERT INTO user_actions 
        (user_id, task_type, block, task_details, is_correct) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmtAction->execute([$user_id, $taskType, $block, $taskDetails, $isCorrect]);
    
    // 2. Обновление основной статистики в users
    $updateUser = $pdo->prepare("
        UPDATE users 
        SET 
            games_played = games_played + 1,
            games_won = games_won + ?,
            total_score = total_score + ?
        WHERE id = ?
    ");
    // Даем 10 очков за правильный ответ
    $updateUser->execute([$isCorrect, $isCorrect * 10, $user_id]);
    
    // 3. Обновление user_statistics
    $stmt = $pdo->prepare("
        SELECT * FROM user_statistics 
        WHERE user_id = ? AND block = ? AND task_type = ?
    ");
    $stmt->execute([$user_id, $block, $taskType]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $total_tasks = $row['total_tasks'] + 1;
        $correct = $row['correct_answers'] + ($isCorrect ? 1 : 0);
        $incorrect = $row['incorrect_answers'] + ($isCorrect ? 0 : 1);

        $updateStmt = $pdo->prepare("
            UPDATE user_statistics 
            SET total_tasks = ?, correct_answers = ?, incorrect_answers = ? 
            WHERE id = ?
        ");
        $updateStmt->execute([$total_tasks, $correct, $incorrect, $row['id']]);
    } else {
        $insertStmt = $pdo->prepare("
            INSERT INTO user_statistics 
            (user_id, email, task_type, block, total_tasks, correct_answers, incorrect_answers)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $insertStmt->execute([
            $user_id,
            $email,
            $taskType,
            $block,
            1,
            $isCorrect ? 1 : 0,
            $isCorrect ? 0 : 1
        ]);
    }
    
    // Фиксация транзакции
    $pdo->commit();
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    // Откат транзакции при ошибке
    $pdo->rollBack();
    file_put_contents('errors.log', date('[Y-m-d H:i:s] ') . 'Update Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    echo json_encode(['success' => false, 'error' => 'Ошибка при обновлении статистики: ' . $e->getMessage()]);
}

exit;