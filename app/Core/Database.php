<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Fournit une connexion PDO unique partagée par toute l'application.
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $connexion;

    private function __construct()
    {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

        try {
            $this->connexion = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnexion(): PDO
    {
        return $this->connexion;
    }

    // Empêche le clonage et la désérialisation de l'instance unique
    private function __clone() {}
    public function __wakeup()
    {
        throw new \Exception('Impossible de désérialiser un singleton.');
    }
}
