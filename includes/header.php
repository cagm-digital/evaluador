<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(APP_NAME) ?> | <?= e(INSTITUTION_NAME) ?></title>
    <meta name="description" content="Calcula la nota mínima que necesitas en el período 4 para aprobar.">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= e(asset('img/escudo-192.png')) ?>">
    <link rel="apple-touch-icon" href="<?= e(asset('img/escudo-192.png')) ?>">

    <script nonce="<?= e(cspNonce()) ?>">
        // Apply saved/system theme before first paint to avoid a flash.
        (function () {
            var theme = 'light';
            try {
                theme = localStorage.getItem('theme')
                    || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            } catch (e) {}
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=block">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= e(asset('css/theme.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/styles.css')) ?>">
</head>
<body data-bs-spy="scroll" data-bs-target="#side-nav" data-bs-root-margin="0px 0px -75% 0px">
<a class="visually-hidden-focusable skip-link" href="#main-content">Saltar al contenido</a>

<header class="app-bar glass">
    <div class="d-flex align-items-center gap-2 gap-md-3 px-3 py-2">
        <button type="button" class="icon-btn d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#sidebar"
                aria-controls="sidebar" aria-label="Abrir menú de navegación">
            <span class="material-symbols-outlined" aria-hidden="true">menu</span>
        </button>
        <img src="<?= e(asset('img/escudo-192.png')) ?>" width="80" height="80" class="app-crest"
             alt="Escudo del <?= e(INSTITUTION_NAME) ?>">
        <div class="flex-grow-1 min-w-0">
            <p class="app-name mb-0 lh-sm"><?= e(APP_NAME) ?></p>
            <p class="mb-0 small text-body-secondary lh-sm d-none d-sm-block"><?= e(INSTITUTION_NAME) ?></p>
        </div>
        <button type="button" class="icon-btn" id="theme-toggle" aria-label="Cambiar a modo oscuro">
            <span class="material-symbols-outlined" aria-hidden="true">dark_mode</span>
        </button>
    </div>
</header>

<div class="app-shell">
    <aside class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebar-title">
        <div class="sidebar-inner">
            <div class="offcanvas-header d-lg-none">
                <p class="h6 mb-0" id="sidebar-title">Menú</p>
                <button type="button" class="icon-btn" data-bs-dismiss="offcanvas" data-bs-target="#sidebar"
                        aria-label="Cerrar menú">
                    <span class="material-symbols-outlined" aria-hidden="true">close</span>
                </button>
            </div>
            <nav id="side-nav" class="nav flex-column gap-1 p-3" aria-label="Secciones">
                <a class="nav-link" href="#bienvenida">
                    <span class="material-symbols-outlined" aria-hidden="true">home</span> Inicio
                </a>
                <a class="nav-link" href="#calculadora">
                    <span class="material-symbols-outlined" aria-hidden="true">calculate</span> Calculadora
                </a>
                <a class="nav-link" href="#resultado">
                    <span class="material-symbols-outlined" aria-hidden="true">fact_check</span> Resultado
                </a>
            </nav>
        </div>
    </aside>

    <div class="app-content">
