<?php
session_start();
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

// Функция для проверки подозрительной активности
function checkSuspiciousActivity($pdo, $ip, $email = null) {
    $alerts = [];
    $threshold = 5; // Максимальное количество попыток
    
    // 1. Проверка множественных попыток входа с одного IP
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS attempts 
        FROM login_logs 
        WHERE ip = ? 
        AND attempt_time > NOW() - INTERVAL 10 MINUTE
    ");
    $stmt->execute([$ip]);
    $result = $stmt->fetch();
    
    if ($result['attempts'] >= $threshold) {
        $alerts[] = "Обнаружено $result[attempts] попыток входа с вашего IP за 10 минут";
    }
    
    // 2. Проверка брутфорса по конкретному email
    if ($email) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS attempts 
            FROM login_logs 
            JOIN users ON users.id = login_logs.user_id 
            WHERE users.email = ? 
            AND attempt_time > NOW() - INTERVAL 5 MINUTE
            AND success = 0
        ");
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        
        if ($result['attempts'] >= 3) {
            $alerts[] = "Обнаружено $result[attempts] неудачных попыток входа для этого email";
        }
    }
    
    // 3. Проверка подозрительных IP (можно расширить списком)
    $suspiciousIPs = ['192.168.1.100', '10.0.0.1']; // Примеры
    if (in_array($ip, $suspiciousIPs)) {
        $alerts[] = "Ваш IP адрес помечен как подозрительный";
    }
    
    // 4. Отправка уведомления администратору при критической активности
    if (count($alerts) > 0 && $result['attempts'] >= 10) {
        $message = "Критическая активность!\nIP: $ip\n";
        if ($email) $message .= "Email: $email\n";
        $message .= "События:\n- " . implode("\n- ", $alerts);
        
        // В реальном приложении нужно раскомментировать
        // mail('admin@example.com', 'Подозрительная активность', $message);
    }
    
    return $alerts;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $suspiciousAlerts = [];
    
    // Определение типа устройства
    $deviceType = 'other';
    if (preg_match('/Mobile|Android|iPhone|iPad|iPod|Windows Phone/', $userAgent)) {
        $deviceType = 'mobile';
    } elseif (preg_match('/Tablet|iPad|Kindle|Nexus 7|Xoom/', $userAgent)) {
        $deviceType = 'tablet';
    } elseif (preg_match('/Windows NT|Macintosh|Linux/', $userAgent)) {
        $deviceType = 'desktop';
    }

    try {
        // Проверка подозрительной активности перед аутентификацией
        $suspiciousAlerts = checkSuspiciousActivity($pdo, $ip, $email);
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $userId = $user['id'];
            
            if (password_verify($password, $user['password_hash'])) {
                // Успешная авторизация
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_email'] = $email;
                
                // Логируем успешную авторизацию
                $logStmt = $pdo->prepare("
                    INSERT INTO login_logs 
                    (user_id, ip, success, user_agent, device_type) 
                    VALUES (?, ?, 1, ?, ?)
                ");
                $logStmt->execute([$userId, $ip, $userAgent, $deviceType]);
                
                header("Location: profile.php");
                exit();
            } else {
                // Логируем неудачную попытку (неверный пароль)
                $logStmt = $pdo->prepare("
                    INSERT INTO login_logs 
                    (user_id, ip, success, failure_reason, user_agent, device_type) 
                    VALUES (?, ?, 0, 'wrong_password', ?, ?)
                ");
                $logStmt->execute([$userId, $ip, $userAgent, $deviceType]);
                
                $error = "Неверный пароль!";
                
                // Обновляем проверку активности после неудачной попытки
                $suspiciousAlerts = array_merge(
                    $suspiciousAlerts, 
                    checkSuspiciousActivity($pdo, $ip, $email)
                );
            }
        } else {
            // Логируем попытку входа с несуществующим email
            $logStmt = $pdo->prepare("
                INSERT INTO login_logs 
                (user_id, ip, success, failure_reason, user_agent, device_type) 
                VALUES (NULL, ?, 0, 'unknown_email', ?, ?)
            ");
            $logStmt->execute([$ip, $userAgent, $deviceType]);
            
            $error = "Пользователь не найден!";
            
            // Обновляем проверку активности после неудачной попытки
            $suspiciousAlerts = array_merge(
                $suspiciousAlerts, 
                checkSuspiciousActivity($pdo, $ip, $email)
            );
        }
    } catch (PDOException $e) {
        // Логируем ошибку базы данных
        $logStmt = $pdo->prepare("
            INSERT INTO login_logs 
            (user_id, ip, success, failure_reason, user_agent, device_type) 
            VALUES (NULL, ?, 0, 'other', ?, ?)
        ");
        $logStmt->execute([$ip, $userAgent, $deviceType]);
        
        $error = "Ошибка системы. Попробуйте позже.";
        $suspiciousAlerts = array_merge(
            $suspiciousAlerts, 
            checkSuspiciousActivity($pdo, $ip, $email)
        );
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Вход</title>
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-warning {
            background-color: #fcf8e3;
            border-color: #faebcc;
            color: #8a6d3b;
        }
        .alert-danger {
            background-color: #f2dede;
            border-color: #ebccd1;
            color: #a94442;
        }
    </style>
</head>
<body>
    <?php if (!empty($suspiciousAlerts)): ?>
        <div class="alert alert-danger">
            <h3>Обнаружена подозрительная активность!</h3>
            <ul>
                <?php foreach ($suspiciousAlerts as $alert): ?>
                    <li><?= htmlspecialchars($alert) ?></li>
                <?php endforeach; ?>
            </ul>
            <p>Если это были не вы, пожалуйста, <a href="contact.php">сообщите нам</a>.</p>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div style="color: red;"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <h2>Вход в систему</h2>
        
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        
        <div>
            <label>Пароль:</label>
            <input type="password" name="password" required>
        </div>
        
        <div>
            <button type="submit">Войти</button>
            <a href="forgot.php">Забыли пароль?</a>
        </div>
        
        <?php if (!empty($suspiciousAlerts)): ?>
            <div style="margin-top: 20px; padding: 10px; background: #fff8e1; border-left: 4px solid #ffc107;">
                <small>
                    Для вашей безопасности, при подозрительной активности мы можем:
                    <ul>
                        <li>Временно заблокировать вход</li>
                        <li>Требовать дополнительную аутентификацию</li>
                        <li>Уведомить вас по email</li>
                    </ul>
                </small>
            </div>
        <?php endif; ?>
    </form>
    
    <script>
        // Добавляем защиту от автоматических ботов
        setTimeout(function() {
            document.querySelector('button[type="submit"]').disabled = false;
        }, 2000);
        
        // Предупреждение при быстром повторном вводе
        let lastSubmit = 0;
        document.querySelector('form').addEventListener('submit', function(e) {
            const now = Date.now();
            if (lastSubmit && (now - lastSubmit) < 2000) {
                e.preventDefault();
                alert('Слишком частые попытки входа. Подождите 2 секунды.');
            }
            lastSubmit = now;
        });
    </script>
</body>
</html>