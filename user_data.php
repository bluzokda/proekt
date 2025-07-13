<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

// Получаем данные пользователя
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Получаем последний вход
$stmt = $pdo->prepare("SELECT ip, login_time FROM login_logs WHERE user_id = ? ORDER BY login_time DESC LIMIT 1");
$stmt->execute([$userId]);
$lastLogin = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'user' => [
        'id' => $user['id'],
        'username' => $user['username'] ?? '—', // Имя пользователя
        'email' => $user['email'],
        'created_at' => $user['created_at'],
    ],
    'last_login' => $lastLogin,
]);
?>