<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php'; // Подключение к БД через $pdo

// Проверка подключения к БД
if (!$pdo) {
    die("Ошибка подключения к базе данных");
}

// Функция для определения типа устройства
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

// Функция логирования в таблицу password_reset_logs
function log_auth_action($pdo, $user_id, $success, $failure_reason = null, $device_type = 'desktop') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    try {
        $stmt = $pdo->prepare("
            INSERT INTO password_reset_logs (
                user_id, success, failure_reason,
                ip_address, user_agent, device_type
            ) VALUES (?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $user_id,
            $success ? 1 : 0,
            $failure_reason,
            $ip,
            $user_agent,
            $device_type
        ]);
    } catch (PDOException $e) {
        error_log("Ошибка записи в лог: " . $e->getMessage());
        return false;
    }
}

$deviceType = get_device_type();
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$token = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    try {
        // Проверяем токен
        $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {
            if ($newPassword === $confirmPassword) {
                // Обновляем пароль
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare("
                    UPDATE users 
                    SET password_hash = ?, reset_token = NULL, reset_expires = NULL 
                    WHERE id = ?
                ");
                $updateStmt->execute([$hashedPassword, $user['id']]); // ✅ Исправлено: правильно закрыта скобка

                // Логируем успешный сброс пароля
                $logResult = log_auth_action($pdo, $user['id'], true, null, $deviceType);
                if (!$logResult) {
                    error_log("Ошибка логирования сброса пароля для user_id: " . $user['id']);
                }

                echo "Пароль успешно изменён! <a href='login.php'>Войти</a>";
            } else {
                // Логируем несовпадение паролей
                $logResult = log_auth_action($pdo, $user['id'], false, 'passwords_mismatch', $deviceType);
                if (!$logResult) {
                    error_log("Ошибка логирования несовпадения паролей для user_id: " . $user['id']);
                }

                echo "Пароли не совпадают";
            }
        } else {
            // Логируем неверный/просроченный токен
            $logResult = log_auth_action($pdo, null, false, 'invalid_token', $deviceType);
            if (!$logResult) {
                error_log("Ошибка логирования недействительного токена: $token");
            }

            echo "Неверная или просроченная ссылка";
        }
    } catch (PDOException $e) {
        // Логируем ошибку БД
        $logResult = log_auth_action($pdo, null, false, 'database_error', $deviceType); // ❗ Не знаем user_id — передаем null
        if (!$logResult) {
            error_log("Ошибка логирования исключения: " . $e->getMessage());
        }

        echo "Произошла ошибка при обработке запроса.";
    }
} else {
    // Форма сброса пароля
    ?>
    <h2>Сброс пароля</h2>
    <form method="POST">
        <label for="password">Новый пароль:</label><br>
        <input type="password" name="password" required><br><br>

        <label for="confirm_password">Подтвердите пароль:</label><br>
        <input type="password" name="confirm_password" required><br><br>

        <button type="submit">Сменить пароль</button>
    </form>
    <?php
}
?>