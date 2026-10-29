<?php
declare(strict_types=1);

// Run: php tests/calculation_test.php
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/functions.php';

$failures = 0;
function check(string $name, $actual, $expected): void
{
    global $failures;
    $ok = $actual === $expected;
    echo ($ok ? 'PASS' : 'FAIL') . " $name" . ($ok ? '' : ' => ' . var_export($actual, true) . ' (expected ' . var_export($expected, true) . ')') . PHP_EOL;
    $failures += $ok ? 0 : 1;
}

// calculateMinimumGrade
$r = calculateMinimumGrade(3.0, 3.0, 3.0);
check('3/3/3 required', $r['required'], 3.0);
check('3/3/3 status', $r['status'], 'reachable');
check('5/5/5 status', calculateMinimumGrade(5, 5, 5)['status'], 'approved');
check('5/5/5 required', calculateMinimumGrade(5, 5, 5)['required'], 0.0);
check('1/1/1 status', calculateMinimumGrade(1, 1, 1)['status'], 'unreachable');
check('0/0/0 status', calculateMinimumGrade(0, 0, 0)['status'], 'unreachable');
check('5/5/0 boundary required', calculateMinimumGrade(5, 5, 0)['required'], 2.5);
check('4/4/4 required', calculateMinimumGrade(4, 4, 4)['required'], 1.5);
check('2/2/1 needs exactly 5.0', calculateMinimumGrade(2, 2, 1)['required'], 5.0);
check('2/2/1 status', calculateMinimumGrade(2, 2, 1)['status'], 'reachable');
check('2/2/0.9 just over 5.0', calculateMinimumGrade(2, 2, 0.9)['status'], 'unreachable');
check('1.0/1.0/1.0 accumulated', calculateMinimumGrade(1, 1, 1)['accumulated'], 0.6);
check('5/5/5 approved even with 0', calculateMinimumGrade(5, 5, 5)['status'], 'approved');
check('4/3/2 required', calculateMinimumGrade(4.0, 3.0, 2.0)['required'], 3.0);

// parseGrade
check('parse "3,5"', parseGrade('3,5')[0], 3.5);
check('parse "3.55"', parseGrade('3.55')[0], 3.55);
check('parse empty', parseGrade('')[1] !== null, true);
check('parse negative', parseGrade('-1')[1] !== null, true);
check('parse > max', parseGrade('5.1')[1] !== null, true);
check('parse text', parseGrade('abc')[1] !== null, true);
check('parse exponent', parseGrade('1e1')[1] !== null, true);
check('parse xss', parseGrade('<script>')[1] !== null, true);
check('parse 3 decimals', parseGrade('3.555')[1] !== null, true);
check('parse array', parseGrade(['3'])[1] !== null, true);

exit($failures > 0 ? 1 : 0);
