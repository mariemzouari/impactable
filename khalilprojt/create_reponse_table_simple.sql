-- Script SQL simple pour créer la table reponse
-- Compatible avec toutes les versions de MySQL/MariaDB
-- À exécuter dans phpMyAdmin sur la base khalilbenhamouda

USE `khalilbenhamouda`;

-- Supprimer la table si elle existe déjà (optionnel, commentez cette ligne si vous voulez garder les données existantes)
-- DROP TABLE IF EXISTS `reponse`;

-- Créer la table reponse
CREATE TABLE IF NOT EXISTS `reponse` (
    `Id_reponse` INT(11) NOT NULL AUTO_INCREMENT,
    `Id_reclamation` INT(11) NOT NULL,
    `Id_utilisateur` INT(11) NOT NULL,
    `message` TEXT NOT NULL,
    `type_reponse` VARCHAR(50) DEFAULT 'premiere',
    `date_reponse` DATETIME NOT NULL,
    `dernier_update` DATETIME DEFAULT NULL,
    PRIMARY KEY (`Id_reponse`),
    INDEX `idx_reclamation` (`Id_reclamation`),
    INDEX `idx_utilisateur` (`Id_utilisateur`),
    INDEX `idx_date_reponse` (`date_reponse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

