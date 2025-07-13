<?php
global $depositData1, $depositData2;
?>

<!-- Виджет 1: Вклады -->
<div class="widget">
    <div class="widget-header">
        <div class="icon deposit-icon">₽</div>
        <h2 class="widget-title">Банковские вклады</h2>
    </div>
    <p class="widget-description">Рассчитайте срок вклада с капитализацией процентов. В последней строке таблицы найдите недостающее значение.</p>

    <table>
        <thead>
            <tr>
                <th>Первоначальный вклад</th>
                <th>Годовая ставка</th>
                <th>Капитализация</th>
                <th>Срок (лет)</th>
                <th>Итоговая сумма</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= number_format($depositData1['initial'], 0, ',', ' ') ?> ₽</td>
                <td><?= $depositData1['rate'] ?>%</td>
                <td><?= $depositData1['capitalization'] ? 'Да' : 'Нет' ?></td>
                <td><?= $depositData1['years'] ?></td>
                <td><?= number_format($depositData1['total'], 0, ',', ' ') ?> ₽</td>
            </tr>
            <tr>
                <td><?= number_format(rand(15000, 60000), 0, ',', ' ') ?> ₽</td>
                <td><?= rand(4, 9) ?>%</td>
                <td><?= rand(0, 1) ? 'Да' : 'Нет' ?></td>
                <td><?= rand(2, 7) ?></td>
                <td><?= number_format(rand(20000, 90000), 0, ',', ' ') ?> ₽</td>
            </tr>
            <tr>
                <td><?= number_format($depositData1['initialToFind'], 0, ',', ' ') ?> ₽</td>
                <td><?= $depositData1['rateToFind'] ?>%</td>
                <td>Да</td>
                <td class="highlight" data-answer="<?= $depositData1['yearsToFind'] ?>">?</td>
                <td><?= number_format($depositData1['totalToFind'], 0, ',', ' ') ?> ₽</td>
            </tr>
        </tbody>
    </table>

    <div class="input-group">
        <input type="number" min="1" max="30" placeholder="Введите срок в годах" class="answer-input">
        <button class="check-btn" data-answer="<?= $depositData1['yearsToFind'] ?>">Проверить</button>
    </div>
    <div class="result"></div>
    <div style="padding: 1rem 2rem; text-align: center;">
        <button class="new-task-btn hidden" onclick="location.reload()">🔄 Новая задача</button>
    </div>
</div>

<!-- Виджет 2: Вклады -->
<div class="widget">
    <div class="widget-header">
        <div class="icon deposit-icon">₽</div>
        <h2 class="widget-title">Банковские вклады</h2>
    </div>
    <p class="widget-description">Рассчитайте срок вклада с капитализацией процентов. В последней строке таблицы найдите недостающее значение.</p>

    <table>
        <thead>
            <tr>
                <th>Первоначальный вклад</th>
                <th>Годовая ставка</th>
                <th>Капитализация</th>
                <th>Срок (лет)</th>
                <th>Итоговая сумма</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= number_format($depositData2['initial'], 0, ',', ' ') ?> ₽</td>
                <td><?= $depositData2['rate'] ?>%</td>
                <td><?= $depositData2['capitalization'] ? 'Да' : 'Нет' ?></td>
                <td><?= $depositData2['years'] ?></td>
                <td><?= number_format($depositData2['total'], 0, ',', ' ') ?> ₽</td>
            </tr>
            <tr>
                <td><?= number_format(rand(15000, 60000), 0, ',', ' ') ?> ₽</td>
                <td><?= rand(4, 9) ?>%</td>
                <td><?= rand(0, 1) ? 'Да' : 'Нет' ?></td>
                <td><?= rand(2, 7) ?></td>
                <td><?= number_format(rand(20000, 90000), 0, ',', ' ') ?> ₽</td>
            </tr>
            <tr>
                <td><?= number_format($depositData2['initialToFind'], 0, ',', ' ') ?> ₽</td>
                <td><?= $depositData2['rateToFind'] ?>%</td>
                <td>Да</td>
                <td class="highlight" data-answer="<?= $depositData2['yearsToFind'] ?>">?</td>
                <td><?= number_format($depositData2['totalToFind'], 0, ',', ' ') ?> ₽</td>
            </tr>
        </tbody>
    </table>

    <div class="input-group">
        <input type="number" min="1" max="30" placeholder="Введите срок в годах" class="answer-input">
        <button class="check-btn" data-answer="<?= $depositData2['yearsToFind'] ?>">Проверить</button>
    </div>
    <div class="result"></div>
    <div style="padding: 1rem 2rem; text-align: center;">
        <button class="new-task-btn hidden" onclick="location.reload()">🔄 Новая задача</button>
    </div>
</div>