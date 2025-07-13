<?php
global $annuityLoanData1, $annuityLoanData2;
?>

<!-- Виджет 1: Аннуитетный кредит -->
<div class="widget">
    <div class="widget-header">
        <h2 class="widget-title">Аннуитетный кредит</h2>
    </div>
    <p class="widget-description">Рассчитайте ежемесячный платеж по аннуитетному кредиту.</p>

    <table>
        <thead>
            <tr>
                <th>Сумма кредита</th>
                <th>Годовая ставка</th>
                <th>Срок (лет)</th>
                <th>Ежемесячный платеж</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= number_format($annuityLoanData1['loanAmount'], 0, ',', ' ') ?> ₽</td>
                <td><?= $annuityLoanData1['rate'] ?>%</td>
                <td><?= $annuityLoanData1['years'] ?></td>
                <td><?= number_format($annuityLoanData1['monthlyPayment'], 0, ',', ' ') ?> ₽</td>
            </tr>
            <tr>
                <td><?= number_format(rand(500000, 1500000), 0, ',', ' ') ?> ₽</td>
                <td><?= rand(8, 15) ?>%</td>
                <td><?= rand(5, 15) ?></td>
                <td><?= number_format(rand(10000, 50000), 0, ',', ' ') ?> ₽</td>
            </tr>
            <tr>
                <td><?= number_format($annuityLoanData1['loanAmountToFind'], 0, ',', ' ') ?> ₽</td>
                <td><?= $annuityLoanData1['rateToFind'] ?>%</td>
                <td><?= $annuityLoanData1['yearsToFind'] ?></td>
                <td class="highlight" data-answer="<?= $annuityLoanData1['monthlyPaymentToFind'] ?>">?</td>
            </tr>
        </tbody>
    </table>

    <div class="input-group">
        <div class="input-label">Введите ежемесячный платеж (₽):</div>
        <input type="number" min="1" placeholder="Сумма платежа" class="answer-input">
        <button class="check-btn" data-answer="<?= $annuityLoanData1['monthlyPaymentToFind'] ?>">Проверить</button>
    </div>
    <div class="result"></div>
    <div style="padding: 1rem 2rem; text-align: center;">
        <button class="new-task-btn hidden" onclick="location.reload()">🔄 Новая задача</button>
    </div>
</div>

<!-- Виджет 2: Аннуитетный кредит -->
<div class="widget">
    <div class="widget-header">
        <h2 class="widget-title">Аннуитетный кредит</h2>
    </div>
    <p class="widget-description">Рассчитайте ежемесячный платеж по аннуитетному кредиту.</p>

    <table>
        <thead>
            <tr>
                <th>Сумма кредита</th>
                <th>Годовая ставка</th>
                <th>Срок (лет)</th>
                <th>Ежемесячный платеж</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= number_format($annuityLoanData2['loanAmount'], 0, ',', ' ') ?> ₽</td>
                <td><?= $annuityLoanData2['rate'] ?>%</td>
                <td><?= $annuityLoanData2['years'] ?></td>
                <td><?= number_format($annuityLoanData2['monthlyPayment'], 0, ',', ' ') ?> ₽</td>
            </tr>
            <tr>
                <td><?= number_format(rand(500000, 1500000), 0, ',', ' ') ?> ₽</td>
                <td><?= rand(8, 15) ?>%</td>
                <td><?= rand(5, 15) ?></td>
                <td><?= number_format(rand(10000, 50000), 0, ',', ' ') ?> ₽</td>
            </tr>
            <tr>
                <td><?= number_format($annuityLoanData2['loanAmountToFind'], 0, ',', ' ') ?> ₽</td>
                <td><?= $annuityLoanData2['rateToFind'] ?>%</td>
                <td><?= $annuityLoanData2['yearsToFind'] ?></td>
                <td class="highlight" data-answer="<?= $annuityLoanData2['monthlyPaymentToFind'] ?>">?</td>
            </tr>
        </tbody>
    </table>

    <div class="input-group">
        <div class="input-label">Введите ежемесячный платеж (₽):</div>
        <input type="number" min="1" placeholder="Сумма платежа" class="answer-input">
        <button class="check-btn" data-answer="<?= $annuityLoanData2['monthlyPaymentToFind'] ?>">Проверить</button>
    </div>
    <div class="result"></div>
    <div style="padding: 1rem 2rem; text-align: center;">
        <button class="new-task-btn hidden" onclick="location.reload()">🔄 Новая задача</button>
    </div>
</div>