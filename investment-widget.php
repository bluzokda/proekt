<?php 
global $investmentData;

// Генерируем данные только для одного вопроса (ETF Bonds)
$initial = $investmentData['initialToFind'] ?? rand(50000, 200000);
$annualReturn = $investmentData['annualReturnToFind'] ?? rand(5, 20);
$years = $investmentData['yearsToFind'] ?? rand(1, 10);
$finalValue = $investmentData['finalValueToFind'] ?? 
              round($initial * pow(1 + ($annualReturn/100), $years));
?>

<!-- Виджет: Инвестиционный портфель -->
<div class="widget">
    <div class="widget-header">
        <h2 class="widget-title">Инвестиционный портфель</h2>
    </div>
    <p class="widget-description">Проанализируйте данные по инвестициям и вычислите итоговую стоимость портфеля для последней строки таблицы.</p>

    <table>
        <thead>
            <tr>
                <th>Актив</th>
                <th>Начальная стоимость</th>
                <th>Среднегодовая доходность</th>
                <th>Срок (лет)</th>
                <th>Итоговая стоимость</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>ETF Global</td>
                <td><?= number_format(rand(100000, 200000), 0, ',', ' ') ?> ₽</td>
                <td><?= rand(5, 15) ?>%</td>
                <td><?= rand(3, 8) ?></td>
                <td><?= number_format(rand(150000, 300000), 0, ',', ' ') ?> ₽</td>
            </tr>
            <tr>
                <td>ETF Tech</td>
                <td><?= number_format(rand(80000, 180000), 0, ',', ' ') ?> ₽</td>
                <td><?= rand(8, 20) ?>%</td>
                <td><?= rand(4, 10) ?></td>
                <td><?= number_format(rand(120000, 350000), 0, ',', ' ') ?> ₽</td>
            </tr>
            <tr>
                <td>ETF Bonds</td>
                <td><?= number_format($initial, 0, ',', ' ') ?> ₽</td>
                <td><?= $annualReturn ?>%</td>
                <td><?= $years ?></td>
                <td class="highlight" data-answer="<?= $finalValue ?>">?</td>
            </tr>
        </tbody>
    </table>

    <div class="input-group">
        <span class="input-label">Итоговая стоимость:</span>
        <input type="number" min="100000" max="1000000" 
               placeholder="Введите сумму" 
               class="answer-input">
        <button class="check-btn" data-answer="<?= $finalValue ?>">Проверить</button>
    </div>
    <div class="result"></div>
    <div style="padding: 1rem 2rem; text-align: center;">
        <button class="new-task-btn hidden" onclick="location.reload()">🔄 Новая задача</button>
    </div>
</div>