<?php

use app\core\Application;

define('ROOT', dirname(__DIR__));

require_once ROOT.'/vendor/autoload.php';

$app = new Application();

require_once ROOT.'/routes/web.php';

$app->run();
