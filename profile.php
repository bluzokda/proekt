<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// Получаем последний IP из логов входа
$stmt = $pdo->prepare("
    SELECT ip 
    FROM login_logs 
    WHERE user_id = ? 
    ORDER BY login_time DESC 
    LIMIT 1
");
$stmt->execute([$userId]);
$row = $stmt->fetch();

if ($row && $row['ip'] !== $ip) {
    session_destroy();
    header("Location: login.php?reason=ip_changed");
    exit;
}

try {
    // Получаем данные пользователя
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Пользователь не найден.");
    }

    // Получаем последний вход
    $stmt = $pdo->prepare("SELECT * FROM login_logs WHERE user_id = ? ORDER BY login_time DESC LIMIT 1");
    $stmt->execute([$userId]);
    $lastLogin = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Ошибка получения данных пользователя: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Профиль</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        /* Глобальные переменные */
        :root {
            --neon-blue: #00f2ff; 
            --neon-purple: #b900ff;
            --dark-bg: #0a0a1a;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--dark-bg);
            color: white;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        h1, h2 {
            color: var(--neon-purple);
            font-weight: 600;
        }

        .neon-text {
            color: var(--neon-blue);
            text-shadow: 0 0 5px var(--neon-blue), 0 0 10px var(--neon-blue);
        }

        header {
            background: rgba(26, 26, 52, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        nav a, .logout-btn {
            color: var(--neon-blue);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 999px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        nav a:hover, .logout-btn:hover {
            background-color: rgba(0, 242, 255, 0.2);
            color: white;
        }

        .logout-btn {
            background-color: transparent;
            border: none;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
        }

        thead {
            background-color: rgba(185, 0, 255, 0.1);
        }

        th, td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            vertical-align: top;
        }

        tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .metric-name {
            color: var(--neon-blue);
        }

        .chart-container {
            background: rgba(26, 26, 52, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .btn-primary {
            background-color: #b900ff;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 999px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            text-align: center;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #a000e0;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(185, 0, 255, 0.4);
        }

        .profile-card {
            background: rgba(26, 26, 52, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 600px) {
            .container {
                margin: 20px 15px;
                padding: 1rem;
            }

            thead th,
            tbody td {
                font-size: 13px;
                padding: 8px;
            }
        }

        /* Анимированные пузырьки */
        #bubbles-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }

        .bubble {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            opacity: 0.6;
            animation: rise linear infinite;
        }

        @keyframes rise {
            0% {
                transform: translateY(100vh) scale(1);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-10vh) scale(0);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Анимированные пузырьки -->
    <div id="bubbles-container"></div>

    <!-- Шапка -->
    <header class="py-4">
        <div class="container">
            <div class="nav-container">
                <h1 class="text-2xl neon-text">Профиль</h1>
                <nav class="flex flex-wrap gap-2">
                    <a href="index.html">Главная</a>
                    <a href="profile.php">Профиль</a>
                    <a href="stats.php">Статистика</a>
                    <a href="leaderboard.php">Лидеры</a>
                    <button class="logout-btn" onclick="window.location.href='logout.php'">Выйти</button>
                </nav>
            </div>
        </div>
    </header>

    <!-- Основной контейнер -->
    <div class="container relative z-1 pt-8 pb-16">
        <!-- Приветствие -->
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-4">Добро пожаловать, <?= htmlspecialchars($user['username'] ?? $user['email']) ?></h2>
            <p class="text-sm text-gray-400">Информация о вашем аккаунте</p>
        </div>

        <!-- Карточка профиля -->
        <div class="profile-card">
            <!-- Информация о пользователе -->
            <table>
                <tbody>
                    <tr>
                        <th class="metric-name">ID</th>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                    </tr>
                    <tr>
                        <th class="metric-name">Email</th>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                    </tr>
                    <tr>
                        <th class="metric-name">Дата регистрации</th>
                        <td><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></td>
                    </tr>
                    <tr>
                        <th class="metric-name">Последний вход</th>
                        <td>
                            <?= $lastLogin ? date('d.m.Y H:i', strtotime($lastLogin['login_time'])) : 'Нет данных' ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Кнопка выхода -->
        <a href="logout.php" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-full transition duration-300">Выйти из аккаунта</a>
    </div>

    <!-- Скрипт для анимации пузырьков -->
    <script>
        function createBubbles() {
            const container = document.getElementById('bubbles-container');
            for (let i = 0; i < 30; i++) {
                const bubble = document.createElement('div');
                bubble.className = 'bubble';
                bubble.style.width = bubble.style.height = `${Math.random() * 20 + 10}px`;
                bubble.style.left = `${Math.random() * 100}%`;
                bubble.style.bottom = `${Math.random() * -100}%`;
                bubble.style.animationDuration = `${Math.random() * 20 + 20}s`;
                bubble.style.opacity = Math.random() * 0.5 + 0.3;
                container.appendChild(bubble);
                
                bubble.addEventListener('animationend', () => {
                    bubble.remove();
                });
            }
        }

        setInterval(createBubbles, 2000);
        createBubbles();
    </script>
</body>
</html>