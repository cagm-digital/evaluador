// Expose the current section to assistive tech (scrollspy only toggles a CSS class).
document.addEventListener('activate.bs.scrollspy', function (event) {
    document.querySelectorAll('#side-nav .nav-link').forEach(function (link) {
        link.removeAttribute('aria-current');
    });
    // relatedTarget is the nav link that became active.
    if (event.relatedTarget) event.relatedTarget.setAttribute('aria-current', 'location');
});

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
    // Limits come from includes/config.php through data attributes.
    var MIN = parseFloat(form.dataset.min), MAX = parseFloat(form.dataset.max);

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

    // Stepper: +/-0.1 within [MIN, MAX]. Keeps the user's decimal separator.
    var STEP = 0.1;

    function parseValue(text) {
        var t = (text || '').trim().replace(',', '.');
        return /^\d+(\.\d{1,2})?$/.test(t) ? parseFloat(t) : null;
    }

    function syncAria(input) {
        var value = parseValue(input.value);
        if (value === null) input.removeAttribute('aria-valuenow');
        else input.setAttribute('aria-valuenow', String(value));
    }

    function stepField(name, direction) {
        var input = document.getElementById(name);
        var raw = input.value.trim();
        var current = parseValue(raw);
        var next = current === null ? MIN : current + direction * STEP;
        next = Math.min(MAX, Math.max(MIN, Math.round(next * 100) / 100));
        var text = Math.round(next * 10) / 10 === next ? next.toFixed(1) : next.toFixed(2);
        if (raw.indexOf(',') !== -1) text = text.replace('.', ',');
        input.value = text;
        syncAria(input);
        setError(name, '');
    }

    fields.forEach(function (name) {
        var input = document.getElementById(name);
        syncAria(input);
        input.addEventListener('input', function () { syncAria(input); });
        input.addEventListener('keydown', function (event) {
            if (event.key !== 'ArrowUp' && event.key !== 'ArrowDown') return;
            event.preventDefault();
            stepField(name, event.key === 'ArrowUp' ? 1 : -1);
        });
    });

    form.querySelectorAll('.stepper-btn').forEach(function (button) {
        // Keep focus in the field while using the arrows with a mouse.
        button.addEventListener('mousedown', function (event) { event.preventDefault(); });
        button.addEventListener('click', function () {
            stepField(button.dataset.field, Number(button.dataset.step));
        });
    });

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

    // "Calculating" animation: a ticker of random digits and bouncing operators while the request runs.
    var CALC_MS = 900;
    var calculating = false;
    var tickTimer = null;
    var submitButton = form.querySelector('button[type="submit"]');
    var LOADING = '<div class="calc-loading">' +
        '<p class="mb-1">Calculando…</p>' +
        '<p class="result-value calc-ticker" aria-hidden="true">0.00</p>' +
        '<p class="calc-ops" aria-hidden="true"><span>+</span><span>&minus;</span><span>&times;</span><span>&divide;</span></p>' +
        '</div>';

    function prefersReducedMotion() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    function startCalculating() {
        calculating = true;
        submitButton.disabled = true;
        region.setAttribute('aria-busy', 'true');
        region.innerHTML = LOADING;
        detail.innerHTML = '';
        revealResult();
        var ticker = region.querySelector('.calc-ticker');
        if (prefersReducedMotion()) return;
        tickTimer = setInterval(function () {
            ticker.textContent = (MIN + Math.random() * (MAX - MIN + 1)).toFixed(2);
        }, 70);
    }

    function stopCalculating() {
        calculating = false;
        clearInterval(tickTimer);
        submitButton.disabled = false;
        region.removeAttribute('aria-busy');
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
        if (calculating) return;

        startCalculating();
        var request = fetch('calculate.php', {
            method: 'POST',
            body: new FormData(form),
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
            .then(function (response) {
                return response.json().then(function (data) { return { ok: response.ok, data: data }; });
            });
        // Keep the animation visible long enough to read, even when the server answers instantly.
        var pause = new Promise(function (resolve) { setTimeout(resolve, prefersReducedMotion() ? 0 : CALC_MS); });

        Promise.all([request, pause])
            .then(function (values) { return values[0]; })
            .then(function (res) {
                stopCalculating();
                if (!res.ok) {
                    region.innerHTML = EMPTY;
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
                stopCalculating();
                region.innerHTML = EMPTY;
                setFormError('No se pudo calcular. Revisa tu conexión e inténtalo de nuevo.');
            });
    });

    form.addEventListener('reset', function () {
        clearErrors();
        region.innerHTML = EMPTY;
        detail.innerHTML = '';
        fields.forEach(function (name) { document.getElementById(name).removeAttribute('aria-valuenow'); });
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
