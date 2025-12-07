-- ============================================
-- Script SQL pour ajouter les clés étrangères
-- à la table reponse
-- ============================================

USE `khalilbenhamouda`;

-- 1. Vérifier d'abord la structure actuelle
DESCRIBE reponse;

-- 2. Ajouter la clé étrangère vers la table reclamation
ALTER TABLE `reponse`
ADD CONSTRAINT `fk_reponse_reclamation`
FOREIGN KEY (`Id_reclamation`) 
REFERENCES `reclamation`(`Id_reclamation`)
ON DELETE CASCADE
ON UPDATE CASCADE;

-- 3. Ajouter la clé étrangère vers la table utilisateur
ALTER TABLE `reponse`
ADD CONSTRAINT `fk_reponse_utilisateur`
FOREIGN KEY (`Id_utilisateur`) 
REFERENCES `utilisateur`(`Id_utilisateur`)
ON DELETE CASCADE
ON UPDATE CASCADE;

-- 4. Vérification
SELECT 'Clés étrangères ajoutées avec succès!' AS Resultat;

-- Afficher les contraintes
SELECT 
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'khalilbenhamouda' 
AND TABLE_NAME = 'reponse'
AND REFERENCED_TABLE_NAME IS NOT NULL;



