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

// Calculator: client-side validation, then server calculation via fetch.
(function () {
    var form = document.getElementById('calc-form');
    if (!form) return;

    var fields = ['p1', 'p2', 'p3'];
    var region = document.getElementById('result-region');
    var detail = document.getElementById('result-detail');
    var EMPTY = '<p class="text-body-secondary mb-0">Aquí verás la nota mínima del período 4.</p>';
    var formError = document.getElementById('form-error');
    var MIN = 0, MAX = 5;

    function validate(raw) {
        var text = (raw || '').trim();
        if (text === '') return 'Ingresa una nota.';
        if (!/^\d+([.,]\d{1,2})?$/.test(text)) return 'Usa solo números, con punto o coma decimal (máximo 2 decimales).';
        var value = parseFloat(text.replace(',', '.'));
        if (value < MIN || value > MAX) return 'La nota debe estar entre ' + MIN.toFixed(1) + ' y ' + MAX.toFixed(1) + '.';
        return '';
    }

    function setError(name, message) {
        var input = document.getElementById(name);
        var box = document.getElementById(name + '-error');
        box.textContent = message;
        input.classList.toggle('is-invalid', message !== '');
        if (message) input.setAttribute('aria-invalid', 'true');
        else input.removeAttribute('aria-invalid');
    }

    function setFormError(message) {
        formError.textContent = message;
        formError.hidden = !message;
    }

    function clearErrors() {
        fields.forEach(function (name) { setError(name, ''); });
        setFormError('');
    }

    function focusFirstInvalid() {
        var first = form.querySelector('.is-invalid');
        if (first) first.focus();
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        clearErrors();

        var hasErrors = false;
        fields.forEach(function (name) {
            var message = validate(document.getElementById(name).value);
            if (message) { setError(name, message); hasErrors = true; }
        });
        if (hasErrors) { focusFirstInvalid(); return; }

        fetch('calculate.php', {
            method: 'POST',
            body: new FormData(form),
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
            .then(function (response) {
                return response.json().then(function (data) { return { ok: response.ok, data: data }; });
            })
            .then(function (res) {
                if (!res.ok) {
                    var errors = res.data.errors || {};
                    fields.forEach(function (name) { if (errors[name]) setError(name, errors[name]); });
                    if (errors.form) setFormError(errors.form);
                    focusFirstInvalid();
                    return;
                }
                region.innerHTML = res.data.html;
                detail.innerHTML = res.data.detail;
                revealResult();
            })
            .catch(function () {
                setFormError('No se pudo calcular. Revisa tu conexión e inténtalo de nuevo.');
            });
    });

    form.addEventListener('reset', function () {
        clearErrors();
        region.innerHTML = EMPTY;
        detail.innerHTML = '';
    });

    // On narrow screens the result sits below the form: bring it into view.
    function revealResult() {
        if (!window.matchMedia('(max-width: 991.98px)').matches) return;
        var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        document.getElementById('resultado').scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'nearest' });
    }

    // "Calcular de nuevo" lives inside injected HTML, so delegate the click.
    detail.addEventListener('click', function (event) {
        if (!event.target.closest('[data-action="recalculate"]')) return;
        form.reset();
        document.getElementById('p1').focus();
    });
})();
