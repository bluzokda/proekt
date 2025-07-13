<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Подключение к БД
require 'db.php';

// Функция для получения реального IP пользователя
function getRealIpAddr() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        return $_SERVER['REMOTE_ADDR'];
    }
    return 'unknown';
}

// Функция для логирования попыток входа
function log_auth_action($pdo, $userId, $success, $error, $deviceType, $ip, $userAgent, $actionType, $email) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO login_logs 
            (user_id, ip_address, device_type, success, error_code, user_agent, email, ip) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $ip,
            $deviceType,
            $success ? 1 : 0,
            $error,
            $userAgent,
            $email,
            $ip
        ]);
    } catch (PDOException $e) {
        // Логируем ошибку (можно отправить в файл или систему мониторинга)
        error_log("DB Log Error: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $ip = getRealIpAddr();
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    // Определение типа устройства
    $deviceType = 'other';
    if (preg_match('/Mobile|Android|iPhone|iPad|iPod|Windows Phone/i', $userAgent)) {
        $deviceType = 'mobile';
    } elseif (preg_match('/Tablet|iPad|Kindle|Nexus 7|Xoom/i', $userAgent)) {
        $deviceType = 'tablet';
    } elseif (preg_match('/Windows NT|Macintosh|Linux/i', $userAgent)) {
        $deviceType = 'desktop';
    }

    try {
        // Поиск пользователя по email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $userId = $user['id'];

            // Проверка пароля
            if (password_verify($password, $user['password_hash'])) {
                // Успешный вход
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_email'] = $email;

                // Обновляем last_login
                $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$userId]);

                // Логируем успешный вход
                log_auth_action($pdo, $userId, true, null, $deviceType, $ip, $userAgent, 'login', $email);

                // Перенаправление
                header("Location: index.html");
                exit();
            } else {
                // Неверный пароль
                log_auth_action($pdo, $userId, false, 'wrong_password', $deviceType, $ip, $userAgent, 'login', $email);
                $error = "Неверный пароль!";
            }
        } else {
            // Пользователь не найден
            log_auth_action($pdo, null, false, 'unknown_email', $deviceType, $ip, $userAgent, 'login', $email);
            $error = "Пользователь не найден!";
        }

    } catch (PDOException $e) {
        // Логируем ошибку базы данных
        log_auth_action($pdo, null, false, 'database_error', $deviceType, $ip, $userAgent, 'login', $email);
        $error = "Ошибка системы. Попробуйте позже.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 15px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        p.error {
            color: red;
        }
    </style>
</head>
<body>
    <h2>Войти в аккаунт</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="email">Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label for="password">Пароль:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Войти</button>
    </form>

    <p>Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a></p>
</body>
</html>