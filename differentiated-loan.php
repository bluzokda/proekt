<?php
include 'generate-data.php';
$differentiatedLoanData1 = generateDifferentiatedLoanData();
$differentiatedLoanData2 = generateDifferentiatedLoanData();
?>
<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Дифференцированный кредит</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Roboto+Mono:wght@300;400&display=swap" rel="stylesheet">
    <style>
        :root {
            --dark-bg: #121212;
            --darker-bg: #0d0d0d;
            --card-bg: #1e1e1e;
            --accent: #6a5acd;
            --accent-hover: #5a4acd;
            --success: #4caf50;
            --error: #f44336;
            --warning: #ff9800;
            --text-primary: #f0f0f0;
            --text-secondary: #b0b0b0;
            --text-highlight: #ffffff;
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
            margin-bottom: 2rem;
            position: relative;
        }

        h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            background: linear-gradient(90deg, #8a7cfb, #633e9c);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .subtitle {
            font-size: 1.3rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
            font-weight: 500;
        }

        .widgets-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2.5rem;
            margin: 2rem 0;
        }

        .widget {
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            transition: transform 0.3s ease;
        }

        .widget:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
        }

        .widget-header {
            background: linear-gradient(90deg, #1a1a2e, #16213e);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border);
        }

        .widget-title {
            font-size: 1.6rem;
            font-weight: 600;
            color: var(--text-highlight);
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .widget-title::before {
            content: "💸";
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--accent);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.4rem;
        }

        .widget-description {
            padding: 1.5rem 2rem;
            color: var(--text-secondary);
            font-size: 1.1rem;
            line-height: 1.6;
            border-bottom: 1px solid var(--border);
            background: rgba(30, 30, 46, 0.5);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            font-family: 'Roboto Mono', monospace;
        }

        th {
            background: rgba(50, 50, 70, 0.7);
            padding: 1.2rem 1.5rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-highlight);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.95rem;
            border-bottom: 2px solid var(--accent);
        }

        td {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--border);
            text-align: left;
            font-size: 1.1rem;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .highlight {
            background: rgba(255, 184, 77, 0.15);
            font-weight: 700;
            color: var(--warning);
            animation: pulse 2s infinite;
            position: relative;
            border: 1px solid rgba(255, 184, 77, 0.3);
        }

    
        @keyframes pulse {
            0% { background-color: rgba(255, 184, 77, 0.15); }
            50% { background-color: rgba(255, 184, 77, 0.3); }
            100% { background-color: rgba(255, 184, 77, 0.15); }
        }

        .input-group {
            display: flex;
            padding: 1.5rem 2rem;
            gap: 1rem;
            background: rgba(30, 30, 46, 0.7);
            align-items: center;
            position: relative;
            flex-wrap: wrap;
        }

        .input-row {
            display: flex;
            flex: 1;
            gap: 1rem;
            min-width: 100%;
            margin-bottom: 1rem;
        }

        .input-label {
            font-size: 1.1rem;
            color: var(--text-secondary);
            min-width: 180px;
            display: flex;
            align-items: center;
        }

        .answer-input {
            flex: 1;
            padding: 1rem 1.2rem;
            border-radius: 8px;
            background: var(--darker-bg);
            border: 2px solid var(--border);
            color: var(--text-primary);
            font-family: 'Roboto Mono', monospace;
            font-size: 1.2rem;
            transition: var(--transition);
        }

        .answer-input:disabled {
            background: rgba(50, 50, 50, 0.5);
            color: var(--text-secondary);
            cursor: not-allowed;
        }

        .answer-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(106, 90, 205, 0.4);
        }

        .check-btn {
            background: var(--accent);
            color: white;
            border: none;
            padding: 1rem 2.2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.2rem;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            z-index: 1;
            min-width: 180px;
            box-shadow: 0 4px 15px rgba(106, 90, 205, 0.4);
            margin-top: 0.5rem;
        }

        .check-btn:disabled {
            background: #555;
            cursor: not-allowed;
            box-shadow: none;
        }

        .check-btn:disabled:hover {
            transform: none;
            box-shadow: none;
        }

        .check-btn:disabled::before {
            display: none;
        }

        .check-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: var(--accent-hover);
            transition: width 0.4s ease;
            z-index: -1;
        }

        .check-btn:hover:not(:disabled)::before {
            width: 100%;
        }

        .check-btn:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(106, 90, 205, 0.6);
        }

        .result {
            padding: 1.2rem 2rem;
            font-weight: 600;
            font-size: 1.2rem;
            display: none;
            border-top: 1px solid var(--border);
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .result.success {
            background: rgba(76, 175, 80, 0.15);
            color: var(--success);
            display: block;
        }

        .result.error {
            background: rgba(244, 67, 54, 0.15);
            color: var(--error);
            display: block;
        }

        /* Стили для кнопки "Новая задача" */
        .new-task-btn {
            background: var(--accent);
            color: white;
            border: none;
            padding: 1rem 2.2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.2rem;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            z-index: 1;
            min-width: 180px;
            box-shadow: 0 4px 15px rgba(106, 90, 205, 0.4);
            margin: 1.5rem auto 0;
            display: block;
        }

        .new-task-btn:hover {
            background: var(--accent-hover);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(106, 90, 205, 0.6);
        }

        .new-task-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: var(--accent-hover);
            transition: width 0.4s ease;
            z-index: -1;
        }

        .new-task-btn:hover::before {
            width: 100%;
        }

        .new-task-btn.hidden {
            display: none;
        }

        .attempts-counter {
            position: absolute;
            right: 20px;
            top: -10px;
            background: var(--error);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: bold;
            transform: translateY(-50%);
        }

        footer {
            text-align: center;
            padding: 2rem 0 1rem;
            color: var(--text-secondary);
            font-size: 1rem;
            margin-top: 3rem;
            border-top: 1px solid var(--border);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1.2rem;
            }
            
            h1 {
                font-size: 2.2rem;
            }
            
            .widget-header, .widget-description, .input-group {
                padding: 1.2rem;
            }
            
            .input-row {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .input-label {
                min-width: auto;
                margin-bottom: 0;
            }
            
            .check-btn {
                width: 100%;
            }
            
            th, td {
                padding: 0.8rem;
                font-size: 0.9rem;
            }
            
            .attempts-counter {
                top: -5px;
                right: 15px;
            }
            
            /* Адаптив для кнопки "Новая задача" */
            .new-task-btn {
                width: 100%;
                margin-top: 1.2rem;
                padding: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Дифференцированный кредит</h1>
            <p class="subtitle">Рассчитайте первый и последний платеж по дифференцированному кредиту</p>
        </header>

        <main>
            <div class="widgets-container">
                <?php include 'differentiated-loan-widget.php'; ?>
            </div>
        </main>

        <footer>
            <p>&copy; <?= date('Y') ?> Финансовые задачи</p>
        </footer>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Добавляем счетчики попыток для каждого виджета
            document.querySelectorAll('.widget').forEach(widget => {
                widget.dataset.attempts = 0;
                
                // Создаем элемент счетчика попыток
                const counter = document.createElement('div');
                counter.className = 'attempts-counter';
                counter.textContent = '0';
                counter.style.display = 'none';
                
                const inputGroup = widget.querySelector('.input-group');
                if (inputGroup) {
                    inputGroup.appendChild(counter);
                }
            });

            // Функция для блокировки виджета и показа кнопки новой задачи
            function disableWidget(widget, correctAnswerFirst, correctAnswerLast) {
                // Находим ячейки с вопросами
                const highlightCells = widget.querySelectorAll('.highlight');
                if (highlightCells.length >= 2) {
                    highlightCells[0].textContent = correctAnswerFirst;
                    highlightCells[0].style.backgroundColor = 'rgba(76, 175, 80, 0.2)';
                    highlightCells[0].style.color = 'var(--success)';
                    highlightCells[0].style.animation = 'none';
                    highlightCells[0].innerHTML = `${correctAnswerFirst} <span style="color:var(--success);margin-left:8px;">✓</span>`;
                    
                    highlightCells[1].textContent = correctAnswerLast;
                    highlightCells[1].style.backgroundColor = 'rgba(76, 175, 80, 0.2)';
                    highlightCells[1].style.color = 'var(--success)';
                    highlightCells[1].style.animation = 'none';
                    highlightCells[1].innerHTML = `${correctAnswerLast} <span style="color:var(--success);margin-left:8px;">✓</span>`;
                }

                // Блокируем поля ввода и кнопку
                const inputs = widget.querySelectorAll('.answer-input');
                const button = widget.querySelector('.check-btn');
                inputs.forEach(input => input.disabled = true);
                if (button) button.disabled = true;

                // Показываем кнопку "Новая задача"
                const newTaskBtn = widget.querySelector('.new-task-btn');
                if (newTaskBtn) {
                    newTaskBtn.classList.remove('hidden');
                }
            }

            // Добавляем обработчики для кнопок проверки
            document.querySelectorAll(".check-btn").forEach(btn => {
                btn.addEventListener("click", function () {
                    const widget = this.closest(".widget");
                    const firstInput = widget.querySelector(".first-payment");
                    const lastInput = widget.querySelector(".last-payment");
                    const resultDiv = widget.querySelector(".result");
                    const correctAnswerFirst = this.getAttribute("data-answer-first");
                    const correctAnswerLast = this.getAttribute("data-answer-last");
                    const counter = widget.querySelector('.attempts-counter');
                    const newTaskBtn = widget.querySelector('.new-task-btn');
                    
                    // Увеличиваем счетчик попыток
                    let attempts = parseInt(widget.dataset.attempts) || 0;
                    attempts++;
                    widget.dataset.attempts = attempts;
                    
                    // Обновляем счетчик
                    if (counter) {
                        counter.textContent = attempts;
                        counter.style.display = 'block';
                    }

                    // Проверяем оба ответа
                    const isFirstCorrect = firstInput.value.toString().trim() === correctAnswerFirst.toString().trim();
                    const isLastCorrect = lastInput.value.toString().trim() === correctAnswerLast.toString().trim();
                    
                    if (isFirstCorrect && isLastCorrect) {
                        resultDiv.textContent = "✅ Оба ответа верны!";
                        resultDiv.className = "result success";
                        resultDiv.style.display = "block";
                        
                        // Блокируем ввод после правильного ответа
                        firstInput.disabled = true;
                        lastInput.disabled = true;
                        btn.disabled = true;
                        
                        // Показываем кнопку "Новая задача"
                        if (newTaskBtn) {
                            newTaskBtn.classList.remove('hidden');
                        }
                        
                        // Обновляем ячейки с правильными ответами
                        const highlightCells = widget.querySelectorAll('.highlight');
                        if (highlightCells.length >= 2) {
                            highlightCells[0].textContent = correctAnswerFirst;
                            highlightCells[0].style.backgroundColor = 'rgba(76, 175, 80, 0.2)';
                            highlightCells[0].style.color = 'var(--success)';
                            highlightCells[0].innerHTML = `${correctAnswerFirst} <span style="color:var(--success);margin-left:8px;">✓</span>`;
                            
                            highlightCells[1].textContent = correctAnswerLast;
                            highlightCells[1].style.backgroundColor = 'rgba(76, 175, 80, 0.2)';
                            highlightCells[1].style.color = 'var(--success)';
                            highlightCells[1].innerHTML = `${correctAnswerLast} <span style="color:var(--success);margin-left:8px;">✓</span>`;
                        }
                    } else {
                        // Формируем сообщение об ошибке
                        let errorMsg = "❌ Неверно:";
                        if (!isFirstCorrect) errorMsg += " первый платеж";
                        if (!isFirstCorrect && !isLastCorrect) errorMsg += " и";
                        if (!isLastCorrect) errorMsg += " последний платеж";
                        errorMsg += `. Попытка ${attempts} из 3.`;
                        
                        resultDiv.textContent = errorMsg;
                        resultDiv.className = "result error";
                        resultDiv.style.display = "block";
                        
                        // Анимация ошибки
                        if (!isFirstCorrect) {
                            firstInput.style.borderColor = "var(--error)";
                            setTimeout(() => {
                                firstInput.style.borderColor = attempts < 3 ? "var(--border)" : "var(--error)";
                            }, 1000);
                        }
                        
                        if (!isLastCorrect) {
                            lastInput.style.borderColor = "var(--error)";
                            setTimeout(() => {
                                lastInput.style.borderColor = attempts < 3 ? "var(--border)" : "var(--error)";
                            }, 1000);
                        }
                        
                        // Если попытки исчерпаны
                        if (attempts >= 3) {
                            disableWidget(widget, correctAnswerFirst, correctAnswerLast);
                        }
                    }
                });
            });

            // Фокусируем первое поле ввода при загрузке
            const firstInput = document.querySelector(".first-payment");
            if (firstInput) {
                firstInput.focus();
            }

            // Обработка нажатия Enter в полях ввода
            document.querySelectorAll(".answer-input").forEach(input => {
                input.addEventListener("keypress", function(e) {
                    if (e.key === "Enter") {
                        const btn = this.closest(".widget").querySelector(".check-btn");
                        if (btn && !btn.disabled) btn.click();
                    }
                });
            });
        });
    </script>
</body>
</html>