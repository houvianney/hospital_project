-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 05 avr. 2026 à 20:06
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_medicale_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `consultations`
--

DROP TABLE IF EXISTS `consultations`;
CREATE TABLE IF NOT EXISTS `consultations` (
  `consultation_id` int NOT NULL AUTO_INCREMENT,
  `nom_patient` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_heure` datetime NOT NULL,
  `motif` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `medecin_id` int DEFAULT NULL,
  `statut` enum('En attente','En cours','Terminée','') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'En attente',
  PRIMARY KEY (`consultation_id`),
  KEY `fk1` (`medecin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `consultations`
--

INSERT INTO `consultations` (`consultation_id`, `nom_patient`, `date_heure`, `motif`, `medecin_id`, `statut`) VALUES
(4, 'BAKE', '2026-04-03 12:32:00', 'azegezsaa', 3, 'Terminée'),
(6, 'BAKE', '2026-04-03 13:24:00', 'azegezs', 1, 'Terminée'),
(8, 'Vianney', '2026-04-04 09:19:00', 'azegezsaa', 3, 'Terminée'),
(9, 'Vianney', '2026-04-04 13:17:00', 'azegezs', NULL, 'En attente');

-- --------------------------------------------------------

--
-- Structure de la table `medecins`
--

DROP TABLE IF EXISTS `medecins`;
CREATE TABLE IF NOT EXISTS `medecins` (
  `medecin_id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenoms` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `specialite` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `disponible` enum('Oui','Non','','') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`medecin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `medecins`
--

INSERT INTO `medecins` (`medecin_id`, `nom`, `prenoms`, `specialite`, `telephone`, `disponible`) VALUES
(1, 'Big', 'Medecin', 'Généraliste', '0152352881', 'Oui'),
(2, 'Boss', 'Doc', 'Dentiste', '0152352881', 'Non'),
(3, 'Bigboss', 'Med', 'Gynecologue', '', 'Oui');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `utilisateur_id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenoms` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('Personnel','user') DEFAULT 'Personnel',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`utilisateur_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`utilisateur_id`, `nom`, `prenoms`, `email`, `mot_de_passe`, `role`, `created_at`) VALUES
(1, 'HOUANGNI', 'Vianney', 'houangnivianney57@gmail.com', '$2y$10$k6mqSn1a7OKITQZcOr4GTeb8Qz0oksh2fnXfzUdA4bvJOFvCNTAc2', 'Personnel', '2026-04-04 12:14:18');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
