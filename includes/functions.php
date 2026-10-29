<?php
declare(strict_types=1);

/**
 * Escape a value for safe HTML output.
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Build a path relative to the app root (works in subfolders on shared hosting).
 * Appends the file modification time so long browser caching never serves stale assets.
 */
function asset(string $path): string
{
    $relative = 'assets/' . ltrim($path, '/');
    $file = dirname(__DIR__) . '/' . $relative;
    return is_file($file) ? $relative . '?v=' . filemtime($file) : $relative;
}

/**
 * Per-request nonce that authorizes the inline theme script under the CSP.
 */
function cspNonce(): string
{
    static $nonce = null;
    if ($nonce === null) {
        $nonce = base64_encode(random_bytes(16));
    }
    return $nonce;
}

/**
 * Send security headers for HTML responses (must run before any output).
 */
function sendSecurityHeaders(): void
{
    $csp = [
        "default-src 'self'",
        "script-src 'self' 'nonce-" . cspNonce() . "' https://cdn.jsdelivr.net",
        "style-src 'self' https://cdn.jsdelivr.net https://fonts.googleapis.com",
        "font-src 'self' https://fonts.gstatic.com",
        "img-src 'self' data:",
        "connect-src 'self'",
        "object-src 'none'",
        "base-uri 'self'",
        "form-action 'self'",
        "frame-ancestors 'self'",
    ];
    header('Content-Security-Policy: ' . implode('; ', $csp));
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}

/**
 * Start a session with hardened cookie flags (must run before any output).
 */
function startSecureSession(): void
{
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfIsValid(?string $token): bool
{
    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Parse a grade typed by the user. Accepts "3", "3.5" or "3,5" (max 2 decimals).
 * Returns the float, or an error message in Spanish.
 *
 * @param  mixed $raw Untyped on purpose: keeps compatibility with PHP 7.4.
 * @return array{0: ?float, 1: ?string} [value, error]
 */
function parseGrade($raw): array
{
    $text = is_string($raw) ? trim($raw) : '';
    if ($text === '') {
        return [null, 'Ingresa una nota.'];
    }
    if (!preg_match('/^\d+([.,]\d{1,2})?$/', $text)) {
        return [null, 'Usa solo números, con punto o coma decimal (máximo 2 decimales).'];
    }
    $value = (float) str_replace(',', '.', $text);
    if ($value < MIN_GRADE || $value > MAX_GRADE) {
        return [null, sprintf('La nota debe estar entre %.1f y %.1f.', MIN_GRADE, MAX_GRADE)];
    }
    return [$value, null];
}

/**
 * Minimum grade needed in period 4 (weight 40%) to reach the passing grade.
 *
 * @return array{accumulated: float, required: float, status: string, grades: array<int, float>}
 *         status: "approved" | "reachable" | "unreachable"
 */
function calculateMinimumGrade(float $p1, float $p2, float $p3): array
{
    $accumulated = $p1 * PERIOD_WEIGHTS[0] + $p2 * PERIOD_WEIGHTS[1] + $p3 * PERIOD_WEIGHTS[2];
    $raw = (PASSING_GRADE - $accumulated) / PERIOD_WEIGHTS[3];
    $epsilon = 1e-9;

    if ($raw <= $epsilon) {
        $status = 'approved';
    } elseif ($raw > MAX_GRADE + $epsilon) {
        $status = 'unreachable';
    } else {
        $status = 'reachable';
    }

    return [
        'accumulated' => round($accumulated, 2),
        'required'    => round(max($raw, 0.0), 2),
        'status'      => $status,
        'grades'      => [$p1, $p2, $p3],
    ];
}

/**
 * Validate the posted form and compute the result.
 *
 * @param  array<string, mixed> $post
 * @return array{errors: array<string, string>, values: array<string, string>, result: ?array}
 */
function handleCalculation(array $post): array
{
    $errors = [];
    $values = [];
    $grades = [];

    if (!csrfIsValid($post['csrf_token'] ?? null)) {
        $errors['form'] = 'La sesión expiró. Recarga la página e inténtalo de nuevo.';
    }

    foreach (['p1', 'p2', 'p3'] as $field) {
        $raw = $post[$field] ?? '';
        $values[$field] = is_string($raw) ? trim($raw) : '';
        [$grades[$field], $error] = parseGrade($raw);
        if ($error !== null) {
            $errors[$field] = $error;
        }
    }

    $result = $errors === [] ? calculateMinimumGrade($grades['p1'], $grades['p2'], $grades['p3']) : null;

    return ['errors' => $errors, 'values' => $values, 'result' => $result];
}

/**
 * Render a result partial to a string (shared by the page and the JSON endpoint).
 *
 * @param array{accumulated: float, required: float, status: string, grades: array<int, float>} $result
 */
function renderPartial(string $partial, array $result): string
{
    ob_start();
    require __DIR__ . '/' . $partial . '.php';
    return (string) ob_get_clean();
}

/** Short summary announced to assistive tech (live region). */
function renderResult(array $result): string
{
    return renderPartial('result', $result);
}

/** Per-period breakdown table and actions (kept outside the live region). */
function renderBreakdown(array $result): string
{
    return renderPartial('breakdown', $result);
}

/** Format a grade with 2 decimals and a dot separator. */
function fmt(float $value): string
{
    return number_format($value, 2, '.', '');
}
