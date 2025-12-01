-- Script simple pour ajouter tous les champs nécessaires à la table reclamation
-- À exécuter dans phpMyAdmin

USE `khalilbenhamouda`;

-- Ajouter les champs un par un (ignorer les erreurs si le champ existe déjà)
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `image` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `nom` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `prenom` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `email` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `telephone` VARCHAR(20) DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `lieu` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `dateIncident` DATE DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `typeHandicap` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `personnesImpliquees` TEXT DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `temoins` TEXT DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `actionsPrecedentes` TEXT DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `solutionSouhaitee` TEXT DEFAULT NULL;

-- Si ADD COLUMN IF NOT EXISTS ne fonctionne pas, utilisez cette version manuelle :
/*
ALTER TABLE `reclamation` ADD COLUMN `image` VARCHAR(255) DEFAULT NULL AFTER `agentAttribue`;
ALTER TABLE `reclamation` ADD COLUMN `nom` VARCHAR(100) DEFAULT NULL AFTER `image`;
ALTER TABLE `reclamation` ADD COLUMN `prenom` VARCHAR(100) DEFAULT NULL AFTER `nom`;
ALTER TABLE `reclamation` ADD COLUMN `email` VARCHAR(255) DEFAULT NULL AFTER `prenom`;
ALTER TABLE `reclamation` ADD COLUMN `telephone` VARCHAR(20) DEFAULT NULL AFTER `email`;
ALTER TABLE `reclamation` ADD COLUMN `lieu` VARCHAR(255) DEFAULT NULL AFTER `telephone`;
ALTER TABLE `reclamation` ADD COLUMN `dateIncident` DATE DEFAULT NULL AFTER `lieu`;
ALTER TABLE `reclamation` ADD COLUMN `typeHandicap` VARCHAR(100) DEFAULT NULL AFTER `dateIncident`;
ALTER TABLE `reclamation` ADD COLUMN `personnesImpliquees` TEXT DEFAULT NULL AFTER `typeHandicap`;
ALTER TABLE `reclamation` ADD COLUMN `temoins` TEXT DEFAULT NULL AFTER `personnesImpliquees`;
ALTER TABLE `reclamation` ADD COLUMN `actionsPrecedentes` TEXT DEFAULT NULL AFTER `temoins`;
ALTER TABLE `reclamation` ADD COLUMN `solutionSouhaitee` TEXT DEFAULT NULL AFTER `actionsPrecedentes`;
*/

