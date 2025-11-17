-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 14 nov. 2025 à 10:58
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `impactable`
--

-- --------------------------------------------------------

--
-- Structure de la table `campagnecollecte`
--

CREATE TABLE `campagnecollecte` (
  `Id_campagne` int(11) NOT NULL,
  `Id_utilisateur` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `categorie_impact` enum('education','logement','sante','alimentation','droits_humains','autre') NOT NULL DEFAULT 'autre',
  `urgence` enum('normale','elevee','critique') NOT NULL DEFAULT 'normale',
  `description` text DEFAULT NULL,
  `statut` enum('active','terminee','objectif_atteint') NOT NULL DEFAULT 'active',
  `image_campagne` varchar(255) DEFAULT NULL,
  `objectif_montant` decimal(15,2) DEFAULT NULL,
  `montant_actuel` decimal(15,2) DEFAULT 0.00,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `candidature`
--

CREATE TABLE `candidature` (
  `Id_candidature` int(11) NOT NULL,
  `Id_offre` int(11) NOT NULL,
  `Id_utilisateur` int(11) NOT NULL,
  `cv` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `status` enum('en_attente','en_revue','entretien','retenu','refuse','retire') NOT NULL DEFAULT 'en_attente',
  `lettre_motivation` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `date_candidature` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `commentaire`
--

CREATE TABLE `commentaire` (
  `Id_commentaire` int(11) NOT NULL,
  `Id_utilisateur` int(11) NOT NULL,
  `Id_post` int(11) NOT NULL,
  `contenu` text DEFAULT NULL,
  `likes` int(11) NOT NULL DEFAULT 0,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modification` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `don`
--

CREATE TABLE `don` (
  `Id_don` int(11) NOT NULL,
  `Id_campagne` int(11) NOT NULL,
  `Id_utilisateur` int(11) NOT NULL,
  `montant` decimal(15,2) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `methode_paiment` enum('carte','virement') NOT NULL,
  `date_don` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `evenement`
--

CREATE TABLE `evenement` (
  `Id_evenement` int(11) NOT NULL,
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
  `date_fin` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `offre`
--

CREATE TABLE `offre` (
  `Id_offre` int(11) NOT NULL,
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
  `date_modification` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `participation`
--

CREATE TABLE `participation` (
  `Id_participation` int(11) NOT NULL,
  `Id_evenement` int(11) NOT NULL,
  `Id_utilisateur` int(11) NOT NULL,
  `statut` enum('inscrit','present','absent','annule') NOT NULL DEFAULT 'inscrit',
  `message` text DEFAULT NULL,
  `besoins_accessibilite` set('lsf','sous_titrage','documents_accessibles','stationnement_adapte','assistance_personnelle','aucun_besoin') NOT NULL DEFAULT 'aucun_besoin',
  `nombre_accompagnants` int(11) NOT NULL DEFAULT 0,
  `date_inscription` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

CREATE TABLE `post` (
  `Id_post` int(11) NOT NULL,
  `Id_utilisateur` int(11) NOT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `categorie` enum('opportunites','evenements','campagnes','questions','ressources','autre') DEFAULT 'autre',
  `contenu` text DEFAULT NULL,
  `piece_jointe` varchar(255) DEFAULT NULL,
  `likes` int(11) NOT NULL DEFAULT 0,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modification` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `profil`
--

CREATE TABLE `profil` (
  `Id_profil` int(11) NOT NULL,
  `Id_utilisateur` int(11) NOT NULL,
  `photo_profil` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `ville` text DEFAULT NULL,
  `pays` text DEFAULT NULL,
  `profession` text DEFAULT NULL,
  `competences` text DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modification` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reclamation`
--

CREATE TABLE `reclamation` (
  `Id_reclamation` int(11) NOT NULL,
  `Id_utilisateur` int(11) NOT NULL,
  `type_reclamation` enum('accessibilite','contenu_inapproprié','technique','discrimination','harcelement','autre') NOT NULL DEFAULT 'autre',
  `niveau_gravite` enum('faible','moyen','eleve') DEFAULT 'faible',
  `objet` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `piece_jointe` varchar(255) DEFAULT NULL,
  `statut` enum('non_traite','traite') DEFAULT 'non_traite',
  `date_reclamation` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reponse`
--

CREATE TABLE `reponse` (
  `Id_reponse` int(11) NOT NULL,
  `Id_reclamation` int(11) NOT NULL,
  `Id_utilisateur` int(11) NOT NULL,
  `message` text NOT NULL,
  `piece_jointe` varchar(255) DEFAULT NULL,
  `type_reponse` enum('premiere','suivi','resolution') NOT NULL DEFAULT 'premiere',
  `date_reponse` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `Id_utilisateur` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `genre` enum('femme','homme','prefere_ne_pas_dire') NOT NULL DEFAULT 'prefere_ne_pas_dire',
  `date_naissance` date DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `numero_tel` varchar(20) DEFAULT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `type_handicap` set('aucun','moteur','visuel','auditif','mental','autre','tous') NOT NULL DEFAULT 'aucun',
  `date_inscription` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `campagnecollecte`
--
ALTER TABLE `campagnecollecte`
  ADD PRIMARY KEY (`Id_campagne`),
  ADD KEY `idx_campagne_admin` (`Id_utilisateur`);

--
-- Index pour la table `candidature`
--
ALTER TABLE `candidature`
  ADD PRIMARY KEY (`Id_candidature`),
  ADD UNIQUE KEY `unique_application` (`Id_offre`,`Id_utilisateur`),
  ADD KEY `idx_candidature_offre` (`Id_offre`),
  ADD KEY `idx_candidature_user` (`Id_utilisateur`);

--
-- Index pour la table `commentaire`
--
ALTER TABLE `commentaire`
  ADD PRIMARY KEY (`Id_commentaire`),
  ADD KEY `idx_comment_post` (`Id_post`),
  ADD KEY `commentaire_ibfk_2` (`Id_utilisateur`);

--
-- Index pour la table `don`
--
ALTER TABLE `don`
  ADD PRIMARY KEY (`Id_don`),
  ADD KEY `don_ibfk_1` (`Id_utilisateur`),
  ADD KEY `Id_campagne` (`Id_campagne`);

--
-- Index pour la table `evenement`
--
ALTER TABLE `evenement`
  ADD PRIMARY KEY (`Id_evenement`),
  ADD KEY `idx_evenement_user` (`Id_utilisateur`);

--
-- Index pour la table `offre`
--
ALTER TABLE `offre`
  ADD PRIMARY KEY (`Id_offre`),
  ADD KEY `idx_offre_user` (`Id_utilisateur`);

--
-- Index pour la table `participation`
--
ALTER TABLE `participation`
  ADD PRIMARY KEY (`Id_participation`),
  ADD UNIQUE KEY `unique_participation` (`Id_evenement`,`Id_utilisateur`),
  ADD KEY `idx_participation_event` (`Id_evenement`),
  ADD KEY `idx_participation_user` (`Id_utilisateur`);

--
-- Index pour la table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`Id_post`),
  ADD KEY `idx_post_user` (`Id_utilisateur`);

--
-- Index pour la table `profil`
--
ALTER TABLE `profil`
  ADD PRIMARY KEY (`Id_profil`),
  ADD UNIQUE KEY `unique_user_profile` (`Id_utilisateur`);

--
-- Index pour la table `reclamation`
--
ALTER TABLE `reclamation`
  ADD PRIMARY KEY (`Id_reclamation`),
  ADD KEY `idx_reclamation_user` (`Id_utilisateur`);

--
-- Index pour la table `reponse`
--
ALTER TABLE `reponse`
  ADD PRIMARY KEY (`Id_reponse`),
  ADD KEY `idx_reponse_reclamation` (`Id_reclamation`),
  ADD KEY `idx_reponse_user` (`Id_utilisateur`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`Id_utilisateur`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `numero_tel` (`numero_tel`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `campagnecollecte`
--
ALTER TABLE `campagnecollecte`
  MODIFY `Id_campagne` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `candidature`
--
ALTER TABLE `candidature`
  MODIFY `Id_candidature` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `commentaire`
--
ALTER TABLE `commentaire`
  MODIFY `Id_commentaire` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `don`
--
ALTER TABLE `don`
  MODIFY `Id_don` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `evenement`
--
ALTER TABLE `evenement`
  MODIFY `Id_evenement` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `offre`
--
ALTER TABLE `offre`
  MODIFY `Id_offre` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `participation`
--
ALTER TABLE `participation`
  MODIFY `Id_participation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `post`
--
ALTER TABLE `post`
  MODIFY `Id_post` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `profil`
--
ALTER TABLE `profil`
  MODIFY `Id_profil` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reclamation`
--
ALTER TABLE `reclamation`
  MODIFY `Id_reclamation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reponse`
--
ALTER TABLE `reponse`
  MODIFY `Id_reponse` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `Id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `campagnecollecte`
--
ALTER TABLE `campagnecollecte`
  ADD CONSTRAINT `campagnecollecte_ibfk_1` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `candidature`
--
ALTER TABLE `candidature`
  ADD CONSTRAINT `candidature_ibfk_1` FOREIGN KEY (`Id_offre`) REFERENCES `offre` (`Id_offre`) ON DELETE CASCADE,
  ADD CONSTRAINT `candidature_ibfk_2` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `commentaire`
--
ALTER TABLE `commentaire`
  ADD CONSTRAINT `commentaire_ibfk_2` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `don`
--
ALTER TABLE `don`
  ADD CONSTRAINT `don_ibfk_1` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`),
  ADD CONSTRAINT `don_ibfk_2` FOREIGN KEY (`Id_campagne`) REFERENCES `campagnecollecte` (`Id_campagne`);

--
-- Contraintes pour la table `evenement`
--
ALTER TABLE `evenement`
  ADD CONSTRAINT `evenement_ibfk_1` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `offre`
--
ALTER TABLE `offre`
  ADD CONSTRAINT `offre_ibfk_1` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `participation_ibfk_1` FOREIGN KEY (`Id_evenement`) REFERENCES `evenement` (`Id_evenement`) ON DELETE CASCADE,
  ADD CONSTRAINT `participation_ibfk_2` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `profil`
--
ALTER TABLE `profil`
  ADD CONSTRAINT `profil_ibfk_1` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reclamation`
--
ALTER TABLE `reclamation`
  ADD CONSTRAINT `reclamation_ibfk_1` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reponse`
--
ALTER TABLE `reponse`
  ADD CONSTRAINT `reponse_ibfk_1` FOREIGN KEY (`Id_reclamation`) REFERENCES `reclamation` (`Id_reclamation`) ON DELETE CASCADE,
  ADD CONSTRAINT `reponse_ibfk_2` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
