<?php
/** @var array{accumulated: float, required: float, status: string, grades: array<int, float>} $result */
$weights = PERIOD_WEIGHTS;
$isReachable = $result['status'] === 'reachable';
$total = $result['accumulated'] + $result['required'] * $weights[3];
?>
<div class="result-enter">
    <div class="table-responsive">
        <table class="table result-table align-middle mb-0">
            <caption>Desglose por período</caption>
            <thead>
                <tr>
                    <th scope="col">Período</th>
                    <th scope="col" class="text-end">Nota</th>
                    <th scope="col" class="text-end">Peso</th>
                    <th scope="col" class="text-end">Aporte</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result['grades'] as $i => $grade): ?>
                    <tr>
                        <th scope="row">Período <?= $i + 1 ?></th>
                        <td class="text-end"><?= fmt($grade) ?></td>
                        <td class="text-end"><?= (int) round($weights[$i] * 100) ?>%</td>
                        <td class="text-end"><?= fmt($grade * $weights[$i]) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="row-target">
                    <th scope="row">Período 4</th>
                    <?php if ($isReachable): ?>
                        <td class="text-end"><?= fmt($result['required']) ?> (mínima)</td>
                        <td class="text-end"><?= (int) round($weights[3] * 100) ?>%</td>
                        <td class="text-end"><?= fmt($result['required'] * $weights[3]) ?></td>
                    <?php else: ?>
                        <td class="text-end"><?= $result['status'] === 'approved' ? 'Cualquiera' : 'No alcanzable' ?></td>
                        <td class="text-end"><?= (int) round($weights[3] * 100) ?>%</td>
                        <td class="text-end">&mdash;</td>
                    <?php endif; ?>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th scope="row" colspan="3">
                        <?= $isReachable ? 'Total con la nota mínima' : 'Acumulado períodos 1 a 3 (60%)' ?>
                    </th>
                    <td class="text-end"><?= fmt($isReachable ? $total : $result['accumulated']) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <button type="button" class="btn btn-outline-secondary mt-3" data-action="recalculate">
        <span class="material-symbols-outlined" aria-hidden="true">restart_alt</span> Calcular de nuevo
    </button>
</div>
