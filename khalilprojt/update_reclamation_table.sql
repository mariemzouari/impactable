-- Script pour ajouter tous les attributs nécessaires à la table reclamation
-- À exécuter dans phpMyAdmin

USE `khalilbenhamouda`;

-- Ajouter les nouveaux champs à la table reclamation
ALTER TABLE `reclamation` 
ADD COLUMN IF NOT EXISTS `image` VARCHAR(255) DEFAULT NULL AFTER `agentAttribue`,
ADD COLUMN IF NOT EXISTS `nom` VARCHAR(100) DEFAULT NULL AFTER `image`,
ADD COLUMN IF NOT EXISTS `prenom` VARCHAR(100) DEFAULT NULL AFTER `nom`,
ADD COLUMN IF NOT EXISTS `email` VARCHAR(255) DEFAULT NULL AFTER `prenom`,
ADD COLUMN IF NOT EXISTS `telephone` VARCHAR(20) DEFAULT NULL AFTER `email`,
ADD COLUMN IF NOT EXISTS `lieu` VARCHAR(255) DEFAULT NULL AFTER `telephone`,
ADD COLUMN IF NOT EXISTS `dateIncident` DATE DEFAULT NULL AFTER `lieu`,
ADD COLUMN IF NOT EXISTS `typeHandicap` VARCHAR(100) DEFAULT NULL AFTER `dateIncident`,
ADD COLUMN IF NOT EXISTS `personnesImpliquees` TEXT DEFAULT NULL AFTER `typeHandicap`,
ADD COLUMN IF NOT EXISTS `temoins` TEXT DEFAULT NULL AFTER `personnesImpliquees`,
ADD COLUMN IF NOT EXISTS `actionsPrecedentes` TEXT DEFAULT NULL AFTER `temoins`,
ADD COLUMN IF NOT EXISTS `solutionSouhaitee` TEXT DEFAULT NULL AFTER `actionsPrecedentes`;

