<?php
declare(strict_types=1);

require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/header.php';
?>
<main id="main-content" class="container-fluid py-4" tabindex="-1">
    <div class="row g-3">
        <section id="bienvenida" class="col-12 col-xl-8" aria-labelledby="welcome-title">
            <div class="glass bento-card h-100">
                <h1 class="h3" id="welcome-title">Bienvenido a <?= e(APP_NAME) ?></h1>
                <p class="mb-0">Ingresa tus notas de los períodos 1, 2 y 3 para conocer la nota mínima que necesitas en el período 4 y aprobar con <?= number_format(PASSING_GRADE, 1) ?>.</p>
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
                <p class="text-body-secondary mb-0">El formulario llega en la Fase 4.</p>
            </div>
        </section>

        <section id="resultado" class="col-12 col-lg-5" aria-labelledby="result-title">
            <div class="glass bento-card h-100">
                <h2 class="h5" id="result-title">Resultado</h2>
                <p class="text-body-secondary mb-0">Aquí verás la nota mínima del período 4.</p>
            </div>
        </section>

        <section id="institucion" class="col-12" aria-labelledby="inst-title">
            <div class="glass bento-card">
                <h2 class="h5" id="inst-title"><?= e(INSTITUTION_NAME) ?></h2>
                <dl class="row mb-0">
                    <dt class="col-sm-3">Especialidad</dt>
                    <dd class="col-sm-9"><?= e(SPECIALTY) ?></dd>
                    <dt class="col-sm-3">Programa</dt>
                    <dd class="col-sm-9"><?= e(PROGRAM) ?></dd>
                    <dt class="col-sm-3">Docente</dt>
                    <dd class="col-sm-9 mb-0"><?= e(TEACHER) ?></dd>
                </dl>
            </div>
        </section>
    </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
