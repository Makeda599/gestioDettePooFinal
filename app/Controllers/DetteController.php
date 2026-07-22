<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Dette;
use App\Models\Utilisateur;

class DetteController extends Controller
{
    private Dette $detteModel;
    private Utilisateur $utilisateurModel;

    public function __construct()
    {
        $this->detteModel = new Dette();
        $this->utilisateurModel = new Utilisateur();
    }

    public function index(): void
    {
        $utilisateur = $_SESSION['utilisateur'] ?? null;

        if (!$utilisateur) {
            $this->redirect('/connexion');
            return;
        }

        $role = $utilisateur['role'] ?? 'client';
        $userIdConnecte = $utilisateur['id'] ?? null;

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        $limit = 3;
        $offset = ($page - 1) * $limit;

        if ($role === 'client') {
            $clientId = $userIdConnecte;
            $estVueClientConnecte = true;
        } else {
            $clientId = isset($_GET['client_id']) ? (int)$_GET['client_id'] : null;
            $estVueClientConnecte = false;
        }

        $etat = trim($_GET['etat'] ?? '');

        $totalDettes = $this->detteModel->countAll($clientId, $etat);
        $totalPages = (int) ceil($totalDettes / $limit);
        if ($totalPages < 1) $totalPages = 1;

        $dettes = $this->detteModel->findAllPaginated($clientId, $etat, $limit, $offset);
        $clientFiltre = $clientId ? $this->utilisateurModel->findById($clientId) : null;

        $this->view('dette/index', [
            'dettes'               => $dettes,
            'pageCourante'         => $page,
            'totalPages'           => $totalPages,
            'estVueDetail'         => $clientId !== null,
            'clientFiltre'         => $clientFiltre,
            'estClientConnecte'    => $estVueClientConnecte
        ]);
    }

    
    public function create(): void
    {
        $this->requireRole('admin');

        $clients = $this->utilisateurModel->findAllClients();
        $clientId = $this->input('client_id') ?? $this->input('client');

        $this->view('dette/create', [
            'clients' => $clients,
            'erreurs' => [],
            'ancien'  => ['utilisateur_id' => $clientId]
        ]);
    }

  
    public function store(): void
    {
        $this->requireRole('admin');

        $utilisateurId = $this->input('utilisateur_id');
        $montant       = $this->input('montant');
        $detteMotif    = $this->input('dette');

        $erreurs = [];

        if (empty($utilisateurId)) {
            $erreurs['utilisateur_id'] = "Veuillez sélectionner un client.";
        }
        if (empty($montant) || !is_numeric($montant) || $montant <= 0) {
            $erreurs['montant'] = "Le montant doit être un nombre supérieur à 0.";
        }
        if (empty($detteMotif)) {
            $erreurs['dette'] = "Le motif de la dette est obligatoire.";
        }

        if (!empty($erreurs)) {
            $clients = $this->utilisateurModel->findAllClients();
            $this->view('dette/create', [
                'clients' => $clients,
                'erreurs' => $erreurs,
                'ancien'  => [
                    'utilisateur_id' => $utilisateurId,
                    'montant'       => $montant,
                    'dette'         => $detteMotif
                ]
            ]);
            return;
        }

        $this->detteModel->creerDette([
            'utilisateur_id' => (int) $utilisateurId,
            'montant'        => (float) $montant,
            'dette'          => trim($detteMotif),
            'etat'           => 'non_solde'
        ]);

        if ($utilisateurId) {
            $this->redirect('/dettes?client_id=' . $utilisateurId);
        } else {
            $this->redirect('/dettes');
        }
    }
}