<?php

namespace App\Models;

use App\Core\Model;

class Utilisateur extends Model
{
    protected string $table = 'utilisateurs';

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function findAllClients(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM utilisateurs WHERE role = 'client' ORDER BY id DESC"
        );
        return $stmt->fetchAll();
    }

    public function creerClient(array $donnees): string|false
    {
        $donnees['role'] = 'client';
        $donnees['etat'] = $donnees['etat'] ?? 'nouveau';
        return $this->insert($donnees);
    }

    public function modifierClient(int $id, array $donnees): bool
    {
        return $this->update($id, $donnees);
    }

    public function mettreAJourEtat(int $utilisateurId): void
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS total FROM dettes WHERE utilisateur_id = :id AND etat = 'non_solde'"
        );
        $stmt->execute(['id' => $utilisateurId]);
        $dettesEnCours = (int) $stmt->fetch()['total'];

        $nouvelEtat = $dettesEnCours > 0 ? 'non_solvable' : 'solvable';
        $this->update($utilisateurId, ['etat' => $nouvelEtat]);
    }

    public function emailExiste(string $email, ?int $ignorerId = null): bool
    {
        $sql = "SELECT id FROM utilisateurs WHERE email = :email";
        $params = ['email' => $email];

        if ($ignorerId !== null) {
            $sql .= " AND id != :id";
            $params['id'] = $ignorerId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool) $stmt->fetch();
    } 
    public function countAll(string $etat = ''): int
    {
        $sql = "SELECT COUNT(*) FROM utilisateurs WHERE role = 'client'";
        $params = [];

        if (!empty($etat)) {
            $sql .= " AND etat = :etat";
            $params[':etat'] = $etat;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) ($stmt->fetchColumn() ?: 0);
    }

    public function findAllPaginated(string $etat = '', int $limit = 3, int $offset = 0): array
    {
        $sql = "SELECT * FROM utilisateurs WHERE role = 'client'";
        if (!empty($etat)) {
            $sql .= " AND etat = :etat";
        }
        $sql .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        if (!empty($etat)) {
            $stmt->bindValue(':etat', $etat, \PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
} 