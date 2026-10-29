        <footer class="app-footer mt-4 py-4">
            <div class="container-fluid small text-body-secondary">
                <p class="mb-1">Especialidad: <?= e(SPECIALTY) ?> · Programa: <?= e(PROGRAM) ?></p>
                <p class="mb-0">Docente: <?= e(TEACHER) ?> · &copy; <?= date('Y') ?> <?= e(INSTITUTION_NAME) ?></p>
            </div>
        </footer>
    </div><!-- /.app-content -->
</div><!-- /.app-shell -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
