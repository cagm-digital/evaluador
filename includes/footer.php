        <footer class="app-footer mt-4 pb-4">
            <div class="footer-card glass text-center">
                <p class="footer-title mb-3">
                    <span class="material-symbols-outlined" aria-hidden="true">code</span>
                    Especialidad <?= e(SPECIALTY) ?> - <?= e(PROGRAM) ?>
                </p>
                <ul class="footer-chips list-unstyled mb-0">
                    <li><span class="material-symbols-outlined" aria-hidden="true">school</span> Docente: <?= e(TEACHER) ?></li>
                    <li><span class="material-symbols-outlined" aria-hidden="true">copyright</span> <?= date('Y') ?> <?= e(INSTITUTION_NAME) ?></li>
                </ul>
            </div>
        </footer>
    </div><!-- /.app-content -->
</div><!-- /.app-shell -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
