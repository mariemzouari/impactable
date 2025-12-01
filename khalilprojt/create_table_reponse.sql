-- Script SQL pour créer la table reponse
-- À exécuter dans phpMyAdmin sur la base de données khalilbenhamouda

USE `khalilbenhamouda`;

-- Créer la table reponse si elle n'existe pas
CREATE TABLE IF NOT EXISTS `reponse` (
    `Id_reponse` INT(11) NOT NULL AUTO_INCREMENT,
    `Id_reclamation` INT(11) NOT NULL,
    `Id_utilisateur` INT(11) NOT NULL,
    `message` TEXT NOT NULL,
    `type_reponse` VARCHAR(50) DEFAULT 'premiere',
    `date_reponse` DATETIME NOT NULL,
    `dernier_update` DATETIME DEFAULT NULL,
    PRIMARY KEY (`Id_reponse`),
    KEY `idx_reclamation` (`Id_reclamation`),
    KEY `idx_utilisateur` (`Id_utilisateur`),
    KEY `idx_date_reponse` (`date_reponse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ajouter les clés étrangères si les tables existent
-- (Décommenter si vous voulez activer les contraintes de clés étrangères)

-- ALTER TABLE `reponse`
-- ADD CONSTRAINT `fk_reponse_reclamation`
-- FOREIGN KEY (`Id_reclamation`) REFERENCES `reclamation` (`id`)
-- ON DELETE CASCADE ON UPDATE CASCADE;

-- ALTER TABLE `reponse`
-- ADD CONSTRAINT `fk_reponse_utilisateur`
-- FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`)
-- ON DELETE CASCADE ON UPDATE CASCADE;

