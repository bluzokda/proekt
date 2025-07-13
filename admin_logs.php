<?php
require 'db.php';
if (!$pdo) {
    die("Ошибка подключения к базе данных");
}

// Для регистрации в режиме JSON
if (!$pdo) {
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка подключения к базе данных'
    ]);
    exit;
}

// Логи авторизации
$loginLogs = $pdo->query("
    SELECT ll.*, u.email
    FROM login_logs ll
    LEFT JOIN users u ON ll.user_id = u.id
    ORDER BY attempt_time DESC
    LIMIT 100
")->fetchAll();

// Логи восстановления пароля
$resetLogs = $pdo->query("
    SELECT prl.*, u.email
    FROM password_reset_logs prl
    LEFT JOIN users u ON prl.user_id = u.id
    ORDER BY request_time DESC
    LIMIT 100
")->fetchAll();
?>

<!-- HTML таблица для login_logs -->
<table>
    <tr>
        <th>Время</th>
        <th>Пользователь</th>
        <th>IP</th>
        <th>Статус</th>
        <th>Устройство</th>
    </tr>
    <?php foreach ($loginLogs as $log): ?>
    <tr>
        <td><?= $log['attempt_time'] ?></td>
        <td><?= $log['email'] ?? 'N/A' ?></td>
        <td><?= $log['ip'] ?></td>
        <td><?= $log['success'] ? '✅ Успех' : '❌ Ошибка: ' . $log['failure_reason'] ?></td>
        <td><?= $log['device_type'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- HTML таблица для password_reset_logs -->
<table>
    <tr>
        <th>Время запроса</th>
        <th>Пользователь</th>
        <th>Статус</th>
        <th>Время сброса</th>
    </tr>
    <?php foreach ($resetLogs as $log): ?>
    <tr>
        <td><?= $log['request_time'] ?></td>
        <td><?= $log['email'] ?? 'N/A' ?></td>
        <td><?= $log['status'] ?></td>
        <td><?= $log['reset_time'] ?? 'N/A' ?></td>
    </tr>
    <?php endforeach; ?>
</table>