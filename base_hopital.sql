-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 29 nov. 2024 à 09:10
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
-- Base de données : `hopital_php`
--
-- Création de la base de données si elle n'existe pas
CREATE DATABASE IF NOT EXISTS `hopital_php` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `hopital_php`;

-- Création de l'utilisateur et attribution des droits
CREATE USER 'user1'@'localhost' IDENTIFIED BY 'hcetylop';
GRANT ALL PRIVILEGES ON `hopital_php`.* TO 'user1'@'localhost';
FLUSH PRIVILEGES;
-- --------------------------------------------------------

--
-- Structure de la table `motifs`
--

DROP TABLE IF EXISTS `motifs`;
CREATE TABLE IF NOT EXISTS `motifs` (
  `Code` int NOT NULL AUTO_INCREMENT,
  `Libellé` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`Code`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `motifs`
--

INSERT INTO `motifs` (`Code`, `Libellé`) VALUES
(1, 'Consultation Libre\r\n'),
(2, 'Urgence'),
(3, 'Prescription');

-- --------------------------------------------------------

--
-- Structure de la table `patients`
--

DROP TABLE IF EXISTS `patients`;
CREATE TABLE IF NOT EXISTS `patients` (
  `Code` int NOT NULL AUTO_INCREMENT,
  `Nom` varchar(100) NOT NULL,
  `Prenom` varchar(100) NOT NULL,
  `Sexe` char(1) NOT NULL,
  `Date_Naissance` date NOT NULL,
  `Numero_SecSoc` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Code_Pays` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Date_Premiere_Entree` date NOT NULL,
  `Code_Motif` int NOT NULL,
  PRIMARY KEY (`Code`),
  UNIQUE KEY `Numero_SecSoc` (`Numero_SecSoc`),
  KEY `Sexe` (`Sexe`),
  KEY `Code_Pays` (`Code_Pays`),
  KEY `Code_Motif` (`Code_Motif`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `patients`
--

INSERT INTO `patients` (`Code`, `Nom`, `Prenom`, `Sexe`, `Date_Naissance`, `Numero_SecSoc`, `Code_Pays`, `Date_Premiere_Entree`, `Code_Motif`) VALUES
(5, 'COTILLARD', 'Marion', 'F', '1975-09-30', '275097503200542', 'FR', '2023-09-26', 1),
(4, 'RENO', 'Jean', 'M', '1948-07-30', NULL, 'MA', '2023-08-18', 1),
(3, 'DUJARDIN', 'Jean', 'M', '1972-06-19', '172065903800855', 'FR', '2023-06-12', 3),
(2, 'DEPARDIEU', 'Gérard', 'M', '1948-12-27', '148127504406759', 'FR', '2023-04-05', 2),
(1, 'SY', 'Omar', 'M', '1978-01-20', '178017830240455', 'FR', '2023-02-01', 1),
(6, 'CASSEL', 'Vincent', 'M', '1966-11-23', '166117500600711', 'FR', '2023-01-01', 3),
(7, 'GREEN', 'Eva', 'F', '1980-06-17', '280067500400733', 'FR', '2023-11-15', 2),
(8, 'EFIRA', 'Virginie', 'F', '1977-05-05', NULL, 'BE', '2023-10-30', 2);

-- --------------------------------------------------------

--
-- Structure de la table `pays`
--

DROP TABLE IF EXISTS `pays`;
CREATE TABLE IF NOT EXISTS `pays` (
  `Code` varchar(2) NOT NULL,
  `Libellé` varchar(100) NOT NULL,
  PRIMARY KEY (`Code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `pays`
--

INSERT INTO `pays` (`Code`, `Libellé`) VALUES
('FR', 'France'),
('BE', 'Belgique'),
('MA', 'Maroc'),
('TN', 'Tunisie'),
('DZ', 'Algérie');

-- --------------------------------------------------------

--
-- Structure de la table `sexe`
--

DROP TABLE IF EXISTS `sexe`;
CREATE TABLE IF NOT EXISTS `sexe` (
  `Code` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Libellé` varchar(10) NOT NULL,
  PRIMARY KEY (`Code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `sexe`
--

INSERT INTO `sexe` (`Code`, `Libellé`) VALUES
('F', 'Féminin'),
('M', 'Masculin'),
('N', 'Ne se pron');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*Structure de la table `documents`*/

CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin VARCHAR(255) NOT NULL,
    type ENUM('ordonnance', 'prescription', 'identité') NOT NULL,
    nature_fichier ENUM('PDF', 'Image', 'Autre') NOT NULL,
    contenu TEXT,
    date_document DATE,
    date_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES Patients(code) ON DELETE CASCADE
);
