# GestionDette — POO + Composer

Application de gestion de dettes clients, en PHP orienté objet (MVC), avec
autoload PSR-4 via Composer. L'administrateur se connecte, gère la liste des
clients et enregistre leurs dettes.

## Architecture

```
app/
  Controllers/      AuthController, ClientController, DetteController
  Core/              Database (singleton), Model (abstrait), Controller (abstrait), Router
  Models/            Utilisateur, Dette
config/
  config.php         Charge le .env et définit les constantes DB_*
database/
  schema.sql          Script SQL (tables + admin par défaut)
public/
  index.php           Front controller
  .htaccess            Réécriture d'URL + HTTPS forcé
routes/
  web.php              Déclaration des routes
views/
  layouts/             header.php / footer.php (sidebar + topbar)
  auth/                login.php
  client/              index.php, create.php, edit.php
  dette/                index.php, create.php, edit.php
```

Diagramme de classes respecté : `Utilisateur` (nom, prénom, téléphone, email,
role: admin/client, état: solvable/non_solvable/nouveau) et `Dette` (id,
montant, dette, état: solde/non_solde), relation 1 Utilisateur → 0..* Dette.
L'état du client est recalculé automatiquement à chaque ajout/modification/
suppression de dette.

## Installation

1. **Dépendances**
   ```bash
   composer install
   ```

2. **Base de données**
   - Créer la base et importer `database/schema.sql` (crée aussi un compte
     admin par défaut : `admin@stagelink.sn` / `admin123`).

3. **Configuration**
   ```bash
   cp .env.example .env
   ```
   Renseigner `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.

4. **Lancer en local**
   ```bash
   php -S localhost:8000 -t public
   ```
   Puis ouvrir http://localhost:8000/connexion

## Déploiement AlwaysData

- Pointer le domaine/sous-domaine vers le dossier `public/`.
- Le front controller calcule `WEBROOT` via `$_SERVER['SCRIPT_NAME']`, donc
  aucun changement de code n'est nécessaire si l'app est dans un sous-dossier.
- Ne pas committer `.env` (déjà ignoré par `.gitignore`) : le créer
  directement sur le serveur avec les identifiants MySQL AlwaysData.

## Sécurité

- Mots de passe hashés avec `password_hash()` / vérifiés avec `password_verify()`.
- Requêtes SQL préparées (PDO) partout — aucune concaténation directe.
- Accès aux pages clients/dettes protégé par `requireRole('admin')`.
- Session régénérée à la connexion (`session_regenerate_id`).
