-- Script SQL pour créer les tables dans la base de données khalilbenhamouda
-- À exécuter dans phpMyAdmin

USE `khalilbenhamouda`;

-- Créer la table utilisateur si elle n'existe pas
CREATE TABLE IF NOT EXISTS `utilisateur` (
    `Id_utilisateur` INT(11) NOT NULL AUTO_INCREMENT,
    `nom` VARCHAR(100) NOT NULL,
    `prenom` VARCHAR(100) NOT NULL,
    `genre` ENUM('femme','homme','prefere_ne_pas_dire') NOT NULL DEFAULT 'prefere_ne_pas_dire',
    `date_naissance` DATE DEFAULT NULL,
    `email` VARCHAR(255) NOT NULL,
    `numero_tel` VARCHAR(20) DEFAULT NULL,
    `mot_de_passe` VARCHAR(255) NOT NULL,
    `role` ENUM('admin','user') DEFAULT 'user',
    `type_handicap` SET('aucun','moteur','visuel','auditif','mental','autre','tous') NOT NULL DEFAULT 'aucun',
    `date_inscription` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`Id_utilisateur`),
    UNIQUE KEY `email` (`email`),
    UNIQUE KEY `numero_tel` (`numero_tel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Supprimer et recréer la table reclamation avec la bonne structure
DROP TABLE IF EXISTS `reclamation`;

CREATE TABLE `reclamation` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sujet` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `categorie` VARCHAR(100) NOT NULL,
    `priorite` VARCHAR(50) NOT NULL,
    `statut` VARCHAR(50) NOT NULL,
    `dateCreation` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `derniereModification` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `utilisateurId` INT NOT NULL,
    `agentAttribue` VARCHAR(255) DEFAULT NULL,
    INDEX `idx_reclamation_user` (`utilisateurId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insérer quelques données de test (optionnel)
-- INSERT INTO `utilisateur` (`nom`, `prenom`, `email`, `mot_de_passe`, `role`) VALUES
-- ('Admin', 'Test', 'admin@test.com', MD5('password123'), 'admin'),
-- ('User', 'Test', 'user@test.com', MD5('password123'), 'user');

-- INSERT INTO `reclamation` (`sujet`, `description`, `categorie`, `priorite`, `statut`, `utilisateurId`, `agentAttribue`) VALUES
-- ('Test Réclamation 1', 'Description de test pour la réclamation 1', 'Technique', 'Urgente', 'En attente', 1, 'Agent Test'),
-- ('Test Réclamation 2', 'Description de test pour la réclamation 2', 'Service', 'Moyenne', 'En cours', 2, NULL);

