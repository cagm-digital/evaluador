<?php
declare(strict_types=1);

require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

startSecureSession();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['ok' => false, 'errors' => ['form' => 'Método no permitido.']]);
    exit;
}

$outcome = handleCalculation($_POST);

if ($outcome['errors'] !== []) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'errors' => $outcome['errors']], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'ok'     => true,
    'result' => $outcome['result'],
    'html'   => renderResult($outcome['result']),
    'detail' => renderBreakdown($outcome['result']),
], JSON_UNESCAPED_UNICODE);
