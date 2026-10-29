// Close the mobile drawer after choosing a section.
(function () {
    var drawer = document.getElementById('sidebar');
    if (!drawer) return;
    drawer.querySelectorAll('.nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            var instance = bootstrap.Offcanvas.getInstance(drawer);
            if (instance) instance.hide();
        });
    });
})();

// Theme toggle. The initial theme is applied by an inline script in header.php.
(function () {
    var root = document.documentElement;
    var button = document.getElementById('theme-toggle');
    if (!button) return;
    var icon = button.querySelector('.material-symbols-outlined');

    function render(theme) {
        var isDark = theme === 'dark';
        icon.textContent = isDark ? 'light_mode' : 'dark_mode';
        button.setAttribute('aria-label', isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
    }

    render(root.getAttribute('data-bs-theme'));

    button.addEventListener('click', function () {
        var next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        root.setAttribute('data-bs-theme', next);
        try { localStorage.setItem('theme', next); } catch (e) {}
        render(next);
    });
})();
