-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : database
-- Généré le : sam. 19 juil. 2025 à 12:34
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
('DoctrineMigrations\\Version20250521202828', '2025-07-13 12:19:49', 129),
('DoctrineMigrations\\Version20250522135805', '2025-07-13 12:19:49', 596),
('DoctrineMigrations\\Version20250522143627', '2025-07-13 12:19:49', 256),
('DoctrineMigrations\\Version20250713114325', '2025-07-13 12:19:50', 41),
('DoctrineMigrations\\Version20250716192405', '2025-07-16 19:24:12', 206);

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
(0x019822ad6ac6703ebec00b380f64a877, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad47fa7062a2eb6fd008cf837c, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b380fdc83d8, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad40127970b9a544af803275ef, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b38102c9a5e, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad66f47ee480bd993a12c986aa, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b381078a228, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad68db740e8eac0cb277cd6846, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b3810ee72c9, 0x019822ad3e27777a97c8479ffdd7f1dd, 0x019822ad6ac577e3b3c3cea1b0a930c1, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b38119afc20, 0x019822ad40127970b9a544af803275ef, 0x019822ad460375ddad9246e8027a6baa, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b38121e3ea1, 0x019822ad40127970b9a544af803275ef, 0x019822ad51917f719b2e0b5fa7709164, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b3812876cec, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad577b77a7bddbfa08baa6e336, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b3812f839e9, 0x019822ad441d76a5982d73fda37847b5, 0x019822ad40127970b9a544af803275ef, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b38131b1acb, 0x019822ad441d76a5982d73fda37847b5, 0x019822ad51917f719b2e0b5fa7709164, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b38139bd511, 0x019822ad460375ddad9246e8027a6baa, 0x019822ad68db740e8eac0cb277cd6846, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b381492b4b5, 0x019822ad460375ddad9246e8027a6baa, 0x019822ad49de7ecb94e409601cf98302, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b38158ec372, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad4bbe73a9b1389b36b0bef27d, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b3815ed2145, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad422976f2bdc447bc3ac747e7, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b3816c9d0e4, 0x019822ad49de7ecb94e409601cf98302, 0x019822ad47fa7062a2eb6fd008cf837c, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b3816ea89a4, 0x019822ad4bbe73a9b1389b36b0bef27d, 0x019822ad5d4a7cc497dadb9b77e72bb8, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b3817326a41, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad65157b949fb8e2026ccdbad7, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b381814d0a8, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad538f782f84b85f6a4653f04f, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b38182c35e7, 0x019822ad4f9e709c96815cc2c083e822, 0x019822ad422976f2bdc447bc3ac747e7, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b3818e4de35, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad595e77228150433d5a83b3ea, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b3819d4da40, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad557e72e0b86244ef84ae1349, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b381a7689db, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad51917f719b2e0b5fa7709164, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b381aea07cd, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad577b77a7bddbfa08baa6e336, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b381b411d29, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad47fa7062a2eb6fd008cf837c, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b381bf7581c, 0x019822ad577b77a7bddbfa08baa6e336, 0x019822ad4bbe73a9b1389b36b0bef27d, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac6703ebec00b381c241427, 0x019822ad577b77a7bddbfa08baa6e336, 0x019822ad631e7a4ba28b9732e59c72fc, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b427669f6ebea0, 0x019822ad595e77228150433d5a83b3ea, 0x019822ad538f782f84b85f6a4653f04f, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b427669fee7705, 0x019822ad5b5477c881098db6f7b091ab, 0x019822ad4d9e7c24b9e2e4e05c920858, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766a0d39777, 0x019822ad5b5477c881098db6f7b091ab, 0x019822ad51917f719b2e0b5fa7709164, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766a166fd0e, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6130711eba790f3dc8ee3295, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766a2321a69, 0x019822ad5f4b7d0b9b3d9de9f72e85ba, 0x019822ad49de7ecb94e409601cf98302, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766a2b0a3e2, 0x019822ad6130711eba790f3dc8ee3295, 0x019822ad65157b949fb8e2026ccdbad7, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766a334437c, 0x019822ad631e7a4ba28b9732e59c72fc, 0x019822ad5b5477c881098db6f7b091ab, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766a3dc2f1f, 0x019822ad66f47ee480bd993a12c986aa, 0x019822ad557e72e0b86244ef84ae1349, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766a491b6c1, 0x019822ad66f47ee480bd993a12c986aa, 0x019822ad3e27777a97c8479ffdd7f1dd, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766a4cd3285, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad5d4a7cc497dadb9b77e72bb8, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766a5594bc0, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad538f782f84b85f6a4653f04f, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766a62e7bd6, 0x019822ad6ac577e3b3c3cea1b0a930c1, 0x019822ad5f4b7d0b9b3d9de9f72e85ba, 'friend', '2025-07-19 12:34:07', '2025-07-19 12:34:07');

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
(22, 0x019822ad65157b949fb8e2026ccdbad7, 'Gogo Morpion #1', 'morpion', '2025-07-18 12:34:07'),
(23, 0x019822ad6ac577e3b3c3cea1b0a930c1, 'Tic Tac Toe #2', 'morpion', '2025-07-19 10:34:07');

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
(0x019822ad6acd77aebbb054c41586c883, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a6641e45, 'Hey userClassique ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c4164e15e0, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a6641e45, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c416fa3278, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766a6641e45, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c4172da2ee, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a6641e45, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c4175e513c, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a6641e45, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c417b5632b, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a6641e45, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c41843efdb, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a6641e45, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c418f15d8b, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a7e41a21, 'Hey userClassique ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c419877315, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766a7e41a21, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c41a13c70e, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a7e41a21, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c41a45a309, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a7e41a21, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c41a7834e4, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766a7e41a21, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c41acae0f9, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a7e41a21, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c41afaaf5d, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a7e41a21, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c41b76bf36, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766a7e41a21, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c41b9e415b, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a7e41a21, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c41bdd89b1, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a83417ed, 'Hey userClassique ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c41c724ab5, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a83417ed, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c41c9809b7, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a83417ed, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c41d3fb0be, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a83417ed, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c41d4a9010, 0x019822ad66f47ee480bd993a12c986aa, 0x019822ad6ac7769d86b42766a83417ed, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acd77aebbb054c41dfcd386, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a83417ed, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d7096f457, 0x019822ad66f47ee480bd993a12c986aa, 0x019822ad6ac7769d86b42766a83417ed, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d71599d01, 0x019822ad66f47ee480bd993a12c986aa, 0x019822ad6ac7769d86b42766a83417ed, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d71faa944, 0x019822ad66f47ee480bd993a12c986aa, 0x019822ad6ac7769d86b42766a83417ed, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d71fd16b1, 0x019822ad66f47ee480bd993a12c986aa, 0x019822ad6ac7769d86b42766a83417ed, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d72e2c804, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a83417ed, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d73577e0d, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad6ac7769d86b42766aa72b74b, 'Hey ami_fixe_2 ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d73f8decf, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766aa72b74b, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d748a3be3, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766aa72b74b, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d74c18396, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766aa72b74b, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d75af3dd4, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766aa72b74b, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d765fa7ff, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766aa72b74b, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d77087eee, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766aa72b74b, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d77fdf055, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766aa72b74b, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d78b19984, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766aa72b74b, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d7996678c, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad6ac7769d86b42766aa72b74b, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d79ca44d3, 0x019822ad6ac577e3b3c3cea1b0a930c1, 0x019822ad6ac7769d86b42766ac8b66de, 'Hey non_ami_fixe ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d7a4b50ef, 0x019822ad6ac577e3b3c3cea1b0a930c1, 0x019822ad6ac7769d86b42766ac8b66de, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d7ab0d22e, 0x019822ad3e27777a97c8479ffdd7f1dd, 0x019822ad6ac7769d86b42766ac8b66de, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d7b34f602, 0x019822ad3e27777a97c8479ffdd7f1dd, 0x019822ad6ac7769d86b42766ac8b66de, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d7bd00158, 0x019822ad6ac577e3b3c3cea1b0a930c1, 0x019822ad6ac7769d86b42766ac8b66de, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d7c1ace48, 0x019822ad6ac577e3b3c3cea1b0a930c1, 0x019822ad6ac7769d86b42766ac8b66de, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d7c465023, 0x019822ad6ac577e3b3c3cea1b0a930c1, 0x019822ad6ac7769d86b42766ac8b66de, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d7d31d65b, 0x019822ad3e27777a97c8479ffdd7f1dd, 0x019822ad6ac7769d86b42766ac8b66de, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d7dc79b3a, 0x019822ad6ac577e3b3c3cea1b0a930c1, 0x019822ad6ac7769d86b42766ac8b66de, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d7e260c17, 0x019822ad3e27777a97c8479ffdd7f1dd, 0x019822ad6ac7769d86b42766ac8b66de, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d7efa8635, 0x019822ad460375ddad9246e8027a6baa, 0x019822ad6ac7769d86b42766ae794c99, 'Hey camille.laine ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d7f06c050, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766ae794c99, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d7f84c884, 0x019822ad460375ddad9246e8027a6baa, 0x019822ad6ac7769d86b42766ae794c99, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d7fcd8ba8, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766ae794c99, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d7fd200c4, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766ae794c99, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d7fea2363, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766ae794c99, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d8026801a, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766ae794c99, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d809f574e, 0x019822ad460375ddad9246e8027a6baa, 0x019822ad6ac7769d86b42766ae794c99, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d813a0135, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766aee50d35, 'Hey ewagner ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d82001d1d, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac7769d86b42766aee50d35, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d82e7f6b7, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac7769d86b42766aee50d35, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d82ef7b47, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766aee50d35, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d83eb38cb, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766aee50d35, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d849c067d, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac7769d86b42766aee50d35, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d859355a5, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac7769d86b42766aee50d35, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d86728535, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac7769d86b42766aee50d35, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d86e45287, 0x019822ad577b77a7bddbfa08baa6e336, 0x019822ad6ac7769d86b42766b191cc93, 'Hey perrin.jules ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ace72f984fe456d87d1679c, 0x019822ad577b77a7bddbfa08baa6e336, 0x019822ad6ac7769d86b42766b191cc93, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bc16fdf93, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac7769d86b42766b191cc93, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bc22dd150, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac7769d86b42766b191cc93, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bc261752d, 0x019822ad577b77a7bddbfa08baa6e336, 0x019822ad6ac7769d86b42766b191cc93, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bc334942a, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac7769d86b42766b191cc93, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bc3f902d0, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac7769d86b42766b191cc93, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bc4d57408, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac7769d86b42766b191cc93, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bc52b962f, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766b3307bab, 'Hey ewagner ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bc6279d5b, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766b3307bab, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bc6829734, 0x019822ad441d76a5982d73fda37847b5, 0x019822ad6ac7769d86b42766b3307bab, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bc74db7b5, 0x019822ad441d76a5982d73fda37847b5, 0x019822ad6ac7769d86b42766b3307bab, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bc7f6b9f9, 0x019822ad441d76a5982d73fda37847b5, 0x019822ad6ac7769d86b42766b3307bab, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bc8d57a67, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766b3307bab, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bc9484187, 0x019822ad441d76a5982d73fda37847b5, 0x019822ad6ac7769d86b42766b3307bab, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bc9536fd2, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766b3307bab, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bc95d3c0e, 0x019822ad441d76a5982d73fda37847b5, 0x019822ad6ac7769d86b42766b3307bab, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bc9d106e5, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766b3307bab, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bca2a3d78, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766b3307bab, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bcae33bc4, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766b3307bab, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bcb16f80b, 0x019822ad441d76a5982d73fda37847b5, 0x019822ad6ac7769d86b42766b4d39cd4, 'Hey froyer ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bcbe07fc5, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac7769d86b42766b4d39cd4, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bccc6b1af, 0x019822ad441d76a5982d73fda37847b5, 0x019822ad6ac7769d86b42766b4d39cd4, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bcd5931f1, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac7769d86b42766b4d39cd4, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bce457bb2, 0x019822ad441d76a5982d73fda37847b5, 0x019822ad6ac7769d86b42766b4d39cd4, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bcf2bf2ac, 0x019822ad441d76a5982d73fda37847b5, 0x019822ad6ac7769d86b42766b4d39cd4, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bd0214728, 0x019822ad441d76a5982d73fda37847b5, 0x019822ad6ac7769d86b42766b4d39cd4, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bd0a2b3dc, 0x019822ad441d76a5982d73fda37847b5, 0x019822ad6ac7769d86b42766b4d39cd4, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bd144ac28, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac7769d86b42766b4d39cd4, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bd22a4db4, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac7769d86b42766b4d39cd4, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bd2e74b1c, 0x019822ad441d76a5982d73fda37847b5, 0x019822ad6ac7769d86b42766b4d39cd4, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bd3856cc8, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad6ac7769d86b42766b75afe46, 'Hey ami_fixe_2 ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bd42b5896, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad6ac7769d86b42766b75afe46, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bd4b9a0bc, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad6ac7769d86b42766b75afe46, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bd544a7cb, 0x019822ad460375ddad9246e8027a6baa, 0x019822ad6ac7769d86b42766b75afe46, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bd5b7cd87, 0x019822ad460375ddad9246e8027a6baa, 0x019822ad6ac7769d86b42766b75afe46, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bd634c5b1, 0x019822ad460375ddad9246e8027a6baa, 0x019822ad6ac7769d86b42766b75afe46, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bd720617c, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad6ac7769d86b42766b75afe46, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bd7feda6f, 0x019822ad460375ddad9246e8027a6baa, 0x019822ad6ac7769d86b42766b75afe46, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bd80cad98, 0x019822ad460375ddad9246e8027a6baa, 0x019822ad6ac7769d86b42766b808c6e8, 'Hey camille.laine ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bd89d9ec7, 0x019822ad49de7ecb94e409601cf98302, 0x019822ad6ac7769d86b42766b808c6e8, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6acf7f209b70867bd8bc2af6, 0x019822ad49de7ecb94e409601cf98302, 0x019822ad6ac7769d86b42766b808c6e8, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d3aeee5b7, 0x019822ad49de7ecb94e409601cf98302, 0x019822ad6ac7769d86b42766b808c6e8, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d3ba013fa, 0x019822ad460375ddad9246e8027a6baa, 0x019822ad6ac7769d86b42766b808c6e8, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d3bbffd79, 0x019822ad460375ddad9246e8027a6baa, 0x019822ad6ac7769d86b42766b808c6e8, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d3cb4ac5b, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766b9631fff, 'Hey martine25 ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d3cc5e79b, 0x019822ad4bbe73a9b1389b36b0bef27d, 0x019822ad6ac7769d86b42766b9631fff, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d3d29edb2, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766b9631fff, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d3ded55d9, 0x019822ad4bbe73a9b1389b36b0bef27d, 0x019822ad6ac7769d86b42766b9631fff, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d3ec2334f, 0x019822ad4bbe73a9b1389b36b0bef27d, 0x019822ad6ac7769d86b42766b9631fff, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d3fa635d6, 0x019822ad4bbe73a9b1389b36b0bef27d, 0x019822ad6ac7769d86b42766b9631fff, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d403baf3a, 0x019822ad4bbe73a9b1389b36b0bef27d, 0x019822ad6ac7769d86b42766b9631fff, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d40df120f, 0x019822ad4bbe73a9b1389b36b0bef27d, 0x019822ad6ac7769d86b42766b9631fff, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d412e2f6e, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766b9631fff, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d41e45723, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766b9631fff, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d423f87be, 0x019822ad4bbe73a9b1389b36b0bef27d, 0x019822ad6ac7769d86b42766b9631fff, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d4299bdee, 0x019822ad4bbe73a9b1389b36b0bef27d, 0x019822ad6ac7769d86b42766b9631fff, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d437b6d8a, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766baead89a, 'Hey martine25 ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d4437dfd2, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766baead89a, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d44a5e792, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766baead89a, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d45280d27, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac7769d86b42766baead89a, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d45bb3df2, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766baead89a, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d45f633ab, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac7769d86b42766baead89a, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d46aa57c6, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac7769d86b42766baead89a, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d47404499, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac7769d86b42766baead89a, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d481082df, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac7769d86b42766baead89a, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d48fba8fd, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac7769d86b42766baead89a, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d490b38da, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac7769d86b42766baead89a, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d49a74626, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766baead89a, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d49e0fbf2, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766bd0953ee, 'Hey martine25 ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d4ab0ca18, 0x019822ad49de7ecb94e409601cf98302, 0x019822ad6ac7769d86b42766bd0953ee, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d4b948861, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766bd0953ee, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d4c20174d, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766bd0953ee, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d4cf687d6, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766bd0953ee, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d4d9e2fb2, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766bd0953ee, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d4de5b32f, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766bd0953ee, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d4ed6066c, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766bd0953ee, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d4f725f6b, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766bd0953ee, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d4f8fdb0f, 0x019822ad49de7ecb94e409601cf98302, 0x019822ad6ac7769d86b42766bd0953ee, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d50247679, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766bd0953ee, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d5046d91d, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766bd0953ee, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d50539679, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b967f184266, 'Hey gerard77 ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad077f2a652499d514eecd1, 0x019822ad4bbe73a9b1389b36b0bef27d, 0x019822ad6ac87dbfbe873b967f184266, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1eedf2107, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b967f184266, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1efa394ab, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b967f184266, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f036aad1, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b967f184266, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f0ab4f6d, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b967f184266, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f119015e, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b967f184266, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f13f6350, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b967f184266, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f177fb9c, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b967f184266, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f1d0d995, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b967f184266, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f27a3c72, 0x019822ad4bbe73a9b1389b36b0bef27d, 0x019822ad6ac87dbfbe873b967f184266, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f2f4cf48, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b967f184266, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f371ee99, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac87dbfbe873b9680932ef7, 'Hey userClassique ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f441f9f8, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad6ac87dbfbe873b9680932ef7, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f521b818, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad6ac87dbfbe873b9680932ef7, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f54ed418, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad6ac87dbfbe873b9680932ef7, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f60987ec, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac87dbfbe873b9680932ef7, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f60ea3c3, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad6ac87dbfbe873b9680932ef7, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f6661888, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad6ac87dbfbe873b9680932ef7, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f7309fde, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad6ac87dbfbe873b9680932ef7, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f8116f1c, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad6ac87dbfbe873b9681e046c8, 'Hey rmarin ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f8545c9d, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad6ac87dbfbe873b9681e046c8, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f93b96b4, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad6ac87dbfbe873b9681e046c8, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1f9f0bb9b, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b9681e046c8, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1fa5c035a, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b9681e046c8, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1fa632df5, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b9681e046c8, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1fafd708f, 0x019822ad4f9e709c96815cc2c083e822, 0x019822ad6ac87dbfbe873b9683255444, 'Hey richard47 ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1fb5f5cd4, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac87dbfbe873b9683255444, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1fc0f7ce2, 0x019822ad4f9e709c96815cc2c083e822, 0x019822ad6ac87dbfbe873b9683255444, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1fc8f2bf6, 0x019822ad4f9e709c96815cc2c083e822, 0x019822ad6ac87dbfbe873b9683255444, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1fd11fd29, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac87dbfbe873b9683255444, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1fde5fe29, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac87dbfbe873b9683255444, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1fe8a53e8, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac87dbfbe873b9683255444, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1ff0081a2, 0x019822ad4f9e709c96815cc2c083e822, 0x019822ad6ac87dbfbe873b9683255444, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1ff7229ba, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac87dbfbe873b9683255444, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef1ffaef58b, 0x019822ad4f9e709c96815cc2c083e822, 0x019822ad6ac87dbfbe873b9683255444, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef20085c897, 0x019822ad595e77228150433d5a83b3ea, 0x019822ad6ac87dbfbe873b968459a72d, 'Hey marques.timothee ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef2017fac11, 0x019822ad595e77228150433d5a83b3ea, 0x019822ad6ac87dbfbe873b968459a72d, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef201a25e0e, 0x019822ad595e77228150433d5a83b3ea, 0x019822ad6ac87dbfbe873b968459a72d, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef20206dd1f, 0x019822ad595e77228150433d5a83b3ea, 0x019822ad6ac87dbfbe873b968459a72d, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef203047bc1, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac87dbfbe873b968459a72d, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad17e8791a90ef203e67290, 0x019822ad595e77228150433d5a83b3ea, 0x019822ad6ac87dbfbe873b968459a72d, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f94624753, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac87dbfbe873b968459a72d, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f95501877, 0x019822ad595e77228150433d5a83b3ea, 0x019822ad6ac87dbfbe873b968459a72d, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f96261102, 0x019822ad595e77228150433d5a83b3ea, 0x019822ad6ac87dbfbe873b968459a72d, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f970d1a96, 0x019822ad595e77228150433d5a83b3ea, 0x019822ad6ac87dbfbe873b968459a72d, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f97b6c7ca, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac87dbfbe873b968459a72d, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f98031a3e, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968529744f, 'Hey xavier35 ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f98070738, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b968529744f, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f98ba73dc, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968529744f, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f99af8e9e, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b968529744f, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f9a24f89d, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b968529744f, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f9a7088ec, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968529744f, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f9b0120aa, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b968529744f, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f9bfecec4, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968529744f, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f9cab004b, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968529744f, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f9d4907da, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b968529744f, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f9d516ae6, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968529744f, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f9e0e0b9a, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac87dbfbe873b968686c70c, 'Hey shoarau ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f9e2967e5, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac87dbfbe873b968686c70c, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f9ece6cc6, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968686c70c, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f9f1093b1, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac87dbfbe873b968686c70c, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f9fc1c97c, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968686c70c, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179f9ff4faff, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968686c70c, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa0958c46, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac87dbfbe873b968686c70c, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa0c4549e, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac87dbfbe873b968686c70c, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa1b29687, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968686c70c, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa208c9c7, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968686c70c, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa25c3563, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b968717f9e2, 'Hey denise10 ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa2d8d3ff, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b968717f9e2, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa3b625eb, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b968717f9e2, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa41af0f5, 0x019822ad577b77a7bddbfa08baa6e336, 0x019822ad6ac87dbfbe873b968717f9e2, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa4e45c68, 0x019822ad577b77a7bddbfa08baa6e336, 0x019822ad6ac87dbfbe873b968717f9e2, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa58922f4, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b9688a33370, 'Hey denise10 ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa62b1d91, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac87dbfbe873b9688a33370, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa6987173, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b9688a33370, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa7222c8b, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b9688a33370, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa723e413, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b9688a33370, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa7348806, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac87dbfbe873b9688a33370, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa7828695, 0x019822ad4bbe73a9b1389b36b0bef27d, 0x019822ad6ac87dbfbe873b968a576481, 'Hey nbarthelemy ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa814d4c8, 0x019822ad577b77a7bddbfa08baa6e336, 0x019822ad6ac87dbfbe873b968a576481, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa8e099f5, 0x019822ad577b77a7bddbfa08baa6e336, 0x019822ad6ac87dbfbe873b968a576481, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa9dd70ad, 0x019822ad577b77a7bddbfa08baa6e336, 0x019822ad6ac87dbfbe873b968a576481, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179fa9dee7f1, 0x019822ad577b77a7bddbfa08baa6e336, 0x019822ad6ac87dbfbe873b968a576481, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad27669a424179faab3b9e6, 0x019822ad4bbe73a9b1389b36b0bef27d, 0x019822ad6ac87dbfbe873b968a576481, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff713e16f090, 0x019822ad4bbe73a9b1389b36b0bef27d, 0x019822ad6ac87dbfbe873b968a576481, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff713ec6c4c1, 0x019822ad577b77a7bddbfa08baa6e336, 0x019822ad6ac87dbfbe873b968cd3dddc, 'Hey perrin.jules ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff713f45084b, 0x019822ad631e7a4ba28b9732e59c72fc, 0x019822ad6ac87dbfbe873b968cd3dddc, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff713f5b722f, 0x019822ad631e7a4ba28b9732e59c72fc, 0x019822ad6ac87dbfbe873b968cd3dddc, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff713fbc0551, 0x019822ad631e7a4ba28b9732e59c72fc, 0x019822ad6ac87dbfbe873b968cd3dddc, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff7140afd0c9, 0x019822ad631e7a4ba28b9732e59c72fc, 0x019822ad6ac87dbfbe873b968cd3dddc, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff71411f0f47, 0x019822ad631e7a4ba28b9732e59c72fc, 0x019822ad6ac87dbfbe873b968cd3dddc, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff7142012947, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968dbf3b51, 'Hey xavier35 ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff7142eec234, 0x019822ad595e77228150433d5a83b3ea, 0x019822ad6ac87dbfbe873b968dbf3b51, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff7143cc733c, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968dbf3b51, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff714468b1c9, 0x019822ad595e77228150433d5a83b3ea, 0x019822ad6ac87dbfbe873b968dbf3b51, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff71451eebc6, 0x019822ad595e77228150433d5a83b3ea, 0x019822ad6ac87dbfbe873b968dbf3b51, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff7145e7ae5c, 0x019822ad595e77228150433d5a83b3ea, 0x019822ad6ac87dbfbe873b968dbf3b51, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff7146d802a7, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968dbf3b51, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff7147378fa4, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968dbf3b51, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff714820faec, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968dbf3b51, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff71489a5324, 0x019822ad595e77228150433d5a83b3ea, 0x019822ad6ac87dbfbe873b968dbf3b51, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff71494f1bd9, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968dbf3b51, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff714a1fa96d, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968dbf3b51, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff714a3ee994, 0x019822ad5b5477c881098db6f7b091ab, 0x019822ad6ac87dbfbe873b968ed9ab23, 'Hey monique.riou ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff714b1752a5, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad6ac87dbfbe873b968ed9ab23, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff714baabb1a, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad6ac87dbfbe873b968ed9ab23, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff714c3ddef5, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad6ac87dbfbe873b968ed9ab23, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff714c968adb, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad6ac87dbfbe873b968ed9ab23, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff714cd07803, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad6ac87dbfbe873b968ed9ab23, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff714dc3439c, 0x019822ad5b5477c881098db6f7b091ab, 0x019822ad6ac87dbfbe873b968ed9ab23, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff714e74b95f, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad6ac87dbfbe873b968ed9ab23, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff714f61f7ca, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac87dbfbe873b96905df17d, 'Hey shoarau ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff715029683d, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac87dbfbe873b96905df17d, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff7150ab9451, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac87dbfbe873b96905df17d, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff7150cfe538, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac87dbfbe873b96905df17d, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff71511552b0, 0x019822ad5b5477c881098db6f7b091ab, 0x019822ad6ac87dbfbe873b96905df17d, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff715180a1e3, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac87dbfbe873b96905df17d, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff715216fdaa, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b96929f86f4, 'Hey gerard77 ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff7152b50d77, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b96929f86f4, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff7152df94e5, 0x019822ad6130711eba790f3dc8ee3295, 0x019822ad6ac87dbfbe873b96929f86f4, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff71532e6840, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b96929f86f4, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff71541f04aa, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b96929f86f4, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad3709ebfc3ff71550d776f, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b96929f86f4, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e3526dad5b7, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b96929f86f4, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e3527b3f0d4, 0x019822ad5f4b7d0b9b3d9de9f72e85ba, 0x019822ad6ac87dbfbe873b9694d6b560, 'Hey dcharles ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e35285f654d, 0x019822ad5f4b7d0b9b3d9de9f72e85ba, 0x019822ad6ac87dbfbe873b9694d6b560, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e3528f58757, 0x019822ad5f4b7d0b9b3d9de9f72e85ba, 0x019822ad6ac87dbfbe873b9694d6b560, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e3529840733, 0x019822ad5f4b7d0b9b3d9de9f72e85ba, 0x019822ad6ac87dbfbe873b9694d6b560, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e352a51b53d, 0x019822ad49de7ecb94e409601cf98302, 0x019822ad6ac87dbfbe873b9694d6b560, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e352aadf8ce, 0x019822ad5f4b7d0b9b3d9de9f72e85ba, 0x019822ad6ac87dbfbe873b9694d6b560, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e352b100510, 0x019822ad49de7ecb94e409601cf98302, 0x019822ad6ac87dbfbe873b9694d6b560, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e352b1eab4f, 0x019822ad5f4b7d0b9b3d9de9f72e85ba, 0x019822ad6ac87dbfbe873b9694d6b560, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e352b9766c9, 0x019822ad49de7ecb94e409601cf98302, 0x019822ad6ac87dbfbe873b9694d6b560, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e352bcd6e82, 0x019822ad5f4b7d0b9b3d9de9f72e85ba, 0x019822ad6ac87dbfbe873b9694d6b560, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e352c0684c6, 0x019822ad6130711eba790f3dc8ee3295, 0x019822ad6ac87dbfbe873b9696165580, 'Hey guillaume.isabelle ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e352c249722, 0x019822ad6130711eba790f3dc8ee3295, 0x019822ad6ac87dbfbe873b9696165580, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e352c5c8f8b, 0x019822ad6130711eba790f3dc8ee3295, 0x019822ad6ac87dbfbe873b9696165580, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e352cea3421, 0x019822ad6130711eba790f3dc8ee3295, 0x019822ad6ac87dbfbe873b9696165580, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e352d9b524d, 0x019822ad6130711eba790f3dc8ee3295, 0x019822ad6ac87dbfbe873b9696165580, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e352de4e31f, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac87dbfbe873b9696165580, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e352dfacdde, 0x019822ad5b5477c881098db6f7b091ab, 0x019822ad6ac87dbfbe873b969757aca4, 'Hey monique.riou ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text');
INSERT INTO `message` (`id`, `sender_id`, `room_id`, `content`, `file_name`, `created_at`, `type`) VALUES
(0x019822ad6ad472d6b31e9e352e03f104, 0x019822ad631e7a4ba28b9732e59c72fc, 0x019822ad6ac87dbfbe873b969757aca4, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e352e1a4e16, 0x019822ad631e7a4ba28b9732e59c72fc, 0x019822ad6ac87dbfbe873b969757aca4, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e352efe870a, 0x019822ad5b5477c881098db6f7b091ab, 0x019822ad6ac87dbfbe873b969757aca4, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e352f57eb16, 0x019822ad631e7a4ba28b9732e59c72fc, 0x019822ad6ac87dbfbe873b969757aca4, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e35301fe550, 0x019822ad631e7a4ba28b9732e59c72fc, 0x019822ad6ac87dbfbe873b969757aca4, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e3530d5cf61, 0x019822ad5b5477c881098db6f7b091ab, 0x019822ad6ac87dbfbe873b969757aca4, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e3531129fe4, 0x019822ad66f47ee480bd993a12c986aa, 0x019822ad6ac87dbfbe873b96982e4141, 'Hey ami_fixe_1 ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e3531189bee, 0x019822ad66f47ee480bd993a12c986aa, 0x019822ad6ac87dbfbe873b96982e4141, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e3531b47747, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b96982e4141, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e35322d93cd, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b96982e4141, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e3532f82d72, 0x019822ad66f47ee480bd993a12c986aa, 0x019822ad6ac87dbfbe873b96982e4141, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e353399a72c, 0x019822ad66f47ee480bd993a12c986aa, 0x019822ad6ac87dbfbe873b96982e4141, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e35345fa6e5, 0x019822ad66f47ee480bd993a12c986aa, 0x019822ad6ac87dbfbe873b96982e4141, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e3534fecee1, 0x019822ad66f47ee480bd993a12c986aa, 0x019822ad6ac87dbfbe873b96982e4141, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e353542b007, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b96982e4141, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad472d6b31e9e353548e299, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b96982e4141, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf91f8fa8864, 0x019822ad3e27777a97c8479ffdd7f1dd, 0x019822ad6ac87dbfbe873b969999c917, 'Hey navarro.lucas ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf91f972fafc, 0x019822ad3e27777a97c8479ffdd7f1dd, 0x019822ad6ac87dbfbe873b969999c917, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf91f9d87e51, 0x019822ad3e27777a97c8479ffdd7f1dd, 0x019822ad6ac87dbfbe873b969999c917, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf91f9edb3e5, 0x019822ad3e27777a97c8479ffdd7f1dd, 0x019822ad6ac87dbfbe873b969999c917, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf91fae43d41, 0x019822ad3e27777a97c8479ffdd7f1dd, 0x019822ad6ac87dbfbe873b969999c917, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf91fb70283b, 0x019822ad66f47ee480bd993a12c986aa, 0x019822ad6ac87dbfbe873b969999c917, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf91fbfb5a8e, 0x019822ad3e27777a97c8479ffdd7f1dd, 0x019822ad6ac87dbfbe873b969999c917, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf91fc504fc1, 0x019822ad66f47ee480bd993a12c986aa, 0x019822ad6ac87dbfbe873b969999c917, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf91fd0cddf6, 0x019822ad66f47ee480bd993a12c986aa, 0x019822ad6ac87dbfbe873b969999c917, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf91fdb8d1b1, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad6ac87dbfbe873b969bacfc75, 'Hey ami_fixe_2 ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf91fe397bab, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad6ac87dbfbe873b969bacfc75, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf91fe58344f, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b969bacfc75, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf91ff21919f, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad6ac87dbfbe873b969bacfc75, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf91ff340dfc, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b969bacfc75, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf920026e46c, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b969bacfc75, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf9200cf4662, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b969bacfc75, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf920119d03c, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b969bacfc75, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf9201cc74b4, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b969bacfc75, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf9201e5df16, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b969bacfc75, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf9202b28bec, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad6ac87dbfbe873b969bacfc75, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf9203072d80, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b969bacfc75, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf92032ea3e6, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b969d93afb1, 'Hey xavier35 ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf92035706b8, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad6ac87dbfbe873b969d93afb1, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf92043a600f, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad6ac87dbfbe873b969d93afb1, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf9204b8b464, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad6ac87dbfbe873b969d93afb1, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf92050735db, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b969d93afb1, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf92051b6200, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b969d93afb1, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf920587b45e, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad6ac87dbfbe873b969d93afb1, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf92066c6283, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b969d93afb1, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf92075005b9, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad6ac87dbfbe873b969d93afb1, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf9207cc87a4, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b969d93afb1, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf9208212821, 0x019822ad6ac577e3b3c3cea1b0a930c1, 0x019822ad6ac87dbfbe873b969e47bad7, 'Hey non_ami_fixe ! Ravi de discuter 😊', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf9208f23dd0, 0x019822ad5f4b7d0b9b3d9de9f72e85ba, 0x019822ad6ac87dbfbe873b969e47bad7, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad57a28823dbf92099533cc, 0x019822ad6ac577e3b3c3cea1b0a930c1, 0x019822ad6ac87dbfbe873b969e47bad7, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49eb68a15fc, 0x019822ad6ac577e3b3c3cea1b0a930c1, 0x019822ad6ac87dbfbe873b969e47bad7, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49eb71a9eae, 0x019822ad5f4b7d0b9b3d9de9f72e85ba, 0x019822ad6ac87dbfbe873b969e47bad7, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49eb7463130, 0x019822ad5f4b7d0b9b3d9de9f72e85ba, 0x019822ad6ac87dbfbe873b969e47bad7, 'Trop cool la dernière partie !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49eb802c634, 0x019822ad6ac577e3b3c3cea1b0a930c1, 0x019822ad6ac87dbfbe873b969e47bad7, 'On se capte quand pour jouer ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49eb897920b, 0x019822ad6ac577e3b3c3cea1b0a930c1, 0x019822ad6ac87dbfbe873b969e47bad7, 'Tu fais quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49eb8b7e592, 0x019822ad5f4b7d0b9b3d9de9f72e85ba, 0x019822ad6ac87dbfbe873b969e47bad7, 'À plus tard !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49eb91d4b25, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac87dbfbe873b969f76606f, 'Hâte d\'être ce week-end !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49eb9a5f427, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b969f76606f, 'On joue à quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49eba11a8fd, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac87dbfbe873b969f76606f, 'Quelqu\'un dispo pour un Morpion ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49ebac53253, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b969f76606f, 'J\'ai trouvé un nouveau jeu à tester.', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49ebb242031, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b969f76606f, 'Hâte d\'être ce week-end !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49ebbed8618, 0x019822ad49de7ecb94e409601cf98302, 0x019822ad6ac87dbfbe873b969f76606f, 'J\'ai trouvé un nouveau jeu à tester.', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49ebc9ed2c9, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b969f76606f, 'J\'ai trouvé un nouveau jeu à tester.', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49ebd13feba, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac87dbfbe873b969f76606f, 'Salut tout le monde !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49ebdd90d58, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac87dbfbe873b969f76606f, 'Vous allez bien ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49ebde2db84, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac87dbfbe873b969f76606f, 'Qui veut créer un groupe privé ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49ebe16490f, 0x019822ad441d76a5982d73fda37847b5, 0x019822ad6ac87dbfbe873b969f76606f, 'Vous allez bien ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49ebefdc603, 0x019822ad460375ddad9246e8027a6baa, 0x019822ad6ac97496bd3d77527962c02b, 'Salut tout le monde !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49ebf32a5f1, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac97496bd3d77527962c02b, 'Quelqu\'un dispo pour un Morpion ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49ebf62ddf3, 0x019822ad4bbe73a9b1389b36b0bef27d, 0x019822ad6ac97496bd3d77527962c02b, 'Salut tout le monde !', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49ebfa2b2e6, 0x019822ad460375ddad9246e8027a6baa, 0x019822ad6ac97496bd3d77527962c02b, 'On joue à quoi ce soir ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49ec0603d6b, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac97496bd3d77527962c02b, 'Qui veut créer un groupe privé ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49ec0b64d9b, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac97496bd3d77527962c02b, 'J\'ai trouvé un nouveau jeu à tester.', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49ec12b4bc6, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac97496bd3d77527962c02b, 'Qui veut créer un groupe privé ?', NULL, '2025-07-19 12:34:07', 'text'),
(0x019822ad6ad675909cd2a49ec1deaf94, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac97496bd3d77527962c02b, 'Salut tout le monde !', NULL, '2025-07-19 12:34:07', 'text');

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
(0x019822ad6ac7769d86b42766a6641e45, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766a7e41a21, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766a83417ed, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766aa72b74b, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766ac8b66de, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766ae794c99, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766aee50d35, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766b191cc93, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766b3307bab, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766b4d39cd4, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766b75afe46, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766b808c6e8, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766b9631fff, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766baead89a, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac7769d86b42766bd0953ee, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b967f184266, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b9680932ef7, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b9681e046c8, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b9683255444, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b968459a72d, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b968529744f, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b968686c70c, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b968717f9e2, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b9688a33370, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b968a576481, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b968cd3dddc, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b968dbf3b51, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b968ed9ab23, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b96905df17d, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b96929f86f4, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b9694d6b560, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b9696165580, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b969757aca4, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b96982e4141, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b969999c917, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b969bacfc75, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b969d93afb1, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b969e47bad7, NULL, 0, '2025-07-19 12:34:07'),
(0x019822ad6ac87dbfbe873b969f76606f, 'Groupe de userClassique #1', 1, '2025-07-19 12:34:07'),
(0x019822ad6ac97496bd3d77527962c02b, 'Groupe de userClassique #2', 1, '2025-07-19 12:34:07');

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
(0x019822ad6ac7769d86b42766a7515fca, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a6641e45, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766a7c8ca8a, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766a6641e45, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766a82c9511, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a7e41a21, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766a8338d6b, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766a7e41a21, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766a88998aa, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766a83417ed, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766a97a2c51, 0x019822ad66f47ee480bd993a12c986aa, 0x019822ad6ac7769d86b42766a83417ed, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766ab4508b4, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac7769d86b42766aa72b74b, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766ac0160a6, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad6ac7769d86b42766aa72b74b, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766ad50ab4c, 0x019822ad3e27777a97c8479ffdd7f1dd, 0x019822ad6ac7769d86b42766ac8b66de, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766ae20a422, 0x019822ad6ac577e3b3c3cea1b0a930c1, 0x019822ad6ac7769d86b42766ac8b66de, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766ae98555e, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766ae794c99, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766aecba0bf, 0x019822ad460375ddad9246e8027a6baa, 0x019822ad6ac7769d86b42766ae794c99, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766afbb043f, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766aee50d35, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766b0b8fd66, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac7769d86b42766aee50d35, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766b28ac47d, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac7769d86b42766b191cc93, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766b2a86deb, 0x019822ad577b77a7bddbfa08baa6e336, 0x019822ad6ac7769d86b42766b191cc93, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766b3d7d95c, 0x019822ad441d76a5982d73fda37847b5, 0x019822ad6ac7769d86b42766b3307bab, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766b4ab4a0c, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac7769d86b42766b3307bab, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766b586ec40, 0x019822ad441d76a5982d73fda37847b5, 0x019822ad6ac7769d86b42766b4d39cd4, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766b6767625, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac7769d86b42766b4d39cd4, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766b7a7dd32, 0x019822ad460375ddad9246e8027a6baa, 0x019822ad6ac7769d86b42766b75afe46, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766b7d64a60, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad6ac7769d86b42766b75afe46, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766b8c6251b, 0x019822ad460375ddad9246e8027a6baa, 0x019822ad6ac7769d86b42766b808c6e8, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766b93ae744, 0x019822ad49de7ecb94e409601cf98302, 0x019822ad6ac7769d86b42766b808c6e8, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766ba027a6d, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766b9631fff, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766ba8c9a08, 0x019822ad4bbe73a9b1389b36b0bef27d, 0x019822ad6ac7769d86b42766b9631fff, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766bb7aa6d4, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766baead89a, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766bc3e391a, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac7769d86b42766baead89a, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766bd2a5760, 0x019822ad49de7ecb94e409601cf98302, 0x019822ad6ac7769d86b42766bd0953ee, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac7769d86b42766bde36889, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac7769d86b42766bd0953ee, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b967f60a7c2, 0x019822ad4bbe73a9b1389b36b0bef27d, 0x019822ad6ac87dbfbe873b967f184266, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b967fac34fe, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b967f184266, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b9680efa502, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad6ac87dbfbe873b9680932ef7, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b9681a989eb, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac87dbfbe873b9680932ef7, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b96821f23b5, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad6ac87dbfbe873b9681e046c8, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b9682bc7dd5, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b9681e046c8, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b9683c71116, 0x019822ad4f9e709c96815cc2c083e822, 0x019822ad6ac87dbfbe873b9683255444, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b968419c7a3, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac87dbfbe873b9683255444, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b9684a53979, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac87dbfbe873b968459a72d, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b9684bf0e04, 0x019822ad595e77228150433d5a83b3ea, 0x019822ad6ac87dbfbe873b968459a72d, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b9685453719, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968529744f, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b9685926ec6, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b968529744f, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b9686ced536, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968686c70c, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b96870d6540, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac87dbfbe873b968686c70c, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b96879cd58a, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b968717f9e2, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b9688646d17, 0x019822ad577b77a7bddbfa08baa6e336, 0x019822ad6ac87dbfbe873b968717f9e2, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b968979f337, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b9688a33370, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b968a1a1063, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac87dbfbe873b9688a33370, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b968af758a1, 0x019822ad577b77a7bddbfa08baa6e336, 0x019822ad6ac87dbfbe873b968a576481, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b968be88bc7, 0x019822ad4bbe73a9b1389b36b0bef27d, 0x019822ad6ac87dbfbe873b968a576481, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b968d51b551, 0x019822ad577b77a7bddbfa08baa6e336, 0x019822ad6ac87dbfbe873b968cd3dddc, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b968db488ae, 0x019822ad631e7a4ba28b9732e59c72fc, 0x019822ad6ac87dbfbe873b968cd3dddc, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b968df129e1, 0x019822ad595e77228150433d5a83b3ea, 0x019822ad6ac87dbfbe873b968dbf3b51, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b968e437923, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b968dbf3b51, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b968f016edc, 0x019822ad5b5477c881098db6f7b091ab, 0x019822ad6ac87dbfbe873b968ed9ab23, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b968fc91d74, 0x019822ad4d9e7c24b9e2e4e05c920858, 0x019822ad6ac87dbfbe873b968ed9ab23, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b9690ec5388, 0x019822ad5b5477c881098db6f7b091ab, 0x019822ad6ac87dbfbe873b96905df17d, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b9691a4f1b7, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac87dbfbe873b96905df17d, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b9693191133, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b96929f86f4, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b9693de23cc, 0x019822ad6130711eba790f3dc8ee3295, 0x019822ad6ac87dbfbe873b96929f86f4, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b9695495fce, 0x019822ad5f4b7d0b9b3d9de9f72e85ba, 0x019822ad6ac87dbfbe873b9694d6b560, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b96958e5d36, 0x019822ad49de7ecb94e409601cf98302, 0x019822ad6ac87dbfbe873b9694d6b560, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b9696f38a0b, 0x019822ad6130711eba790f3dc8ee3295, 0x019822ad6ac87dbfbe873b9696165580, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b9697109986, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac87dbfbe873b9696165580, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b9697b75db8, 0x019822ad631e7a4ba28b9732e59c72fc, 0x019822ad6ac87dbfbe873b969757aca4, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b9697cdcc95, 0x019822ad5b5477c881098db6f7b091ab, 0x019822ad6ac87dbfbe873b969757aca4, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b969905d0f2, 0x019822ad66f47ee480bd993a12c986aa, 0x019822ad6ac87dbfbe873b96982e4141, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b969982fdac, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b96982e4141, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b969a16ee26, 0x019822ad66f47ee480bd993a12c986aa, 0x019822ad6ac87dbfbe873b969999c917, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b969b087a50, 0x019822ad3e27777a97c8479ffdd7f1dd, 0x019822ad6ac87dbfbe873b969999c917, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b969c791e2f, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad6ac87dbfbe873b969bacfc75, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b969d745314, 0x019822ad5d4a7cc497dadb9b77e72bb8, 0x019822ad6ac87dbfbe873b969bacfc75, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b969e0b874f, 0x019822ad68db740e8eac0cb277cd6846, 0x019822ad6ac87dbfbe873b969d93afb1, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b969e259ca2, 0x019822ad538f782f84b85f6a4653f04f, 0x019822ad6ac87dbfbe873b969d93afb1, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b969f1819d1, 0x019822ad6ac577e3b3c3cea1b0a930c1, 0x019822ad6ac87dbfbe873b969e47bad7, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b969f3ad40e, 0x019822ad5f4b7d0b9b3d9de9f72e85ba, 0x019822ad6ac87dbfbe873b969e47bad7, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b96a0515bef, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac87dbfbe873b969f76606f, 'owner', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b96a0f87b5c, 0x019822ad49de7ecb94e409601cf98302, 0x019822ad6ac87dbfbe873b969f76606f, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b96a10f3a85, 0x019822ad557e72e0b86244ef84ae1349, 0x019822ad6ac87dbfbe873b969f76606f, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac87dbfbe873b96a1de38ed, 0x019822ad441d76a5982d73fda37847b5, 0x019822ad6ac87dbfbe873b969f76606f, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac97496bd3d7752789e8f3b, 0x019822ad51917f719b2e0b5fa7709164, 0x019822ad6ac87dbfbe873b969f76606f, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac97496bd3d77527948e4eb, 0x019822ad47fa7062a2eb6fd008cf837c, 0x019822ad6ac87dbfbe873b969f76606f, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac97496bd3d77527979f6df, 0x019822ad65157b949fb8e2026ccdbad7, 0x019822ad6ac97496bd3d77527962c02b, 'owner', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac97496bd3d77527a76ba43, 0x019822ad40127970b9a544af803275ef, 0x019822ad6ac97496bd3d77527962c02b, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac97496bd3d77527b6507ff, 0x019822ad422976f2bdc447bc3ac747e7, 0x019822ad6ac97496bd3d77527962c02b, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac97496bd3d77527b86ab01, 0x019822ad4bbe73a9b1389b36b0bef27d, 0x019822ad6ac97496bd3d77527962c02b, 'user', '2025-07-19 12:34:07', NULL, 1),
(0x019822ad6ac97496bd3d77527c43c77e, 0x019822ad460375ddad9246e8027a6baa, 0x019822ad6ac97496bd3d77527962c02b, 'user', '2025-07-19 12:34:07', NULL, 1);

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
  `friend_code` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `is_verified`, `roles`, `password`, `pseudo`, `image_name`, `created_at`, `updated_at`, `friend_code`) VALUES
(0x019822ad3e27777a97c8479ffdd7f1dd, 'aurelie.blin@example.net', 1, '[]', '$2y$13$nKQ5hn.X19Re7iMXMRWWgONu.0orST3MfYwM8LLDZgM7u61scZR2K', 'navarro.lucas', 'default-avatar4.svg', '2025-07-19 12:33:55', '2025-07-19 12:33:55', '9AFAC554602030CB3D98'),
(0x019822ad40127970b9a544af803275ef, 'valerie.bodin@example.org', 1, '[]', '$2y$13$XDBZd4suu.DNmlATTIySUOn4JKSmW2WxdARx0YEtSZ42S1SpHGeY2', 'ewagner', 'default-avatar3.svg', '2025-07-19 12:33:56', '2025-07-19 12:33:56', '1063DBAF4F67312D01B1'),
(0x019822ad422976f2bdc447bc3ac747e7, 'gerard.isabelle@example.com', 1, '[]', '$2y$13$.XOL0hQyPOgv4/rKdOEKyegVQMfFjpT1nB03qFRidds2uH3QbZYNm', 'jacques65', 'default-avatar5.svg', '2025-07-19 12:33:56', '2025-07-19 12:33:56', '4F94E727CE21A87B5783'),
(0x019822ad441d76a5982d73fda37847b5, 'chretien.audrey@example.com', 1, '[]', '$2y$13$8GRCk7kvDkcxMQDSbSHbI.bOBpCMhdQegczrruBScb3pT3ErqFt0u', 'froyer', 'default-avatar2.svg', '2025-07-19 12:33:57', '2025-07-19 12:33:57', '5C72C20B0829ED19A54A'),
(0x019822ad460375ddad9246e8027a6baa, 'gallet.thibaut@example.com', 1, '[]', '$2y$13$D5pKeTRPaA5OFKtC/rVD8.EiSWi/00Uj83pNNVoXSIDf1eDLjggIm', 'camille.laine', 'default-avatar5.svg', '2025-07-19 12:33:57', '2025-07-19 12:33:57', '89890BF3E3D5C78BDF1C'),
(0x019822ad47fa7062a2eb6fd008cf837c, 'andre.robert@example.net', 1, '[]', '$2y$13$eKclQRlshYvwBmFLBIBXp.RdunS2fL35PXSIIlMl59h/wUUBuO.gK', 'martine25', 'default-avatar4.svg', '2025-07-19 12:33:58', '2025-07-19 12:33:58', '833C0D8087EFD906F861'),
(0x019822ad49de7ecb94e409601cf98302, 'gilles.boulanger@example.com', 1, '[]', '$2y$13$iGxsh88dG3a3PVIHuU6mluVOeqf14KGknzbNNCnNnKMW2qGvS9bSa', 'sdidier', 'default-avatar5.svg', '2025-07-19 12:33:58', '2025-07-19 12:33:58', '9E22EE3FB5A14EBA5E4A'),
(0x019822ad4bbe73a9b1389b36b0bef27d, 'nleblanc@example.net', 1, '[]', '$2y$13$2MhbBYFW3PnmdaOxwqm9g.moEtXIFYcHMJOB3cREm7Y4tvVp0BkF2', 'nbarthelemy', 'default-avatar4.svg', '2025-07-19 12:33:59', '2025-07-19 12:33:59', '108BA03264265E4BD66A'),
(0x019822ad4d9e7c24b9e2e4e05c920858, 'jacquet.julie@example.org', 1, '[]', '$2y$13$E27q2Inp7vs8EyZ3sWlI.ODX32OAI4w1Jl79vtFJ.UluLNrAuO2C2', 'rmarin', 'default-avatar4.svg', '2025-07-19 12:33:59', '2025-07-19 12:33:59', '201D8CC7B8E05BF6CA94'),
(0x019822ad4f9e709c96815cc2c083e822, 'eperret@example.com', 1, '[]', '$2y$13$kHXgn6i4Kl5JmwMskg7sjO4ZTQbjlMUl88qbi9.pwigQ9MYlPnSYq', 'richard47', 'default-avatar5.svg', '2025-07-19 12:34:00', '2025-07-19 12:34:00', 'D6FBB8A21D485F2E4DC4'),
(0x019822ad51917f719b2e0b5fa7709164, 'tristan.marechal@example.net', 1, '[]', '$2y$13$oP0ZV.QLSsptASKCM5BoJe1LnSAxTh5k4ybk3Wi1Wv1dQYb7Olz5C', 'shoarau', 'default-avatar2.svg', '2025-07-19 12:34:00', '2025-07-19 12:34:00', 'B9944E18C376FEF343D4'),
(0x019822ad538f782f84b85f6a4653f04f, 'bertrand19@example.net', 1, '[]', '$2y$13$.n5KxY0UGbsoJHVh8LfGj.puCO36i1GNPcFSjE/9aWr1lQ2Nen.xW', 'xavier35', 'default-avatar2.svg', '2025-07-19 12:34:01', '2025-07-19 12:34:01', 'EE0BB57576523854195A'),
(0x019822ad557e72e0b86244ef84ae1349, 'madeleine89@example.net', 1, '[]', '$2y$13$BitXzM8cljLXWdTXZhu17.67SQ9WRv9qeMpebTgOM.rd/WZmKSwQ.', 'denise10', 'default-avatar4.svg', '2025-07-19 12:34:01', '2025-07-19 12:34:01', '520747537469A2371FDB'),
(0x019822ad577b77a7bddbfa08baa6e336, 'lemoine.robert@example.org', 1, '[]', '$2y$13$aM0H1lcfOhjbpbYJGQiiWepCRB/rnENEgO5D9O61DRJfJhhlHJ3E6', 'perrin.jules', 'default-avatar4.svg', '2025-07-19 12:34:02', '2025-07-19 12:34:02', 'E134DFCAA94950C319B6'),
(0x019822ad595e77228150433d5a83b3ea, 'moreau.hortense@example.org', 1, '[]', '$2y$13$1YM39Afgd1LqG8bBiWqrfe0gmagQfvUfARgFwONwESWVJ9oJg2sAe', 'marques.timothee', 'default-avatar5.svg', '2025-07-19 12:34:02', '2025-07-19 12:34:02', '1F132C51B8BBDF34026E'),
(0x019822ad5b5477c881098db6f7b091ab, 'renault.remy@example.org', 1, '[]', '$2y$13$B8xs62YiO8Ur3qV9hLMNBu6J.e002OKmZae6gGVef.xQ/Vz9a6Vk.', 'monique.riou', 'default-avatar4.svg', '2025-07-19 12:34:03', '2025-07-19 12:34:03', '41B51DA2F9DDFB0A5476'),
(0x019822ad5d4a7cc497dadb9b77e72bb8, 'mercier.luce@example.net', 1, '[]', '$2y$13$VUXh6xmGI6aYJQQAlnwB4.L0cLPOSsQQvu3b4ngVRNkd4Q6xh6TTa', 'gerard77', 'default-avatar2.svg', '2025-07-19 12:34:03', '2025-07-19 12:34:03', 'D1F80FC9B12139EAC317'),
(0x019822ad5f4b7d0b9b3d9de9f72e85ba, 'marcel.alves@example.org', 1, '[]', '$2y$13$xCJ.sFMOLkvPB329jlyNpeDNe89MTVibpXrlBoRJD7eMkojNdJy0e', 'dcharles', 'default-avatar4.svg', '2025-07-19 12:34:04', '2025-07-19 12:34:04', '8635E1183C52B101050F'),
(0x019822ad6130711eba790f3dc8ee3295, 'marcelle87@example.com', 1, '[]', '$2y$13$Xd/JGo5huOPgP078Bct.DOLvZf0nwmD373plRg84LMps0I1FWwwfK', 'guillaume.isabelle', 'default-avatar2.svg', '2025-07-19 12:34:04', '2025-07-19 12:34:04', '1EE7ADCB07287ABE7A0F'),
(0x019822ad631e7a4ba28b9732e59c72fc, 'chantal05@example.com', 1, '[]', '$2y$13$IGud1nhb1JFOEa5/HrIyyu0T6wg7EdOWRbF4olU7XDgOGfQ/j0x4u', 'vincent.joly', 'default-avatar4.svg', '2025-07-19 12:34:05', '2025-07-19 12:34:05', '71E72DDAFBFFDF484BE1'),
(0x019822ad65157b949fb8e2026ccdbad7, 'user@user.com', 1, '[]', '$2y$13$4TNYetR/VrA8vuCZ.zNBlOYbSgftjZZaGSRVcmN9hpaFE.UKCmWVK', 'userClassique', NULL, '2025-07-19 12:34:05', '2025-07-19 12:34:05', '7699D702D32C6C31A652'),
(0x019822ad66f47ee480bd993a12c986aa, 'ami1@roomies.test', 1, '[]', '$2y$13$wcRAPomEqmNnaqq.IqIqT.34eSWxMXANQz6ql9JQ/8nFsc5acfN/6', 'ami_fixe_1', 'default-avatar5.svg', '2025-07-19 12:34:06', '2025-07-19 12:34:06', '47A350833EA98050C4E9'),
(0x019822ad68db740e8eac0cb277cd6846, 'ami2@roomies.test', 1, '[]', '$2y$13$9r51vSLW80dV0VmzWxvz/u7GWsrI/KqBp0HJhIYhCIy1w/HvvD0M.', 'ami_fixe_2', 'default-avatar2.svg', '2025-07-19 12:34:06', '2025-07-19 12:34:06', '581193634B58880F5ECE'),
(0x019822ad6ac577e3b3c3cea1b0a930c1, 'nonami@roomies.test', 1, '[]', '$2y$13$zN.LEE7beUJzHoWwqw0sb.H2ak0Azyf9Cgw5Y2kEmVzFtp/g.f8wu', 'non_ami_fixe', 'default-avatar5.svg', '2025-07-19 12:34:07', '2025-07-19 12:34:07', '03BDD87491F927C8E43C');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `password_reset_token`
--
ALTER TABLE `password_reset_token`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=196;

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
