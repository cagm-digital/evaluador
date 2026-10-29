<?php
/** @var array{accumulated: float, required: float, status: string, grades: array<int, float>} $result */
$labels = [
    'approved'    => ['Ya aprobaste', 'check_circle'],
    'reachable'   => ['Aún puedes aprobar', 'info'],
    'unreachable' => ['Matemáticamente no alcanzable', 'cancel'],
];
[$statusLabel, $statusIcon] = $labels[$result['status']];
?>
<div class="result-enter">
    <p class="result-status" data-status="<?= e($result['status']) ?>">
        <span class="material-symbols-outlined" aria-hidden="true"><?= e($statusIcon) ?></span>
        <?= e($statusLabel) ?>
    </p>

    <?php if ($result['status'] === 'reachable'): ?>
        <p class="mb-1">Necesitas al menos esta nota en el período 4:</p>
        <p class="result-value"><?= fmt($result['required']) ?></p>
    <?php elseif ($result['status'] === 'approved'): ?>
        <p>Con tus notas acumuladas ya alcanzas <?= number_format(PASSING_GRADE, 1) ?>, incluso si sacas 0 en el período 4.</p>
    <?php else: ?>
        <p>Incluso con <?= number_format(MAX_GRADE, 1) ?> en el período 4 no llegarías a <?= number_format(PASSING_GRADE, 1) ?>.</p>
    <?php endif; ?>
</div>
