<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Устанавливаем заголовок для JSON-ответа
header('Content-Type: application/json');

// Подключаем БД и функции логирования
require 'db.php';
require 'auth_functions.php';

// Диагностика подключения к БД
if (!$pdo) {
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка подключения к базе данных'
    ]);
    exit;
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

// Проверяем метод запроса
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Получаем JSON-данные из запроса
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    // Проверяем наличие обязательных полей
    if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Имя, email и пароль обязательны'
    ]);
    exit;
}
    
    $name = trim($data['name']);
    $email = trim($data['email']);
    $password = trim($data['password']);
    $deviceType = get_device_type();
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Валидация email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Диагностика перед логированием
        $diagnostic_data = [
            'file' => __FILE__,
            'line' => __LINE__,
            'user_id' => null,
            'success' => false,
            'failure_reason' => 'invalid_email',
            'device_type' => $deviceType,
            'ip' => $ip,
            'user_agent' => $userAgent
        ];
        
        file_put_contents('register_diagnostic.log', 
            "[" . date('Y-m-d H:i:s') . "] Параметры для логирования:\n" . 
            print_r($diagnostic_data, true) . "\n\n",
            FILE_APPEND
        );
        
        // Логируем ошибку валидации
        log_auth_action($pdo, null, false, 'invalid_email', $deviceType);
        
        echo json_encode([
            'success' => false,
            'message' => 'Некорректный формат email'
        ]);
        exit;
    }
    
    try {
        // Проверяем, не зарегистрирован ли уже email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            // Диагностика перед логированием
            $diagnostic_data = [
                'file' => __FILE__,
                'line' => __LINE__,
                'user_id' => null,
                'success' => false,
                'failure_reason' => 'email_exists',
                'device_type' => $deviceType,
                'ip' => $ip,
                'user_agent' => $userAgent
            ];
            
            file_put_contents('register_diagnostic.log', 
                "[" . date('Y-m-d H:i:s') . "] Параметры для логирования:\n" . 
                print_r($diagnostic_data, true) . "\n\n",
                FILE_APPEND
            );
            
            // Логируем попытку регистрации существующего email
            log_auth_action($pdo, null, false, 'email_exists', $deviceType);
            
            echo json_encode([
                'success' => false,
                'message' => 'Этот email уже зарегистрирован'
            ]);
            exit;
        }
        
        // Хэшируем пароль
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Добавляем нового пользователя
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $password_hash]);
        if (strlen($password) < 6) {
    echo json_encode([
        'success' => false,
        'message' => 'Пароль должен быть не менее 6 символов'
    ]);
    exit;
}
        
        // Получаем ID нового пользователя
        $new_user_id = $pdo->lastInsertId();
        
        // Диагностика перед логированием
        $diagnostic_data = [
            'file' => __FILE__,
            'line' => __LINE__,
            'user_id' => $new_user_id,
            'success' => true,
            'failure_reason' => null,
            'device_type' => $deviceType,
            'ip' => $ip,
            'user_agent' => $userAgent
        ];
        
        file_put_contents('register_diagnostic.log', 
            "[" . date('Y-m-d H:i:s') . "] Параметры для логирования:\n" . 
            print_r($diagnostic_data, true) . "\n\n",
            FILE_APPEND
        );
        
        // Логируем успешную регистрацию
        $logResult = log_auth_action($pdo, $new_user_id, true, null, $deviceType);
        
        if (!$logResult) {
            error_log("Ошибка логирования регистрации для user_id: $new_user_id");
        }
        
        // Успешный ответ
        echo json_encode([
            'success' => true,
            'message' => 'Регистрация успешна!'
        ]);
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, created_at) VALUES (?, ?, NOW())");
$stmt->execute([$email, $password_hash]);

// Инициализация статистики
$new_user_id = $pdo->lastInsertId();
$initStmt = $pdo->prepare("
    UPDATE users 
    SET 
        total_score = 0,
        games_played = 0,
        games_won = 0
    WHERE id = ?
");
$initStmt->execute([$new_user_id]);
        
    } catch (PDOException $e) {
        // Диагностика перед логированием
        $diagnostic_data = [
            'file' => __FILE__,
            'line' => __LINE__,
            'user_id' => null,
            'success' => false,
            'failure_reason' => 'database_error',
            'device_type' => $deviceType,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'exception' => $e->getMessage()
        ];
        
        file_put_contents('register_diagnostic.log', 
            "[" . date('Y-m-d H:i:s') . "] Параметры для логирования:\n" . 
            print_r($diagnostic_data, true) . "\n\n",
            FILE_APPEND
        );
        
        // Логируем ошибку базы данных
        log_auth_action($pdo, null, false, 'database_error', $deviceType);
        
        // Ловим ошибки БД
        echo json_encode([
            'success' => false,
            'message' => 'Ошибка базы данных: ' . $e->getMessage()
        ]);
    }
} else {
    // Ответ для неправильного метода
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Метод не поддерживается'
    ]);
}
?>