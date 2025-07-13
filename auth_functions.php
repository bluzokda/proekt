<?php
function log_auth_action($pdo, $userId, $success, $errorCode, $deviceType, $ip, $userAgent, $actionType) {
    try {
        // Вставляем в общую таблицу аутентификации
        $stmt = $pdo->prepare("
            INSERT INTO auth_logs 
            (user_id, action_type, ip_address, user_agent, success, error_code) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $actionType,
            $ip,
            $userAgent,
            (int)$success,
            $errorCode
        ]);

        // Также записываем в специализированную таблицу при необходимости
        if ($actionType === 'login') {
            $loginStmt = $pdo->prepare("
                INSERT INTO login_logs 
                (user_id, ip_address, user_agent, device_type, success, error_code) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $loginStmt->execute([
                $userId,
                $ip,
                $userAgent,
                $deviceType,
                (int)$success,
                $errorCode
            ]);
        }

        return true;
    } catch (PDOException $e) {
        // Резервное логирование в файл если ошибка БД
        $logMessage = date('[Y-m-d H:i:s]') . " Auth log failed: " 
                    . "User: " . ($userId ?? 'null') . ", "
                    . "Success: " . ($success ? 'true' : 'false') . ", "
                    . "Error: " . ($errorCode ?? 'null') . ", "
                    . "Device: $deviceType, IP: $ip\n";
        
        file_put_contents('auth_errors.log', $logMessage, FILE_APPEND);
        throw new Exception("Database logging failed: " . $e->getMessage());
    }
}
?>