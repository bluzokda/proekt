<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Финансовые задачи</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Roboto+Mono:wght@300;400&display=swap" rel="stylesheet">
    <style>
        :root {
            --dark-bg: #121212;
            --darker-bg: #0d0d0d;
            --card-bg: #1e1e1e;
            --accent: #6a5acd;
            --accent-hover: #5a4acd;
            --text-primary: #f0f0f0;
            --text-secondary: #b0b0b0;
            --border: #333;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.7);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--dark-bg);
            color: var(--text-primary);
            font-family: 'Montserrat', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(40, 40, 60, 0.1) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(60, 40, 80, 0.1) 0%, transparent 20%);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            flex: 1;
        }

        header {
            text-align: center;
            padding: 2rem 0;
            margin-bottom: 3rem;
            border-bottom: 1px solid var(--border);
            position: relative;
        }

        header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: var(--accent);
            border-radius: 3px;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: linear-gradient(90deg, #8a7cfb, #633e9c);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .subtitle {
            font-size: 1.1rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .tabs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin: 3rem 0;
        }

        .tab-btn {
            background: var(--card-bg);
            color: var(--text-primary);
            text-decoration: none;
            padding: 1.8rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.2rem;
            text-align: center;
            transition: var(--transition);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .tab-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 0;
            background: var(--accent);
            transition: height 0.3s ease;
            z-index: -1;
            opacity: 0.8;
        }

        .tab-btn:hover {
            transform: translateY(-5px);
            border-color: var(--accent);
            box-shadow: 0 6px 25px rgba(106, 90, 205, 0.4);
        }

        .tab-btn:hover::before {
            height: 100%;
        }

        .tab-btn:nth-child(2n):hover {
            box-shadow: 0 6px 25px rgba(86, 182, 194, 0.4);
        }

        .tab-btn:nth-child(2n)::before {
            background: #56b6c2;
        }

        .tab-btn:nth-child(3n):hover {
            box-shadow: 0 6px 25px rgba(220, 120, 200, 0.4);
        }

        .tab-btn:nth-child(3n)::before {
            background: #dc78c8;
        }

        footer {
            text-align: center;
            padding: 2rem 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
            border-top: 1px solid var(--border);
            margin-top: 3rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1.5rem;
            }
            
            .tabs {
                grid-template-columns: 1fr;
            }
            
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Выберите тип задачи</h1>
            <p class="subtitle">Перейдите на нужную страницу, чтобы решить соответствующую задачу</p>
        </header>

        <nav class="tabs">
            <a href="deposits.php" class="tab-btn">Вклады</a>
            <a href="annuity-loan.php" class="tab-btn">Аннуитетный кредит</a>
            <a href="differentiated-loan.php" class="tab-btn">Дифференцированный кредит</a>
            <a href="investments.php" class="tab-btn">Инвестиции</a>
            <a href="ege.php" class="tab-btn">ЕГЭ</a>
            <a href="olympiads.php" class="tab-btn">Коллекция олимпиад</a>
        </nav>

        <footer>
            <p>&copy; <?= date('Y') ?> Финансовые задачи</p>
        </footer>
    </div>
</body>
</html>