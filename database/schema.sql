CREATE DATABASE IF NOT EXISTS gestion_dette CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_dette;

-- 1. Nettoyage
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS dettes;
DROP TABLE IF EXISTS utilisateurs;
SET FOREIGN_KEY_CHECKS = 1;

-- 2. Table utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NULL,
    photo VARCHAR(255) NULL,
    role ENUM('admin', 'client') NOT NULL DEFAULT 'client',
    etat ENUM('solvable', 'non_solvable', 'nouveau') NOT NULL DEFAULT 'nouveau',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Table dettes
CREATE TABLE dettes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    montant DECIMAL(12, 2) NOT NULL,
    dette VARCHAR(255) NOT NULL COMMENT 'Description ou libellé de la dette',
    etat ENUM('solde', 'non_solde') NOT NULL DEFAULT 'non_solde',
    utilisateur_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_dette_utilisateur FOREIGN KEY (utilisateur_id)
        REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Données de test (Mots de passe en brut)
INSERT INTO utilisateurs (id, nom, prenom, telephone, email, mot_de_passe, photo, role, etat) VALUES
(1, 'Admin', 'Système', '770000000', 'admin@gmail.sn', 'admin123', NULL, 'admin', 'solvable'),
(2, 'Traoré', 'Awa', '770000002', 'awa.traore@mail.com', 'client123', NULL, 'client', 'solvable'),
(3, 'Konaté', 'Ibrahim', '770000003', 'ibrahim.konate@mail.com', 'client123', NULL, 'client', 'non_solvable'),
(4, 'Bamba', 'Fatou', '770000004', 'fatou.bamba@mail.com', 'client123', NULL, 'client', 'nouveau');

-- Dettes de test
INSERT INTO dettes (montant, dette, etat, utilisateur_id) VALUES
(150000.00, 'Achat sac de riz et huile', 'non_solde', 2),
(75000.00, 'Recharge gaz et fournitures', 'solde', 2),
(300000.00, 'Crédit marchandise', 'non_solde', 3),
(50000.00, 'Avance sur commande', 'non_solde', 4);