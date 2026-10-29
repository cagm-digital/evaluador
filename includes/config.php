<?php
declare(strict_types=1);

// Never show PHP errors to visitors in production. Set to true only on a local machine.
const APP_DEBUG = false;
error_reporting(E_ALL);
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');

// Institutional data (edit here to change what the UI displays).
const INSTITUTION_NAME = 'Instituto Técnico Superior de Pereira';
const SPECIALTY        = 'Informática';
const PROGRAM          = 'Diseño y Desarrollo de Sitios Web';
const TEACHER          = 'César Adolfo González Marín';
const APP_NAME         = 'Evaluador';

// Grading rules.
const PASSING_GRADE = 3.0;
const MIN_GRADE     = 1.0;
const MAX_GRADE     = 5.0;

// Period weights (periods 1-3 = 20% each, period 4 = 40%).
const PERIOD_WEIGHTS = [0.2, 0.2, 0.2, 0.4];
