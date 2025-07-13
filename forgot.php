<?php
session_start();

// Отображение ошибок для диагностики
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php'; // Подключение к БД через $pdo

// Проверка подключения
if (!$pdo) {
    die("Ошибка подключения к базе данных");
}

// Определение типа устройства
function get_device_type() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (preg_match('/Mobile|Android|iPhone|iPad|iPod|Windows Phone/i', $userAgent)) {
        return 'mobile';
    } elseif (preg_match('/Tablet|iPad|Kindle|Nexus 7|Xoom/i', $userAgent)) {
        return 'tablet';
    } elseif (preg_match('/Windows NT|Macintosh|Linux/i', $userAgent)) {
        return 'desktop';
    }
    return 'other';
}

$deviceType = get_device_type();
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    try {
        // Ищем пользователя по email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $userId = $user['id'];
            
            // Генерируем токен и срок действия
            $token = bin2hex(random_bytes(50));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Обновляем данные пользователя
            $updateStmt = $pdo->prepare("
                UPDATE users 
                SET reset_token = ?, reset_expires = ? 
                WHERE id = ?
            ");
            $updateStmt->execute([$token, $expires, $userId]);

            // Логируем успешный запрос восстановления
            $logResult = log_auth_action(
                $pdo,
                $userId,
                true,
                null,
                $deviceType,
                $ip,
                $userAgent,
                'password_reset'
            );
            if (!$logResult) {
                error_log("Ошибка логирования успешного запроса для user_id: $userId");
            }

            // Формируем ссылку для сброса пароля
            $reset_link = "https://ваш-сайт/reset.php?token=$token";
            echo "Ссылка для сброса: <a href='$reset_link'>$reset_link</a>";
        } else {
            // Логируем попытку восстановления для несуществующего email
            $logResult = log_auth_action(
                $pdo,
                null,
                false,
                'unknown_email',
                $deviceType,
                $ip,
                $userAgent,
                'password_reset'
            );
            if (!$logResult) {
                error_log("Ошибка логирования для неизвестного email: $email");
            }

            echo "Если email зарегистрирован, на него отправлена инструкция";
        }
    } catch (Exception $e) {
        // Логируем ошибку
        $logResult = log_auth_action(
            $pdo,
            null,
            false,
            'exception',
            $deviceType,
            $ip,
            $userAgent,
            'password_reset'
        );
        if (!$logResult) {
            error_log("Ошибка логирования исключения");
        }

        error_log("Ошибка в forgot.php: " . $e->getMessage());
        echo "Произошла ошибка при обработке запроса.";
    }
}
?>

<!-- HTML форма -->
<h2>Восстановление пароля</h2>
<form method="POST">
    <label for="email">Введите ваш email:</label><br>
    <input type="email" name="email" required><br><br>
    <button type="submit">Восстановить пароль</button>
</form>