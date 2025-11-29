-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 23 nov. 2025 à 13:39
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

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
  `montant_actuel` decimal(15,2) NOT NULL DEFAULT 0.00,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `campagnecollecte`
--

INSERT INTO `campagnecollecte` (`Id_campagne`, `Id_utilisateur`, `titre`, `categorie_impact`, `urgence`, `description`, `statut`, `image_campagne`, `objectif_montant`, `montant_actuel`, `date_debut`, `date_fin`) VALUES
(2, 2, 'lala lala lala lala', 'sante', 'critique', 'Financement de matériel pédagogique adapté et formation des enseignants pour l inclusion des enfants malentendants dans les écoles primaires.', 'active', NULL, 30000.00, 8000.00, '2025-12-02', '2025-12-30'),
(23, 3, 'Transport médical adapté', 'sante', 'elevee', 'Service de transport médical pour consultations et traitements des personnes à mobilité réduite', 'active', NULL, 40000.00, 0.00, '2025-01-10', '2025-12-31'),
(24, 4, 'Ateliers artistiques inclusifs', '', 'normale', 'Organisation d\'ateliers de peinture, musique et poterie accessibles à tous les handicaps', 'active', NULL, 12000.00, 5400.00, '2024-11-30', '2025-05-31'),
(25, 5, 'Équipement sportif paralympique', '', 'elevee', 'Achat de matériel sportif spécialisé pour la pratique du basket fauteuil et athlétisme handisport', 'active', NULL, 28000.00, 8900.00, '2024-12-10', '2025-08-31'),
(28, 2, 'Équipement médical pour enfants handicapés', 'sante', 'critique', 'Achat de fauteuils roulants et équipements orthopédiques pour enfants handicapés moteurs', 'active', NULL, 25000.00, 4500.00, '2024-11-20', '2025-02-28');

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
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `campagnecollecte`
--
ALTER TABLE `campagnecollecte`
  MODIFY `Id_campagne` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `campagnecollecte`
--
ALTER TABLE `campagnecollecte`
  ADD CONSTRAINT `campagnecollecte_ibfk_1` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
