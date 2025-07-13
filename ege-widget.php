<?php global $egeBasicData, $egeAdvancedData; ?>

<!-- Базовый уровень -->
<div class="widget">
    <div class="widget-header">
        <div class="widget-title">🎓 Базовый уровень</div>
    </div>
    <div class="widget-description"><?= htmlspecialchars_decode($egeBasicData['question']) ?></div>
    <table>
        <thead>
            <tr>
                <th>Сумма вклада</th>
                <th>Ставка %</th>
                <th>Капитализация</th>
                <th>Срок (лет)</th>
                <th>Итоговая сумма</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= number_format($egeBasicData['amount'], 0, ',', ' ') ?> ₽</td>
                <td><?= $egeBasicData['rate'] ?>%</td>
                <td><?= $egeBasicData['capitalization'] ?></td>
                <td><?= $egeBasicData['years'] ?></td>
                <td class="highlight" data-answer="<?= $egeBasicData['finalAmount'] ?>">?</td>
            </tr>
        </tbody>
    </table>
    <div class="input-group">
        <input type="number" min="1" placeholder="Введите ответ" class="answer-input">
        <button class="check-btn" data-answer="<?= $egeBasicData['finalAmount'] ?>" data-level="basic">Проверить</button>
    </div>
    <div class="result"></div>
    <div style="padding: 1rem 2rem; text-align: center;">
        <button class="new-task-btn hidden" onclick="location.reload()">🔄 Новая задача</button>
    </div>
</div>

<!-- Повышенный уровень -->
<div class="widget">
    <div class="widget-header">
        <div class="widget-title">🎓 Повышенный уровень</div>
    </div>
    <div class="widget-description"><?= htmlspecialchars_decode($egeAdvancedData['question']) ?></div>
    <table>
        <thead>
            <tr>
                <th>Сумма кредита</th>
                <th>Процентная ставка</th>
                <th>Срок (лет)</th>
                <th>Ежемесячный платёж</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= number_format($egeAdvancedData['amount'], 0, ',', ' ') ?> ₽</td>
                <td><?= $egeAdvancedData['rate'] ?>%</td>
                <td><?= $egeAdvancedData['years'] ?></td>
                <td class="highlight" data-answer="<?= $egeAdvancedData['totalPayment'] ?>">?</td>
            </tr>
        </tbody>
    </table>
    <div class="input-group">
        <input type="number" min="1" placeholder="Введите ответ" class="answer-input">
        <button class="check-btn" data-answer="<?= $egeAdvancedData['totalPayment'] ?>" data-level="advanced">Проверить</button>
    </div>
    <div class="result"></div>
    <div style="padding: 1rem 2rem; text-align: center;">
        <button class="new-task-btn hidden" onclick="location.reload()">🔄 Новая задача</button>
    </div>
</div>