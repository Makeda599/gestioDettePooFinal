<?php

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\ClientController;
use App\Controllers\DetteController;

$router = new Router();

$router->get('/', [AuthController::class, 'showLogin']);
$router->get('/connexion', [AuthController::class, 'showLogin']);
$router->post('/connexion', [AuthController::class, 'login']);
$router->get('/deconnexion', [AuthController::class, 'logout']);

$router->get('/clients', [ClientController::class, 'index']);
$router->get('/clients/creer', [ClientController::class, 'create']);
$router->post('/clients/creer', [ClientController::class, 'store']);
$router->get('/clients/modifier', [ClientController::class, 'edit']);
$router->post('/clients/modifier', [ClientController::class, 'update']);
$router->post('/clients/supprimer', [ClientController::class, 'delete']);

$router->get('/dettes', [DetteController::class, 'index']);
$router->get('/dettes/creer', [DetteController::class, 'create']);
$router->post('/dettes/creer', [DetteController::class, 'store']);
$router->get('/dettes/modifier', [DetteController::class, 'edit']);
$router->post('/dettes/modifier', [DetteController::class, 'update']);
$router->post('/dettes/solder', [DetteController::class, 'solder']);
$router->post('/dettes/supprimer', [DetteController::class, 'delete']);

return $router;
