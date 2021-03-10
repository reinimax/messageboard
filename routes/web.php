<?php

// Here the routes for the application are registered
$app->registerGet('/', [app\controllers\PostController::class, 'index']);
$app->registerGet('/index', [app\controllers\PostController::class, 'index']);
$app->registerGet('/login', [app\controllers\HomeController::class, 'login']);
$app->registerPost('/login', [app\controllers\HomeController::class, 'login']);
$app->registerGet('/logout', [app\controllers\HomeController::class, 'logout']);
$app->registerGet('/register', [app\controllers\HomeController::class, 'register']);
$app->registerPost('/register', [app\controllers\HomeController::class, 'register']);

$app->registerGet('/create', [app\controllers\PostController::class, 'create']);
$app->registerPost('/create', [app\controllers\PostController::class, 'save']);
$app->registerGet('/show', [app\controllers\PostController::class, 'show']);
$app->registerGet('/edit', [app\controllers\PostController::class, 'edit']);
$app->registerPut('/edit', [app\controllers\PostController::class, 'update']);
$app->registerDelete('/delete', [app\controllers\PostController::class, 'delete']);

$app->registerGet('/search', [app\controllers\PostController::class, 'search']);
$app->registerGet('/createtag', [app\controllers\PostController::class, 'createtag']);
$app->registerPost('/createtag', [app\controllers\PostController::class, 'createtag']);
