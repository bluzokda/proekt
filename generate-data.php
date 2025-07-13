<?php
function generateDepositData() {
    // Генерация данных для первого вклада
    $initial = rand(10000, 100000);
    $rate = rand(3, 10);
    $capitalization = (bool)rand(0, 1);
    $years = rand(1, 10);
    
    // Расчет итоговой суммы
    if ($capitalization) {
        // Сложные проценты (с капитализацией)
        $total = $initial * pow(1 + $rate/100, $years);
    } else {
        // Простые проценты (без капитализации)
        $total = $initial * (1 + ($rate/100) * $years);
    }
    $total = round($total);

    // Генерация данных для задания (искомый срок)
    $initialToFind = rand(15000, 150000);
    $rateToFind = rand(4, 12);
    $yearsToFind = rand(1, 15);
    
    // Расчет для задания (всегда с капитализацией)
    $totalToFind = $initialToFind * pow(1 + $rateToFind/100, $yearsToFind);
    $totalToFind = round($totalToFind);

    return [
        'initial' => $initial,
        'rate' => $rate,
        'capitalization' => $capitalization,
        'years' => $years,
        'total' => $total,
        'initialToFind' => $initialToFind,
        'rateToFind' => $rateToFind,
        'yearsToFind' => $yearsToFind,
        'totalToFind' => $totalToFind
    ];
}function generateAnnuityLoanData() {
    $loanAmount = rand(500000, 1500000);
    $rate = rand(8, 15);
    $years = rand(5, 15);
    // Рассчитываем ежемесячный платеж
    $monthlyPayment = round($loanAmount * ($rate / 100 / 12) / (1 - pow(1 + $rate / 100 / 12, -$years * 12)));
    $loanAmountToFind = rand(700000, 2000000);
    $rateToFind = rand(6, 18);
    $yearsToFind = rand(3, 10);
    $monthlyPaymentToFind = round($loanAmountToFind * ($rateToFind / 100 / 12) / (1 - pow(1 + $rateToFind / 100 / 12, -$yearsToFind * 12)));
    return [
        'loanAmount' => $loanAmount,
        'rate' => $rate,
        'years' => $years,
        'monthlyPayment' => $monthlyPayment,
        'loanAmountToFind' => $loanAmountToFind,
        'rateToFind' => $rateToFind,
        'yearsToFind' => $yearsToFind,
        'monthlyPaymentToFind' => $monthlyPaymentToFind
    ];
}

function generateDifferentiatedLoanData() {
    $loanAmount = rand(500000, 1500000);
    $rate = rand(8, 15);
    $years = rand(5, 15);
    // Рассчитываем первый и последний платеж
    $firstPayment = round($loanAmount / ($years * 12) + ($loanAmount * ($rate / 100 / 12)));
    $lastPayment = round($loanAmount / ($years * 12));
    $loanAmountToFind = rand(700000, 2000000);
    $rateToFind = rand(6, 18);
    $yearsToFind = rand(3, 10);
    $firstPaymentToFind = round($loanAmountToFind / ($yearsToFind * 12) + ($loanAmountToFind * ($rateToFind / 100 / 12)));
    $lastPaymentToFind = round($loanAmountToFind / ($yearsToFind * 12));
    return [
        'loanAmount' => $loanAmount,
        'rate' => $rate,
        'years' => $years,
        'firstPayment' => $firstPayment,
        'lastPayment' => $lastPayment,
        'loanAmountToFind' => $loanAmountToFind,
        'rateToFind' => $rateToFind,
        'yearsToFind' => $yearsToFind,
        'firstPaymentToFind' => $firstPaymentToFind,
        'lastPaymentToFind' => $lastPaymentToFind
    ];
}

function generateOlympiadData() {
    // Список олимпиадных задач
    $tasks = [
        [
            'university' => "МГУ им. М.В. Ломоносова",
            'question' => "Инвестор купил акцию по цене 450 руб. в начале года. В конце года он получил дивиденд 19 руб. и продал акцию за 551 руб. Какова общая доходность инвестиции в процентах? (Ответ округлите до двух знаков после запятой)",
            'answer' => "26.67",
            'solution' => "Доходность = ((551 + 19 - 450) / 450) × 100% = (120/450)*100% ≈ 26.67%"
        ],
        [
            'university' => "НИУ ВШЭ",
            'question' => "Предприятие выпустило облигации номиналом 1000 руб. со сроком погашения 5 лет и купоном 13% годовых. Какова цена облигации, если рыночная ставка составляет 14%? (Ответ округлите до двух знаков после запятой)",
            'answer' => "960.41",
            'solution' => "Цена = Σ(Купон/(1+r)^t) + Номинал/(1+r)^n = 130/1.14 + 130/1.14² + 130/1.14³ + 130/1.14⁴ + 130/1.14⁵ + 1000/1.14⁵ ≈ 960.41 руб."
        ],
        [
            'university' => "РЭУ им. Г.В. Плеханова",
            'question' => "Портфель состоит из двух активов: 33% акций с доходностью 15% и 67% облигаций с доходностью 9%. Какова ожидаемая доходность портфеля? (Ответ округлите до двух знаков после запятой)",
            'answer' => "10.98",
            'solution' => "Доходность портфеля = (0.33 * 15%) + (0.67 * 9%) = 4.95% + 6.03% = 10.98%"
        ],
        [
            'university' => "МГИМО",
            'question' => "Страховая компания предлагает аннуитет: выплата 150 тыс. руб. ежегодно в течение 15 лет. Сколько стоит такой аннуитет при ставке дисконтирования 5%? (Ответ округлите до целых тысяч)",
            'answer' => "1557",
            'solution' => "Стоимость аннуитета = 150 × [1 - (1+0.05)^-15]/0.05 ≈ 150 × 10.3797 ≈ 1557 тыс. руб."
        ],
        [
            'university' => "Финансовый университет",
            'question' => "Банк выдал кредит 1.5 млн руб. под 10% годовых на 3 года с аннуитетными платежами. Какова сумма переплаты? (Ответ округлите до целых тысяч)",
            'answer' => "243",
            'solution' => "Расчет аннуитетного платежа и общей переплаты"
        ]
    ];

    // Выбираем случайную задачу
    $randomIndex = array_rand($tasks);
    $currentTask = $tasks[$randomIndex];

    // Генерируем 3 случайные задачи для таблицы
    $archiveTasks = [];
    $usedIndexes = [$randomIndex];
    
    for ($i = 0; $i < 3; $i++) {
        // Выбираем уникальный индекс
        do {
            $index = array_rand($tasks);
        } while (in_array($index, $usedIndexes));
        
        $usedIndexes[] = $index;
        $archiveTasks[] = $tasks[$index];
    }

    return [
        'current' => $currentTask,
        'archive' => $archiveTasks
    ];
}


function generateInvestmentData() {
    $initial = rand(50000, 200000);
    $annualReturn = rand(5, 20);
    $years = rand(1, 10);
    $finalValue = round($initial * pow(1 + ($annualReturn/100), $years));
    
    return [
        'initialToFind' => $initial,
        'annualReturnToFind' => $annualReturn,
        'yearsToFind' => $years,
        'finalValueToFind' => $finalValue
    ];
}

function generateEgeTask($level = 'basic') {
    if ($level === 'basic') {
        // Базовый уровень - обычная капитализация
        $amount = rand(10000, 100000);
        $rate = rand(5, 20);
        $capTypes = ['ежегодно', 'ежеквартально', 'ежемесячно'];
        $cap = $capTypes[array_rand($capTypes)];
        $years = rand(1, 5);
        $periods = [
            'ежегодно' => 1,
            'ежеквартально' => 4,
            'ежемесячно' => 12
        ][$cap];
        $totalPeriods = $years * $periods;
        $periodRate = $rate / $periods / 100;
        $S = round($amount * pow(1 + $periodRate, $totalPeriods));

        return [
            'question' => "Вкладчик положил в банк " . number_format($amount, 0, ',', ' ') . " рублей под {$rate}% годовых с капитализацией {$cap}. Какая сумма будет на счету через {$years} лет?",
            'amount' => $amount,
            'rate' => $rate,
            'capitalization' => $cap,
            'years' => $years,
            'finalAmount' => $S
        ];
    } else {
        // Повышенный уровень — сложные задачи (аналог ЕГЭ №17)
        $taskTypes = ['two-payments', 'equal-reduction', 'varying-payments', 'deposit-additions'];
        $type = $taskTypes[array_rand($taskTypes)];

        $amount = round((1000000 + mt_rand() / mt_getrandmax() * 9000000) / 100000) * 100000; // От 1 до 10 млн
        $years = 2 + rand(0, 3); // От 2 до 5 лет
        $rate = 10 + rand(0, 20); // От 10% до 30%

        switch ($type) {
            case 'two-payments':
                // Кредит с двумя равными платежами
                $totalAmount = $amount * pow(1 + $rate / 100, 2);
                $payment = round($totalAmount / (1 + (1 + $rate / 100)));

                return [
                    'question' => "31 декабря 2024 года заемщик взял в банке " . number_format($amount / 1000000, 0, ',', ' ') . " млн рублей в кредит под {$rate}% годовых. Схема выплаты кредита следующая — 31 декабря каждого следующего года банк начисляет проценты на оставшуюся сумму долга, затем заемщик переводит в банк X рублей. Какой должна быть сумма X, чтобы заемщик выплатил долг двумя равными платежами?",
                    'amount' => $amount,
                    'rate' => $rate,
                    'years' => $years,
                    'totalPayment' => $payment
                ];

            case 'equal-reduction':
                // Кредит с равномерным уменьшением долга
                $months = $years * 12;
                $totalPayment = round($amount * (1 + 0.3 + 0.1 * mt_rand() / mt_getrandmax()));
                $r = round(($totalPayment / $amount - 1) * 1000) / 10;

                return [
                    'question' => "15 января планируется взять кредит в банке на {$months} месяцев. Условия его возврата таковы: 1-го числа каждого месяца долг возрастает на r% по сравнению с концом предыдущего месяца; со 2-го по 14-е число каждого месяца необходимо выплатить часть долга; 15-го числа каждого месяца долг должен быть на одну и ту же сумму меньше долга на 15-е число предыдущего месяца. Известно, что общая сумма выплат после полного погашения кредита на " . round(($totalPayment / $amount - 1) * 100) . "% больше суммы, взятой в кредит. Найдите r.",
                    'amount' => $amount,
                    'rate' => $r,
                    'years' => $years,
                    'totalPayment' => $r
                ];

            case 'varying-payments':
                // Кредит с разными выплатами по годам
                $annualPayment = round($amount / $years);
                $totalInterest = round($annualPayment * $rate / 100 * ($years + 1) / 2);
                $totalPayment = $amount + $totalInterest;

                return [
                    'question' => "В июле планируется взять кредит на сумму " . number_format($amount / 1000000, 0, ',', ' ') . " млн рублей на {$years} " . ($years === 1 ? "год" : ($years < 5 ? "года" : "лет")) . ". Условия возврата: каждый январь долг возрастает на {$rate}% по сравнению с концом предыдущего года; с февраля по июнь каждого года необходимо выплатить часть долга; в июле каждого года долг должен быть на одну и ту же сумму меньше долга на июль предыдущего года. Сколько рублей составит общая сумма выплат?",
                    'amount' => $amount,
                    'rate' => $rate,
                    'years' => $years,
                    'totalPayment' => $totalPayment
                ];

            case 'deposit-additions':
                // Вклад с ежегодными пополнениями
                $additions = round((100000 + mt_rand() / mt_getrandmax() * 400000) / 10000) * 10000;
                $finalAmount = round(
                    $amount * pow(1 + $rate / 100, 5) +
                    $additions * (pow(1 + $rate / 100, 4) + pow(1 + $rate / 100, 3) + pow(1 + $rate / 100, 2) + (1 + $rate / 100))
                );

                return [
                    'question' => "В банк помещена сумма " . number_format($amount / 1000000, 0, ',', ' ') . " млн рублей под {$rate}% годовых. В конце каждого из первых четырех лет хранения после начисления процентов вкладчик дополнительно вносил на счет " . number_format($additions, 0, ',', ' ') . " рублей. Какая сумма будет на счету к концу пятого года?",
                    'amount' => $amount,
                    'rate' => $rate,
                    'years' => $years,
                    'totalPayment' => $finalAmount
                ];
        }
    }
}