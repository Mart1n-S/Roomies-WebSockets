-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : database
-- Généré le : lun. 21 juil. 2025 à 00:17
-- Version du serveur : 9.3.0
-- Version de PHP : 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bdd_roomies_test`
--
CREATE DATABASE IF NOT EXISTS `bdd_roomies_test` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `bdd_roomies_test`;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250521202828', '2025-07-20 23:33:15', 255),
('DoctrineMigrations\\Version20250522135805', '2025-07-20 23:33:15', 1168),
('DoctrineMigrations\\Version20250522143627', '2025-07-20 23:33:16', 188),
('DoctrineMigrations\\Version20250713114325', '2025-07-20 23:33:17', 94),
('DoctrineMigrations\\Version20250716192405', '2025-07-20 23:33:17', 139),
('DoctrineMigrations\\Version20250720183217', '2025-07-20 23:33:17', 102);

-- --------------------------------------------------------

--
-- Structure de la table `friendship`
--

CREATE TABLE `friendship` (
  `id` binary(16) NOT NULL COMMENT '(DC2Type:uuid)',
  `applicant_id` binary(16) NOT NULL COMMENT '(DC2Type:uuid)',
  `recipient_id` binary(16) NOT NULL COMMENT '(DC2Type:uuid)',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `friendship`
--

INSERT INTO `friendship` (`id`, `applicant_id`, `recipient_id`, `status`, `created_at`, `updated_at`) VALUES
(0x01982a57469e7d95bf1eb89524fe713f, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a572d3a7fc39ccb365b807abe45, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469e7d95bf1eb89525eb7119, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5722a176e9911daa2f3536d0cb, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469e7d95bf1eb8952699480d, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5742b37b9387d55b8748d9b342, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469e7d95bf1eb895279179ee, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5744a5710b92b2a95ddec0f5f2, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469e7d95bf1eb8952829e305, 0x01982a5718ac7cc78dea86e21d7f38fb, 0x01982a5726de7aa6832a680ee8bd9ca5, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469e7d95bf1eb89528dacacd, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5722a176e9911daa2f3536d0cb, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469e7d95bf1eb89529d062d9, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5737047b81b1b0d050c878dbc3, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469e7d95bf1eb8952a39f1c3, 0x01982a571c8a7474977fa27e565a025e, 0x01982a5720827705acf25634cd3eaedb, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469e7d95bf1eb8952ad8cec5, 0x01982a571c8a7474977fa27e565a025e, 0x01982a5737047b81b1b0d050c878dbc3, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469e7d95bf1eb8952bb688d1, 0x01982a571e7e78488e3db42f63d9ac44, 0x01982a5724997b4196355691d5f6e022, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469e7d95bf1eb8952c34119c, 0x01982a571e7e78488e3db42f63d9ac44, 0x01982a5740d17362bd011b3bee97dcb3, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469e7d95bf1eb8952cc512dc, 0x01982a5720827705acf25634cd3eaedb, 0x01982a5733237047aba87f42a3e4864a, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d8129bc0c8, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5735137fe38da4a6967b3a6ad4, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d812b6625b, 0x01982a5724997b4196355691d5f6e022, 0x01982a57469e7d95bf1eb89524b08f15, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d81387e84a, 0x01982a5724997b4196355691d5f6e022, 0x01982a57291878d0a1d70e1cfa8add72, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d813c53e56, 0x01982a5726de7aa6832a680ee8bd9ca5, 0x01982a572b327901b0e83d3d7b37477c, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d814043cb5, 0x01982a5726de7aa6832a680ee8bd9ca5, 0x01982a5740d17362bd011b3bee97dcb3, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d815026927, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a571c8a7474977fa27e565a025e, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d815baafd5, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a573ed6770dbf26364df3ff9b13, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d8164265f4, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a571e7e78488e3db42f63d9ac44, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d81702116c, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5742b37b9387d55b8748d9b342, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d817e80a7a, 0x01982a572d3a7fc39ccb365b807abe45, 0x01982a5735137fe38da4a6967b3a6ad4, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d81860fcff, 0x01982a572f347f1185eb9ba408f9ae78, 0x01982a571a9f74169f83319d5a7760ca, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d81941ce74, 0x01982a57313179fd81df21c22f43b39a, 0x01982a573cdb7fb4aa8a1a70bf4dc149, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d819b99be1, 0x01982a57313179fd81df21c22f43b39a, 0x01982a5738eb7c1ca1beaefa92041549, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d819e0588a, 0x01982a5733237047aba87f42a3e4864a, 0x01982a5740d17362bd011b3bee97dcb3, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d819ec3128, 0x01982a5733237047aba87f42a3e4864a, 0x01982a571a9f74169f83319d5a7760ca, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d81a5352de, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a57313179fd81df21c22f43b39a, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d81b39e04b, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a57291878d0a1d70e1cfa8add72, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d81b465265, 0x01982a5738eb7c1ca1beaefa92041549, 0x01982a573ed6770dbf26364df3ff9b13, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d81c103318, 0x01982a5738eb7c1ca1beaefa92041549, 0x01982a57291878d0a1d70e1cfa8add72, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d81c12a3c7, 0x01982a573adf734b9780b364ffa21a9f, 0x01982a5718ac7cc78dea86e21d7f38fb, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d81c3392ca, 0x01982a573adf734b9780b364ffa21a9f, 0x01982a5744a5710b92b2a95ddec0f5f2, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d81c6a49ff, 0x01982a573cdb7fb4aa8a1a70bf4dc149, 0x01982a5742b37b9387d55b8748d9b342, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d81cc3b1cc, 0x01982a573ed6770dbf26364df3ff9b13, 0x01982a5718ac7cc78dea86e21d7f38fb, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d81cc9dbf8, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a573adf734b9780b364ffa21a9f, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d81ce7e749, 0x01982a5744a5710b92b2a95ddec0f5f2, 0x01982a5733237047aba87f42a3e4864a, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d81ddb6c61, 0x01982a5744a5710b92b2a95ddec0f5f2, 0x01982a572b327901b0e83d3d7b37477c, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59'),
(0x01982a57469f7a4cbd4de4d81e4d0ae6, 0x01982a57469e7d95bf1eb89524b08f15, 0x01982a5742b37b9387d55b8748d9b342, 'friend', '2025-07-21 00:16:59', '2025-07-21 00:16:59');

-- --------------------------------------------------------

--
-- Structure de la table `game_room`
--

CREATE TABLE `game_room` (
  `id` int NOT NULL,
  `creator_id` binary(16) DEFAULT NULL COMMENT '(DC2Type:uuid)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `game` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `game_room`
--

INSERT INTO `game_room` (`id`, `creator_id`, `name`, `game`, `created_at`) VALUES
(5, 0x01982a5740d17362bd011b3bee97dcb3, 'Gogo Morpion #1', 'morpion', '2025-07-20 00:16:59'),
(6, 0x01982a5718ac7cc78dea86e21d7f38fb, 'Tic Tac Toe #2', 'morpion', '2025-07-20 22:16:59');

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `id` binary(16) NOT NULL COMMENT '(DC2Type:uuid)',
  `sender_id` binary(16) NOT NULL COMMENT '(DC2Type:uuid)',
  `room_id` binary(16) NOT NULL COMMENT '(DC2Type:uuid)',
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`id`, `sender_id`, `room_id`, `content`, `file_name`, `created_at`, `type`) VALUES
(0x01982a5746ab7a62b6e189fa61cf4d19, 0x01982a572d3a7fc39ccb365b807abe45, 0x01982a5746a07d7fbe292d152c044467, 'Hey delaunay.julie ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c57648972b85c9, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152c044467, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c5764897d0cda9, 0x01982a572d3a7fc39ccb365b807abe45, 0x01982a5746a07d7fbe292d152c044467, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c5764898877e5f, 0x01982a572d3a7fc39ccb365b807abe45, 0x01982a5746a07d7fbe292d152c044467, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c5764898c03056, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152c044467, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c576489917da2a, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152c044467, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c5764899252821, 0x01982a572d3a7fc39ccb365b807abe45, 0x01982a5746a07d7fbe292d152c044467, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c57648999fe993, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152c044467, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c576489a0b6298, 0x01982a572d3a7fc39ccb365b807abe45, 0x01982a5746a07d7fbe292d152c044467, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c576489a270b20, 0x01982a572d3a7fc39ccb365b807abe45, 0x01982a5746a07d7fbe292d152c044467, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c576489adedb64, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5746a07d7fbe292d152c5f026d, 'Hey laurence.humbert ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c576489b932ed0, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5746a07d7fbe292d152c5f026d, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c576489ba32a83, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5746a07d7fbe292d152c5f026d, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c576489befab24, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152c5f026d, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c576489cc29344, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152c5f026d, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c576489ccad0c3, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5746a07d7fbe292d152c5f026d, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c576489db938f7, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152c5f026d, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c576489eb832cf, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152c5f026d, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c576489ec27926, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152c5f026d, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c576489f6f46a3, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152c5f026d, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c57648a023818b, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152c5f026d, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c57648a0c7786b, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5746a07d7fbe292d152c5f026d, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c57648a113fbf1, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a07d7fbe292d152d9941b8, 'Hey ami_fixe_1 ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c57648a1a2caf9, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a07d7fbe292d152d9941b8, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c57648a1edda7f, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152d9941b8, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c57648a273143b, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a07d7fbe292d152d9941b8, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c57648a30a2b27, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152d9941b8, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ac7cefa2c57648a320bb21, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152d9941b8, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6df4e4c11, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a07d7fbe292d152d9941b8, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6df70662f, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a07d7fbe292d152d9941b8, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6dfb5d4f6, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152fcad123, 'Hey userClassique ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e05d29a5, 0x01982a5744a5710b92b2a95ddec0f5f2, 0x01982a5746a07d7fbe292d152fcad123, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e0d4989e, 0x01982a5744a5710b92b2a95ddec0f5f2, 0x01982a5746a07d7fbe292d152fcad123, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e129e464, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152fcad123, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e13a5582, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152fcad123, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e199b91a, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152fcad123, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e1cd723b, 0x01982a5726de7aa6832a680ee8bd9ca5, 0x01982a5746a07d7fbe292d15310fb76a, 'Hey salmon.anouk ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e207b1e3, 0x01982a5718ac7cc78dea86e21d7f38fb, 0x01982a5746a07d7fbe292d15310fb76a, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e2b3977a, 0x01982a5726de7aa6832a680ee8bd9ca5, 0x01982a5746a07d7fbe292d15310fb76a, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e346282b, 0x01982a5718ac7cc78dea86e21d7f38fb, 0x01982a5746a07d7fbe292d15310fb76a, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e4040c0f, 0x01982a5726de7aa6832a680ee8bd9ca5, 0x01982a5746a07d7fbe292d15310fb76a, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e43d66f7, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5746a07d7fbe292d1533007c66, 'Hey laurence.humbert ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e4f063bc, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5746a07d7fbe292d1533007c66, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e50e80ce, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a07d7fbe292d1533007c66, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e51ba470, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5746a07d7fbe292d1533007c66, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e59ae848, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a07d7fbe292d1533007c66, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e5c8e637, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5746a07d7fbe292d1533007c66, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e615b04e, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5746a07d7fbe292d1533007c66, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e7073e72, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5746a07d7fbe292d1533007c66, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e7847503, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a07d7fbe292d1533007c66, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e7c65ea8, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a07d7fbe292d1533fc17b0, 'Hey aimee08 ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e7f69eef, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a07d7fbe292d1533fc17b0, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e8cc8ec9, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a07d7fbe292d1533fc17b0, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e95e20df, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a07d7fbe292d1533fc17b0, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6e9e5034d, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a07d7fbe292d1533fc17b0, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6ead7ba81, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a07d7fbe292d1533fc17b0, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6ebca1e7f, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a07d7fbe292d1533fc17b0, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6ebe5c505, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a07d7fbe292d1533fc17b0, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6ec126937, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a07d7fbe292d1533fc17b0, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6ecd03a09, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a07d7fbe292d1533fc17b0, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6ed81b99a, 0x01982a571c8a7474977fa27e565a025e, 0x01982a5746a07d7fbe292d15357a7933, 'Hey ilefort ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6ed8d67b5, 0x01982a571c8a7474977fa27e565a025e, 0x01982a5746a07d7fbe292d15357a7933, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6edfafe95, 0x01982a5720827705acf25634cd3eaedb, 0x01982a5746a07d7fbe292d15357a7933, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6eeb8d9c9, 0x01982a571c8a7474977fa27e565a025e, 0x01982a5746a07d7fbe292d15357a7933, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6eede1f3f, 0x01982a571c8a7474977fa27e565a025e, 0x01982a5746a07d7fbe292d15357a7933, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6ef2ddf5a, 0x01982a571c8a7474977fa27e565a025e, 0x01982a5746a07d7fbe292d15357a7933, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ad7c12841c56e6efb05e0f, 0x01982a5720827705acf25634cd3eaedb, 0x01982a5746a07d7fbe292d15357a7933, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9a41fa12a, 0x01982a571c8a7474977fa27e565a025e, 0x01982a5746a07d7fbe292d15357a7933, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9a43d0684, 0x01982a571c8a7474977fa27e565a025e, 0x01982a5746a07d7fbe292d15357a7933, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9a44e61b2, 0x01982a5720827705acf25634cd3eaedb, 0x01982a5746a07d7fbe292d15357a7933, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9a4b7ac12, 0x01982a5720827705acf25634cd3eaedb, 0x01982a5746a07d7fbe292d15357a7933, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9a4e587d7, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a07d7fbe292d1537e2aa1b, 'Hey aimee08 ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9a5420a99, 0x01982a571c8a7474977fa27e565a025e, 0x01982a5746a07d7fbe292d1537e2aa1b, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9a5a6fa7f, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a07d7fbe292d1537e2aa1b, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9a63159ae, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a07d7fbe292d1537e2aa1b, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9a7003c6f, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a07d7fbe292d1537e2aa1b, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9a7b4fe16, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a07d7fbe292d1537e2aa1b, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9a838fdfe, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a07d7fbe292d1537e2aa1b, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9a86ac388, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a07d7fbe292d1537e2aa1b, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9a952377e, 0x01982a571c8a7474977fa27e565a025e, 0x01982a5746a07d7fbe292d1537e2aa1b, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9a9fd8dcf, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a07d7fbe292d1537e2aa1b, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9aaf78f08, 0x01982a571e7e78488e3db42f63d9ac44, 0x01982a5746a07d7fbe292d1539cf9a46, 'Hey bbarbe ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9ab575796, 0x01982a5724997b4196355691d5f6e022, 0x01982a5746a07d7fbe292d1539cf9a46, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9ac30f21f, 0x01982a571e7e78488e3db42f63d9ac44, 0x01982a5746a07d7fbe292d1539cf9a46, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9ac99cfde, 0x01982a5724997b4196355691d5f6e022, 0x01982a5746a07d7fbe292d1539cf9a46, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9acab97cd, 0x01982a5724997b4196355691d5f6e022, 0x01982a5746a07d7fbe292d1539cf9a46, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9ad192626, 0x01982a5724997b4196355691d5f6e022, 0x01982a5746a07d7fbe292d1539cf9a46, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9ad5612f5, 0x01982a5724997b4196355691d5f6e022, 0x01982a5746a07d7fbe292d1539cf9a46, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9ad591dc6, 0x01982a5724997b4196355691d5f6e022, 0x01982a5746a07d7fbe292d1539cf9a46, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9ae3e796f, 0x01982a571e7e78488e3db42f63d9ac44, 0x01982a5746a07d7fbe292d1539cf9a46, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9aee7223d, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d153a227f98, 'Hey userClassique ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9af71631a, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d153a227f98, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9aff267a5, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d153a227f98, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9b00963ea, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d153a227f98, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9b0e5446c, 0x01982a571e7e78488e3db42f63d9ac44, 0x01982a5746a07d7fbe292d153a227f98, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9b1b37aed, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d153a227f98, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9b2a6165e, 0x01982a571e7e78488e3db42f63d9ac44, 0x01982a5746a07d7fbe292d153a227f98, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9b37355d0, 0x01982a571e7e78488e3db42f63d9ac44, 0x01982a5746a07d7fbe292d153a227f98, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9b41402f2, 0x01982a571e7e78488e3db42f63d9ac44, 0x01982a5746a07d7fbe292d153a227f98, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9b48eb793, 0x01982a571e7e78488e3db42f63d9ac44, 0x01982a5746a07d7fbe292d153a227f98, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9b58726d2, 0x01982a571e7e78488e3db42f63d9ac44, 0x01982a5746a07d7fbe292d153a227f98, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9b65cab85, 0x01982a571e7e78488e3db42f63d9ac44, 0x01982a5746a07d7fbe292d153a227f98, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9b7112933, 0x01982a5733237047aba87f42a3e4864a, 0x01982a5746a07d7fbe292d153b324e1d, 'Hey dmartin ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9b7310f46, 0x01982a5720827705acf25634cd3eaedb, 0x01982a5746a07d7fbe292d153b324e1d, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9b75322c5, 0x01982a5733237047aba87f42a3e4864a, 0x01982a5746a07d7fbe292d153b324e1d, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9b7a8139c, 0x01982a5733237047aba87f42a3e4864a, 0x01982a5746a07d7fbe292d153b324e1d, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9b89d61b8, 0x01982a5733237047aba87f42a3e4864a, 0x01982a5746a07d7fbe292d153b324e1d, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746ae7b6fa49c19e9b8bc2f4e, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a07d7fbe292d153d1a864a, 'Hey tfoucher ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6a2bd2845, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a07d7fbe292d153d1a864a, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6a36f2edf, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a07d7fbe292d153d1a864a, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6a410ae03, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a07d7fbe292d153d1a864a, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6a4ddbcf2, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a07d7fbe292d153d1a864a, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6a4eb3905, 0x01982a5724997b4196355691d5f6e022, 0x01982a5746a07d7fbe292d153edd544a, 'Hey matthieu00 ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6a59d7065, 0x01982a5724997b4196355691d5f6e022, 0x01982a5746a07d7fbe292d153edd544a, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6a5fa5f56, 0x01982a5724997b4196355691d5f6e022, 0x01982a5746a07d7fbe292d153edd544a, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6a69077c5, 0x01982a57469e7d95bf1eb89524b08f15, 0x01982a5746a07d7fbe292d153edd544a, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6a789e45f, 0x01982a57469e7d95bf1eb89524b08f15, 0x01982a5746a07d7fbe292d153edd544a, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6a7c35b17, 0x01982a5724997b4196355691d5f6e022, 0x01982a5746a07d7fbe292d153edd544a, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6a8135452, 0x01982a57469e7d95bf1eb89524b08f15, 0x01982a5746a07d7fbe292d153edd544a, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6a85d07c0, 0x01982a5724997b4196355691d5f6e022, 0x01982a5746a07d7fbe292d153edd544a, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6a8ba14e4, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a07d7fbe292d15406adb2d, 'Hey arnaude.jacquot ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6a9a0b72b, 0x01982a5724997b4196355691d5f6e022, 0x01982a5746a07d7fbe292d15406adb2d, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6aa986bdb, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a07d7fbe292d15406adb2d, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6aacf2ba9, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a07d7fbe292d15406adb2d, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6ab4adc71, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a07d7fbe292d15406adb2d, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6ab8a7d6f, 0x01982a5724997b4196355691d5f6e022, 0x01982a5746a07d7fbe292d15406adb2d, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6ac1ab712, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a07d7fbe292d15406adb2d, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6acb001a2, 0x01982a5726de7aa6832a680ee8bd9ca5, 0x01982a5746a07d7fbe292d154204b237, 'Hey salmon.anouk ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6ad8d707a, 0x01982a5726de7aa6832a680ee8bd9ca5, 0x01982a5746a07d7fbe292d154204b237, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6ae327feb, 0x01982a5726de7aa6832a680ee8bd9ca5, 0x01982a5746a07d7fbe292d154204b237, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6aeaf22ae, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a07d7fbe292d154204b237, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6af91c92e, 0x01982a5726de7aa6832a680ee8bd9ca5, 0x01982a5746a07d7fbe292d154204b237, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6afe89a6f, 0x01982a5726de7aa6832a680ee8bd9ca5, 0x01982a5746a07d7fbe292d154204b237, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6b07eda7e, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a07d7fbe292d154204b237, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6b084a353, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d1543cf66c1, 'Hey userClassique ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6b12ec927, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d1543cf66c1, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6b1a81169, 0x01982a5726de7aa6832a680ee8bd9ca5, 0x01982a5746a07d7fbe292d1543cf66c1, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6b25eb7f7, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d1543cf66c1, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6b29bf519, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d1543cf66c1, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6b350c46c, 0x01982a5726de7aa6832a680ee8bd9ca5, 0x01982a5746a07d7fbe292d1543cf66c1, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6b3bd67ed, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d1543cf66c1, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6b4480f80, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d1543cf66c1, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6b4f2d66b, 0x01982a5726de7aa6832a680ee8bd9ca5, 0x01982a5746a07d7fbe292d1543cf66c1, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6b58b1113, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a07d7fbe292d1545c8a1e6, 'Hey arnaude.jacquot ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6b651c744, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a07d7fbe292d1545c8a1e6, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6b6e582e5, 0x01982a571c8a7474977fa27e565a025e, 0x01982a5746a07d7fbe292d1545c8a1e6, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6b7af1903, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a07d7fbe292d1545c8a1e6, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746af70c49e5c7fa6b7e3cff5, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a07d7fbe292d1545c8a1e6, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396e9a7bba49, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a07d7fbe292d1545c8a1e6, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396e9a9fb207, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a07d7fbe292d1545c8a1e6, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396e9b876dc5, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a07d7fbe292d1545c8a1e6, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396e9c234fac, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a07d7fbe292d1547d6647f, 'Hey arnaude.jacquot ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396e9c6d025e, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a07d7fbe292d1547d6647f, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396e9cd0c1a5, 0x01982a573ed6770dbf26364df3ff9b13, 0x01982a5746a07d7fbe292d1547d6647f, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396e9da4d045, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a07d7fbe292d1547d6647f, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396e9e9d2a7f, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a07d7fbe292d1547d6647f, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396e9ec5b4ce, 0x01982a571e7e78488e3db42f63d9ac44, 0x01982a5746a07d7fbe292d1549a42f7b, 'Hey bbarbe ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396e9ee38f41, 0x01982a571e7e78488e3db42f63d9ac44, 0x01982a5746a07d7fbe292d1549a42f7b, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396e9fc5d7a4, 0x01982a571e7e78488e3db42f63d9ac44, 0x01982a5746a07d7fbe292d1549a42f7b, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396ea0089cd7, 0x01982a571e7e78488e3db42f63d9ac44, 0x01982a5746a07d7fbe292d1549a42f7b, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396ea05abe74, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a07d7fbe292d1549a42f7b, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396ea143d1ba, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a07d7fbe292d154b3ffb44, 'Hey nguyen.paul ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396ea1fa2013, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a07d7fbe292d154b3ffb44, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396ea2e2b759, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a07d7fbe292d154b3ffb44, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396ea365f9ff, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a07d7fbe292d154b3ffb44, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396ea42b91f2, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a07d7fbe292d154b3ffb44, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396ea4864be2, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a07d7fbe292d154b3ffb44, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396ea49440b0, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a07d7fbe292d154b3ffb44, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396ea5275d4f, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a07d7fbe292d154b3ffb44, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396ea5a9b026, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a07d7fbe292d154b3ffb44, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396ea697d271, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a07d7fbe292d154b3ffb44, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396ea7004704, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a07d7fbe292d154b3ffb44, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396ea79c4a71, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a07d7fbe292d154b3ffb44, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396ea8674faa, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a07d7fbe292d154cd7fcc5, 'Hey tfoucher ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396ea92b25a6, 0x01982a572d3a7fc39ccb365b807abe45, 0x01982a5746a07d7fbe292d154cd7fcc5, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396ea92cdf77, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a07d7fbe292d154cd7fcc5, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396ea9d29266, 0x01982a572d3a7fc39ccb365b807abe45, 0x01982a5746a07d7fbe292d154cd7fcc5, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396ea9f97acc, 0x01982a572d3a7fc39ccb365b807abe45, 0x01982a5746a07d7fbe292d154cd7fcc5, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b07b3fb566396eaaeb8efd, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a07d7fbe292d154e8eb9d7, 'Hey zweber ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab72874a733ec, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a07d7fbe292d154e8eb9d7, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab72875563e57, 0x01982a572f347f1185eb9ba408f9ae78, 0x01982a5746a07d7fbe292d154e8eb9d7, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab72875a326ed, 0x01982a572f347f1185eb9ba408f9ae78, 0x01982a5746a07d7fbe292d154e8eb9d7, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab7287632095c, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a07d7fbe292d154e8eb9d7, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab7287693b1f9, 0x01982a572f347f1185eb9ba408f9ae78, 0x01982a5746a07d7fbe292d154e8eb9d7, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab7287765e1e6, 0x01982a572f347f1185eb9ba408f9ae78, 0x01982a5746a07d7fbe292d154e8eb9d7, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab72878141c91, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a07d7fbe292d154e8eb9d7, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab728788adabe, 0x01982a57313179fd81df21c22f43b39a, 0x01982a5746a07d7fbe292d154f9f6921, 'Hey edith65 ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab728792a5d57, 0x01982a57313179fd81df21c22f43b39a, 0x01982a5746a07d7fbe292d154f9f6921, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab728798e697a, 0x01982a573cdb7fb4aa8a1a70bf4dc149, 0x01982a5746a07d7fbe292d154f9f6921, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab7287a42bb79, 0x01982a57313179fd81df21c22f43b39a, 0x01982a5746a07d7fbe292d154f9f6921, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab7287a91c65d, 0x01982a573cdb7fb4aa8a1a70bf4dc149, 0x01982a5746a07d7fbe292d154f9f6921, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab7287b0cf482, 0x01982a573cdb7fb4aa8a1a70bf4dc149, 0x01982a5746a07d7fbe292d154f9f6921, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab7287bf52e99, 0x01982a57313179fd81df21c22f43b39a, 0x01982a5746a07d7fbe292d154f9f6921, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab7287cde9bf9, 0x01982a573cdb7fb4aa8a1a70bf4dc149, 0x01982a5746a07d7fbe292d154f9f6921, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab7287d347958, 0x01982a5738eb7c1ca1beaefa92041549, 0x01982a5746a07d7fbe292d1550bb0f62, 'Hey brunel.juliette ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab7287df8d522, 0x01982a5738eb7c1ca1beaefa92041549, 0x01982a5746a07d7fbe292d1550bb0f62, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab7287e94f3a1, 0x01982a57313179fd81df21c22f43b39a, 0x01982a5746a07d7fbe292d1550bb0f62, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab7287f14a2b3, 0x01982a57313179fd81df21c22f43b39a, 0x01982a5746a07d7fbe292d1550bb0f62, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab7287f91b6d3, 0x01982a57313179fd81df21c22f43b39a, 0x01982a5746a07d7fbe292d1550bb0f62, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab7287fe745d6, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d1551a7db8d, 'Hey userClassique ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab72880b9c253, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d1551a7db8d, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab7288133501e, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d1551a7db8d, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab72881b68b3b, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d1551a7db8d, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab728823fdd92, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d1551a7db8d, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab7288289f050, 0x01982a5733237047aba87f42a3e4864a, 0x01982a5746a07d7fbe292d1551a7db8d, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab728828a1c37, 0x01982a5733237047aba87f42a3e4864a, 0x01982a5746a07d7fbe292d1551a7db8d, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab72883055e5d, 0x01982a5733237047aba87f42a3e4864a, 0x01982a5746a07d7fbe292d1552722881, 'Hey dmartin ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab7288316ba0c, 0x01982a5733237047aba87f42a3e4864a, 0x01982a5746a07d7fbe292d1552722881, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab728838941c1, 0x01982a5733237047aba87f42a3e4864a, 0x01982a5746a07d7fbe292d1552722881, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab72883db067e, 0x01982a5733237047aba87f42a3e4864a, 0x01982a5746a07d7fbe292d1552722881, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b17c4d82cab72884a898c9, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a07d7fbe292d1552722881, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a561ed2bd7, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a07d7fbe292d1552722881, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a5620ded98, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a07d7fbe292d1552722881, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a56222c985, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a07d7fbe292d1552722881, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a5626545d7, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a07d7fbe292d1554a80fa4, 'Hey tfoucher ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a5633a6596, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a07d7fbe292d1554a80fa4, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a563950cbd, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a07d7fbe292d1554a80fa4, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a5640d3a96, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a07d7fbe292d1554a80fa4, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a5642bd358, 0x01982a57313179fd81df21c22f43b39a, 0x01982a5746a07d7fbe292d1554a80fa4, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a564629ead, 0x01982a57313179fd81df21c22f43b39a, 0x01982a5746a07d7fbe292d1554a80fa4, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a564bbc07e, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a07d7fbe292d1554a80fa4, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a564e649db, 0x01982a57313179fd81df21c22f43b39a, 0x01982a5746a07d7fbe292d1554a80fa4, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a56575e1fc, 0x01982a57313179fd81df21c22f43b39a, 0x01982a5746a07d7fbe292d1554a80fa4, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a5661b6ac9, 0x01982a57313179fd81df21c22f43b39a, 0x01982a5746a07d7fbe292d1554a80fa4, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a567141a8b, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a1782f9d8f812319697f1d, 'Hey aimee08 ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a567951af6, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a1782f9d8f812319697f1d, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a567e5d006, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a1782f9d8f812319697f1d, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a568519abd, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a1782f9d8f812319697f1d, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a569043e0c, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a1782f9d8f812319697f1d, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a56985540d, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a1782f9d8f812319697f1d, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a569b2229d, 0x01982a5738eb7c1ca1beaefa92041549, 0x01982a5746a1782f9d8f81231ab3297b, 'Hey brunel.juliette ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a56a2525d2, 0x01982a573ed6770dbf26364df3ff9b13, 0x01982a5746a1782f9d8f81231ab3297b, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a56acae260, 0x01982a5738eb7c1ca1beaefa92041549, 0x01982a5746a1782f9d8f81231ab3297b, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a56b9cafe8, 0x01982a5738eb7c1ca1beaefa92041549, 0x01982a5746a1782f9d8f81231ab3297b, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a56c30c8a0, 0x01982a573ed6770dbf26364df3ff9b13, 0x01982a5746a1782f9d8f81231ab3297b, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a56cdf886a, 0x01982a573ed6770dbf26364df3ff9b13, 0x01982a5746a1782f9d8f81231ab3297b, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a56d29ba96, 0x01982a573ed6770dbf26364df3ff9b13, 0x01982a5746a1782f9d8f81231ab3297b, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a56d48a7d5, 0x01982a573ed6770dbf26364df3ff9b13, 0x01982a5746a1782f9d8f81231ab3297b, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a56d8e7481, 0x01982a5738eb7c1ca1beaefa92041549, 0x01982a5746a1782f9d8f81231c6f0c5a, 'Hey brunel.juliette ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a56db65386, 0x01982a5738eb7c1ca1beaefa92041549, 0x01982a5746a1782f9d8f81231c6f0c5a, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a56eb4e22a, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a1782f9d8f81231c6f0c5a, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a56f15eeda, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a1782f9d8f81231c6f0c5a, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a56f534b04, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a1782f9d8f81231c6f0c5a, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a56fb8a40f, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a1782f9d8f81231c6f0c5a, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a5707bc5b4, 0x01982a5738eb7c1ca1beaefa92041549, 0x01982a5746a1782f9d8f81231c6f0c5a, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b270e8a04b52a570c39654, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a1782f9d8f81231c6f0c5a, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e3933336b339c0, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a1782f9d8f81231c6f0c5a, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e3933336bb0754, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a1782f9d8f81231c6f0c5a, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e3933336be4867, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a1782f9d8f81231c6f0c5a, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e393333765216d, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a1782f9d8f81231c6f0c5a, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e3933337ad0f3d, 0x01982a5718ac7cc78dea86e21d7f38fb, 0x01982a5746a1782f9d8f81231d92dacc, 'Hey nicole64 ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e3933337bf6262, 0x01982a5718ac7cc78dea86e21d7f38fb, 0x01982a5746a1782f9d8f81231d92dacc, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e3933338060258, 0x01982a5718ac7cc78dea86e21d7f38fb, 0x01982a5746a1782f9d8f81231d92dacc, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e3933338e6d0ab, 0x01982a5718ac7cc78dea86e21d7f38fb, 0x01982a5746a1782f9d8f81231d92dacc, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e39333390fe1c2, 0x01982a573adf734b9780b364ffa21a9f, 0x01982a5746a1782f9d8f81231d92dacc, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e39333395b26e1, 0x01982a573adf734b9780b364ffa21a9f, 0x01982a5746a1782f9d8f81231d92dacc, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e393333a57090f, 0x01982a5718ac7cc78dea86e21d7f38fb, 0x01982a5746a1782f9d8f81231d92dacc, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e393333a88791f, 0x01982a573adf734b9780b364ffa21a9f, 0x01982a5746a1782f9d8f81231d92dacc, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e393333adf9594, 0x01982a573adf734b9780b364ffa21a9f, 0x01982a5746a1782f9d8f81231d92dacc, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e393333b8679c7, 0x01982a573adf734b9780b364ffa21a9f, 0x01982a5746a1782f9d8f81231d92dacc, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e393333bf11496, 0x01982a573adf734b9780b364ffa21a9f, 0x01982a5746a1782f9d8f81231d92dacc, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e393333c25e793, 0x01982a573adf734b9780b364ffa21a9f, 0x01982a5746a1782f9d8f81231ea74c27, 'Hey fernandes.marine ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e393333d1fa65b, 0x01982a573adf734b9780b364ffa21a9f, 0x01982a5746a1782f9d8f81231ea74c27, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e393333d7a61b7, 0x01982a573adf734b9780b364ffa21a9f, 0x01982a5746a1782f9d8f81231ea74c27, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e393333dc1a56b, 0x01982a5744a5710b92b2a95ddec0f5f2, 0x01982a5746a1782f9d8f81231ea74c27, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e393333e508230, 0x01982a5744a5710b92b2a95ddec0f5f2, 0x01982a5746a1782f9d8f81231ea74c27, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e393333f0aac24, 0x01982a5744a5710b92b2a95ddec0f5f2, 0x01982a5746a1782f9d8f81231ea74c27, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e393333fed1e71, 0x01982a573adf734b9780b364ffa21a9f, 0x01982a5746a1782f9d8f81231ea74c27, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e39333404cc353, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a1782f9d8f81232089505c, 'Hey ami_fixe_1 ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e3933340a34e74, 0x01982a573cdb7fb4aa8a1a70bf4dc149, 0x01982a5746a1782f9d8f81232089505c, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e39333415a08d6, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a1782f9d8f81232089505c, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e39333418684b8, 0x01982a573cdb7fb4aa8a1a70bf4dc149, 0x01982a5746a1782f9d8f81232089505c, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e393334248a934, 0x01982a573cdb7fb4aa8a1a70bf4dc149, 0x01982a5746a1782f9d8f81232089505c, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e39333427e00b4, 0x01982a573cdb7fb4aa8a1a70bf4dc149, 0x01982a5746a1782f9d8f81232089505c, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e3933343566aae, 0x01982a5718ac7cc78dea86e21d7f38fb, 0x01982a5746a1782f9d8f812321eab733, 'Hey nicole64 ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e393334429d5c9, 0x01982a573ed6770dbf26364df3ff9b13, 0x01982a5746a1782f9d8f812321eab733, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e3933344eac08c, 0x01982a573ed6770dbf26364df3ff9b13, 0x01982a5746a1782f9d8f812321eab733, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e39333457bc757, 0x01982a5718ac7cc78dea86e21d7f38fb, 0x01982a5746a1782f9d8f812321eab733, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e39333465ef404, 0x01982a5718ac7cc78dea86e21d7f38fb, 0x01982a5746a1782f9d8f812321eab733, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e3933346ef1c59, 0x01982a573adf734b9780b364ffa21a9f, 0x01982a5746a1782f9d8f812323edb07c, 'Hey fernandes.marine ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e3933347812153, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a1782f9d8f812323edb07c, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e39333483a6c41, 0x01982a573adf734b9780b364ffa21a9f, 0x01982a5746a1782f9d8f812323edb07c, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e3933349314763, 0x01982a573adf734b9780b364ffa21a9f, 0x01982a5746a1782f9d8f812323edb07c, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e3933349d153f5, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a1782f9d8f812323edb07c, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b37bfa95e3933349f3afc1, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a1782f9d8f812323edb07c, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108dd3c4b46, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a1782f9d8f812323edb07c, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text');
INSERT INTO `message` (`id`, `sender_id`, `room_id`, `content`, `file_name`, `created_at`, `type`) VALUES
(0x01982a5746b470748a03e108ddd72ae8, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a1782f9d8f812323edb07c, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108de971048, 0x01982a5744a5710b92b2a95ddec0f5f2, 0x01982a5746a1782f9d8f812325c88617, 'Hey ami_fixe_2 ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108df510a93, 0x01982a5733237047aba87f42a3e4864a, 0x01982a5746a1782f9d8f812325c88617, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108dfa886e1, 0x01982a5733237047aba87f42a3e4864a, 0x01982a5746a1782f9d8f812325c88617, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108dfdce232, 0x01982a5744a5710b92b2a95ddec0f5f2, 0x01982a5746a1782f9d8f812325c88617, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e0200eeb, 0x01982a5744a5710b92b2a95ddec0f5f2, 0x01982a5746a1782f9d8f812325c88617, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e0a3ff21, 0x01982a5744a5710b92b2a95ddec0f5f2, 0x01982a5746a1782f9d8f812325c88617, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e0c2602d, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a1782f9d8f8123272cbbe1, 'Hey nguyen.paul ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e15dd1d8, 0x01982a5744a5710b92b2a95ddec0f5f2, 0x01982a5746a1782f9d8f8123272cbbe1, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e178d347, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a1782f9d8f8123272cbbe1, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e23c1d97, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a1782f9d8f8123272cbbe1, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e30ab2ce, 0x01982a5744a5710b92b2a95ddec0f5f2, 0x01982a5746a1782f9d8f8123272cbbe1, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e3d09397, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a1782f9d8f8123272cbbe1, 'Trop cool la dernière partie !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e4af886e, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a1782f9d8f8123272cbbe1, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e4f9cec0, 0x01982a5744a5710b92b2a95ddec0f5f2, 0x01982a5746a1782f9d8f8123272cbbe1, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e55233e0, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a1782f9d8f8123272cbbe1, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e57f159c, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a1782f9d8f8123272cbbe1, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e5bf3e03, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a1782f9d8f8123272cbbe1, 'On se capte quand pour jouer ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e649c527, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a1782f9d8f8123272cbbe1, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e66611a0, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a1782f9d8f812328942e3a, 'Hey ami_fixe_1 ! Ravi de discuter 😊', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e69acdd1, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a1782f9d8f812328942e3a, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e6eae2f8, 0x01982a57469e7d95bf1eb89524b08f15, 0x01982a5746a1782f9d8f812328942e3a, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e7a9adcc, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a1782f9d8f812328942e3a, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e8282e3b, 0x01982a57469e7d95bf1eb89524b08f15, 0x01982a5746a1782f9d8f812328942e3a, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e8d7a49a, 0x01982a57469e7d95bf1eb89524b08f15, 0x01982a5746a1782f9d8f812328942e3a, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108e97e5918, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a1782f9d8f812328942e3a, 'À plus tard !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108ea5665f4, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a1782f9d8f812328942e3a, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108eb49a1ae, 0x01982a57469e7d95bf1eb89524b08f15, 0x01982a5746a1782f9d8f812328942e3a, 'Tu fais quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108eb5085b6, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a1782f9d8f812329f3e5e0, 'Quelqu\'un dispo pour un Morpion ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108ebc69790, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5746a1782f9d8f812329f3e5e0, 'Quelqu\'un dispo pour un Morpion ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108ec031314, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a1782f9d8f812329f3e5e0, 'J\'ai trouvé un nouveau jeu à tester.', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108eccc9be4, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a1782f9d8f812329f3e5e0, 'Hâte d\'être ce week-end !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b470748a03e108edbb2063, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a1782f9d8f812329f3e5e0, 'J\'ai trouvé un nouveau jeu à tester.', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b57fe8a7bf7cdabca707b2, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5746a1782f9d8f812329f3e5e0, 'Vous allez bien ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b57fe8a7bf7cdabd97fd08, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a1782f9d8f812329f3e5e0, 'On joue à quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b57fe8a7bf7cdabdde3832, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5746a1782f9d8f812329f3e5e0, 'Qui veut créer un groupe privé ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b57fe8a7bf7cdabe295c88, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5746a1782f9d8f812329f3e5e0, 'Quelqu\'un dispo pour un Morpion ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b57fe8a7bf7cdabebe707c, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a1782f9d8f812329f3e5e0, 'Vous allez bien ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b57fe8a7bf7cdabf4876db, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a1782f9d8f812329f3e5e0, 'On joue à quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b57fe8a7bf7cdabf4cb727, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a1782f9d8f81232c11946f, 'Vous allez bien ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b57fe8a7bf7cdac04428cc, 0x01982a572d3a7fc39ccb365b807abe45, 0x01982a5746a1782f9d8f81232c11946f, 'Qui veut créer un groupe privé ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b57fe8a7bf7cdac1200b17, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a1782f9d8f81232c11946f, 'On joue à quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b57fe8a7bf7cdac1fe44cd, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a1782f9d8f81232c11946f, 'Salut tout le monde !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b57fe8a7bf7cdac2d5320e, 0x01982a572d3a7fc39ccb365b807abe45, 0x01982a5746a1782f9d8f81232c11946f, 'Quelqu\'un dispo pour un Morpion ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b57fe8a7bf7cdac355d2d2, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a1782f9d8f81232c11946f, 'On joue à quoi ce soir ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b673a2a94385065a49eff5, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a1782f9d8f81232c11946f, 'Hâte d\'être ce week-end !', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b673a2a94385065adfe3f6, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a1782f9d8f81232c11946f, 'Qui veut créer un groupe privé ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b673a2a94385065b8f419f, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a1782f9d8f81232c11946f, 'Vous allez bien ?', NULL, '2025-07-21 00:16:59', 'text'),
(0x01982a5746b673a2a94385065c61beda, 0x01982a572d3a7fc39ccb365b807abe45, 0x01982a5746a1782f9d8f81232c11946f, 'J\'ai trouvé un nouveau jeu à tester.', NULL, '2025-07-21 00:16:59', 'text');

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_token`
--

CREATE TABLE `password_reset_token` (
  `id` int NOT NULL,
  `user_id` binary(16) NOT NULL COMMENT '(DC2Type:uuid)',
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `refresh_tokens`
--

CREATE TABLE `refresh_tokens` (
  `id` int NOT NULL,
  `refresh_token` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valid` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `room`
--

CREATE TABLE `room` (
  `id` binary(16) NOT NULL COMMENT '(DC2Type:uuid)',
  `name` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_group` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `room`
--

INSERT INTO `room` (`id`, `name`, `is_group`, `created_at`) VALUES
(0x01982a5746a07d7fbe292d152c044467, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d152c5f026d, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d152d9941b8, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d152fcad123, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d15310fb76a, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d1533007c66, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d1533fc17b0, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d15357a7933, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d1537e2aa1b, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d1539cf9a46, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d153a227f98, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d153b324e1d, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d153d1a864a, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d153edd544a, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d15406adb2d, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d154204b237, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d1543cf66c1, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d1545c8a1e6, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d1547d6647f, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d1549a42f7b, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d154b3ffb44, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d154cd7fcc5, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d154e8eb9d7, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d154f9f6921, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d1550bb0f62, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d1551a7db8d, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d1552722881, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a07d7fbe292d1554a80fa4, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a1782f9d8f812319697f1d, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a1782f9d8f81231ab3297b, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a1782f9d8f81231c6f0c5a, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a1782f9d8f81231d92dacc, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a1782f9d8f81231ea74c27, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a1782f9d8f81232089505c, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a1782f9d8f812321eab733, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a1782f9d8f812323edb07c, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a1782f9d8f812325c88617, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a1782f9d8f8123272cbbe1, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a1782f9d8f812328942e3a, NULL, 0, '2025-07-21 00:16:59'),
(0x01982a5746a1782f9d8f812329f3e5e0, 'Groupe de userClassique #1', 1, '2025-07-21 00:16:59'),
(0x01982a5746a1782f9d8f81232c11946f, 'Groupe de userClassique #2', 1, '2025-07-21 00:16:59');

-- --------------------------------------------------------

--
-- Structure de la table `room_user`
--

CREATE TABLE `room_user` (
  `id` binary(16) NOT NULL COMMENT '(DC2Type:uuid)',
  `user_id` binary(16) NOT NULL COMMENT '(DC2Type:uuid)',
  `room_id` binary(16) NOT NULL COMMENT '(DC2Type:uuid)',
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `joined_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `last_seen_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `is_visible` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `room_user`
--

INSERT INTO `room_user` (`id`, `user_id`, `room_id`, `role`, `joined_at`, `last_seen_at`, `is_visible`) VALUES
(0x01982a5746a07d7fbe292d152c1385df, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152c044467, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d152c50a536, 0x01982a572d3a7fc39ccb365b807abe45, 0x01982a5746a07d7fbe292d152c044467, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d152c84b04b, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152c5f026d, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d152c99fe30, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5746a07d7fbe292d152c5f026d, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d152e89dd14, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152d9941b8, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d152f4c7a36, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a07d7fbe292d152d9941b8, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d15307d8534, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d152fcad123, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d1530f1accf, 0x01982a5744a5710b92b2a95ddec0f5f2, 0x01982a5746a07d7fbe292d152fcad123, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d1531f1de92, 0x01982a5718ac7cc78dea86e21d7f38fb, 0x01982a5746a07d7fbe292d15310fb76a, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d15323d5327, 0x01982a5726de7aa6832a680ee8bd9ca5, 0x01982a5746a07d7fbe292d15310fb76a, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d15330a09c3, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a07d7fbe292d1533007c66, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d15336fc4de, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5746a07d7fbe292d1533007c66, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d1534b194a9, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a07d7fbe292d1533fc17b0, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d1534f41976, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a07d7fbe292d1533fc17b0, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d15365d1219, 0x01982a571c8a7474977fa27e565a025e, 0x01982a5746a07d7fbe292d15357a7933, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d1537422f2b, 0x01982a5720827705acf25634cd3eaedb, 0x01982a5746a07d7fbe292d15357a7933, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d1537ff947a, 0x01982a571c8a7474977fa27e565a025e, 0x01982a5746a07d7fbe292d1537e2aa1b, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d1538f2dbbb, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a07d7fbe292d1537e2aa1b, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d153a02a662, 0x01982a571e7e78488e3db42f63d9ac44, 0x01982a5746a07d7fbe292d1539cf9a46, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d153a1ecef3, 0x01982a5724997b4196355691d5f6e022, 0x01982a5746a07d7fbe292d1539cf9a46, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d153ab5cbae, 0x01982a571e7e78488e3db42f63d9ac44, 0x01982a5746a07d7fbe292d153a227f98, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d153b0557c9, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d153a227f98, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d153bba471b, 0x01982a5720827705acf25634cd3eaedb, 0x01982a5746a07d7fbe292d153b324e1d, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d153c40262f, 0x01982a5733237047aba87f42a3e4864a, 0x01982a5746a07d7fbe292d153b324e1d, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d153d30eecd, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5746a07d7fbe292d153d1a864a, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d153e1bb1f3, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a07d7fbe292d153d1a864a, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d153f45b9ed, 0x01982a5724997b4196355691d5f6e022, 0x01982a5746a07d7fbe292d153edd544a, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d153fb792a9, 0x01982a57469e7d95bf1eb89524b08f15, 0x01982a5746a07d7fbe292d153edd544a, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d15409bc051, 0x01982a5724997b4196355691d5f6e022, 0x01982a5746a07d7fbe292d15406adb2d, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d15415158f6, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a07d7fbe292d15406adb2d, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d1542f8e402, 0x01982a5726de7aa6832a680ee8bd9ca5, 0x01982a5746a07d7fbe292d154204b237, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d15431a5d09, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a07d7fbe292d154204b237, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d1543d14675, 0x01982a5726de7aa6832a680ee8bd9ca5, 0x01982a5746a07d7fbe292d1543cf66c1, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d1544d01f31, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d1543cf66c1, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d1546ac8915, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a07d7fbe292d1545c8a1e6, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d1547a331ee, 0x01982a571c8a7474977fa27e565a025e, 0x01982a5746a07d7fbe292d1545c8a1e6, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d1548760eae, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a07d7fbe292d1547d6647f, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d15490b39fa, 0x01982a573ed6770dbf26364df3ff9b13, 0x01982a5746a07d7fbe292d1547d6647f, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d154a8c3e90, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a07d7fbe292d1549a42f7b, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d154b304594, 0x01982a571e7e78488e3db42f63d9ac44, 0x01982a5746a07d7fbe292d1549a42f7b, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d154ba84789, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a07d7fbe292d154b3ffb44, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d154bf0c7d4, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a07d7fbe292d154b3ffb44, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d154dab40d1, 0x01982a572d3a7fc39ccb365b807abe45, 0x01982a5746a07d7fbe292d154cd7fcc5, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d154e617047, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a07d7fbe292d154cd7fcc5, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d154f055944, 0x01982a572f347f1185eb9ba408f9ae78, 0x01982a5746a07d7fbe292d154e8eb9d7, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d154f94005e, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a07d7fbe292d154e8eb9d7, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d15509885c8, 0x01982a57313179fd81df21c22f43b39a, 0x01982a5746a07d7fbe292d154f9f6921, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d1550a47369, 0x01982a573cdb7fb4aa8a1a70bf4dc149, 0x01982a5746a07d7fbe292d154f9f6921, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d15518bc06f, 0x01982a57313179fd81df21c22f43b39a, 0x01982a5746a07d7fbe292d1550bb0f62, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d1551a3db18, 0x01982a5738eb7c1ca1beaefa92041549, 0x01982a5746a07d7fbe292d1550bb0f62, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d1551c04250, 0x01982a5733237047aba87f42a3e4864a, 0x01982a5746a07d7fbe292d1551a7db8d, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d1551ccc193, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a07d7fbe292d1551a7db8d, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d155318eec8, 0x01982a5733237047aba87f42a3e4864a, 0x01982a5746a07d7fbe292d1552722881, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a07d7fbe292d15540b6db3, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a07d7fbe292d1552722881, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f812318287b4a, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a07d7fbe292d1554a80fa4, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f8123190f15ff, 0x01982a57313179fd81df21c22f43b39a, 0x01982a5746a07d7fbe292d1554a80fa4, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f8123199056ba, 0x01982a5737047b81b1b0d050c878dbc3, 0x01982a5746a1782f9d8f812319697f1d, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f81231a8b42ce, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a1782f9d8f812319697f1d, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f81231b021e52, 0x01982a5738eb7c1ca1beaefa92041549, 0x01982a5746a1782f9d8f81231ab3297b, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f81231bb90161, 0x01982a573ed6770dbf26364df3ff9b13, 0x01982a5746a1782f9d8f81231ab3297b, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f81231c6f2399, 0x01982a5738eb7c1ca1beaefa92041549, 0x01982a5746a1782f9d8f81231c6f0c5a, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f81231d4cb048, 0x01982a57291878d0a1d70e1cfa8add72, 0x01982a5746a1782f9d8f81231c6f0c5a, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f81231dad9fcd, 0x01982a573adf734b9780b364ffa21a9f, 0x01982a5746a1782f9d8f81231d92dacc, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f81231df64160, 0x01982a5718ac7cc78dea86e21d7f38fb, 0x01982a5746a1782f9d8f81231d92dacc, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f81231f3eb7bf, 0x01982a573adf734b9780b364ffa21a9f, 0x01982a5746a1782f9d8f81231ea74c27, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f81231fe92f47, 0x01982a5744a5710b92b2a95ddec0f5f2, 0x01982a5746a1782f9d8f81231ea74c27, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f8123216533a9, 0x01982a573cdb7fb4aa8a1a70bf4dc149, 0x01982a5746a1782f9d8f81232089505c, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f812321dd399f, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a1782f9d8f81232089505c, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f812322d40764, 0x01982a573ed6770dbf26364df3ff9b13, 0x01982a5746a1782f9d8f812321eab733, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f812322f71b99, 0x01982a5718ac7cc78dea86e21d7f38fb, 0x01982a5746a1782f9d8f812321eab733, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f812324e4e4c6, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a1782f9d8f812323edb07c, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f8123253d8e3b, 0x01982a573adf734b9780b364ffa21a9f, 0x01982a5746a1782f9d8f812323edb07c, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f812326681eb9, 0x01982a5744a5710b92b2a95ddec0f5f2, 0x01982a5746a1782f9d8f812325c88617, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f81232671ea7c, 0x01982a5733237047aba87f42a3e4864a, 0x01982a5746a1782f9d8f812325c88617, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f8123273b3e58, 0x01982a5744a5710b92b2a95ddec0f5f2, 0x01982a5746a1782f9d8f8123272cbbe1, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f812327f88a28, 0x01982a572b327901b0e83d3d7b37477c, 0x01982a5746a1782f9d8f8123272cbbe1, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f812329377941, 0x01982a57469e7d95bf1eb89524b08f15, 0x01982a5746a1782f9d8f812328942e3a, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f812329e636e8, 0x01982a5742b37b9387d55b8748d9b342, 0x01982a5746a1782f9d8f812328942e3a, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f81232a224538, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a1782f9d8f812329f3e5e0, 'owner', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f81232a7c7fce, 0x01982a571a9f74169f83319d5a7760ca, 0x01982a5746a1782f9d8f812329f3e5e0, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f81232b62fb93, 0x01982a5722a176e9911daa2f3536d0cb, 0x01982a5746a1782f9d8f812329f3e5e0, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f81232cc0d81d, 0x01982a5740d17362bd011b3bee97dcb3, 0x01982a5746a1782f9d8f81232c11946f, 'owner', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f81232d0642b2, 0x01982a572d3a7fc39ccb365b807abe45, 0x01982a5746a1782f9d8f81232c11946f, 'user', '2025-07-21 00:16:59', NULL, 1),
(0x01982a5746a1782f9d8f81232d0cae79, 0x01982a5735137fe38da4a6967b3a6ad4, 0x01982a5746a1782f9d8f81232c11946f, 'user', '2025-07-21 00:16:59', NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` binary(16) NOT NULL COMMENT '(DC2Type:uuid)',
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_verified` tinyint(1) NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pseudo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `friend_code` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `push_notifications_enabled` tinyint(1) NOT NULL,
  `push_endpoint` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `push_p256dh` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `push_auth` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `is_verified`, `roles`, `password`, `pseudo`, `image_name`, `created_at`, `updated_at`, `friend_code`, `push_notifications_enabled`, `push_endpoint`, `push_p256dh`, `push_auth`) VALUES
(0x01982a5718ac7cc78dea86e21d7f38fb, 'maryse.evrard@example.net', 1, '[]', '$2y$13$PbsqAWCB7Rwag5Vevx.I6.KPjL/Td/EqVlQUnROxDVMbMAHbL3hZe', 'nicole64', 'default-avatar3.svg', '2025-07-21 00:16:47', '2025-07-21 00:16:47', '3BEE2F0A9025F14EBA6F', 0, NULL, NULL, NULL),
(0x01982a571a9f74169f83319d5a7760ca, 'wmoreau@example.com', 1, '[]', '$2y$13$KyOvDM5cQmQTwJh0IbxK6e1LmPvQA.izsQ2OT7FynB0brJklGZDie', 'zweber', 'default-avatar4.svg', '2025-07-21 00:16:48', '2025-07-21 00:16:48', '296892FC30CEB48ECC6A', 0, NULL, NULL, NULL),
(0x01982a571c8a7474977fa27e565a025e, 'jules75@example.com', 1, '[]', '$2y$13$YqDGNaOtMRSznfmpKu/GtOSO9ytn03wYhvSR3colZZOBx2Kx1kIpi', 'ilefort', 'default-avatar3.svg', '2025-07-21 00:16:48', '2025-07-21 00:16:48', 'B558DBDEC6F944885B20', 0, NULL, NULL, NULL),
(0x01982a571e7e78488e3db42f63d9ac44, 'klemoine@example.com', 1, '[]', '$2y$13$RHDL/zDc5fSWhBa2AnNBuOqqAQwPxEptbaFYyF0PbnZ9gWIS4Wh8i', 'bbarbe', 'default-avatar2.svg', '2025-07-21 00:16:49', '2025-07-21 00:16:49', '19808662ED337E0F27D5', 0, NULL, NULL, NULL),
(0x01982a5720827705acf25634cd3eaedb, 'breton.richard@example.org', 1, '[]', '$2y$13$UQhyytSPhzR/MdXk5knNuOUN5/dEJovS3rQTzuPxlHKzLPcq35iiq', 'dherve', 'default-avatar5.svg', '2025-07-21 00:16:49', '2025-07-21 00:16:49', 'CA1014A274E079E9AA8D', 0, NULL, NULL, NULL),
(0x01982a5722a176e9911daa2f3536d0cb, 'adossantos@example.org', 1, '[]', '$2y$13$YWRXiyG80DRvPp3ncwPzFu5EdAvC3jIcATpNksMny8AQmZGB5L3cm', 'laurence.humbert', 'default-avatar5.svg', '2025-07-21 00:16:50', '2025-07-21 00:16:50', '61A866647DA8656DBDD9', 0, NULL, NULL, NULL),
(0x01982a5724997b4196355691d5f6e022, 'pdurand@example.org', 1, '[]', '$2y$13$rSo.pCTHXnphNENBPz5ukemQZpf8CHb/6CXg1FGfbX1IdIChIFLIa', 'matthieu00', 'default-avatar5.svg', '2025-07-21 00:16:50', '2025-07-21 00:16:50', '6CA2F46AC2E00FCA61A0', 0, NULL, NULL, NULL),
(0x01982a5726de7aa6832a680ee8bd9ca5, 'jacques42@example.net', 1, '[]', '$2y$13$FfkZd3ZHNhd4VFarB7IOy.Q2LaSw6htUzsGqKULmF1jiEKpPldHOG', 'salmon.anouk', 'default-avatar4.svg', '2025-07-21 00:16:51', '2025-07-21 00:16:51', 'C7364FA76F52ADAD600E', 0, NULL, NULL, NULL),
(0x01982a57291878d0a1d70e1cfa8add72, 'giraud.thibault@example.org', 1, '[]', '$2y$13$FuDEy6B43KDS0hwTYLvuwOVR0RPR09sASfOfPp.ndDUIZNJLbEpWO', 'arnaude.jacquot', 'default-avatar4.svg', '2025-07-21 00:16:51', '2025-07-21 00:16:51', '0A7698746DB76F87B674', 0, NULL, NULL, NULL),
(0x01982a572b327901b0e83d3d7b37477c, 'alexandrie97@example.net', 1, '[]', '$2y$13$YNZkgsWrwxxOh5CkqXjRSOd9kE7r5a9FLvJQCaq/AHSqzFRj4KY0W', 'nguyen.paul', 'default-avatar5.svg', '2025-07-21 00:16:52', '2025-07-21 00:16:52', '7CAC6352476FD570AE54', 0, NULL, NULL, NULL),
(0x01982a572d3a7fc39ccb365b807abe45, 'zoe.bodin@example.net', 1, '[]', '$2y$13$Ug9RS7WAQETUXDBRxokhLe3phfcCDu2pP/YiBxvk.2/5JbsoLaft2', 'delaunay.julie', 'default-avatar4.svg', '2025-07-21 00:16:53', '2025-07-21 00:16:53', '29C63EDF16C1FE839ED4', 0, NULL, NULL, NULL),
(0x01982a572f347f1185eb9ba408f9ae78, 'vguibert@example.net', 1, '[]', '$2y$13$YwKn7AQVkEDr3nSL1K2WOOUg8If7szX15lXRjaZjCMgfTmOENbl9K', 'ulambert', 'default-avatar2.svg', '2025-07-21 00:16:53', '2025-07-21 00:16:53', '3B01191C7257B5BD7A37', 0, NULL, NULL, NULL),
(0x01982a57313179fd81df21c22f43b39a, 'laurence.carre@example.com', 1, '[]', '$2y$13$0RKTgdVy6u483XpDnwrbH.kWRktdAFP2BPSHa9D9lGjAv7zdm8w6C', 'edith65', 'default-avatar3.svg', '2025-07-21 00:16:54', '2025-07-21 00:16:54', 'A20B67727FEBCCE567DA', 0, NULL, NULL, NULL),
(0x01982a5733237047aba87f42a3e4864a, 'guerin.theodore@example.org', 1, '[]', '$2y$13$ufJhmGSFwNb/hCdmrFLDT.0J3ltLja.B.qj/MXwPNU17aB5lGH.b2', 'dmartin', 'default-avatar4.svg', '2025-07-21 00:16:54', '2025-07-21 00:16:54', '736C94E3C72C74D8F136', 0, NULL, NULL, NULL),
(0x01982a5735137fe38da4a6967b3a6ad4, 'richard.courtois@example.org', 1, '[]', '$2y$13$F91m1eAtxmv57M2g22pVLOTFw3IOQOzryFxfHHRwJ47gD8GqFnUP6', 'tfoucher', 'default-avatar5.svg', '2025-07-21 00:16:55', '2025-07-21 00:16:55', 'F34440D1E68030291E7E', 0, NULL, NULL, NULL),
(0x01982a5737047b81b1b0d050c878dbc3, 'nicolas.jacqueline@example.com', 1, '[]', '$2y$13$sOspwD45u9gepgNEXUjhaus3EhWkdyFLTZn0QLv0bUrXiz/5Indhy', 'aimee08', 'default-avatar3.svg', '2025-07-21 00:16:55', '2025-07-21 00:16:55', '82C37C774308BD5C30CF', 0, NULL, NULL, NULL),
(0x01982a5738eb7c1ca1beaefa92041549, 'ojacob@example.com', 1, '[]', '$2y$13$DxkF8vFVrqzAloHYswu16OI9WEKA4aj/1SJ5T7UXZMBeXtTCNYqYu', 'brunel.juliette', 'default-avatar4.svg', '2025-07-21 00:16:56', '2025-07-21 00:16:56', 'AB4479E35959AB664843', 0, NULL, NULL, NULL),
(0x01982a573adf734b9780b364ffa21a9f, 'maury.tristan@example.net', 1, '[]', '$2y$13$sqVr6V.5876jBYw/A3.Ul.PuN.sp9loYIx3NM5//upYRHuJU9TNHu', 'fernandes.marine', 'default-avatar5.svg', '2025-07-21 00:16:56', '2025-07-21 00:16:56', 'E0728CC3697AA6E075E1', 0, NULL, NULL, NULL),
(0x01982a573cdb7fb4aa8a1a70bf4dc149, 'eugene92@example.net', 1, '[]', '$2y$13$3nH3/wCc2KY2dS.BgrIv0Oj02PXMECs76xqMRwuiVNScppn5mrPCO', 'marguerite92', 'default-avatar2.svg', '2025-07-21 00:16:57', '2025-07-21 00:16:57', 'B1D9881037CCB3593308', 0, NULL, NULL, NULL),
(0x01982a573ed6770dbf26364df3ff9b13, 'aime.courtois@example.com', 1, '[]', '$2y$13$5lJDjgWEgw/yOa4OYwjZQOQBAEINT6zZg77JZb.YheIuW/ddXRUja', 'gilbert.barbe', 'default-avatar3.svg', '2025-07-21 00:16:57', '2025-07-21 00:16:57', '1A1D8488BB5F8D52B0D9', 0, NULL, NULL, NULL),
(0x01982a5740d17362bd011b3bee97dcb3, 'user@user.com', 1, '[]', '$2y$13$KpYV0uw3sHNOC8ubIgfU7ONI8nr1hhqDqSmgoyBuevciOcemZRAze', 'userClassique', NULL, '2025-07-21 00:16:58', '2025-07-21 00:16:58', '7699D702D32C6C31A652', 0, NULL, NULL, NULL),
(0x01982a5742b37b9387d55b8748d9b342, 'ami1@roomies.test', 1, '[]', '$2y$13$6hQrpiVbHCxA6Y1zIljxdO5BQOY7t3TEbdxpjVBBYvN2jW/v8C8Gy', 'ami_fixe_1', 'default-avatar2.svg', '2025-07-21 00:16:58', '2025-07-21 00:16:58', '47A350833EA98050C4E9', 0, NULL, NULL, NULL),
(0x01982a5744a5710b92b2a95ddec0f5f2, 'ami2@roomies.test', 1, '[]', '$2y$13$QVIn.OpbEz/m0kBdepETYON.gWbUxUHzjIpU.NhGa1t9E3KzBFLsa', 'ami_fixe_2', 'default-avatar5.svg', '2025-07-21 00:16:59', '2025-07-21 00:16:59', '581193634B58880F5ECE', 0, NULL, NULL, NULL),
(0x01982a57469e7d95bf1eb89524b08f15, 'nonami@roomies.test', 1, '[]', '$2y$13$vQm3iBZKV7yDv6hdf2sJ6Ov5tWC2glClj09lZl.SJ4b8xw7JO2hGG', 'non_ami_fixe', 'default-avatar4.svg', '2025-07-21 00:16:59', '2025-07-21 00:16:59', '03BDD87491F927C8E43C', 0, NULL, NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `friendship`
--
ALTER TABLE `friendship`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_friendship` (`applicant_id`,`recipient_id`),
  ADD KEY `IDX_7234A45F97139001` (`applicant_id`),
  ADD KEY `IDX_7234A45FE92F8F78` (`recipient_id`),
  ADD KEY `idx_recipient_status` (`recipient_id`,`status`);

--
-- Index pour la table `game_room`
--
ALTER TABLE `game_room`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_998A3DB761220EA6` (`creator_id`);

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_B6BD307FF624B39D` (`sender_id`),
  ADD KEY `IDX_B6BD307F54177093` (`room_id`),
  ADD KEY `idx_message_room_created` (`room_id`,`created_at`);

--
-- Index pour la table `password_reset_token`
--
ALTER TABLE `password_reset_token`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_6B7BA4B65F37A13B` (`token`),
  ADD KEY `IDX_6B7BA4B6A76ED395` (`user_id`);

--
-- Index pour la table `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_9BACE7E1C74F2195` (`refresh_token`);

--
-- Index pour la table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `room_user`
--
ALTER TABLE `room_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_room` (`user_id`,`room_id`),
  ADD KEY `IDX_EE973C2DA76ED395` (`user_id`),
  ADD KEY `IDX_EE973C2D54177093` (`room_id`),
  ADD KEY `idx_room_user` (`room_id`,`role`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_PSEUDO` (`pseudo`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`),
  ADD UNIQUE KEY `UNIQ_FRIEND_CODE` (`friend_code`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `game_room`
--
ALTER TABLE `game_room`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `password_reset_token`
--
ALTER TABLE `password_reset_token`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `friendship`
--
ALTER TABLE `friendship`
  ADD CONSTRAINT `FK_7234A45F97139001` FOREIGN KEY (`applicant_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_7234A45FE92F8F78` FOREIGN KEY (`recipient_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `game_room`
--
ALTER TABLE `game_room`
  ADD CONSTRAINT `FK_998A3DB761220EA6` FOREIGN KEY (`creator_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `FK_B6BD307F54177093` FOREIGN KEY (`room_id`) REFERENCES `room` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_B6BD307FF624B39D` FOREIGN KEY (`sender_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `password_reset_token`
--
ALTER TABLE `password_reset_token`
  ADD CONSTRAINT `FK_6B7BA4B6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `room_user`
--
ALTER TABLE `room_user`
  ADD CONSTRAINT `FK_EE973C2D54177093` FOREIGN KEY (`room_id`) REFERENCES `room` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_EE973C2DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
