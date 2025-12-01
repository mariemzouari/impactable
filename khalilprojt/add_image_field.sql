-- Script pour ajouter le champ image à la table reclamation
-- À exécuter dans phpMyAdmin

USE `khalilbenhamouda`;

-- Ajouter le champ image si il n'existe pas déjà
ALTER TABLE `reclamation` 
ADD COLUMN IF NOT EXISTS `image` VARCHAR(255) DEFAULT NULL AFTER `agentAttribue`;

