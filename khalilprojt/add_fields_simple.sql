-- Script SQL simple pour ajouter tous les champs nécessaires
-- Compatible avec toutes les versions de MySQL/MariaDB
-- À exécuter dans phpMyAdmin sur la base khalilbenhamouda

USE `khalilbenhamouda`;

-- Ajouter les champs (exécuter chaque ligne séparément si une erreur survient)
-- Ignorer les erreurs "Duplicate column name" si les champs existent déjà

ALTER TABLE `reclamation` ADD COLUMN `image` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN `nom` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN `prenom` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN `email` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN `telephone` VARCHAR(20) DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN `lieu` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN `dateIncident` DATE DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN `typeHandicap` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN `personnesImpliquees` TEXT DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN `temoins` TEXT DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN `actionsPrecedentes` TEXT DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN `solutionSouhaitee` TEXT DEFAULT NULL;

