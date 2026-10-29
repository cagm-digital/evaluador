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
        <p>Con tus notas acumuladas ya alcanzas <?= number_format(PASSING_GRADE, 1) ?>, incluso si sacas <?= number_format(MIN_GRADE, 1) ?> en el período 4.</p>
    <?php else: ?>
        <?php
        $bestFinal = $result['accumulated'] + MAX_GRADE * PERIOD_WEIGHTS[3];
        $shortBy = PASSING_GRADE - $bestFinal;
        ?>
        <p class="mb-1">Para llegar a <?= number_format(PASSING_GRADE, 1) ?> necesitarías en el período 4:</p>
        <p class="result-value result-value-over"><?= fmt($result['required']) ?></p>
        <p>Pero la nota máxima es <?= number_format(MAX_GRADE, 1) ?>. Aun sacando <?= number_format(MAX_GRADE, 1) ?> tu nota final sería <strong><?= fmt($bestFinal) ?></strong>, es decir, te faltarían <strong><?= fmt($shortBy) ?></strong> para aprobar.</p>
    <?php endif; ?>
</div>
