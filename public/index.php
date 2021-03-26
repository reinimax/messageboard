<?php

use app\core\Application;

define('ROOT', dirname(__DIR__));
/**
 * Set this variable to false for development in order to enable more detailed error messages,
 * and to true for production to enable more general error messages and disable PHP error reporting
 */
define('PRODUCTION', false);

if (PRODUCTION === false) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} elseif (PRODUCTION === true) {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
}

require_once ROOT.'/vendor/autoload.php';

$app = new Application();

require_once ROOT.'/routes/web.php';

$app->run();
