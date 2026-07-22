<?php

namespace App\Models;

use App\Core\Model;

class Dette extends Model
{
    protected string $table = 'dettes';

   
    public function findAllAvecClient(?int $utilisateurId = null): array
    {
        $sql = "SELECT d.*, u.nom, u.prenom, u.email, u.telephone
                FROM dettes d
                INNER JOIN utilisateurs u ON u.id = d.utilisateur_id";
        $params = [];

        if ($utilisateurId !== null) {
            $sql .= " WHERE d.utilisateur_id = :utilisateur_id";
            $params['utilisateur_id'] = $utilisateurId;
        }

        $sql .= " ORDER BY d.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

 
    public function countAll(?int $utilisateurId = null, string $etat = ''): int
    {
        $sql = "SELECT COUNT(*) FROM dettes WHERE 1=1";
        $params = [];

        if ($utilisateurId !== null) {
            $sql .= " AND utilisateur_id = :utilisateur_id";
            $params['utilisateur_id'] = $utilisateurId;
        }

        if (!empty($etat)) {
            $sql .= " AND etat = :etat";
            $params['etat'] = $etat;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) ($stmt->fetchColumn() ?: 0);
    }

    public function findAllPaginated(?int $utilisateurId = null, string $etat = '', int $limit = 3, int $offset = 0): array
    {
        $sql = "SELECT d.*, u.nom, u.prenom, u.email, u.telephone
                FROM dettes d
                INNER JOIN utilisateurs u ON u.id = d.utilisateur_id
                WHERE 1=1";

        if ($utilisateurId !== null) {
            $sql .= " AND d.utilisateur_id = :utilisateur_id";
        }

        if (!empty($etat)) {
            $sql .= " AND d.etat = :etat";
        }

        $sql .= " ORDER BY d.id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        if ($utilisateurId !== null) {
            $stmt->bindValue(':utilisateur_id', $utilisateurId, \PDO::PARAM_INT);
        }
        if (!empty($etat)) {
            $stmt->bindValue(':etat', $etat, \PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findByUtilisateur(int $utilisateurId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM dettes WHERE utilisateur_id = :id ORDER BY id DESC"
        );
        $stmt->execute(['id' => $utilisateurId]);
        return $stmt->fetchAll();
    }

    public function creerDette(array $donnees): string|false
    {
        $donnees['etat'] = $donnees['etat'] ?? 'non_solde';
        return $this->insert($donnees);
    }

    public function modifierDette(int $id, array $donnees): bool
    {
        return $this->update($id, $donnees);
    }

    public function marquerSoldee(int $id): bool
    {
        return $this->update($id, ['etat' => 'solde']);
    }
}