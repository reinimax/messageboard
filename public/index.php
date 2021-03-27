<?php

use app\core\Application;

define('ROOT', dirname(__DIR__));

/**
 * This constant holds the URL of the site. This is useful because on production or when changing servers, I have
 * to change the URL only in one place (for example, the URL may be used in Links, in emails ...)
 */
define('URL', 'http://messageboard.loc');

/**
 * Email constants
 */
define('EMAIL_ADDR', 'info@messageboard.loc');
define('EMAIL_NAME', 'reinimax\' Messageboard');

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
