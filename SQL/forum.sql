-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 29 mars 2023 à 14:37
-- Version du serveur : 8.0.32
-- Version de PHP : 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `forum`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

DROP TABLE IF EXISTS `articles`;
CREATE TABLE IF NOT EXISTS `articles` (
  `idArticle` int NOT NULL AUTO_INCREMENT,
  `datePublication` datetime NOT NULL,
  `contenu` text COLLATE utf8mb4_bin NOT NULL,
  `idUser` int NOT NULL,
  PRIMARY KEY (`idArticle`),
  KEY `fk_articles_iduser` (`idUser`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`idArticle`, `datePublication`, `contenu`, `idUser`) VALUES
(3, '2023-03-29 16:20:47', 'Les grèves ça fait beaucoup de bouchon sur l\'autoroute', 10),
(4, '2023-03-29 16:22:39', 'Chat Gpt une nouvelle version miaou miaou miaou miaou!', 11),
(5, '2023-03-29 16:25:43', 'La Cavalerie est là mais pas ici', 12),
(6, '2023-03-29 16:26:31', 'Le steak de cheval est mon plat préféré', 12),
(7, '2023-03-29 16:28:04', 'Je suis une saucisse', 11),
(8, '2023-03-29 16:32:29', 'Non je n\'ai pas d\'autre phrases d\'exemples', 10);

-- --------------------------------------------------------

--
-- Structure de la table `liker`
--

DROP TABLE IF EXISTS `liker`;
CREATE TABLE IF NOT EXISTS `liker` (
  `idArticle` int NOT NULL,
  `idUser` int NOT NULL,
  `etatLike` tinyint(1) NOT NULL,
  PRIMARY KEY (`idArticle`,`idUser`),
  KEY `fk_liker_idUser` (`idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `liker`
--

INSERT INTO `liker` (`idArticle`, `idUser`, `etatLike`) VALUES
(3, 12, 0),
(4, 10, 1),
(5, 11, 0),
(6, 12, 1),
(7, 10, 1),
(8, 11, 1);

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `idRole` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(20) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`idRole`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`idRole`, `libelle`) VALUES
(1, 'moderator'),
(2, 'publisher');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `idUser` int NOT NULL AUTO_INCREMENT,
  `login` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `idRole` int NOT NULL,
  PRIMARY KEY (`idUser`),
  UNIQUE KEY `login` (`login`),
  KEY `fk_utilisateur_idrole` (`idRole`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`idUser`, `login`, `password`, `idRole`) VALUES
(10, 'Neiko', '$2y$12$E9xqFvle876mywNqTizwx.QTokHTQBM4doQrLa7NQhantYyy6ccGK', 2),
(11, 'Chalumeau', '$2y$12$mNZmUzetEH9x1iWGWjwlmulpFVHtDR8hlQ4xEkmqibC4aS2oJR1Zm', 2),
(12, 'Menwizz', '$2y$12$KH.AOmTEq7ddPzi/l6hqDOnOJlylgxqTQTIm0FjRoM.5JSPGAZtea', 2),
(13, 'Spiegel', '$2y$12$uqHfzv88ELen/oKBeD5OX.TiqatfRNYLpmx3FKbZc4G5S7/YuhIcW', 1),
(14, 'admin', '$2y$12$xfLsBEw9Ou9qslLNx8kmLejA8ebU6rRuohdBTrJXwJ2tu6cPeV.Ru', 1);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `fk_articles_iduser` FOREIGN KEY (`idUser`) REFERENCES `utilisateur` (`idUser`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `liker`
--
ALTER TABLE `liker`
  ADD CONSTRAINT `fk_liker_idArticle` FOREIGN KEY (`idArticle`) REFERENCES `articles` (`idArticle`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_liker_idUser` FOREIGN KEY (`idUser`) REFERENCES `utilisateur` (`idUser`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `fk_utilisateur_idrole` FOREIGN KEY (`idRole`) REFERENCES `role` (`idRole`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
