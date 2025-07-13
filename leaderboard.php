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

try {
    // Получаем топ 10 игроков по общему счету
    $stmt = $pdo->prepare("SELECT id, email, total_score FROM users ORDER BY total_score DESC LIMIT 10");
    $stmt->execute();
    $leaders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Ошибка получения данных лидеров: " . $e->getMessage());
    die("Произошла ошибка при получении данных лидеров.");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Таблица лидеров</title>
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

        .leader-row:nth-child(1) {
            background: rgba(255, 215, 0, 0.1);
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.3);
        }
        
        .leader-row:nth-child(2) {
            background: rgba(192, 192, 192, 0.1);
            box-shadow: 0 0 10px rgba(192, 192, 192, 0.2);
        }
        
        .leader-row:nth-child(3) {
            background: rgba(205, 127, 50, 0.1);
            box-shadow: 0 0 8px rgba(205, 127, 50, 0.2);
        }

        .leader-row:hover {
            transform: scale(1.01);
            transition: transform 0.3s ease;
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
                <h1 class="text-2xl neon-text">Таблица лидеров</h1>
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
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-4">ТОП 10 Игроков</h2>
        </div>

        <!-- Таблица лидеров -->
        <div class="chart-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Имя пользователя</th>
                        <th>Общий счет</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaders as $index => $leader): ?>
                    <tr class="leader-row">
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($leader['email']) ?></td>
                        <td><?= htmlspecialchars($leader['total_score']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Назад к профилю -->
        <div class="mt-8">
            <button onclick="window.location.href='index.html'" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-full transition duration-300">
                Назад на главную страницу
            </button>
        </div>
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
                
                // Удаляем пузырек после завершения анимации
                bubble.addEventListener('animationend', () => {
                    bubble.remove();
                });
            }
        }

        // Создаем новые пузырьки каждые 2 секунды
        setInterval(createBubbles, 2000);
        createBubbles();
    </script>
</body>
</html>