<?php

namespace App\Core;

use PDO;

/**
 * Fournit les opérations CRUD génériques que chaque modèle hérite.
 */
abstract class Model
{
    protected PDO $db;
    protected string $table;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnexion();
    }

    public function findAll(string $orderBy = 'id DESC'): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY {$orderBy}");
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    protected function insert(array $donnees): string|false
    {
        $colonnes = implode(', ', array_keys($donnees));
        $placeholders = ':' . implode(', :', array_keys($donnees));

        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$colonnes}) VALUES ({$placeholders})");
        $stmt->execute($donnees);

        return $this->db->lastInsertId();
    }

    protected function update(int $id, array $donnees): bool
    {
        $champs = [];
        foreach (array_keys($donnees) as $cle) {
            $champs[] = "{$cle} = :{$cle}";
        }
        $champs = implode(', ', $champs);

        $donnees['id'] = $id;
        $stmt = $this->db->prepare("UPDATE {$this->table} SET {$champs} WHERE id = :id");
        return $stmt->execute($donnees);
    }
}
