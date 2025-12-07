-- Script SQL pour ajouter la colonne dernier_update à la table reponse
-- Optionnel : exécuter si vous voulez suivre les dates de modification

USE `khalilbenhamouda`;

-- Ajouter la colonne dernier_update si elle n'existe pas
ALTER TABLE `reponse` 
ADD COLUMN IF NOT EXISTS `dernier_update` DATETIME DEFAULT NULL 
AFTER `date_reponse`;

-- Afficher confirmation
SELECT 'Colonne dernier_update ajoutée avec succès (ou existe déjà)' AS Resultat;




