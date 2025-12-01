-- Script SQL pour créer la table reponse
-- À exécuter dans phpMyAdmin sur la base de données khalilbenhamouda

USE `khalilbenhamouda`;

-- Créer la table reponse si elle n'existe pas
CREATE TABLE IF NOT EXISTS `reponse` (
  `Id_reponse` INT(11) NOT NULL AUTO_INCREMENT,
  `Id_reclamation` INT(11) NOT NULL,
  `Id_utilisateur` INT(11) NOT NULL,
  `message` TEXT NOT NULL,
  `piece_jointe` VARCHAR(255) DEFAULT NULL,
  `type_reponse` ENUM('premiere','suivi','resolution') NOT NULL DEFAULT 'premiere',
  `date_reponse` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id_reponse`),
  KEY `idx_reponse_reclamation` (`Id_reclamation`),
  KEY `idx_reponse_user` (`Id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Afficher un message de confirmation
SELECT 'Table reponse créée avec succès !' AS Resultat;