<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Utilisateur;

class AuthController extends Controller
{
    private Utilisateur $utilisateurModel;

    public function __construct()
    {
        $this->utilisateurModel = new Utilisateur();
    }

 public function showLogin(): void
{
    if (!empty($_SESSION['utilisateur'])) {
        if ($_SESSION['utilisateur']['role'] === 'admin') {
            $this->redirect('/clients');
        } else {
            $this->redirect('/dettes');
        }
    }

    $this->view('auth/login', ['erreur' => $_SESSION['erreur_connexion'] ?? null]);
    unset($_SESSION['erreur_connexion']);
}

public function login(): void
{
    $email = $this->input('email');
    $motDePasse = $this->input('mot_de_passe');

    $utilisateur = $this->utilisateurModel->findByEmail($email);

    if (!$utilisateur || $motDePasse !== $utilisateur['mot_de_passe']) {
        $_SESSION['erreur_connexion'] = 'Email ou mot de passe incorrect.';
        $this->redirect('/connexion');
    }

    session_regenerate_id(true);
    $_SESSION['utilisateur'] = [
        'id'     => $utilisateur['id'],
        'nom'    => $utilisateur['nom'],
        'prenom' => $utilisateur['prenom'],
        'email'  => $utilisateur['email'],
        'role'   => $utilisateur['role'],
    ];

    if ($utilisateur['role'] === 'admin') {
        $this->redirect('/clients');
    } else {
        $this->redirect('/dettes'); 
    }
}
    public function logout(): void
    {
        $_SESSION = [];
        session_unset();
        session_destroy();
        $this->redirect('/connexion');
    }
}
