-- Création de la base si elle n'existe pas
CREATE DATABASE IF NOT EXISTS `impactable` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `impactable`;

-- Désactiver temporairement les checks des clés étrangères
SET FOREIGN_KEY_CHECKS = 0;

-- Table utilisateur
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `Id_utilisateur` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `genre` enum('femme','homme','prefere_ne_pas_dire') NOT NULL DEFAULT 'prefere_ne_pas_dire',
  `date_naissance` date DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `numero_tel` varchar(20) DEFAULT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `type_handicap` set('aucun','moteur','visuel','auditif','mental','autre','tous') NOT NULL DEFAULT 'aucun',
  `date_inscription` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`Id_utilisateur`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `numero_tel` (`numero_tel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table campagnecollecte
CREATE TABLE IF NOT EXISTS `campagnecollecte` (
  `Id_campagne` int(11) NOT NULL AUTO_INCREMENT,
  `Id_utilisateur` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `categorie_impact` enum('education','logement','sante','alimentation','droits_humains','autre') NOT NULL DEFAULT 'autre',
  `urgence` enum('normale','elevee','critique') NOT NULL DEFAULT 'normale',
  `description` text DEFAULT NULL,
  `statut` enum('active','terminee','objectif_atteint') NOT NULL DEFAULT 'active',
  `image_campagne` varchar(255) DEFAULT NULL,
  `objectif_montant` decimal(15,2) DEFAULT NULL,
  `montant_actuel` decimal(15,2) NOT NULL DEFAULT 0.00,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  PRIMARY KEY (`Id_campagne`),
  KEY `idx_campagne_admin` (`Id_utilisateur`),
  CONSTRAINT `campagnecollecte_ibfk_1` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table don
CREATE TABLE IF NOT EXISTS `don` (
  `Id_don` int(11) NOT NULL AUTO_INCREMENT,
  `Id_campagne` int(11) NOT NULL,
  `Id_utilisateur` int(11) NOT NULL,
  `montant` decimal(15,2) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `methode_paiment` enum('carte','virement') NOT NULL,
  `date_don` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`Id_don`),
  KEY `don_ibfk_1` (`Id_utilisateur`),
  KEY `Id_campagne` (`Id_campagne`),
  CONSTRAINT `don_ibfk_1` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`),
  CONSTRAINT `don_ibfk_2` FOREIGN KEY (`Id_campagne`) REFERENCES `campagnecollecte` (`Id_campagne`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table evenement
CREATE TABLE IF NOT EXISTS `evenement` (
  `Id_evenement` int(11) NOT NULL AUTO_INCREMENT,
  `Id_utilisateur` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `type_evenement` enum('formation','solidarite','conference','environnement','autre') NOT NULL DEFAULT 'autre',
  `mode` enum('presentiel','en_ligne','hybride') NOT NULL DEFAULT 'presentiel',
  `description` text DEFAULT NULL,
  `photo_evenement` varchar(255) DEFAULT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `lieu_online` varchar(255) DEFAULT NULL,
  `max_participants` int(11) NOT NULL DEFAULT 0,
  `date_debut` datetime DEFAULT NULL,
  `date_fin` datetime DEFAULT NULL,
  PRIMARY KEY (`Id_evenement`),
  KEY `idx_evenement_user` (`Id_utilisateur`),
  CONSTRAINT `evenement_ibfk_1` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table offre
CREATE TABLE IF NOT EXISTS `offre` (
  `Id_offre` int(11) NOT NULL AUTO_INCREMENT,
  `Id_utilisateur` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date_expiration` date DEFAULT NULL,
  `impact_sociale` text NOT NULL,
  `disability_friendly` tinyint(1) NOT NULL DEFAULT 0,
  `type_handicap` set('moteur','visuel','auditif','mental','autre','tous') DEFAULT NULL,
  `photo_offre` varchar(255) DEFAULT NULL,
  `type_offre` enum('emploi','stage','volontariat','formation','autre') NOT NULL DEFAULT 'emploi',
  `mode` enum('presentiel','en_ligne','hybride') NOT NULL DEFAULT 'presentiel',
  `horaire` enum('temps_plein','temps_partiel') DEFAULT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `date_publication` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modification` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`Id_offre`),
  KEY `idx_offre_user` (`Id_utilisateur`),
  CONSTRAINT `offre_ibfk_1` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table candidature
CREATE TABLE IF NOT EXISTS `candidature` (
  `Id_candidature` int(11) NOT NULL AUTO_INCREMENT,
  `Id_offre` int(11) NOT NULL,
  `Id_utilisateur` int(11) NOT NULL,
  `cv` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `status` enum('en_attente','en_revue','entretien','retenu','refuse','retire') NOT NULL DEFAULT 'en_attente',
  `lettre_motivation` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `date_candidature` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`Id_candidature`),
  UNIQUE KEY `unique_application` (`Id_offre`,`Id_utilisateur`),
  KEY `idx_candidature_offre` (`Id_offre`),
  KEY `idx_candidature_user` (`Id_utilisateur`),
  CONSTRAINT `candidature_ibfk_1` FOREIGN KEY (`Id_offre`) REFERENCES `offre` (`Id_offre`) ON DELETE CASCADE,
  CONSTRAINT `candidature_ibfk_2` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table profil
CREATE TABLE IF NOT EXISTS `profil` (
  `Id_profil` int(11) NOT NULL AUTO_INCREMENT,
  `Id_utilisateur` int(11) NOT NULL,
  `photo_profil` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `ville` text DEFAULT NULL,
  `pays` text DEFAULT NULL,
  `profession` text DEFAULT NULL,
  `competences` text DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modification` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`Id_profil`),
  UNIQUE KEY `unique_user_profile` (`Id_utilisateur`),
  CONSTRAINT `profil_ibfk_1` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table post
CREATE TABLE IF NOT EXISTS `post` (
  `Id_post` int(11) NOT NULL AUTO_INCREMENT,
  `Id_utilisateur` int(11) NOT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `categorie` enum('opportunites','evenements','campagnes','questions','ressources','autre') DEFAULT 'autre',
  `contenu` text DEFAULT NULL,
  `piece_jointe` varchar(255) DEFAULT NULL,
  `likes` int(11) NOT NULL DEFAULT 0,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modification` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`Id_post`),
  KEY `idx_post_user` (`Id_utilisateur`),
  CONSTRAINT `post_ibfk_1` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table commentaire
CREATE TABLE IF NOT EXISTS `commentaire` (
  `Id_commentaire` int(11) NOT NULL AUTO_INCREMENT,
  `Id_utilisateur` int(11) NOT NULL,
  `Id_post` int(11) NOT NULL,
  `contenu` text DEFAULT NULL,
  `likes` int(11) NOT NULL DEFAULT 0,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modification` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`Id_commentaire`),
  KEY `idx_comment_post` (`Id_post`),
  KEY `commentaire_ibfk_2` (`Id_utilisateur`),
  CONSTRAINT `commentaire_ibfk_2` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table reclamation
CREATE TABLE IF NOT EXISTS `reclamation` (
  `Id_reclamation` int(11) NOT NULL AUTO_INCREMENT,
  `Id_utilisateur` int(11) NOT NULL,
  `type_reclamation` enum('accessibilite','contenu_inapproprié','technique','discrimination','harcelement','autre') NOT NULL DEFAULT 'autre',
  `niveau_gravite` enum('faible','moyen','eleve') DEFAULT 'faible',
  `objet` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `piece_jointe` varchar(255) DEFAULT NULL,
  `statut` enum('non_traite','traite') DEFAULT 'non_traite',
  `date_reclamation` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`Id_reclamation`),
  KEY `idx_reclamation_user` (`Id_utilisateur`),
  CONSTRAINT `reclamation_ibfk_1` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table reponse
CREATE TABLE IF NOT EXISTS `reponse` (
  `Id_reponse` int(11) NOT NULL AUTO_INCREMENT,
  `Id_reclamation` int(11) NOT NULL,
  `Id_utilisateur` int(11) NOT NULL,
  `message` text NOT NULL,
  `piece_jointe` varchar(255) DEFAULT NULL,
  `type_reponse` enum('premiere','suivi','resolution') NOT NULL DEFAULT 'premiere',
  `date_reponse` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`Id_reponse`),
  KEY `idx_reponse_reclamation` (`Id_reclamation`),
  KEY `idx_reponse_user` (`Id_utilisateur`),
  CONSTRAINT `reponse_ibfk_1` FOREIGN KEY (`Id_reclamation`) REFERENCES `reclamation` (`Id_reclamation`) ON DELETE CASCADE,
  CONSTRAINT `reponse_ibfk_2` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table participation
CREATE TABLE IF NOT EXISTS `participation` (
  `Id_participation` int(11) NOT NULL AUTO_INCREMENT,
  `Id_evenement` int(11) NOT NULL,
  `Id_utilisateur` int(11) NOT NULL,
  `statut` enum('inscrit','present','absent','annule') NOT NULL DEFAULT 'inscrit',
  `message` text DEFAULT NULL,
  `besoins_accessibilite` set('lsf','sous_titrage','documents_accessibles','stationnement_adapte','assistance_personnelle','aucun_besoin') NOT NULL DEFAULT 'aucun_besoin',
  `nombre_accompagnants` int(11) NOT NULL DEFAULT 0,
  `date_inscription` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`Id_participation`),
  UNIQUE KEY `unique_participation` (`Id_evenement`,`Id_utilisateur`),
  KEY `idx_participation_event` (`Id_evenement`),
  KEY `idx_participation_user` (`Id_utilisateur`),
  CONSTRAINT `participation_ibfk_1` FOREIGN KEY (`Id_evenement`) REFERENCES `evenement` (`Id_evenement`) ON DELETE CASCADE,
  CONSTRAINT `participation_ibfk_2` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Réactiver les checks des clés étrangères
SET FOREIGN_KEY_CHECKS = 1;