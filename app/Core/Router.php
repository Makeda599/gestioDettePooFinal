<?php

namespace App\Core;

/**
 * Associe une URL + une méthode HTTP à [Controller, action].
 */
class Router
{
    private array $routes = [];

    public function get(string $url, array $action): void
    {
        $this->routes['GET'][$this->normaliser($url)] = $action;
    }

    public function post(string $url, array $action): void
    {
        $this->routes['POST'][$this->normaliser($url)] = $action;
    }

    private function normaliser(string $url): string
    {
        $url = parse_url($url, PHP_URL_PATH) ?? '/';
        $url = rtrim($url, '/');
        return $url === '' ? '/' : $url;
    }

    public function dispatch(string $uri, string $methode): void
    {
        $uri = $this->normaliser($uri);
        $action = $this->routes[$methode][$uri] ?? null;

        if ($action === null) {
            http_response_code(404);
            require dirname(__DIR__, 2) . '/views/errors/404.php';
            return;
        }

        [$controllerClasse, $methodeControleur] = $action;
        $controller = new $controllerClasse();
        $controller->$methodeControleur();
    }
}
