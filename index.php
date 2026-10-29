<?php
declare(strict_types=1);

require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

startSecureSession();
sendSecurityHeaders();

// No-JS fallback: the form posts to this same page.
$outcome = ['errors' => [], 'values' => ['p1' => '', 'p2' => '', 'p3' => ''], 'result' => null];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $outcome = handleCalculation($_POST);
}
$errors = $outcome['errors'];
$values = $outcome['values'];

require __DIR__ . '/includes/header.php';
?>
<main id="main-content" class="container-fluid py-4" tabindex="-1">
    <div class="row g-3">
        <section id="bienvenida" class="col-12 col-xl-8" aria-labelledby="welcome-title">
            <div class="glass bento-card h-100">
                <h1 class="h3" id="welcome-title"><?= e(APP_NAME) ?> de próxima nota</h1>
                <p class="mb-0">Ingresa tus notas de los períodos 1, 2 y 3 para conocer la nota mínima que necesitas en el período 4 y conseguir aprobar con nota de <?= number_format(PASSING_GRADE, 1) ?>.</p>
            </div>
        </section>

        <section class="col-12 col-md-6 col-xl-4" aria-labelledby="summary-title">
            <div class="glass bento-card h-100">
                <h2 class="h6 text-body-secondary" id="summary-title">Peso por período</h2>
                <ul class="list-unstyled d-flex flex-wrap gap-2 mb-0">
                    <?php foreach (PERIOD_WEIGHTS as $i => $weight): ?>
                        <li><span class="badge-weight">P<?= $i + 1 ?> · <?= (int) round($weight * 100) ?>%</span></li>
                    <?php endforeach; ?>
                </ul>
                <p class="small text-body-secondary mt-3 mb-0">Se aprueba con <?= number_format(PASSING_GRADE, 1) ?> o más.</p>
            </div>
        </section>

        <section id="calculadora" class="col-12 col-lg-7" aria-labelledby="calc-title">
            <div class="glass bento-card h-100">
                <h2 class="h5" id="calc-title">Calculadora</h2>
                <form id="calc-form" method="post" action="index.php#resultado" novalidate
                      data-min="<?= MIN_GRADE ?>" data-max="<?= MAX_GRADE ?>">
                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

                    <div id="form-error" class="alert alert-danger" role="alert" <?= isset($errors['form']) ? '' : 'hidden' ?>>
                        <?= e($errors['form'] ?? '') ?>
                    </div>

                    <div class="row g-3">
                        <?php foreach (['p1' => 'Período 1', 'p2' => 'Período 2', 'p3' => 'Período 3'] as $field => $label): ?>
                            <div class="col-12 col-sm-4">
                                <label for="<?= $field ?>" class="form-label"><?= $label ?> (20%)</label>
                                <?php /* type=text keeps "3,5" intact in every browser/locale (type=number drops it in some),
                                         so the arrows are custom buttons and the field is exposed as a spinbutton. */ ?>
                                <div class="stepper">
                                    <input type="text" class="form-control<?= isset($errors[$field]) ? ' is-invalid' : '' ?>"
                                           id="<?= $field ?>" name="<?= $field ?>"
                                           value="<?= e($values[$field]) ?>"
                                           role="spinbutton" aria-valuemin="<?= MIN_GRADE ?>" aria-valuemax="<?= MAX_GRADE ?>"
                                           inputmode="decimal" autocomplete="off" maxlength="5" required
                                           aria-describedby="<?= $field ?>-help <?= $field ?>-error"
                                           <?= isset($errors[$field]) ? 'aria-invalid="true"' : '' ?>>
                                    <?php /* Mouse/touch only: keyboard and screen-reader users use ArrowUp/ArrowDown on the field. */ ?>
                                    <div class="stepper-btns" aria-hidden="true">
                                        <button type="button" tabindex="-1" class="stepper-btn" data-field="<?= $field ?>" data-step="1">
                                            <span class="material-symbols-outlined">keyboard_arrow_up</span>
                                        </button>
                                        <button type="button" tabindex="-1" class="stepper-btn" data-field="<?= $field ?>" data-step="-1">
                                            <span class="material-symbols-outlined">keyboard_arrow_down</span>
                                        </button>
                                    </div>
                                </div>
                                <div id="<?= $field ?>-help" class="form-text">Entre <?= number_format(MIN_GRADE, 1) ?> y <?= number_format(MAX_GRADE, 1) ?></div>
                                <div id="<?= $field ?>-error" class="invalid-feedback"><?= e($errors[$field] ?? '') ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <span class="material-symbols-outlined" aria-hidden="true">calculate</span> Calcular
                        </button>
                        <button type="reset" class="btn btn-outline-secondary btn-lg" id="calc-reset">Limpiar</button>
                    </div>
                </form>
            </div>
        </section>

        <section id="resultado" class="col-12 col-lg-5" aria-labelledby="result-title">
            <div class="glass bento-card h-100">
                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <h2 class="h5 mb-0" id="result-title">Resultado</h2>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                            data-bs-target="#formula-modal">
                        <span class="material-symbols-outlined" aria-hidden="true">help</span> ¿Cómo se calcula?
                    </button>
                </div>
                <div id="result-region" role="status" aria-live="polite">
                    <?php if ($outcome['result'] !== null): ?>
                        <?= renderResult($outcome['result']) ?>
                    <?php else: ?>
                        <p class="text-body-secondary mb-0">Aquí verás la nota mínima del período 4.</p>
                    <?php endif; ?>
                </div>
                <div id="result-detail">
                    <?= $outcome['result'] !== null ? renderBreakdown($outcome['result']) : '' ?>
                </div>
            </div>
        </section>

    </div>
</main>

<?php
$weightText = static fn (float $w): string => rtrim(rtrim(number_format($w, 2, '.', ''), '0'), '.');
$example = calculateMinimumGrade(3.5, 3.0, 4.0);
$passing = number_format(PASSING_GRADE, 1);
?>
<div class="modal fade" id="formula-modal" tabindex="-1" aria-labelledby="formula-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="formula-title">¿Cómo se calcula?</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>Cada período aporta un porcentaje de la nota final. Los períodos 1, 2 y 3 valen <?= (int) round(PERIOD_WEIGHTS[0] * 100) ?>% cada uno y el período 4 vale <?= (int) round(PERIOD_WEIGHTS[3] * 100) ?>%.</p>
                <p class="formula">
                    N4 = (<?= $passing ?> &minus; <?= $weightText(PERIOD_WEIGHTS[0]) ?> &times; (N1 + N2 + N3)) &divide; <?= $weightText(PERIOD_WEIGHTS[3]) ?>
                </p>
                <p class="mb-1"><strong>Ejemplo</strong> con 3.5, 3.0 y 4.0:</p>
                <ul>
                    <li>Acumulado: <?= $weightText(PERIOD_WEIGHTS[0]) ?> &times; 10.5 = <?= fmt($example['accumulated']) ?></li>
                    <li>Mínima en el período 4: (<?= $passing ?> &minus; <?= fmt($example['accumulated']) ?>) &divide; <?= $weightText(PERIOD_WEIGHTS[3]) ?> = <?= fmt($example['required']) ?></li>
                </ul>
                <p class="mb-0">Si el resultado es <?= number_format(MIN_GRADE, 1) ?> o menos, ya aprobaste, porque esa es la nota más baja posible. Si supera <?= number_format(MAX_GRADE, 1) ?>, no es posible aprobar.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
