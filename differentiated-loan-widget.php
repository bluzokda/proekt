<?php
global $differentiatedLoanData1, $differentiatedLoanData2;
?>

<!-- Виджет 1: Дифференцированный кредит -->
<div class="widget">
    <div class="widget-header">
        <h2 class="widget-title">Дифференцированный кредит</h2>
    </div>
    <p class="widget-description">Рассчитайте первый и последний ежемесячный платеж по дифференцированному кредиту.</p>

    <table>
        <thead>
            <tr>
                <th>Сумма кредита</th>
                <th>Годовая ставка</th>
                <th>Срок (лет)</th>
                <th>Первый платеж</th>
                <th>Последний платеж</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= number_format($differentiatedLoanData1['loanAmount'], 0, ',', ' ') ?> ₽</td>
                <td><?= $differentiatedLoanData1['rate'] ?>%</td>
                <td><?= $differentiatedLoanData1['years'] ?></td>
                <td><?= number_format($differentiatedLoanData1['firstPayment'], 0, ',', ' ') ?> ₽</td>
                <td><?= number_format($differentiatedLoanData1['lastPayment'], 0, ',', ' ') ?> ₽</td>
            </tr>
            <tr>
                <td><?= number_format(rand(500000, 1500000), 0, ',', ' ') ?> ₽</td>
                <td><?= rand(8, 15) ?>%</td>
                <td><?= rand(5, 15) ?></td>
                <td><?= number_format(rand(10000, 50000), 0, ',', ' ') ?> ₽</td>
                <td><?= number_format(rand(5000, 25000), 0, ',', ' ') ?> ₽</td>
            </tr>
            <tr>
                <td><?= number_format($differentiatedLoanData1['loanAmountToFind'], 0, ',', ' ') ?> ₽</td>
                <td><?= $differentiatedLoanData1['rateToFind'] ?>%</td>
                <td><?= $differentiatedLoanData1['yearsToFind'] ?></td>
                <td class="highlight" data-answer="<?= $differentiatedLoanData1['firstPaymentToFind'] ?>">?</td>
                <td class="highlight" data-answer="<?= $differentiatedLoanData1['lastPaymentToFind'] ?>">?</td>
            </tr>
        </tbody>
    </table>

    <div class="input-group">
        <div class="input-row">
            <div class="input-label">Первый платеж (₽):</div>
            <input type="number" min="1" placeholder="Первый платеж" class="answer-input first-payment">
        </div>
        <div class="input-row">
            <div class="input-label">Последний платеж (₽):</div>
            <input type="number" min="1" placeholder="Последний платеж" class="answer-input last-payment">
        </div>
        <button class="check-btn" 
                data-answer-first="<?= $differentiatedLoanData1['firstPaymentToFind'] ?>" 
                data-answer-last="<?= $differentiatedLoanData1['lastPaymentToFind'] ?>">Проверить</button>
    </div>
    <div class="result"></div>
    <div style="padding: 1rem 2rem; text-align: center;">
        <button class="new-task-btn hidden" onclick="location.reload()">🔄 Новая задача</button>
    </div>
</div>

<!-- Виджет 2: Дифференцированный кредит -->
<div class="widget">
    <div class="widget-header">
        <h2 class="widget-title">Дифференцированный кредит</h2>
    </div>
    <p class="widget-description">Рассчитайте первый и последний ежемесячный платеж по дифференцированному кредиту.</p>

    <table>
        <thead>
            <tr>
                <th>Сумма кредита</th>
                <th>Годовая ставка</th>
                <th>Срок (лет)</th>
                <th>Первый платеж</th>
                <th>Последний платеж</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= number_format($differentiatedLoanData2['loanAmount'], 0, ',', ' ') ?> ₽</td>
                <td><?= $differentiatedLoanData2['rate'] ?>%</td>
                <td><?= $differentiatedLoanData2['years'] ?></td>
                <td><?= number_format($differentiatedLoanData2['firstPayment'], 0, ',', ' ') ?> ₽</td>
                <td><?= number_format($differentiatedLoanData2['lastPayment'], 0, ',', ' ') ?> ₽</td>
            </tr>
            <tr>
                <td><?= number_format(rand(500000, 1500000), 0, ',', ' ') ?> ₽</td>
                <td><?= rand(8, 15) ?>%</td>
                <td><?= rand(5, 15) ?></td>
                <td><?= number_format(rand(10000, 50000), 0, ',', ' ') ?> ₽</td>
                <td><?= number_format(rand(5000, 25000), 0, ',', ' ') ?> ₽</td>
            </tr>
            <tr>
                <td><?= number_format($differentiatedLoanData2['loanAmountToFind'], 0, ',', ' ') ?> ₽</td>
                <td><?= $differentiatedLoanData2['rateToFind'] ?>%</td>
                <td><?= $differentiatedLoanData2['yearsToFind'] ?></td>
                <td class="highlight" data-answer="<?= $differentiatedLoanData2['firstPaymentToFind'] ?>">?</td>
                <td class="highlight" data-answer="<?= $differentiatedLoanData2['lastPaymentToFind'] ?>">?</td>
            </tr>
        </tbody>
    </table>

    <div class="input-group">
        <div class="input-row">
            <div class="input-label">Первый платеж (₽):</div>
            <input type="number" min="1" placeholder="Первый платеж" class="answer-input first-payment">
        </div>
        <div class="input-row">
            <div class="input-label">Последний платеж (₽):</div>
            <input type="number" min="1" placeholder="Последний платеж" class="answer-input last-payment">
        </div>
        <button class="check-btn" 
                data-answer-first="<?= $differentiatedLoanData2['firstPaymentToFind'] ?>" 
                data-answer-last="<?= $differentiatedLoanData2['lastPaymentToFind'] ?>">Проверить</button>
    </div>
    <div class="result"></div>
    <div style="padding: 1rem 2rem; text-align: center;">
        <button class="new-task-btn hidden" onclick="location.reload()">🔄 Новая задача</button>
    </div>
</div>