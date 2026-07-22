<?php

namespace App\Core;

/**
 * Fournit le rendu des vues, les redirections et la protection des routes (RBAC).
 */
abstract class Controller
{
    protected function view(string $vue, array $donnees = []): void
    {
        extract($donnees);
        $cheminVue = dirname(__DIR__, 2) . "/views/{$vue}.php";

        if (!file_exists($cheminVue)) {
            http_response_code(404);
            require dirname(__DIR__, 2) . '/views/errors/404.php';
            return;
        }

        require $cheminVue;
    }

    protected function redirect(string $chemin): never
    {
        header("Location: {$chemin}");
        exit;
    }

    protected function requireRole(string ...$rolesAutorises): void
    {
        if (empty($_SESSION['utilisateur'])) {
            $this->redirect('/connexion');
        }

        if (!in_array($_SESSION['utilisateur']['role'], $rolesAutorises, true)) {
            http_response_code(403);
            die('Accès refusé : vous n\'avez pas les droits nécessaires.');
        }
    }

    protected function input(string $cle, $defaut = null)
    {
        $valeur = $_POST[$cle] ?? $_GET[$cle] ?? $defaut;
        return is_string($valeur) ? trim($valeur) : $valeur;
    }
}
