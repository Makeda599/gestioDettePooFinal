<?php

session_start();

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/config.php';


define('WEBROOT', str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']));

$router = require dirname(__DIR__) . '/routes/web.php';

$uri = $_SERVER['REQUEST_URI'];
if (WEBROOT !== '' && str_starts_with($uri, WEBROOT)) {
    $uri = substr($uri, strlen(WEBROOT));
}

$methode = $_SERVER['REQUEST_METHOD'];

$router->dispatch($uri, $methode);
