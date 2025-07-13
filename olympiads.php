<?php
// Встроенная генерация данных вместо подключения внешнего файла
function generateOlympiadData() {
    // Генератор случайных задач с динамическими параметрами
    $tasks = [];

    // 1. Задача на доходность акции (МГУ)
    $price = rand(300, 800);
    $dividend = rand(10, 50);
    $sell_price = $price + rand(50, 200);
    $income = $sell_price + $dividend - $price;
    $yield = round(($income / $price) * 100, 2);
    $tasks[] = [
        'university' => "МГУ им. М.В. Ломоносова",
        'question' => "Инвестор купил акцию по цене $price руб. в начале года. В конце года он получил дивиденд $dividend руб. и продал акцию за $sell_price руб. Какова общая доходность инвестиции в процентах? (Ответ округлите до двух знаков после запятой)",
        'answer' => number_format($yield, 2, '.', ''),
        'solution' => "Доходность = (($sell_price + $dividend - $price) / $price) × 100% = ($income/$price)*100% ≈ $yield%"
    ];

    // 2. Задача на цену облигации (ВШЭ)
    $nominal = 1000;
    $coupon_rate = rand(10, 15);
    $market_rate = $coupon_rate + rand(1, 3);
    $years = 5;
    
    $price = 0;
    for ($t = 1; $t <= $years; $t++) {
        $price += ($nominal * $coupon_rate/100) / pow(1 + $market_rate/100, $t);
    }
    $price += $nominal / pow(1 + $market_rate/100, $years);
    $price = round($price, 2);
    
    $tasks[] = [
        'university' => "НИУ ВШЭ",
        'question' => "Предприятие выпустило облигации номиналом $nominal руб. со сроком погашения $years лет и купоном $coupon_rate% годовых. Какова цена облигации, если рыночная ставка составляет $market_rate%? (Ответ округлите до двух знаков после запятой)",
        'answer' => number_format($price, 2, '.', ''),
        'solution' => "Цена = Σ(Купон/(1+r)^t) + Номинал/(1+r)^n = "
                     . ($nominal * $coupon_rate/100) . "/1.$market_rate + ... + $nominal/1.$market_rate<sup>$years</sup> ≈ $price руб."
    ];

    // 3. Задача на доходность портфеля (Плеханов)
    $share_percent = rand(30, 40);
    $bond_percent = 100 - $share_percent;
    $share_yield = rand(10, 20);
    $bond_yield = rand(5, 10);
    $portfolio_yield = round(($share_percent/100 * $share_yield) + ($bond_percent/100 * $bond_yield), 2);
    
    $tasks[] = [
        'university' => "РЭУ им. Г.В. Плеханова",
        'question' => "Портфель состоит из двух активов: $share_percent% акций с доходностью $share_yield% и $bond_percent% облигаций с доходностью $bond_yield%. Какова ожидаемая доходность портфеля? (Ответ округлите до двух знаков после запятой)",
        'answer' => number_format($portfolio_yield, 2, '.', ''),
        'solution' => "Доходность портфеля = ($share_percent% × $share_yield%) + ($bond_percent% × $bond_yield%) = "
                     . round($share_percent/100 * $share_yield, 2) . "% + " . round($bond_percent/100 * $bond_yield, 2) . "% = $portfolio_yield%"
    ];

    // 4. Задача на аннуитет (МГИМО)
    $payment = rand(100, 200) * 1000;
    $years = rand(10, 20);
    $rate = rand(4, 8);
    
    $pv = 0;
    for ($y = 1; $y <= $years; $y++) {
        $pv += $payment / pow(1 + $rate/100, $y);
    }
    $pv = round($pv / 1000) * 1000;
    
    $tasks[] = [
        'university' => "МГИМО",
        'question' => "Страховая компания предлагает аннуитет: выплата " . number_format($payment, 0, ',', ' ') . " руб. ежегодно в течение $years лет. Сколько стоит такой аннуитет при ставке дисконтирования $rate%? (Ответ округлите до целых тысяч)",
        'answer' => number_format($pv, 0, '.', ''),
        'solution' => "Стоимость аннуитета = Σ(Платеж/(1+r)<sup>n</sup>) = "
                     . number_format($payment, 0, ',', ' ') . " × [1 - (1+$rate%)<sup>-$years</sup>]/$rate% ≈ " . number_format($pv, 0, ',', ' ') . " руб."
    ];

    // 5. Задача на переплату по кредиту (ФУ)
    $loan = rand(1000, 2000) * 1000;
    $rate = rand(8, 15);
    $years = rand(2, 5);
    
    $monthly_rate = $rate / 100 / 12;
    $months = $years * 12;
    $payment = $loan * $monthly_rate / (1 - pow(1 + $monthly_rate, -$months));
    $total_payment = $payment * $months;
    $overpayment = round(($total_payment - $loan) / 1000) * 1000;
    
    $tasks[] = [
        'university' => "Финансовый университет",
        'question' => "Банк выдал кредит " . number_format($loan, 0, ',', ' ') . " руб. под $rate% годовых на $years года с аннуитетными платежами. Какова сумма переплаты? (Ответ округлите до целых тысяч)",
        'answer' => number_format($overpayment, 0, '.', ''),
        'solution' => "Переплата = (Ежемесячный платеж × $months) - Сумма кредита = "
                     . number_format(round($payment), 0, ',', ' ') . " × $months - " . number_format($loan, 0, ',', ' ') . " ≈ " . number_format($overpayment, 0, ',', ' ') . " руб."
    ];

    // Выбираем случайную задачу
    $randomIndex = array_rand($tasks);
    return [
        'current' => $tasks[$randomIndex]
    ];
}

$olympiadData = generateOlympiadData();
?>

<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Финансовые олимпиады</title>
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
            content: "🏆";
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
            text-align: center;
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

        .solution {
            padding: 1.5rem 2rem;
            background: rgba(30, 30, 46, 0.7);
            display: none;
            border-top: 1px solid var(--border);
            animation: fadeIn 0.5s ease;
        }

        .solution.show {
            display: block;
        }

        .solution-title {
            font-weight: 600;
            margin-bottom: 0.8rem;
            color: var(--accent);
        }

        .solution-content {
            line-height: 1.6;
            font-family: 'Roboto Mono', monospace;
        }

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

        .question-cell {
            line-height: 1.6;
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
            
            .input-group {
                flex-direction: column;
                align-items: stretch;
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
            <h1>Олимпиадные задачи</h1>
            <p class="subtitle">Решите задачи из ведущих экономических вузов России</p>
        </header>

        <main>
            <div class="widgets-container">
                <div class="widget">
                    <div class="widget-header">
                        <h2 class="widget-title">Финансовые олимпиады</h2>
                    </div>
                    <p class="widget-description">Решите финансовую задачу. Введите ответ в поле ниже. У вас есть 3 попытки.</p>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Университет</th>
                                <th>Задача</th>
                                <th>Сложность</th>
                                <th>Ответ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= htmlspecialchars($olympiadData['current']['university']) ?></td>
                                <td class="question-cell"><?= htmlspecialchars($olympiadData['current']['question']) ?></td>
                                <td><?= rand(75, 95) ?>%</td>
                                <td class="highlight" data-answer="<?= htmlspecialchars($olympiadData['current']['answer']) ?>">?</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="input-group">
                        <input 
                            type="text" 
                            class="answer-input"
                            placeholder="Введите ваш ответ"
                        >
                        <button class="check-btn" data-answer="<?= htmlspecialchars($olympiadData['current']['answer']) ?>">Проверить</button>
                    </div>
                    <div class="result"></div>
                    
                    <div class="solution">
                        <div class="solution-title">Решение:</div>
                        <div class="solution-content"><?= htmlspecialchars($olympiadData['current']['solution']) ?></div>
                    </div>
                    
                    <div style="padding: 1rem 2rem; text-align: center;">
                        <button class="new-task-btn hidden" onclick="location.reload()">🔄 Новая задача</button>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; <?= date('Y') ?> Финансовые олимпиады</p>
        </footer>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const widget = document.querySelector('.widget');
            widget.dataset.attempts = 0;
            
            const counter = document.createElement('div');
            counter.className = 'attempts-counter';
            counter.textContent = '0';
            counter.style.display = 'none';
            
            const inputGroup = widget.querySelector('.input-group');
            if (inputGroup) {
                inputGroup.appendChild(counter);
            }

            function disableWidget(correctAnswer) {
                const highlightCell = widget.querySelector('.highlight');
                if (highlightCell) {
                    highlightCell.textContent = correctAnswer;
                    highlightCell.style.backgroundColor = 'rgba(76, 175, 80, 0.2)';
                    highlightCell.style.color = 'var(--success)';
                    highlightCell.style.animation = 'none';
                    highlightCell.style.borderColor = 'rgba(76, 175, 80, 0.3)';
                }

                const input = widget.querySelector('.answer-input');
                const button = widget.querySelector('.check-btn');
                if (input) input.disabled = true;
                if (button) button.disabled = true;

                const newTaskBtn = widget.querySelector('.new-task-btn');
                if (newTaskBtn) {
                    newTaskBtn.classList.remove('hidden');
                }
                
                const solution = widget.querySelector('.solution');
                if (solution) {
                    solution.classList.add('show');
                }
            }

            document.querySelector(".check-btn").addEventListener("click", function () {
                const widget = this.closest(".widget");
                const input = widget.querySelector(".answer-input");
                const resultDiv = widget.querySelector(".result");
                const correctAnswer = this.getAttribute("data-answer");
                const counter = widget.querySelector('.attempts-counter');
                const solution = widget.querySelector('.solution');
                
                let attempts = parseInt(widget.dataset.attempts) || 0;
                attempts++;
                widget.dataset.attempts = attempts;
                
                if (counter) {
                    counter.textContent = attempts;
                    counter.style.display = 'block';
                }

                // Нормализация ввода: удаление пробелов и замена запятых на точки
                const userAnswer = input.value.trim().replace(',', '.');
                const normalizedCorrect = correctAnswer.replace(',', '.');

                if (userAnswer === normalizedCorrect) {
                    resultDiv.textContent = "✅ Верно!";
                    resultDiv.className = "result success";
                    resultDiv.style.display = "block";
                    
                    input.disabled = true;
                    this.disabled = true;
                    
                    const newTaskBtn = widget.querySelector('.new-task-btn');
                    if (newTaskBtn) {
                        newTaskBtn.classList.remove('hidden');
                    }
                    
                    if (solution) {
                        solution.classList.add('show');
                    }

                    const highlightCell = widget.querySelector('.highlight');
                    if (highlightCell) {
                        highlightCell.textContent = correctAnswer;
                        highlightCell.style.backgroundColor = 'rgba(76, 175, 80, 0.2)';
                        highlightCell.style.color = 'var(--success)';
                        highlightCell.style.animation = 'none';
                    }
                } else {
                    if (attempts >= 3) {
                        resultDiv.textContent = `❌ Неверно. Правильный ответ: ${correctAnswer}`;
                        resultDiv.className = "result error";
                        resultDiv.style.display = "block";
                        disableWidget(correctAnswer);
                    } else {
                        resultDiv.textContent = `❌ Неверно. Попытка ${attempts} из 3.`;
                        resultDiv.className = "result error";
                        resultDiv.style.display = "block";
                        
                        input.style.borderColor = "var(--error)";
                        setTimeout(() => {
                            input.style.borderColor = "var(--border)";
                        }, 1000);
                    }
                }
            });

            const firstInput = document.querySelector(".answer-input");
            if (firstInput) {
                firstInput.focus();
            }

            document.querySelector(".answer-input").addEventListener("keypress", function(e) {
                if (e.key === "Enter") {
                    const btn = this.closest(".widget").querySelector(".check-btn");
                    if (btn && !btn.disabled) btn.click();
                }
            });
        });
    </script>
</body>
</html>