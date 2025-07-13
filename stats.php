<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Удаляем прямое подключение к БД - будем использовать только db.php
require 'db.php'; // Этот файл должен содержать правильные реквизиты доступа

if (!$pdo) {
    die("Ошибка подключения к базе данных");
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

try {
    // Получаем данные пользователя
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        throw new Exception("Пользователь не найден");
    }
    
    // Обработка NULL значений
    $user['total_score'] = $user['total_score'] ?? 0;
    $user['games_played'] = $user['games_played'] ?? 0;
    $user['games_won'] = $user['games_won'] ?? 0;
    
    // Получаем последние действия
    $stmt = $pdo->prepare("SELECT * FROM user_actions WHERE user_id = ? ORDER BY created_at DESC LIMIT 20");
    $stmt->execute([$user_id]);
    $actions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Получаем дневную статистику
    $stmt = $pdo->prepare("
        SELECT 
            DATE(created_at) as date, 
            COUNT(*) as total,
            SUM(is_correct) as correct 
        FROM user_actions 
        WHERE user_id = ? 
        GROUP BY DATE(created_at) 
        ORDER BY date DESC 
        LIMIT 7
    ");
    $stmt->execute([$user_id]);
    $daily_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Ошибка получения данных: " . $e->getMessage());
    die("Произошла ошибка при получении данных: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Статистика</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
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

        .date-row {
            color: #00ffe7;
            font-size: 12px;
            box-shadow: 0 0 10px rgba(0, 255, 231, 0.3);
            transition: all 0.3s ease;
            cursor: default;
        }

        .date-row:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(0, 255, 231, 0.5);
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
    <div id="bubbles-container"></div>

    <header class="py-4">
        <div class="container">
            <div class="nav-container">
                <h1 class="text-2xl neon-text">Статистика</h1>
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

    <div class="container relative z-1 pt-8 pb-16">
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-4"><?= htmlspecialchars($user['username'] ?? 'Пользователь') ?></h2>
            <p class="text-sm text-gray-400"><?= htmlspecialchars($user['email'] ?? '') ?></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-gray-800/30 p-4 rounded-lg border border-gray-700/50">
                <h3 class="text-neon-blue text-sm uppercase tracking-wider mb-2">Общий счет</h3>
                <p class="text-3xl font-bold"><?= number_format($user['total_score'], 0, '', ' ') ?></p>
            </div>
            <div class="bg-gray-800/30 p-4 rounded-lg border border-gray-700/50">
                <h3 class="text-neon-blue text-sm uppercase tracking-wider mb-2">Задач решено</h3>
                <p class="text-3xl font-bold"><?= number_format($user['games_played'], 0, '', ' ') ?></p>
            </div>
            <div class="bg-gray-800/30 p-4 rounded-lg border border-gray-700/50">
                <h3 class="text-neon-blue text-sm uppercase tracking-wider mb-2">Правильных ответов</h3>
                <p class="text-3xl font-bold"><?= number_format($user['games_won'], 0, '', ' ') ?></p>
            </div>
            <div class="bg-gray-800/30 p-4 rounded-lg border border-gray-700/50">
                <h3 class="text-neon-blue text-sm uppercase tracking-wider mb-2">Процент успеха</h3>
                <p class="text-3xl font-bold">
                    <?= ($user['games_played'] > 0) 
                        ? round(($user['games_won'] / $user['games_played']) * 100, 1) 
                        : 0 ?>%
                </p>
            </div>
        </div>

        <div class="chart-container">
            <h2 class="text-xl font-bold mb-4">История решений</h2>
            <?php if (!empty($actions)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Тип задачи</th>
                            <th>Блок</th>
                            <th>Ответ</th>
                            <th>Результат</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($actions as $action): ?>
                            <tr>
                                <td><?= date('d.m.Y H:i', strtotime($action['created_at'])) ?></td>
                                <td><?= htmlspecialchars($action['task_type'] ?? '') ?></td>
                                <td><?= htmlspecialchars($action['block'] ?? '') ?></td>
                                <td><?= htmlspecialchars($action['task_details'] ?? '') ?></td>
                                <td class="<?= ($action['is_correct'] ? 'text-green-400' : 'text-red-400') ?>">
                                    <?= $action['is_correct'] ? 'Правильно' : 'Неправильно' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-gray-400 mt-4">Вы еще не решили ни одной задачи.</p>
            <?php endif; ?>
        </div>

        <div class="chart-container mt-8">
            <h2 class="text-xl font-bold mb-4">Ежедневная активность</h2>
            <?php if (!empty($daily_stats)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Задач решено</th>
                            <th>Правильных</th>
                            <th>Процент</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daily_stats as $stat): ?>
                            <tr>
                                <td><?= date('d.m.Y', strtotime($stat['date'])) ?></td>
                                <td><?= $stat['total'] ?></td>
                                <td><?= $stat['correct'] ?></td>
                                <td>
                                    <?= ($stat['total'] > 0) 
                                        ? round(($stat['correct'] / $stat['total']) * 100, 1) 
                                        : 0 ?>%
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-gray-400 mt-4">Нет данных о ежедневной активности.</p>
            <?php endif; ?>
        </div>

        <div class="mt-8">
            <button onclick="window.location.href='index.html'" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-full transition duration-300">
                Назад на главную страницу
            </button>
        </div>
    </div>

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
    </script>
</body>
</html>