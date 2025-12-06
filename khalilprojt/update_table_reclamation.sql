-- Script SQL pour ajouter les champs manquants à la table reclamation
-- Exécuter ce script dans phpMyAdmin si vous avez des erreurs "Unknown column"

USE `khalilbenhamouda`;

-- Ajouter le champ image s'il n'existe pas
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `image` VARCHAR(255) DEFAULT NULL;

-- Ajouter les champs d'informations personnelles
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `nom` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `prenom` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `email` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `telephone` VARCHAR(20) DEFAULT NULL;

-- Ajouter les champs de détails de l'incident
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `lieu` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `dateIncident` DATE DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `typeHandicap` VARCHAR(100) DEFAULT NULL;

-- Ajouter les champs de personnes impliquées
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `personnesImpliquees` TEXT DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `temoins` TEXT DEFAULT NULL;

-- Ajouter les champs d'actions et solutions
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `actionsPrecedentes` TEXT DEFAULT NULL;
ALTER TABLE `reclamation` ADD COLUMN IF NOT EXISTS `solutionSouhaitee` TEXT DEFAULT NULL;

-- Afficher la structure finale de la table
DESCRIBE `reclamation`;

