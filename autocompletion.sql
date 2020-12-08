-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  mar. 08 déc. 2020 à 22:11
-- Version du serveur :  10.4.10-MariaDB
-- Version de PHP :  7.4.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `autocompletion`
--

-- --------------------------------------------------------

--
-- Structure de la table `operators`
--

DROP TABLE IF EXISTS `operators`;
CREATE TABLE IF NOT EXISTS `operators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operator_name` varchar(255) NOT NULL,
  `side` varchar(255) NOT NULL,
  `operation_reveal` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=60 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `operators`
--

INSERT INTO `operators` (`id`, `operator_name`, `side`, `operation_reveal`) VALUES
(1, 'Recrue', 'att/def', 'pionniers'),
(2, 'Sledge', 'att', 'pionniers'),
(3, 'Thatcher', 'att', 'pionniers'),
(4, 'Smoke ', 'def', 'pionniers'),
(5, 'Mute', 'def', 'pionniers'),
(6, 'Ash', 'att', 'pionniers'),
(7, 'Thermite', 'att', 'pionniers'),
(8, 'Castle', 'def', 'pionniers'),
(9, 'Pulse', 'def', 'pionniers'),
(10, 'Twitch', 'att', 'pionniers'),
(11, 'Montagne', 'att', 'pionniers'),
(12, 'Doc', 'def', 'pionniers'),
(13, 'Rook', 'def\r\n', 'pionniers'),
(14, 'Glaz', 'att\r\n', 'pionniers'),
(15, 'Fuze', 'att', 'pionniers'),
(16, 'Kapkan', 'def\r\n', 'pionniers'),
(17, 'Tachanka', 'def\r\n', 'pionniers'),
(18, 'Blitz', 'att\r\n', 'pionniers'),
(19, 'IQ', 'att', 'pionniers'),
(20, 'Jäger', 'def', 'pionniers'),
(21, 'Bandit', 'def', 'pionniers'),
(22, 'Buck', 'att', 'Operation Burnt Horizon '),
(23, 'Frost', 'def\r\n', 'Operation Burnt Horizon '),
(24, 'Blackbeard', 'att', 'Operation Burnt Horizon '),
(25, 'Valkyrie', 'def', 'Operation Burnt Horizon '),
(26, 'Capitão', 'att', 'Operation Phantom Sight'),
(27, 'Caveira', 'def', 'Operation Phantom Sight'),
(28, 'Hibana\r\n', 'att', 'Operation Red Crow'),
(29, 'Echo', 'def', 'Operation Red Crow'),
(30, 'Jackal', 'att', 'Operation Velvet Shell'),
(31, 'Mira', 'def', 'Operation Velvet Shell'),
(32, 'Ying', 'att\r\n', 'Operation Blood Orchid '),
(33, 'Lesion', 'def', 'Operation Blood Orchid '),
(34, 'Zofia', 'att', 'Operation White Noise '),
(35, 'Ela', 'def', 'Operation Blood Orchid '),
(36, 'Dokkaebi', 'att', 'Operation White Noise'),
(37, 'Vigil ', 'def', 'Operation White Noise '),
(38, 'Lion', 'att', 'Operation Chimera'),
(39, 'Finka', 'att', 'Operation Chimera'),
(40, 'Maestro', 'def', 'Operation Para Bellum'),
(41, 'Alibi', 'def\r\n', 'Operation Para Bellum'),
(42, 'Maverick', 'att', 'Operation Grim Sky'),
(43, 'Clash', 'def', 'Operation Grim Sky'),
(44, 'Nomad', 'att', 'Operation Wind Bastion '),
(45, 'Kaid', 'def', 'Operation Wind Bastion '),
(46, 'Gridlock', 'att', 'Operation Burnt Horizon '),
(47, 'Mozzie', 'def', 'Operation Burnt Horizon '),
(48, 'Nøkk', 'att', 'Operation Phantom Sight '),
(49, 'Warden\r\n', 'def', 'Operation Phantom Sight '),
(50, 'Amaru', 'att', 'Operation Ember Rise'),
(51, 'Goyo', 'def', 'Operation Ember Rise'),
(52, 'Kali', 'att', 'Operation Shifting Tides '),
(53, 'Wamai', 'def', 'Operation Shifting Tides '),
(54, 'Iana', 'att', 'Operation Void Edge'),
(55, 'Oryx', 'def', 'Operation Void Edge'),
(56, 'Ace', 'att', 'Operation Steel Wave'),
(57, 'Melusi', 'def', 'Operation Steel Wave'),
(58, 'Zero', 'att', 'Operation Shadow Legacy'),
(59, 'Aruni\r\n', 'def', 'Operation Neon Dawn ');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
