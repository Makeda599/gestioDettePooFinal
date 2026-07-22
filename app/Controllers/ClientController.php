<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Utilisateur;
use App\Models\Dette;

class ClientController extends Controller
{
    private Utilisateur $utilisateurModel;

    public function __construct()
    {
        $this->requireRole('admin');
        $this->utilisateurModel = new Utilisateur();
    }

public function index(): void
{
    $etat = trim($_GET['etat'] ?? '');
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    
    if ($page < 1) {
        $page = 1;
    }

    $limit = 3; 
    $offset = ($page - 1) * $limit;

    // 1. Nombre total selon le filtre
    $totalClients = $this->utilisateurModel->countAll($etat);

    // 2. Calcul du nombre total de pages
    $totalPages = (int) ceil($totalClients / $limit);
    if ($totalPages < 1) {
        $totalPages = 1;
    }

    // 3. Récupération des données paginées
    $clients = $this->utilisateurModel->findAllPaginated($etat, $limit, $offset);

    // 4. Transmission à la vue
    $this->view('client/index', [
        'clients'      => $clients,
        'pageCourante' => $page,
        'totalPages'   => $totalPages,
        'etatFiltre'   => $etat
    ]);
}
    public function create(): void
    {
        $this->view('client/create', ['erreurs' => [], 'ancien' => []]);
    }

    public function store(): void
    {
        $donnees = [
            'nom' => $this->input('nom'),
            'prenom' => $this->input('prenom'),
            'email' => $this->input('email'),
            'telephone' => $this->input('telephone'),
        ];
        $motDePasse = $this->input('mot_de_passe');

        $erreurs = $this->valider($donnees, $motDePasse);
        $erreurPhoto = null;
        $cheminPhoto = null;

        if (!empty($_FILES['photo']['name'])) {
            [$cheminPhoto, $erreurPhoto] = $this->traiterPhoto($_FILES['photo']);
            if ($erreurPhoto) {
                $erreurs['photo'] = $erreurPhoto;
            }
        }

        if (!empty($erreurs)) {
            $this->view('client/create', ['erreurs' => $erreurs, 'ancien' => $donnees]);
            return;
        }

        $donnees['mot_de_passe'] = password_hash($motDePasse, PASSWORD_DEFAULT);
        $donnees['photo'] = $cheminPhoto;
        $this->utilisateurModel->creerClient($donnees);

        $this->redirect('/clients');
    }

    public function edit(): void
    {
        $id = (int) $this->input('id');
        $client = $this->utilisateurModel->findById($id);

        if (!$client) {
            $this->redirect('/clients');
        }

        $this->view('client/edit', ['client' => $client, 'erreurs' => []]);
    }

    public function update(): void
    {
        $id = (int) $this->input('id');
        $donnees = [
            'nom' => $this->input('nom'),
            'prenom' => $this->input('prenom'),
            'email' => $this->input('email'),
            'telephone' => $this->input('telephone'),
        ];

        $erreurs = $this->valider($donnees, null, $id);
        $erreurPhoto = null;
        $nouveauChemin = null;

        if (!empty($_FILES['photo']['name'])) {
            [$nouveauChemin, $erreurPhoto] = $this->traiterPhoto($_FILES['photo']);
            if ($erreurPhoto) {
                $erreurs['photo'] = $erreurPhoto;
            }
        }

        if (!empty($erreurs)) {
            $client = array_merge(['id' => $id], $donnees);
            $this->view('client/edit', ['client' => $client, 'erreurs' => $erreurs]);
            return;
        }

        $motDePasse = $this->input('mot_de_passe');
        if (!empty($motDePasse)) {
            $donnees['mot_de_passe'] = password_hash($motDePasse, PASSWORD_DEFAULT);
        }

        if ($nouveauChemin !== null) {
            $ancien = $this->utilisateurModel->findById($id);
            if (!empty($ancien['photo'])) {
                $this->supprimerFichierPhoto($ancien['photo']);
            }
            $donnees['photo'] = $nouveauChemin;
        }

        $this->utilisateurModel->modifierClient($id, $donnees);
        $this->redirect('/clients');
    }

    public function delete(): void
    {
        $id = (int) $this->input('id');
        $client = $this->utilisateurModel->findById($id);

        if ($client && !empty($client['photo'])) {
            $this->supprimerFichierPhoto($client['photo']);
        }

        $this->utilisateurModel->delete($id);
        $this->redirect('/clients');
    }

    private function valider(array $donnees, ?string $motDePasse, ?int $ignorerId = null): array
    {
        $erreurs = [];

        if (empty($donnees['nom']) || strlen($donnees['nom']) < 2) {
            $erreurs['nom'] = 'Le nom doit contenir au moins 2 caractères.';
        }

        if (empty($donnees['prenom']) || strlen($donnees['prenom']) < 2) {
            $erreurs['prenom'] = 'Le prénom doit contenir au moins 2 caractères.';
        }

        if (empty($donnees['email']) || !filter_var($donnees['email'], FILTER_VALIDATE_EMAIL)) {
            $erreurs['email'] = 'Adresse email invalide.';
        } elseif ($this->utilisateurModel->emailExiste($donnees['email'], $ignorerId)) {
            $erreurs['email'] = 'Cet email est déjà utilisé.';
        }

        if (empty($donnees['telephone']) || !preg_match('/^[0-9+ ]{9,15}$/', $donnees['telephone'])) {
            $erreurs['telephone'] = 'Numéro de téléphone invalide.';
        }

        if ($motDePasse !== null && strlen($motDePasse) < 6) {
            $erreurs['mot_de_passe'] = 'Le mot de passe doit contenir au moins 6 caractères.';
        }

        return $erreurs;
    }

    private function traiterPhoto(array $fichier): array
    {
        if ($fichier['error'] !== UPLOAD_ERR_OK) {
            return [null, "Erreur lors de l'envoi de la photo."];
        }

        $extensionsAutorisees = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $extensionsAutorisees, true)) {
            return [null, 'Format non supporté (jpg, jpeg, png, webp uniquement).'];
        }

        $tailleMaxOctets = 2 * 1024 * 1024; 
        if ($fichier['size'] > $tailleMaxOctets) {
            return [null, 'La photo ne doit pas dépasser 2 Mo.'];
        }

        $infosImage = @getimagesize($fichier['tmp_name']);
        if ($infosImage === false) {
            return [null, "Le fichier envoyé n'est pas une image valide."];
        }

        $dossierDestination = dirname(__DIR__, 2) . '/public/uploads/clients';
        if (!is_dir($dossierDestination)) {
            mkdir($dossierDestination, 0755, true);
        }

        $nomFichier = uniqid('client_', true) . '.' . $extension;
        $cheminComplet = $dossierDestination . '/' . $nomFichier;

        if (!move_uploaded_file($fichier['tmp_name'], $cheminComplet)) {
            return [null, "Impossible d'enregistrer la photo."];
        }

        return ['uploads/clients/' . $nomFichier, null];
    }

    private function supprimerFichierPhoto(string $cheminRelatif): void
    {
        $cheminComplet = dirname(__DIR__, 2) . '/public/' . $cheminRelatif;
        if (is_file($cheminComplet)) {
            unlink($cheminComplet);
        }
    }
}
