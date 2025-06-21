-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 21 juin 2025 à 18:35
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
-- Base de données : `parking_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `abonnements`
--

CREATE TABLE `abonnements` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `duree` varchar(20) NOT NULL,
  `reduction` decimal(5,2) NOT NULL,
  `free_minutes` int(11) DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `price` decimal(10,2) DEFAULT NULL COMMENT 'Prix de l''abonnement'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `abonnements`
--

INSERT INTO `abonnements` (`id`, `nom`, `duree`, `reduction`, `free_minutes`, `description`, `created_at`, `updated_at`, `price`) VALUES
(1, 'Hebdomadaire', 'hebdomadaire', 5.00, 5, 'Abonnement hebdomadaire avec 5% de réduction sur toutes les réservations', '2025-06-16 20:53:16', '2025-06-19 20:01:20', 19.98),
(2, 'Mensuel', 'mensuel', 15.00, 15, 'Abonnement mensuel avec 15% de réduction sur toutes les réservations', '2025-06-16 20:53:16', '2025-06-19 00:33:28', 49.99),
(3, 'Annuel', 'annuel', 30.00, 30, 'Abonnement annuel avec 30% de réduction sur toutes les réservations', '2025-06-16 20:53:16', '2025-06-19 00:33:28', 159.00);

-- --------------------------------------------------------

--
-- Structure de la table `alertes_disponibilite`
--

CREATE TABLE `alertes_disponibilite` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `place_id` int(11) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `statut` enum('en_attente','notifiee','expiree') DEFAULT 'en_attente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `availability_alerts`
--

CREATE TABLE `availability_alerts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `place_id` int(11) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `notified` tinyint(1) DEFAULT 0,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `factures`
--

CREATE TABLE `factures` (
  `id` int(11) NOT NULL,
  `paiement_id` int(11) NOT NULL,
  `numero_facture` varchar(20) NOT NULL,
  `chemin_pdf` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `factures`
--

INSERT INTO `factures` (`id`, `paiement_id`, `numero_facture`, `chemin_pdf`, `created_at`) VALUES
(1, 1, '20250518-0001', 'factures/facture_20250518-0001.pdf', '2025-05-18 20:50:31'),
(2, 2, '20250518-0002', 'factures/facture_20250518-0002.pdf', '2025-05-18 20:50:31'),
(3, 3, '20250518-0003', 'factures/facture_20250518-0003.pdf', '2025-05-18 20:50:31'),
(4, 4, '20250518-0004', 'factures/facture_20250518-0004.pdf', '2025-05-18 20:50:31'),
(5, 5, '20250518-0005', 'factures/facture_20250518-0005.pdf', '2025-05-18 20:50:31'),
(6, 6, '20250518-0006', 'factures/facture_20250518-0006.pdf', '2025-05-18 20:50:31'),
(7, 7, '20250518-0007', 'factures/facture_20250518-0007.pdf', '2025-05-18 20:50:31'),
(8, 8, '20250518-0008', 'factures/facture_20250518-0008.pdf', '2025-05-18 20:50:31'),
(9, 9, '20250518-0009', 'factures/facture_20250518-0009.pdf', '2025-05-18 20:50:31'),
(10, 10, '20250518-0010', 'factures/facture_20250518-0010.pdf', '2025-05-18 20:50:31'),
(11, 23, '20250613-0001', 'factures/facture_20250613-0001.pdf', '2025-06-13 13:39:04'),
(12, 24, '20250613-0002', 'factures/facture_20250613-0002.pdf', '2025-06-13 20:04:14'),
(13, 25, '20250614-0001', 'factures/facture_20250614-0001.pdf', '2025-06-13 22:38:55'),
(14, 26, '20250614-0002', 'factures/facture_20250614-0002.pdf', '2025-06-14 10:29:44'),
(15, 27, '20250614-0003', 'factures/facture_20250614-0003.pdf', '2025-06-14 11:26:14'),
(16, 28, '20250614-0004', 'factures/facture_20250614-0004.pdf', '2025-06-14 14:47:18'),
(17, 29, '20250614-0005', 'factures/facture_20250614-0005.pdf', '2025-06-14 18:51:47'),
(18, 30, '20250614-0006', 'factures/facture_20250614-0006.pdf', '2025-06-14 19:37:25'),
(19, 31, '20250614-0007', 'factures/facture_20250614-0007.pdf', '2025-06-14 19:37:55'),
(20, 32, '20250614-0008', 'factures/facture_20250614-0008.pdf', '2025-06-14 19:49:48'),
(21, 33, '20250615-0001', 'factures/facture_20250615-0001.pdf', '2025-06-15 10:21:24'),
(22, 34, '20250615-0002', 'factures/facture_20250615-0002.pdf', '2025-06-15 10:32:00'),
(23, 35, '20250615-0003', 'factures/facture_20250615-0003.pdf', '2025-06-15 12:54:50'),
(24, 36, '20250615-0004', 'factures/facture_20250615-0004.pdf', '2025-06-15 12:57:57'),
(25, 37, '20250615-0005', 'factures/facture_20250615-0005.pdf', '2025-06-15 13:09:53'),
(26, 38, '20250615-0006', 'factures/facture_20250615-0006.pdf', '2025-06-15 13:10:31'),
(27, 39, '20250615-0007', 'factures/facture_20250615-0007.pdf', '2025-06-15 13:12:15'),
(28, 40, '20250615-0008', 'factures/facture_20250615-0008.pdf', '2025-06-15 13:22:18'),
(29, 41, '20250615-0009', 'factures/facture_20250615-0009.pdf', '2025-06-15 13:24:11'),
(30, 42, '20250615-0010', 'factures/facture_20250615-0010.pdf', '2025-06-15 13:34:31'),
(31, 43, '20250615-0011', 'factures/facture_20250615-0011.pdf', '2025-06-15 15:16:13'),
(32, 44, '20250615-0012', 'factures/facture_20250615-0012.pdf', '2025-06-15 15:34:17'),
(33, 45, '20250615-0013', 'factures/facture_20250615-0013.pdf', '2025-06-15 19:24:07'),
(34, 46, '20250615-0014', 'factures/facture_20250615-0014.pdf', '2025-06-15 21:32:26'),
(35, 47, '20250615-0015', 'factures/facture_20250615-0015.pdf', '2025-06-15 21:47:18'),
(36, 48, '20250616-0001', 'factures/facture_20250616-0001.pdf', '2025-06-15 22:41:03'),
(37, 49, '20250616-0002', 'factures/facture_20250616-0002.pdf', '2025-06-15 22:44:36'),
(38, 50, '20250616-0003', 'factures/facture_20250616-0003.pdf', '2025-06-15 23:42:18'),
(39, 51, '20250616-0004', 'factures/facture_20250616-0004.pdf', '2025-06-16 12:15:26'),
(40, 52, '20250616-0005', 'factures/facture_20250616-0005.pdf', '2025-06-16 12:16:05'),
(41, 53, '20250616-0006', 'factures/facture_20250616-0006.pdf', '2025-06-16 12:52:29'),
(42, 54, '20250616-0007', 'factures/facture_20250616-0007.pdf', '2025-06-16 12:55:00'),
(43, 55, '20250616-0008', 'factures/facture_20250616-0008.pdf', '2025-06-16 12:57:41'),
(44, 56, '20250616-0009', 'factures/facture_20250616-0009.pdf', '2025-06-16 13:24:10'),
(45, 57, '20250616-0010', 'factures/facture_20250616-0010.pdf', '2025-06-16 13:25:44'),
(46, 58, '20250616-0011', 'factures/facture_20250616-0011.pdf', '2025-06-16 13:34:25'),
(47, 59, '20250616-0012', 'factures/facture_20250616-0012.pdf', '2025-06-16 13:54:55'),
(48, 60, '20250616-0013', 'factures/facture_20250616-0013.pdf', '2025-06-16 15:12:18'),
(49, 61, '20250616-0014', 'factures/facture_20250616-0014.pdf', '2025-06-16 16:30:54'),
(50, 62, '20250616-0015', 'factures/facture_20250616-0015.pdf', '2025-06-16 16:37:36'),
(51, 63, '20250616-0016', 'factures/facture_20250616-0016.pdf', '2025-06-16 16:48:43'),
(52, 64, '20250616-0017', 'factures/facture_20250616-0017.pdf', '2025-06-16 16:52:02'),
(53, 65, '20250616-0018', 'factures/facture_20250616-0018.pdf', '2025-06-16 16:59:22'),
(54, 66, '20250616-0019', 'factures/facture_20250616-0019.pdf', '2025-06-16 17:01:34'),
(55, 67, '20250616-0020', 'factures/facture_20250616-0020.pdf', '2025-06-16 17:05:14'),
(56, 68, '20250616-0021', 'factures/facture_20250616-0021.pdf', '2025-06-16 17:08:36'),
(57, 69, '20250616-0022', 'factures/facture_20250616-0022.pdf', '2025-06-16 17:37:08'),
(58, 70, '20250616-0023', 'factures/facture_20250616-0023.pdf', '2025-06-16 17:49:14'),
(59, 71, '20250616-0024', 'factures/facture_20250616-0024.pdf', '2025-06-16 18:24:13'),
(60, 72, '20250616-0025', 'factures/facture_20250616-0025.pdf', '2025-06-16 18:25:23'),
(61, 73, '20250616-0026', 'factures/facture_20250616-0026.pdf', '2025-06-16 19:58:47'),
(62, 74, '20250616-0027', 'factures/facture_20250616-0027.pdf', '2025-06-16 20:41:39'),
(63, 75, '20250617-0001', 'factures/facture_20250617-0001.pdf', '2025-06-17 08:42:26'),
(64, 76, '20250617-0002', 'factures/facture_20250617-0002.pdf', '2025-06-17 10:09:02'),
(65, 77, '20250617-0003', 'factures/facture_20250617-0003.pdf', '2025-06-17 10:09:20'),
(66, 78, '20250617-0004', 'factures/facture_20250617-0004.pdf', '2025-06-17 14:17:04'),
(67, 79, '20250617-0005', 'factures/facture_20250617-0005.pdf', '2025-06-17 14:17:29'),
(68, 80, '20250617-0006', 'factures/facture_20250617-0006.pdf', '2025-06-17 16:50:40'),
(69, 81, '20250618-0001', 'factures/facture_20250618-0001.pdf', '2025-06-18 11:01:50'),
(70, 82, '20250618-0002', 'factures/facture_20250618-0002.pdf', '2025-06-18 11:15:29'),
(71, 83, '20250618-0003', 'factures/facture_20250618-0003.pdf', '2025-06-18 12:26:17'),
(72, 84, '20250618-0004', 'factures/facture_20250618-0004.pdf', '2025-06-18 18:34:49'),
(73, 85, '20250618-0005', 'factures/facture_20250618-0005.pdf', '2025-06-18 20:55:43'),
(74, 86, '20250618-0006', 'factures/facture_20250618-0006.pdf', '2025-06-18 20:57:19'),
(75, 87, '20250618-0007', 'factures/facture_20250618-0007.pdf', '2025-06-18 21:03:18'),
(76, 88, '20250619-0001', 'factures/facture_20250619-0001.pdf', '2025-06-19 11:07:20'),
(77, 89, '20250619-0002', 'factures/facture_20250619-0002.pdf', '2025-06-19 11:41:03'),
(78, 90, '20250619-0003', 'factures/facture_20250619-0003.pdf', '2025-06-19 12:16:49'),
(79, 91, '20250619-0004', 'factures/facture_20250619-0004.pdf', '2025-06-19 15:47:46'),
(80, 92, '20250619-0005', 'factures/facture_20250619-0005.pdf', '2025-06-19 15:48:21'),
(81, 93, '20250619-0006', 'factures/facture_20250619-0006.pdf', '2025-06-19 18:32:54'),
(82, 93, '20250619-0007', 'factures/facture_20250619-0007.pdf', '2025-06-19 18:32:54'),
(83, 94, '20250619-0008', 'factures/facture_20250619-0008.pdf', '2025-06-19 18:37:42'),
(84, 94, '20250619-0009', 'factures/facture_20250619-0009.pdf', '2025-06-19 18:37:42'),
(85, 95, '20250620-0001', 'factures/facture_20250620-0001.pdf', '2025-06-19 23:51:15'),
(86, 95, '20250620-0002', 'factures/facture_20250620-0002.pdf', '2025-06-19 23:51:15'),
(87, 96, '20250620-0003', 'factures/facture_20250620-0003.pdf', '2025-06-20 00:08:32'),
(88, 96, '20250620-0004', 'factures/facture_20250620-0004.pdf', '2025-06-20 00:08:32'),
(89, 97, '20250620-0005', 'factures/facture_20250620-0005.pdf', '2025-06-20 01:36:40'),
(90, 97, '20250620-0006', 'factures/facture_20250620-0006.pdf', '2025-06-20 01:36:40'),
(91, 98, '20250620-0007', 'factures/facture_20250620-0007.pdf', '2025-06-20 01:46:36'),
(92, 98, '20250620-0008', 'factures/facture_20250620-0008.pdf', '2025-06-20 01:46:36'),
(93, 99, '20250620-0009', 'factures/facture_20250620-0009.pdf', '2025-06-20 01:52:04'),
(94, 99, '20250620-0010', 'factures/facture_20250620-0010.pdf', '2025-06-20 01:52:04'),
(95, 100, '20250620-0011', 'factures/facture_20250620-0011.pdf', '2025-06-20 01:54:56'),
(96, 100, '20250620-0012', 'factures/facture_20250620-0012.pdf', '2025-06-20 01:54:56'),
(97, 101, '20250621-0001', 'factures/facture_20250621-0001.pdf', '2025-06-21 16:34:18'),
(98, 101, '20250621-0002', 'factures/facture_20250621-0002.pdf', '2025-06-21 16:34:18');

-- --------------------------------------------------------

--
-- Structure de la table `horaires_ouverture`
--

CREATE TABLE `horaires_ouverture` (
  `id` int(11) NOT NULL,
  `jour_semaine` int(11) NOT NULL,
  `heure_ouverture` time NOT NULL,
  `heure_fermeture` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `horaires_ouverture`
--

INSERT INTO `horaires_ouverture` (`id`, `jour_semaine`, `heure_ouverture`, `heure_fermeture`) VALUES
(1, 1, '08:00:00', '20:00:00'),
(2, 2, '08:00:00', '20:00:00'),
(3, 3, '08:00:00', '20:00:00'),
(4, 4, '08:00:00', '20:00:00'),
(5, 5, '08:00:00', '22:00:00'),
(6, 6, '09:00:00', '22:00:00'),
(7, 7, '09:00:00', '20:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `action`, `description`, `created_at`) VALUES
(1, 7, 'connexion', 'Connexion réussie', '2025-06-13 10:38:36'),
(2, 7, 'déconnexion', 'Déconnexion réussie', '2025-06-13 10:52:37'),
(3, 7, 'connexion', 'Connexion réussie', '2025-06-13 11:38:59'),
(4, 7, 'reservation_creation', 'Réservation #28 créée', '2025-06-13 13:38:11'),
(5, 7, 'paiement', 'Paiement #23 effectué pour la réservation #28', '2025-06-13 13:39:04'),
(6, 7, 'admin_toggle_user_status', 'Statut de l\'utilisateur #2 changé en inactif', '2025-06-13 14:51:42'),
(7, 7, 'admin_toggle_user_status', 'Statut de l\'utilisateur #4 changé en inactif', '2025-06-13 14:51:59'),
(8, 7, 'suppression_utilisateur', 'Suppression de l\'utilisateur #1 par un administrateur', '2025-06-13 14:59:00'),
(9, 7, 'admin_create_place', 'Place #9 de type standard créée', '2025-06-13 15:11:35'),
(10, 7, 'admin_create_place', 'Place #921 de type handicape créée', '2025-06-13 15:12:04'),
(11, 7, 'déconnexion', 'Déconnexion réussie', '2025-06-13 18:07:07'),
(12, 7, 'connexion', 'Connexion réussie', '2025-06-13 18:08:04'),
(14, 7, 'admin_edit_place', 'Modification place #36: type: standard → handicape', '2025-06-13 18:16:49'),
(15, 7, 'admin_edit_place', 'Modification place #1: type: handicape → standard', '2025-06-13 18:16:58'),
(16, 7, 'admin_edit_place', 'Modification place #37: type: handicape → standard', '2025-06-13 18:17:20'),
(17, 7, 'admin_edit_place', 'Modification place #36: type: handicape → standard', '2025-06-13 19:03:42'),
(18, 7, 'admin_edit_place', 'Modification place #36: type: standard → electrique', '2025-06-13 19:03:48'),
(19, 7, 'admin_delete_place', 'Suppression de la place #36 (Numéro: 9, Type: electrique) avec suppression des réservations associées', '2025-06-13 19:09:49'),
(20, 7, 'admin_edit_place', 'Modification place #37: type: standard → handicape', '2025-06-13 19:09:58'),
(21, 7, 'admin_edit_place', 'Modification place #37: statut: libre → occupe', '2025-06-13 19:10:04'),
(22, 7, 'admin_edit_place', 'Modification place #37: statut: occupe → maintenance', '2025-06-13 19:10:07'),
(23, 7, 'admin_edit_place', 'Modification place #37: statut: maintenance → libre', '2025-06-13 19:10:13'),
(24, 7, 'admin_edit_user', 'Modification de l\'utilisateur #9 (guest@parkme.in)', '2025-06-13 19:11:56'),
(25, 7, 'admin_edit_user', 'Modification de l\'utilisateur #9 (guest@parkme.in)', '2025-06-13 19:12:03'),
(26, 7, 'admin_edit_user', 'Modification de l\'utilisateur #9 (guest@parkme.in)', '2025-06-13 19:12:10'),
(27, 7, 'admin_edit_user_status', 'Le compte utilisateur #7 (sasa@gmail.com) a été désactivé.', '2025-06-13 19:50:31'),
(28, 7, 'admin_edit_user_status', 'Le compte utilisateur #7 (sasa@gmail.com) a été activé.', '2025-06-13 19:50:34'),
(29, 7, 'annulation_reservation', 'Annulation de la réservation #1 par un administrateur', '2025-06-13 19:51:51'),
(30, 7, 'annulation_reservation', 'Annulation de la réservation #28 par un administrateur', '2025-06-13 20:02:55'),
(31, 7, 'reservation_creation', 'Réservation #31 créée', '2025-06-13 20:03:19'),
(32, 7, 'paiement', 'Paiement #24 effectué pour la réservation #31', '2025-06-13 20:04:14'),
(33, 7, 'annulation', 'Réservation #31 annulée', '2025-06-13 20:37:35'),
(34, 7, 'admin_edit_user_status', 'Le compte utilisateur #9 (guest@parkme.in) a été activé.', '2025-06-13 22:28:17'),
(35, 7, 'reservation_creation', 'Réservation #32 créée', '2025-06-13 22:37:25'),
(36, 7, 'paiement', 'Paiement #25 effectué pour la réservation #32', '2025-06-13 22:38:55'),
(37, 7, 'reservation_creation', 'Réservation #33 créée', '2025-06-13 22:39:20'),
(38, 7, 'annulation', 'Réservation #33 annulée', '2025-06-13 22:51:11'),
(40, 10, 'inscription', 'Inscription réussie', '2025-06-14 10:25:39'),
(41, 10, 'connexion', 'Connexion réussie', '2025-06-14 10:25:55'),
(42, 9, 'guest_reservation_creation', 'Réservation invité #35 créée par labidi.neeth@gmail.com', '2025-06-14 10:29:04'),
(43, 7, 'guest_payment', 'Paiement #26 effectué pour la réservation #35 par invité labidi.neeth@gmail.com', '2025-06-14 10:29:44'),
(44, 10, 'reservation_creation', 'Réservation #36 créée', '2025-06-14 11:25:35'),
(45, 10, 'paiement', 'Paiement #27 effectué pour la réservation #36', '2025-06-14 11:26:14'),
(46, 10, 'déconnexion', 'Déconnexion réussie', '2025-06-14 11:26:25'),
(47, 7, 'connexion', 'Connexion réussie', '2025-06-14 11:26:45'),
(48, 7, 'annulation_reservation', 'Annulation de la réservation #32 par un administrateur', '2025-06-14 11:28:22'),
(49, 7, 'annulation_reservation', 'Annulation de la réservation #34 par un administrateur', '2025-06-14 11:28:34'),
(50, 7, 'annulation_reservation', 'Annulation de la réservation #35 par un administrateur', '2025-06-14 11:28:37'),
(51, 7, 'annulation_reservation', 'Annulation de la réservation #36 par un administrateur', '2025-06-14 11:28:39'),
(52, 7, 'reservation_creation', 'Réservation #37 créée', '2025-06-14 14:47:01'),
(53, 7, 'paiement', 'Paiement #28 effectué pour la réservation #37', '2025-06-14 14:47:18'),
(54, 7, 'annulation_reservation', 'Annulation de la réservation #37 par un administrateur', '2025-06-14 14:49:13'),
(55, 7, 'reservation_creation', 'Réservation #38 créée', '2025-06-14 18:51:24'),
(56, 7, 'paiement', 'Paiement #29 effectué pour la réservation #38', '2025-06-14 18:51:47'),
(57, 7, 'reservation_creation', 'Réservation #39 créée', '2025-06-14 18:59:04'),
(58, 7, 'reservation_creation', 'Réservation #40 créée', '2025-06-14 18:59:14'),
(59, 7, 'annulation', 'Réservation #39 annulée', '2025-06-14 18:59:46'),
(60, 7, 'annulation', 'Réservation #40 annulée', '2025-06-14 18:59:51'),
(61, 7, 'reservation_creation', 'Réservation #41 créée', '2025-06-14 19:37:02'),
(62, 7, 'paiement', 'Paiement #30 effectué pour la réservation #41', '2025-06-14 19:37:25'),
(63, 7, 'reservation_creation', 'Réservation #42 créée', '2025-06-14 19:37:37'),
(64, 7, 'paiement', 'Paiement #31 effectué pour la réservation #42', '2025-06-14 19:37:55'),
(65, 7, 'annulation', 'Réservation #42 annulée', '2025-06-14 19:38:07'),
(66, 7, 'annulation', 'Réservation #41 annulée', '2025-06-14 19:38:11'),
(67, 7, 'reservation_creation', 'Réservation #43 créée', '2025-06-14 19:49:07'),
(68, 7, 'paiement', 'Paiement #32 effectué pour la réservation #43', '2025-06-14 19:49:48'),
(69, 7, 'admin_edit_user_status', 'Le compte utilisateur #10 (labidi.neeth@gmail.com) a été désactivé.', '2025-06-14 19:50:55'),
(70, 7, 'admin_edit_user_status', 'Le compte utilisateur #10 (labidi.neeth@gmail.com) a été activé.', '2025-06-14 19:51:02'),
(71, 9, 'guest_reservation_creation', 'Réservation invité #44 créée par labidi.neeth@gmail.com', '2025-06-15 10:20:56'),
(72, 7, 'guest_payment', 'Paiement #33 effectué pour la réservation #44 par invité labidi.neeth@gmail.com', '2025-06-15 10:21:24'),
(73, 7, 'connexion', 'Connexion réussie', '2025-06-15 10:24:01'),
(74, 7, 'déconnexion', 'Déconnexion réussie', '2025-06-15 10:26:42'),
(75, 7, 'connexion', 'Connexion réussie', '2025-06-15 10:30:13'),
(76, 7, 'déconnexion', 'Déconnexion réussie', '2025-06-15 10:31:33'),
(77, 9, 'guest_reservation_creation', 'Réservation invité #45 créée par labidi.neeth@gmail.com', '2025-06-15 10:31:55'),
(78, 7, 'guest_payment', 'Paiement #34 effectué pour la réservation #45 par invité labidi.neeth@gmail.com', '2025-06-15 10:32:00'),
(79, 11, 'inscription', 'Inscription réussie', '2025-06-15 10:33:00'),
(80, 11, 'connexion', 'Connexion réussie', '2025-06-15 10:33:26'),
(81, 11, 'déconnexion', 'Déconnexion réussie', '2025-06-15 10:34:08'),
(82, 7, 'connexion', 'Connexion réussie', '2025-06-15 10:34:18'),
(83, 7, 'suppression_utilisateur', 'Suppression de l\'utilisateur #11 par un administrateur', '2025-06-15 10:51:56'),
(84, 7, 'suppression_utilisateur', 'Suppression de l\'utilisateur #11 par un administrateur', '2025-06-15 10:52:04'),
(85, 7, 'force_suppression_utilisateur', 'Suppression forcée de l\'utilisateur #11 par un administrateur', '2025-06-15 11:08:09'),
(86, 7, 'admin_edit_user_status', 'Le compte utilisateur #3 (marie.martin@example.com) a été désactivé.', '2025-06-15 11:08:19'),
(87, 7, 'admin_edit_user_status', 'Le compte utilisateur #3 (marie.martin@example.com) a été activé.', '2025-06-15 11:08:32'),
(88, 7, 'cron_update_reservations', 'Mise à jour auto des réservations: 0 annulée(s), 0 en cours, 7 terminée(s), 2 place(s) libérée(s)', '2025-06-15 11:25:50'),
(89, 7, 'cron_update_reservations', 'Mise à jour auto des réservations: 4 annulée(s), 0 en cours, 0 terminée(s), 0 place(s) libérée(s)', '2025-06-15 11:26:30'),
(90, 7, 'cron_update_reservations', 'Mise à jour auto des réservations: 0 annulée(s), 0 en cours, 0 terminée(s), 0 place(s) libérée(s)', '2025-06-15 11:27:46'),
(91, 7, 'admin_update_reservations', 'Mise à jour des statuts de réservation: 0 vers \'en cours\', 0 terminée(s), 0 place(s) libérée(s)', '2025-06-15 11:28:08'),
(92, 7, 'admin_update_reservations', 'Mise à jour des statuts de réservation: 1 vers \'en cours\', 0 terminée(s), 0 place(s) libérée(s)', '2025-06-15 12:33:51'),
(93, 7, 'annulation_reservation', 'Annulation de la réservation #46 par un administrateur', '2025-06-15 12:33:55'),
(94, 7, 'annulation_reservation', 'Annulation de la réservation #45 par un administrateur', '2025-06-15 12:33:58'),
(95, 7, 'annulation_reservation', 'Annulation de la réservation #44 par un administrateur', '2025-06-15 12:34:00'),
(96, 7, 'annulation_reservation', 'Annulation de la réservation #43 par un administrateur', '2025-06-15 12:34:02'),
(97, 7, 'annulation_reservation', 'Annulation de la réservation #38 par un administrateur', '2025-06-15 12:34:05'),
(98, 7, 'admin_update_reservations', 'Mise à jour des statuts de réservation: 0 vers \'en cours\', 0 terminée(s), 0 place(s) libérée(s)', '2025-06-15 12:34:08'),
(99, 7, 'annulation_reservation', 'Annulation de la réservation #47 par un administrateur', '2025-06-15 12:35:33'),
(100, 7, 'annulation_reservation', 'Annulation de la réservation #48 par un administrateur', '2025-06-15 12:40:58'),
(101, 7, 'paiement', 'Paiement #35 effectué pour la réservation #49', '2025-06-15 12:54:50'),
(102, 7, 'paiement', 'Paiement #36 effectué pour la réservation #50', '2025-06-15 12:57:57'),
(103, 7, 'paiement', 'Paiement #37 effectué pour la réservation #51', '2025-06-15 13:09:53'),
(104, 7, 'paiement', 'Paiement #38 effectué pour la réservation #52', '2025-06-15 13:10:31'),
(105, 7, 'paiement', 'Paiement #39 effectué pour la réservation #53', '2025-06-15 13:12:15'),
(106, 7, 'paiement', 'Paiement #40 effectué pour la réservation #54', '2025-06-15 13:22:18'),
(107, 7, 'paiement', 'Paiement #41 effectué pour la réservation #55', '2025-06-15 13:24:11'),
(108, 7, 'admin_update_reservations', 'Mise à jour des statuts de réservation: 7 vers \'en cours\', 7 terminée(s), 0 place(s) libérée(s)', '2025-06-15 13:24:55'),
(109, 7, 'paiement', 'Paiement #42 effectué pour la réservation #56', '2025-06-15 13:34:31'),
(110, 7, 'reservation_creation', 'Réservation #60 créée', '2025-06-15 15:16:09'),
(111, 7, 'paiement', 'Paiement #43 effectué pour la réservation #60', '2025-06-15 15:16:13'),
(112, 7, 'annulation', 'Réservation #60 annulée', '2025-06-15 15:18:58'),
(113, 7, 'paiement', 'Paiement #44 effectué pour la réservation #61', '2025-06-15 15:34:17'),
(114, 7, 'admin_update_reservations', 'Mise à jour des statuts de réservation: 2 vers \'en cours\', 2 terminée(s), 0 place(s) libérée(s)', '2025-06-15 17:09:28'),
(115, 7, 'admin_edit_user', 'Modification de l\'utilisateur #10 (labidi.neeth@gmail.com)', '2025-06-15 17:10:03'),
(116, 7, 'admin_edit_user', 'Modification de l\'utilisateur #10 (labidi.neeth@gmail.com)', '2025-06-15 17:10:12'),
(117, 7, 'admin_edit_place', 'Modification place #37: statut: libre → maintenance', '2025-06-15 17:10:28'),
(118, 7, 'admin_edit_place', 'Modification place #37: statut: maintenance → occupe', '2025-06-15 17:10:34'),
(119, 7, 'admin_edit_place', 'Modification place #37: statut: occupe → libre', '2025-06-15 17:10:39'),
(120, 7, 'reservation_creation', 'Réservation #62 créée', '2025-06-15 19:23:52'),
(121, 7, 'paiement', 'Paiement #45 effectué pour la réservation #62', '2025-06-15 19:24:07'),
(122, 7, 'annulation', 'Réservation #62 annulée', '2025-06-15 19:24:21'),
(123, 7, 'admin_update_reservations', 'Mise à jour des statuts de réservation: 0 vers \'en cours\', 0 terminée(s), 0 place(s) libérée(s)', '2025-06-15 20:57:57'),
(124, 7, 'reservation_creation', 'Réservation #65 créée', '2025-06-15 21:32:21'),
(125, 7, 'paiement', 'Paiement #46 effectué pour la réservation #65', '2025-06-15 21:32:26'),
(126, 7, 'paiement', 'Paiement #47 effectué pour la réservation #66', '2025-06-15 21:47:18'),
(127, 7, 'paiement', 'Paiement #48 effectué pour la réservation #68', '2025-06-15 22:41:03'),
(128, 7, 'admin_update_reservations', 'Mise à jour des statuts de réservation: 3 vers \'en cours\', 2 terminée(s), 0 place(s) libérée(s)', '2025-06-15 22:41:14'),
(129, 7, 'paiement', 'Paiement #49 effectué pour la réservation #70', '2025-06-15 22:44:36'),
(130, 7, 'reservation_creation', 'Réservation #72 créée', '2025-06-15 23:42:15'),
(131, 7, 'paiement', 'Paiement #50 effectué pour la réservation #72', '2025-06-15 23:42:18'),
(132, 7, 'annulation_reservation', 'Annulation de la réservation #70 par un administrateur', '2025-06-16 12:09:42'),
(133, 7, 'annulation_reservation', 'Annulation de la réservation #72 par un administrateur', '2025-06-16 12:09:44'),
(134, 7, 'annulation_reservation', 'Annulation de la réservation #71 par un administrateur', '2025-06-16 12:09:45'),
(135, 7, 'annulation_reservation', 'Annulation de la réservation #69 par un administrateur', '2025-06-16 12:09:46'),
(136, 7, 'annulation_reservation', 'Annulation de la réservation #68 par un administrateur', '2025-06-16 12:09:46'),
(137, 7, 'annulation_reservation', 'Annulation de la réservation #67 par un administrateur', '2025-06-16 12:09:48'),
(138, 7, 'annulation_reservation', 'Annulation de la réservation #66 par un administrateur', '2025-06-16 12:09:50'),
(139, 7, 'annulation_reservation', 'Annulation de la réservation #65 par un administrateur', '2025-06-16 12:09:51'),
(140, 7, 'annulation_reservation', 'Annulation de la réservation #64 par un administrateur', '2025-06-16 12:09:53'),
(141, 7, 'annulation_reservation', 'Annulation de la réservation #63 par un administrateur', '2025-06-16 12:09:55'),
(142, 7, 'annulation_reservation', 'Annulation de la réservation #61 par un administrateur', '2025-06-16 12:09:58'),
(143, 7, 'paiement', 'Paiement #51 effectué pour la réservation #73', '2025-06-16 12:15:26'),
(144, 7, 'paiement', 'Paiement #52 effectué pour la réservation #74', '2025-06-16 12:16:05'),
(145, 7, 'admin_update_reservations', 'Mise à jour des statuts de réservation: 2 vers \'en cours\', 2 terminée(s), 0 place(s) libérée(s)', '2025-06-16 12:16:20'),
(146, 7, 'paiement', 'Paiement #53 effectué pour la réservation #75', '2025-06-16 12:52:29'),
(147, 7, 'paiement', 'Paiement #54 effectué pour la réservation #76', '2025-06-16 12:55:00'),
(148, 7, 'admin_update_reservations', 'Mise à jour des statuts de réservation: 2 vers \'en cours\', 2 terminée(s), 0 place(s) libérée(s)', '2025-06-16 12:55:22'),
(149, 7, 'paiement', 'Paiement #55 effectué pour la réservation #77', '2025-06-16 12:57:41'),
(150, 7, 'paiement', 'Paiement #56 effectué pour la réservation #78', '2025-06-16 13:24:10'),
(151, 7, 'admin_update_reservations', 'Mise à jour des statuts de réservation: 2 vers \'en cours\', 2 terminée(s), 0 place(s) libérée(s)', '2025-06-16 13:24:30'),
(152, 7, 'paiement', 'Paiement #57 effectué pour la réservation #80', '2025-06-16 13:25:44'),
(153, 7, 'paiement', 'Paiement #58 effectué pour la réservation #81', '2025-06-16 13:34:25'),
(154, 7, 'paiement', 'Paiement #59 effectué pour la réservation #82', '2025-06-16 13:54:55'),
(155, 7, 'admin_update_reservations', 'Mise à jour des statuts de réservation: 3 vers \'en cours\', 3 terminée(s), 0 place(s) libérée(s)', '2025-06-16 14:08:47'),
(156, 7, 'paiement', 'Paiement #60 effectué pour la réservation #83', '2025-06-16 15:12:18'),
(157, 7, 'admin_update_reservations', 'Mise à jour des statuts de réservation: 1 vers \'en cours\', 1 terminée(s), 0 place(s) libérée(s)', '2025-06-16 15:12:30'),
(158, 7, 'paiement', 'Paiement #61 effectué pour la réservation #90', '2025-06-16 16:30:54'),
(159, 7, 'paiement', 'Paiement #62 effectué pour la réservation #91', '2025-06-16 16:37:36'),
(160, 7, 'paiement', 'Paiement #63 effectué pour la réservation #92', '2025-06-16 16:48:43'),
(161, 7, 'admin_update_reservations', 'Mise à jour des statuts de réservation: 3 vers \'en cours\', 3 terminée(s), 0 place(s) libérée(s)', '2025-06-16 16:50:09'),
(162, 7, 'paiement', 'Paiement #64 effectué pour la réservation #93', '2025-06-16 16:52:02'),
(163, 7, 'reservation_creation', 'Réservation #94 créée', '2025-06-16 16:59:19'),
(164, 7, 'paiement', 'Paiement #65 effectué pour la réservation #94', '2025-06-16 16:59:22'),
(165, 7, 'reservation_creation', 'Réservation #95 créée', '2025-06-16 17:01:15'),
(166, 7, 'reservation_creation', 'Réservation #96 créée', '2025-06-16 17:01:31'),
(167, 7, 'paiement', 'Paiement #66 effectué pour la réservation #96', '2025-06-16 17:01:34'),
(168, 7, 'annulation', 'Réservation #94 annulée', '2025-06-16 17:04:13'),
(169, 7, 'annulation', 'Réservation #95 annulée', '2025-06-16 17:04:23'),
(170, 7, 'paiement', 'Paiement #67 effectué pour la réservation #97', '2025-06-16 17:05:14'),
(171, 7, 'annulation_reservation', 'Annulation de la réservation #97 par un administrateur', '2025-06-16 17:05:37'),
(172, 7, 'admin_update_reservations', 'Mise à jour des statuts de réservation: 2 vers \'en cours\', 1 terminée(s), 0 place(s) libérée(s)', '2025-06-16 17:05:39'),
(173, 7, 'reservation_creation', 'Réservation #98 créée', '2025-06-16 17:08:33'),
(174, 7, 'paiement', 'Paiement #68 effectué pour la réservation #98', '2025-06-16 17:08:36'),
(175, 7, 'annulation', 'Réservation #98 annulée', '2025-06-16 17:08:50'),
(176, 7, 'reservation_creation', 'Réservation #99 créée', '2025-06-16 17:37:05'),
(177, 7, 'paiement', 'Paiement #69 effectué pour la réservation #99', '2025-06-16 17:37:08'),
(178, 7, 'annulation', 'Réservation #99 annulée - Place 2, Dates: 16/06/2025 19:38 - 16/06/2025 20:08', '2025-06-16 17:37:27'),
(179, 7, 'admin_create_place', 'Place #301 de type standard créée', '2025-06-16 17:39:01'),
(180, 7, 'admin_create_place', 'Place #302 de type standard créée', '2025-06-16 17:39:01'),
(181, 7, 'admin_create_place', 'Place #303 de type standard créée', '2025-06-16 17:39:01'),
(182, 7, 'admin_create_place', 'Place #304 de type standard créée', '2025-06-16 17:39:01'),
(183, 7, 'admin_create_place', 'Place #305 de type standard créée', '2025-06-16 17:39:01'),
(184, 7, 'admin_update_reservations', 'Mise à jour des statuts de réservation: 0 vers \'en cours\', 1 terminée(s), 0 place(s) libérée(s)', '2025-06-16 17:41:04'),
(185, 7, 'admin_update_reservations', 'Mise à jour des statuts de réservation: 0 vers \'en cours\', 0 terminée(s), 0 place(s) libérée(s)', '2025-06-16 17:41:44'),
(186, 7, 'reservation_creation', 'Réservation #101 créée', '2025-06-16 17:49:12'),
(187, 7, 'paiement', 'Paiement #70 effectué pour la réservation #101', '2025-06-16 17:49:14'),
(188, 7, 'annulation', 'Réservation #101 annulée - Place 2, Dates: 16/06/2025 19:51 - 16/06/2025 20:21', '2025-06-16 17:49:30'),
(189, 7, 'admin_edit_place', 'Modification place #35: statut: libre → occupe', '2025-06-16 18:16:32'),
(190, 7, 'admin_edit_place', 'Modification place #35: statut: occupe → libre', '2025-06-16 18:16:38'),
(191, 7, 'admin_edit_place', 'Modification place #21: type: handicape → standard', '2025-06-16 18:16:56'),
(192, 7, 'admin_edit_place', 'Modification place #39: type: standard → handicape', '2025-06-16 18:17:23'),
(193, 7, 'admin_edit_place', 'Modification place #38: type: standard → handicape', '2025-06-16 18:17:30'),
(194, 7, 'déconnexion', 'Déconnexion réussie', '2025-06-16 18:23:06'),
(195, 12, 'inscription', 'Inscription réussie', '2025-06-16 18:23:45'),
(196, 12, 'connexion', 'Connexion réussie', '2025-06-16 18:24:04'),
(197, 12, 'reservation_creation', 'Réservation #102 créée', '2025-06-16 18:24:10'),
(198, 12, 'paiement', 'Paiement #71 effectué pour la réservation #102', '2025-06-16 18:24:14'),
(199, 12, 'paiement', 'Paiement #72 effectué pour la réservation #104', '2025-06-16 18:25:23'),
(200, 12, 'déconnexion', 'Déconnexion réussie', '2025-06-16 18:35:10'),
(201, 7, 'connexion', 'Connexion réussie', '2025-06-16 18:35:20'),
(202, 7, 'reservation_creation', 'Réservation #105 créée', '2025-06-16 19:58:43'),
(203, 7, 'paiement', 'Paiement #73 effectué pour la réservation #105', '2025-06-16 19:58:47'),
(204, 7, 'annulation', 'Réservation #105 annulée - Place 2, Dates: 16/06/2025 22:00 - 16/06/2025 22:30', '2025-06-16 19:59:23'),
(205, 7, 'reservation_immediate', 'Réservation immédiate #106 créée pour la place 2', '2025-06-16 20:36:43'),
(206, 7, 'reservation_immediate_end', 'Réservation immédiate #106 terminée après 0.16666666666667 minutes', '2025-06-16 20:36:53'),
(207, 7, 'paiement', 'Paiement #74 effectué pour la réservation #106', '2025-06-16 20:41:39'),
(208, 7, 'ajout_tarif', 'Ajout du tarif pour le type de place velo', '2025-06-16 22:24:20'),
(209, 7, 'modification_type', 'Type de place modifié de  vers velo pour le tarif #4', '2025-06-16 23:50:13'),
(210, 7, 'modification_tarif', 'Modification du tarif pour  vers velo #4', '2025-06-16 23:50:13'),
(211, 7, 'modification_type', 'Type de place modifié de  vers standard pour le tarif #4', '2025-06-17 00:02:19'),
(212, 7, 'modification_tarif', 'Modification du tarif pour  vers standard #4', '2025-06-17 00:02:19'),
(213, 7, 'modification_type', 'Type de place modifié de velo vers moto pour le tarif #5', '2025-06-17 00:13:48'),
(214, 7, 'modification_tarif', 'Modification du tarif pour velo vers moto #5', '2025-06-17 00:13:48'),
(215, 7, 'modification_tarif', 'Modification du tarif pour moto vers moto #5', '2025-06-17 00:14:07'),
(216, 7, 'modification_tarif', 'Modification du tarif pour moto vers moto #5', '2025-06-17 00:14:08'),
(217, 7, 'modification_tarif', 'Modification du tarif pour moto vers moto #5', '2025-06-17 00:15:48'),
(218, 7, 'modification_tarif', 'Modification du tarif pour velo vers velo #6', '2025-06-17 00:18:45'),
(219, 7, 'reservation_creation', 'Réservation #107 créée', '2025-06-17 08:42:24'),
(220, 7, 'paiement', 'Paiement #75 effectué pour la réservation #107', '2025-06-17 08:42:26'),
(221, 7, 'annulation', 'Réservation #107 annulée - Place 2, Dates: 17/06/2025 10:45 - 17/06/2025 11:15', '2025-06-17 08:42:38'),
(222, 7, 'reservation_immediate', 'Réservation immédiate #108 créée pour la place 2', '2025-06-17 10:08:53'),
(223, 7, 'reservation_immediate_end', 'Réservation immédiate #108 terminée après 0.083333333333333 minutes', '2025-06-17 10:08:58'),
(224, 7, 'paiement', 'Paiement #76 effectué pour la réservation #108', '2025-06-17 10:09:02'),
(225, 7, 'reservation_creation', 'Réservation #109 créée', '2025-06-17 10:09:18'),
(226, 7, 'paiement', 'Paiement #77 effectué pour la réservation #109', '2025-06-17 10:09:20'),
(227, 7, 'annulation', 'Réservation #109 annulée - Place 2, Dates: 17/06/2025 12:11 - 17/06/2025 12:41', '2025-06-17 10:09:27'),
(228, 7, 'admin_create_place', 'Place #u05 de type moto créée', '2025-06-17 10:33:32'),
(229, 7, 'admin_create_place', 'Place #9 de type moto créée', '2025-06-17 10:34:17'),
(230, 7, 'admin_create_place', 'Place #8 de type moto/scooter créée', '2025-06-17 10:38:43'),
(231, 7, 'admin_create_place', 'Place #7 de type velo créée', '2025-06-17 10:39:04'),
(232, 7, 'admin_edit_place', 'Modification place #44: type:  → electrique', '2025-06-17 10:39:13'),
(233, 7, 'admin_edit_place', 'Modification place #43: type:  → handicape', '2025-06-17 10:39:34'),
(234, 7, 'admin_delete_place', 'Suppression de la place #45 (Numéro: 8, Type: moto/scooter)', '2025-06-17 10:48:42'),
(235, 7, 'admin_edit_place', 'Modification place #46: type: velo → moto/scooter', '2025-06-17 10:48:49'),
(236, 7, 'admin_edit_place', 'Modification place #42: type: standard → velo', '2025-06-17 10:49:00'),
(237, 7, 'reservation_creation', 'Réservation #110 créée', '2025-06-17 14:17:02'),
(238, 7, 'paiement', 'Paiement #78 effectué pour la réservation #110', '2025-06-17 14:17:04'),
(239, 7, 'reservation_immediate', 'Réservation immédiate #111 créée pour la place 2', '2025-06-17 14:17:12'),
(240, 7, 'reservation_immediate_end', 'Réservation immédiate #111 terminée après 0.23333333333333 minutes', '2025-06-17 14:17:26'),
(241, 7, 'paiement', 'Paiement #79 effectué pour la réservation #111', '2025-06-17 14:17:29'),
(242, 7, 'reservation_immediate', 'Réservation immédiate #112 créée pour la place 2', '2025-06-17 16:39:04'),
(243, 7, 'reservation_immediate_end', 'Réservation immédiate #112 terminée après 11.533333333333 minutes', '2025-06-17 16:50:36'),
(244, 7, 'paiement', 'Paiement #80 effectué pour la réservation #112', '2025-06-17 16:50:40'),
(245, 7, 'admin_create_place', 'Place #123 de type moto/scooter créée', '2025-06-17 16:54:15'),
(246, 7, 'connexion', 'Connexion réussie', '2025-06-18 11:01:32'),
(247, 7, 'reservation_immediate', 'Réservation immédiate #113 créée pour la place 2', '2025-06-18 11:01:42'),
(248, 7, 'reservation_immediate_end', 'Réservation immédiate #113 terminée après 0.066666666666667 minutes', '2025-06-18 11:01:46'),
(249, 7, 'paiement', 'Paiement #81 effectué pour la réservation #113', '2025-06-18 11:01:50'),
(250, 7, 'reservation_immediate', 'Réservation immédiate #114 créée pour la place 2', '2025-06-18 11:15:17'),
(251, 7, 'reservation_immediate_end', 'Réservation immédiate #114 terminée après 0.15 minutes', '2025-06-18 11:15:26'),
(252, 7, 'paiement', 'Paiement #82 effectué pour la réservation #114', '2025-06-18 11:15:29'),
(253, 7, 'reservation_immediate', 'Réservation immédiate #115 créée pour la place 2', '2025-06-18 12:26:08'),
(254, 7, 'reservation_immediate_end', 'Réservation immédiate #115 terminée après 0.1 minutes', '2025-06-18 12:26:14'),
(255, 7, 'paiement', 'Paiement #83 effectué pour la réservation #115', '2025-06-18 12:26:17'),
(256, 7, 'paiement', 'Paiement #84 effectué pour la réservation #116', '2025-06-18 18:34:49'),
(257, 7, 'reservation_creation', 'Réservation #117 créée', '2025-06-18 20:55:33'),
(258, 7, 'paiement', 'Paiement #85 effectué pour la réservation #117', '2025-06-18 20:55:43'),
(259, 7, 'reservation_creation', 'Réservation #118 créée', '2025-06-18 20:57:16'),
(260, 7, 'paiement', 'Paiement #86 effectué pour la réservation #118', '2025-06-18 20:57:19'),
(261, 7, 'annulation', 'Réservation #118 annulée - Place 123, Dates: 19/06/2025 22:56 - 19/06/2025 23:26', '2025-06-18 21:00:58'),
(262, 7, 'paiement', 'Paiement #87 effectué pour la réservation #119', '2025-06-18 21:03:18'),
(263, 7, 'modification_tarif', 'Modification du tarif #1 par un administrateur', '2025-06-18 21:17:03'),
(264, 7, 'ajout_tarif', 'Ajout d\'un nouveau tarif - Type: velo, Prix/h: 0.06€, Prix/jour: 0.09€, Prix/mois: 0.04€, Minutes gratuites: 0', '2025-06-18 21:21:12'),
(265, 7, 'modification_tarif', 'Modification du tarif #12 - Type: velo -> handicape, Prix/h: 0.06€ -> 0.06€, Prix/jour: 0.09€ -> 0.09€, Prix/mois: 0.04€ -> 0.04€', '2025-06-18 21:21:21'),
(266, 7, 'suppression_tarif', 'Suppression du tarif #12 - Type: handicape, Prix/h: 0.06€, Prix/jour: 0.09€, Prix/mois: 0.04€', '2025-06-18 21:21:25'),
(267, 7, 'paiement', 'Paiement #88 effectué pour la réservation #120', '2025-06-19 11:07:20'),
(268, 7, 'reservation_creation', 'Réservation #122 créée', '2025-06-19 11:40:59'),
(269, 7, 'paiement', 'Paiement #89 effectué pour la réservation #122', '2025-06-19 11:41:03'),
(270, 7, 'reservation_creation', 'Réservation #123 créée', '2025-06-19 12:16:43'),
(271, 7, 'paiement', 'Paiement #90 effectué pour la réservation #123', '2025-06-19 12:16:49'),
(272, 7, 'connexion', 'Connexion réussie', '2025-06-19 13:24:15'),
(273, 7, 'reservation_creation', 'Réservation #124 créée', '2025-06-19 15:47:41'),
(274, 7, 'paiement', 'Paiement #91 effectué pour la réservation #124', '2025-06-19 15:47:46'),
(275, 7, 'annulation', 'Réservation #124 annulée - Place 2, Dates: 19/06/2025 17:49 - 19/06/2025 18:19', '2025-06-19 15:47:54'),
(276, 7, 'reservation_creation', 'Réservation #125 créée', '2025-06-19 15:48:14'),
(277, 7, 'paiement', 'Paiement #92 effectué pour la réservation #125', '2025-06-19 15:48:21'),
(278, 7, 'annulation', 'Réservation #125 annulée - Place 123, Dates: 19/06/2025 17:50 - 19/06/2025 18:20', '2025-06-19 15:48:33'),
(279, 7, 'reservation_create', 'Nouvelle réservation créée: 126', '2025-06-19 17:44:38'),
(280, 7, 'reservation_create', 'Nouvelle réservation créée: 127', '2025-06-19 17:53:10'),
(281, 7, 'reservation_create', 'Nouvelle réservation créée: 128', '2025-06-19 18:01:58'),
(282, 7, 'reservation_cancel', 'Réservation annulée: 127', '2025-06-19 18:02:16'),
(283, 7, 'reservation_cancel', 'Réservation annulée: 128', '2025-06-19 18:02:21'),
(284, 7, 'reservation_cancel', 'Réservation annulée: 126', '2025-06-19 18:02:24'),
(285, 7, 'reservation_create', 'Nouvelle réservation créée: 129', '2025-06-19 18:22:50'),
(286, 7, 'immediate_reservation_create', 'Réservation immédiate créée: 130', '2025-06-19 18:23:58'),
(287, 7, 'reservation_cancel', 'Réservation annulée: 129', '2025-06-19 18:28:45'),
(288, 7, 'reservation_create', 'Nouvelle réservation créée: 131', '2025-06-19 18:32:51'),
(289, 7, 'payment_create', 'Paiement effectué: 93', '2025-06-19 18:32:54'),
(290, 7, 'reservation_create', 'Nouvelle réservation créée: 132', '2025-06-19 18:37:39'),
(291, 7, 'payment_create', 'Paiement effectué: 94', '2025-06-19 18:37:42'),
(292, 7, 'immediate_reservation_end', 'Réservation immédiate terminée: 130', '2025-06-19 18:43:20'),
(293, 7, 'reservation_create', 'Nouvelle réservation créée: 133', '2025-06-19 23:51:12'),
(294, 7, 'payment_create', 'Paiement effectué: 95', '2025-06-19 23:51:15'),
(295, 7, 'immediate_reservation_create', 'Réservation immédiate créée: 134', '2025-06-19 23:51:24'),
(296, 7, 'immediate_reservation_end', 'Réservation immédiate terminée: 134', '2025-06-19 23:54:27'),
(297, 7, 'immediate_reservation_create', 'Réservation immédiate créée: 135', '2025-06-20 00:00:45'),
(298, 7, 'immediate_reservation_end', 'Réservation immédiate terminée: 135', '2025-06-20 00:04:37'),
(299, 7, 'reservation_create', 'Nouvelle réservation créée: 136', '2025-06-20 00:08:29'),
(300, 7, 'payment_create', 'Paiement effectué: 96', '2025-06-20 00:08:32'),
(301, 7, 'reservation_cancel', 'Réservation annulée: 132', '2025-06-20 00:08:40'),
(302, 7, 'immediate_reservation_create', 'Réservation immédiate créée: 140', '2025-06-20 00:56:51'),
(303, 7, 'immediate_reservation_end', 'Réservation immédiate terminée: 140', '2025-06-20 01:11:25'),
(304, 7, 'immediate_reservation_create', 'Réservation immédiate créée: 141', '2025-06-20 01:11:34'),
(305, 7, 'immediate_reservation_end', 'Réservation immédiate terminée: 141', '2025-06-20 01:21:59'),
(306, 7, 'immediate_reservation_create', 'Réservation immédiate créée: 142', '2025-06-20 01:27:15'),
(307, 7, 'immediate_reservation_end', 'Réservation immédiate terminée: 142', '2025-06-20 01:28:23'),
(308, 7, 'immediate_reservation_create', 'Réservation immédiate créée: 143', '2025-06-20 01:30:31'),
(309, 7, 'immediate_reservation_end', 'Réservation immédiate terminée: 143', '2025-06-20 01:33:37'),
(310, 7, 'immediate_reservation_create', 'Réservation immédiate créée: 144', '2025-06-20 01:36:07'),
(311, 7, 'immediate_reservation_end', 'Réservation immédiate terminée: 144', '2025-06-20 01:36:29'),
(312, 7, 'payment_create', 'Paiement effectué: 97', '2025-06-20 01:36:40'),
(313, 7, 'immediate_reservation_create', 'Réservation immédiate créée: 147', '2025-06-20 01:46:18'),
(314, 7, 'immediate_reservation_end', 'Réservation immédiate terminée: 147', '2025-06-20 01:46:26'),
(315, 7, 'payment_create', 'Paiement effectué: 98', '2025-06-20 01:46:36'),
(316, 7, 'immediate_reservation_create', 'Réservation immédiate créée: 148', '2025-06-20 01:47:10'),
(317, 2, 'immediate_reservation_end', 'Réservation immédiate terminée: 149', '2025-06-20 01:47:38'),
(318, 7, 'immediate_reservation_end', 'Réservation immédiate terminée: 148', '2025-06-20 01:49:47'),
(319, 2, 'immediate_reservation_end', 'Réservation immédiate terminée: 150', '2025-06-20 01:51:33'),
(320, 7, 'reservation_create', 'Nouvelle réservation créée: 151', '2025-06-20 01:52:01'),
(321, 7, 'payment_create', 'Paiement effectué: 99', '2025-06-20 01:52:04'),
(322, 7, 'immediate_reservation_create', 'Réservation immédiate créée: 152', '2025-06-20 01:52:30'),
(323, 7, 'immediate_reservation_end', 'Réservation immédiate terminée: 152', '2025-06-20 01:52:37'),
(324, 7, 'immediate_reservation_create', 'Réservation immédiate créée: 155', '2025-06-20 01:54:41'),
(325, 7, 'immediate_reservation_end', 'Réservation immédiate terminée: 155', '2025-06-20 01:54:45'),
(326, 7, 'payment_create', 'Paiement effectué: 100', '2025-06-20 01:54:56'),
(327, 7, 'immediate_reservation_create', 'Réservation immédiate créée: 156', '2025-06-20 01:58:47'),
(328, 7, 'immediate_reservation_end', 'Réservation immédiate terminée: 156', '2025-06-20 01:59:27'),
(329, 7, 'reservation_cancel', 'Réservation annulée: 159', '2025-06-21 16:30:17'),
(330, 7, 'reservation_create', 'Nouvelle réservation créée: 161', '2025-06-21 16:30:24'),
(331, 7, 'reservation_create', 'Nouvelle réservation créée: 162', '2025-06-21 16:31:17'),
(332, 7, 'reservation_create', 'Nouvelle réservation créée: 163', '2025-06-21 16:32:07'),
(333, 7, 'reservation_create', 'Nouvelle réservation créée: 164', '2025-06-21 16:34:12'),
(334, 7, 'payment_create', 'Paiement effectué: 101', '2025-06-21 16:34:18'),
(335, 7, 'reservation_cancel', 'Réservation annulée: 163', '2025-06-21 16:34:25'),
(336, 7, 'reservation_cancel', 'Réservation annulée: 162', '2025-06-21 16:34:28'),
(337, 7, 'reservation_cancel', 'Réservation annulée: 161', '2025-06-21 16:34:32');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `titre` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `type` enum('reservation','paiement','rappel','system') NOT NULL,
  `lu` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `titre`, `message`, `type`, `lu`, `created_at`) VALUES
(1, 2, 'Réservation confirmée', 'Votre réservation a été confirmée.', 'reservation', 1, '2025-05-18 20:50:31'),
(2, 2, 'Information système', 'Le système sera en maintenance demain.', 'system', 0, '2025-05-18 20:50:31'),
(3, 3, 'Réservation confirmée', 'Votre réservation a été confirmée.', 'reservation', 1, '2025-05-18 20:50:31'),
(4, 3, 'Information système', 'Le système sera en maintenance demain.', 'system', 1, '2025-05-18 20:50:31'),
(5, 4, 'Rappel de réservation', 'Votre réservation commence dans 1 heure.', 'rappel', 1, '2025-05-18 20:50:31'),
(6, 4, 'Information système', 'Le système sera en maintenance demain.', 'system', 0, '2025-05-18 20:50:31'),
(7, 6, 'Paiement reçu', 'Nous avons bien reçu votre paiement.', 'paiement', 0, '2025-05-18 20:50:31'),
(8, 6, 'Information système', 'Le système sera en maintenance demain.', 'system', 1, '2025-05-18 20:50:31'),
(9, 7, 'Réservation confirmée', 'Votre réservation de la place n°A03 du 21/05/2025 17:17 au 21/05/2025 19:17 a été confirmée.', 'reservation', 1, '2025-05-21 14:17:13'),
(10, 7, 'Confirmation de paiement', '{\"reservation_id\":24,\"montant\":\"4.00\",\"status\":\"valid\\u00e9\"}', 'paiement', 1, '2025-05-21 14:17:29'),
(11, 7, 'Réservation annulée', 'Votre réservation de la place n°A03 a été annulée avec succès.', '', 1, '2025-05-21 14:21:59'),
(12, 7, 'Réservation confirmée', 'Votre réservation de la place n°A02 du 21/05/2025 17:27 au 21/05/2025 19:27 a été confirmée.', 'reservation', 1, '2025-05-21 14:27:51'),
(13, 7, 'Confirmation de paiement', '{\"reservation_id\":25,\"montant\":\"4.00\",\"status\":\"valid\\u00e9\"}', 'paiement', 1, '2025-05-21 14:28:08'),
(14, 7, 'Réservation annulée', 'Votre réservation de la place n°A02 a été annulée avec succès.', '', 1, '2025-05-21 14:28:32'),
(15, 7, 'Réservation confirmée', 'Votre réservation de la place n°A02 du 21/05/2025 17:35 au 21/05/2025 19:35 a été confirmée.', 'reservation', 1, '2025-05-21 14:35:55'),
(16, 7, 'Confirmation de paiement', '{\"reservation_id\":26,\"montant\":\"4.00\",\"status\":\"valid\\u00e9\"}', 'paiement', 1, '2025-05-21 14:36:10'),
(17, 7, 'Réservation annulée', 'Votre réservation de la place n°A02 a été annulée avec succès.', '', 1, '2025-05-21 14:36:34'),
(18, 7, 'Réservation confirmée', 'Votre réservation de la place n°H04 du 21/05/2025 20:55 au 21/05/2025 22:55 a été confirmée.', 'reservation', 1, '2025-05-21 17:55:49'),
(19, 7, 'Confirmation de paiement', '{\"reservation_id\":27,\"montant\":\"3.00\",\"status\":\"valid\\u00e9\"}', 'paiement', 1, '2025-05-21 17:56:09'),
(20, 7, 'Réservation annulée', 'Votre réservation de la place n°H04 a été annulée avec succès.', '', 1, '2025-05-21 18:01:47'),
(21, 7, 'Paiement confirmé', 'Votre paiement de 3.00€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-13 13:39:04'),
(22, 7, 'Paiement confirmé', 'Votre paiement de 6.00€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-13 20:04:14'),
(23, 7, 'Réservation annulée', 'Votre réservation de la place 2 a été annulée avec succès.', 'system', 1, '2025-06-13 20:37:35'),
(24, 7, 'Paiement confirmé', 'Votre paiement de 3.00€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-13 22:38:55'),
(25, 7, 'Réservation annulée', 'Votre réservation de la place 921 a été annulée avec succès.', 'system', 1, '2025-06-13 22:51:11'),
(26, 10, 'Bienvenue sur ParkMe In', 'Merci de vous être inscrit sur notre plateforme. Vous pouvez maintenant réserver des places de parking en quelques clics.', 'system', 1, '2025-06-14 10:25:39'),
(27, 10, 'Paiement confirmé', 'Votre paiement de 3.00€ a été confirmé pour la réservation de la place 2.', 'paiement', 0, '2025-06-14 11:26:14'),
(28, 7, 'Paiement confirmé', 'Votre paiement de 3.00€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-14 14:47:18'),
(29, 7, 'Paiement confirmé', 'Votre paiement de 1.50€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-14 18:51:47'),
(30, 7, 'Réservation annulée', 'Votre réservation de la place 2 a été annulée avec succès.', 'system', 1, '2025-06-14 18:59:46'),
(31, 7, 'Réservation annulée', 'Votre réservation de la place 921 a été annulée avec succès.', 'system', 1, '2025-06-14 18:59:51'),
(32, 7, 'Paiement confirmé', 'Votre paiement de 1.50€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-14 19:37:25'),
(33, 7, 'Paiement confirmé', 'Votre paiement de 1.50€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-14 19:37:55'),
(34, 7, 'Réservation annulée', 'Votre réservation de la place 2 a été annulée avec succès.', 'system', 1, '2025-06-14 19:38:07'),
(35, 7, 'Réservation annulée', 'Votre réservation de la place 2 a été annulée avec succès.', 'system', 1, '2025-06-14 19:38:11'),
(36, 7, 'Paiement confirmé', 'Votre paiement de 1.50€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-14 19:49:48'),
(38, 7, 'Paiement confirmé', 'Votre paiement de 0.67€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-15 12:54:50'),
(39, 7, 'Paiement confirmé', 'Votre paiement de 0.02€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-15 12:57:57'),
(40, 7, 'Paiement confirmé', 'Votre paiement de 0.02€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-15 13:09:53'),
(41, 7, 'Paiement confirmé', 'Votre paiement de 0.00€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-15 13:10:31'),
(42, 7, 'Paiement confirmé', 'Votre paiement de 0.01€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-15 13:12:15'),
(43, 7, 'Paiement confirmé', 'Votre paiement de 0.01€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-15 13:22:18'),
(44, 7, 'Paiement confirmé', 'Votre paiement de 0.00€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-15 13:24:11'),
(45, 7, 'Paiement confirmé', 'Votre paiement de 0.01€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-15 13:34:31'),
(46, 7, 'Paiement confirmé', 'Votre paiement de 1.50€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-15 15:16:13'),
(47, 7, 'Réservation annulée', 'Votre réservation de la place 2 a été annulée avec succès.', 'system', 1, '2025-06-15 15:18:58'),
(48, 7, 'Paiement confirmé', 'Votre paiement de 0.47€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-15 15:34:17'),
(49, 7, 'Paiement confirmé', 'Votre paiement de 1.50€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-15 19:24:07'),
(50, 7, 'Réservation annulée', 'Votre réservation de la place 2 a été annulée avec succès.', 'system', 1, '2025-06-15 19:24:21'),
(51, 7, 'Paiement confirmé', 'Votre paiement de 7.50€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-15 21:32:26'),
(52, 7, 'Paiement confirmé', 'Votre paiement de 0.73€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-15 21:47:18'),
(53, 7, 'Paiement confirmé', 'Votre paiement de 0.01€ a été confirmé pour la réservation de la place 921.', 'paiement', 1, '2025-06-15 22:41:03'),
(54, 7, 'Paiement confirmé', 'Votre paiement de 0.01€ a été confirmé pour la réservation de la place 921.', 'paiement', 1, '2025-06-15 22:44:36'),
(55, 7, 'Paiement confirmé', 'Votre paiement de 0.75€ a été confirmé pour la réservation de la place 921.', 'paiement', 1, '2025-06-15 23:42:18'),
(56, 7, 'Paiement confirmé', 'Votre paiement de 0.01€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-16 12:15:26'),
(57, 7, 'Paiement confirmé', 'Votre paiement de 0.01€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-16 12:16:05'),
(58, 7, 'Paiement confirmé', 'Votre paiement de 0.13€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-16 12:52:29'),
(59, 7, 'Paiement confirmé', 'Votre paiement de 0.04€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-16 12:55:00'),
(60, 7, 'Paiement confirmé', 'Votre paiement de 0.01€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-16 12:57:41'),
(61, 7, 'Paiement confirmé', 'Votre paiement de 0.01€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-16 13:24:10'),
(62, 7, 'Paiement confirmé', 'Votre paiement de 0.00€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-16 13:25:44'),
(63, 7, 'Paiement confirmé', 'Votre paiement de 0.00€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-16 13:34:25'),
(64, 7, 'Paiement confirmé', 'Votre paiement de 0.00€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-16 13:54:55'),
(65, 7, 'Paiement confirmé', 'Votre paiement de 0.00€ a été confirmé pour la réservation de la place 921.', 'paiement', 1, '2025-06-16 15:12:18'),
(66, 7, 'Paiement confirmé', 'Votre paiement de 2.35€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-16 16:30:54'),
(67, 7, 'Paiement confirmé', 'Votre paiement de 0.11€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-16 16:37:36'),
(68, 7, 'Paiement confirmé', 'Votre paiement de 0.24€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-16 16:48:43'),
(69, 7, 'Paiement confirmé', 'Votre paiement de 0.01€ a été confirmé pour la réservation de la place A02.', 'paiement', 1, '2025-06-16 16:52:02'),
(70, 7, 'Paiement confirmé', 'Votre paiement de 1.50€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-16 16:59:22'),
(71, 7, 'Paiement confirmé', 'Votre paiement de 1.50€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-16 17:01:34'),
(72, 7, 'Réservation annulée', 'Votre réservation de la place 2 a été annulée avec succès.', 'system', 1, '2025-06-16 17:04:13'),
(73, 7, 'Réservation annulée', 'Votre réservation de la place 2 a été annulée avec succès.', 'system', 1, '2025-06-16 17:04:23'),
(74, 7, 'Paiement confirmé', 'Votre paiement de 0.01€ a été confirmé pour la réservation de la place 921.', 'paiement', 1, '2025-06-16 17:05:14'),
(75, 7, 'Paiement confirmé', 'Votre paiement de 1.50€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-16 17:08:36'),
(76, 7, 'Réservation annulée', 'Votre réservation de la place 2 a été annulée avec succès.', 'system', 1, '2025-06-16 17:08:50'),
(77, 7, 'Paiement confirmé', 'Votre paiement de 1.50€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-16 17:37:08'),
(78, 7, 'Réservation annulée', 'Votre réservation #99 de la place 2 pour le 16/06/2025 à 19:38 a été annulée avec succès.', 'system', 1, '2025-06-16 17:37:27'),
(79, 7, 'Paiement confirmé', 'Votre paiement de 1.50€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-16 17:49:14'),
(80, 7, 'Réservation annulée', 'Votre réservation #101 de la place 2 pour le 16/06/2025 à 19:51 a été annulée avec succès.', 'system', 1, '2025-06-16 17:49:30'),
(81, 12, 'Bienvenue sur ParkMe In', 'Merci de vous être inscrit sur notre plateforme. Vous pouvez maintenant réserver des places de parking en quelques clics.', 'system', 0, '2025-06-16 18:23:45'),
(82, 12, 'Paiement confirmé', 'Votre paiement de 1.50€ a été confirmé pour la réservation de la place 2.', 'paiement', 0, '2025-06-16 18:24:14'),
(83, 12, 'Paiement confirmé', 'Votre paiement de 0.03€ a été confirmé pour la réservation de la place 2.', 'paiement', 0, '2025-06-16 18:25:23'),
(84, 7, 'Paiement confirmé', 'Votre paiement de 1.50€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-16 19:58:47'),
(85, 7, 'Réservation annulée', 'Votre réservation #105 de la place 2 pour le 16/06/2025 à 22:00 a été annulée avec succès.', 'system', 1, '2025-06-16 19:59:23'),
(86, 7, 'Paiement confirmé', 'Votre paiement de 0.01€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-16 20:41:39'),
(87, 7, 'Paiement confirmé', 'Votre paiement de 1.50€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-17 08:42:26'),
(88, 7, 'Réservation annulée', 'Votre réservation #107 de la place 2 pour le 17/06/2025 à 10:45 a été annulée avec succès.', 'system', 1, '2025-06-17 08:42:38'),
(89, 7, 'Paiement confirmé', 'Votre paiement de 0.00€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-17 10:09:02'),
(90, 7, 'Paiement confirmé', 'Votre paiement de 1.50€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-17 10:09:20'),
(91, 7, 'Réservation annulée', 'Votre réservation #109 de la place 2 pour le 17/06/2025 à 12:11 a été annulée avec succès.', 'system', 1, '2025-06-17 10:09:27'),
(92, 7, 'Paiement confirmé', 'Votre paiement de 1.50€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-17 14:17:04'),
(93, 7, 'Paiement confirmé', 'Votre paiement de 0.01€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-17 14:17:29'),
(94, 7, 'Paiement confirmé', 'Votre paiement de 0.58€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-17 16:50:40'),
(95, 7, 'Paiement confirmé', 'Votre paiement de 0.00€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-18 11:01:50'),
(96, 7, 'Paiement confirmé', 'Votre paiement de 0.01€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-18 11:15:29'),
(97, 7, 'Paiement confirmé', 'Votre paiement de 0.01€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-18 12:26:17'),
(98, 7, 'Paiement confirmé', 'Votre paiement de 0.02€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-18 18:34:49'),
(99, 7, 'Paiement confirmé', 'Votre paiement de 0.45€ a été confirmé pour la réservation de la place 123.', 'paiement', 1, '2025-06-18 20:55:43'),
(100, 7, 'Paiement confirmé', 'Votre paiement de 0.45€ a été confirmé pour la réservation de la place 123.', 'paiement', 1, '2025-06-18 20:57:19'),
(101, 7, 'Réservation annulée', 'Votre réservation #118 de la place 123 pour le 19/06/2025 à 22:56 a été annulée avec succès.', 'system', 1, '2025-06-18 21:00:58'),
(102, 7, 'Paiement confirmé', 'Votre paiement de 0.08€ a été confirmé pour la réservation de la place 123.', 'paiement', 1, '2025-06-18 21:03:18'),
(103, 7, 'Paiement confirmé', 'Votre paiement de 0.00€ a été confirmé pour la réservation de la place 301.', 'paiement', 1, '2025-06-19 11:07:20'),
(104, 7, 'Paiement confirmé', 'Votre paiement de 0.64€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-19 11:41:03'),
(105, 7, 'Paiement confirmé', 'Votre paiement de 0.19€ a été confirmé pour la réservation de la place 123.', 'paiement', 1, '2025-06-19 12:16:49'),
(106, 7, 'Paiement confirmé', 'Votre paiement de 0.64€ a été confirmé pour la réservation de la place 2.', 'paiement', 1, '2025-06-19 15:47:46'),
(107, 7, 'Réservation annulée', 'Votre réservation #124 de la place 2 pour le 19/06/2025 à 17:49 a été annulée avec succès.', 'system', 1, '2025-06-19 15:47:54'),
(108, 7, 'Paiement confirmé', 'Votre paiement de 0.19€ a été confirmé pour la réservation de la place 123.', 'paiement', 1, '2025-06-19 15:48:21'),
(109, 7, 'Réservation annulée', 'Votre réservation #125 de la place 123 pour le 19/06/2025 à 17:50 a été annulée avec succès.', 'system', 1, '2025-06-19 15:48:33'),
(110, 7, 'Réservation annulée', 'Votre réservation #127 a été annulée.', '', 1, '2025-06-19 18:02:16'),
(111, 7, 'Réservation annulée', 'Votre réservation #128 a été annulée.', '', 1, '2025-06-19 18:02:21'),
(112, 7, 'Réservation annulée', 'Votre réservation #126 a été annulée.', '', 1, '2025-06-19 18:02:24'),
(113, 7, 'Réservation annulée', 'Votre réservation #129 a été annulée.', '', 1, '2025-06-19 18:28:45'),
(114, 7, 'Réservation annulée', 'Votre réservation #132 a été annulée.', '', 1, '2025-06-20 00:08:40'),
(115, 7, '🔔 Réservation aujourd\'hui !', 'N\'oubliez pas ! Votre réservation #164 pour la place Place 123 commence dans quelques heures le 21/06/2025 à 23:34.', '', 0, '2025-06-21 16:34:12'),
(116, 7, 'Réservation annulée', 'Votre réservation #163 a été annulée.', '', 0, '2025-06-21 16:34:25'),
(117, 7, 'Réservation annulée', 'Votre réservation #162 a été annulée.', '', 0, '2025-06-21 16:34:28'),
(118, 7, 'Réservation annulée', 'Votre réservation #161 a été annulée.', '', 0, '2025-06-21 16:34:32');

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

CREATE TABLE `paiements` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `mode_paiement` enum('carte','paypal','virement') DEFAULT NULL,
  `status` enum('en_attente','valide','refuse','annule') DEFAULT 'en_attente',
  `date_paiement` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `paiements`
--

INSERT INTO `paiements` (`id`, `reservation_id`, `montant`, `mode_paiement`, `status`, `date_paiement`) VALUES
(1, 1, 8.00, 'paypal', 'annule', '2025-05-18 20:50:31'),
(2, 2, 6.00, 'paypal', 'valide', '2025-05-18 20:50:31'),
(3, 3, 8.00, 'paypal', 'valide', '2025-05-18 20:50:31'),
(4, 4, 8.00, 'paypal', 'valide', '2025-05-18 20:50:31'),
(5, 5, 6.00, 'carte', 'valide', '2025-05-18 20:50:31'),
(6, 6, 6.00, 'carte', 'valide', '2025-05-18 20:50:31'),
(7, 7, 6.00, 'paypal', 'valide', '2025-05-18 20:50:31'),
(8, 8, 8.00, 'paypal', 'valide', '2025-05-18 20:50:31'),
(9, 9, 8.00, 'carte', 'valide', '2025-05-18 20:50:31'),
(10, 10, 8.00, 'paypal', 'valide', '2025-05-18 20:50:31'),
(11, 11, 6.00, 'paypal', 'valide', '2025-05-18 20:50:31'),
(12, 12, 10.00, 'paypal', 'valide', '2025-05-18 20:50:31'),
(13, 13, 12.00, 'paypal', 'valide', '2025-05-18 20:50:31'),
(14, 14, 16.00, 'carte', 'valide', '2025-05-18 20:50:31'),
(15, 16, 16.00, 'paypal', 'valide', '2025-05-18 20:50:31'),
(16, 18, 12.00, 'paypal', 'valide', '2025-05-18 20:50:31'),
(17, 20, 16.00, 'carte', 'valide', '2025-05-18 20:50:31'),
(18, 22, 16.00, 'paypal', 'valide', '2025-05-18 20:50:31'),
(20, 25, 4.00, NULL, 'annule', '2025-05-21 14:27:51'),
(21, 26, 4.00, NULL, 'annule', '2025-05-21 14:35:55'),
(22, 27, 3.00, NULL, 'annule', '2025-05-21 17:55:49'),
(23, 28, 3.00, 'carte', 'annule', '2025-06-13 13:39:04'),
(24, 31, 6.00, 'carte', 'annule', '2025-06-13 20:04:14'),
(25, 32, 3.00, 'carte', 'annule', '2025-06-13 22:38:55'),
(26, 35, 3.00, 'carte', 'annule', '2025-06-14 10:29:44'),
(27, 36, 3.00, 'carte', 'annule', '2025-06-14 11:26:14'),
(28, 37, 3.00, 'carte', 'annule', '2025-06-14 14:47:18'),
(29, 38, 1.50, 'carte', 'annule', '2025-06-14 18:51:47'),
(30, 41, 1.50, 'carte', 'annule', '2025-06-14 19:37:25'),
(31, 42, 1.50, 'carte', 'annule', '2025-06-14 19:37:55'),
(32, 43, 1.50, 'carte', 'annule', '2025-06-14 19:49:48'),
(33, 44, 1.50, 'carte', 'annule', '2025-06-15 10:21:24'),
(34, 45, 1.50, 'paypal', 'annule', '2025-06-15 10:32:00'),
(35, 49, 0.67, 'carte', 'valide', '2025-06-15 12:54:50'),
(36, 50, 0.02, 'carte', 'valide', '2025-06-15 12:57:57'),
(37, 51, 0.02, 'carte', 'valide', '2025-06-15 13:09:53'),
(38, 52, 0.00, 'carte', 'valide', '2025-06-15 13:10:30'),
(39, 53, 0.01, 'carte', 'valide', '2025-06-15 13:12:15'),
(40, 54, 0.01, 'carte', 'valide', '2025-06-15 13:22:18'),
(41, 55, 0.00, 'carte', 'valide', '2025-06-15 13:24:11'),
(42, 56, 0.01, 'carte', 'valide', '2025-06-15 13:34:31'),
(43, 60, 1.50, 'carte', 'annule', '2025-06-15 15:16:13'),
(44, 61, 0.47, 'carte', 'annule', '2025-06-15 15:34:17'),
(45, 62, 1.50, 'carte', 'annule', '2025-06-15 19:24:07'),
(46, 65, 7.50, 'carte', 'annule', '2025-06-15 21:32:26'),
(47, 66, 0.73, 'carte', 'annule', '2025-06-15 21:47:18'),
(48, 68, 0.01, 'carte', 'annule', '2025-06-15 22:41:03'),
(49, 70, 0.01, 'carte', 'annule', '2025-06-15 22:44:36'),
(50, 72, 0.75, 'carte', 'annule', '2025-06-15 23:42:18'),
(51, 73, 0.01, 'carte', 'valide', '2025-06-16 12:15:26'),
(52, 74, 0.01, 'carte', 'valide', '2025-06-16 12:16:05'),
(53, 75, 0.13, 'carte', 'valide', '2025-06-16 12:52:29'),
(54, 76, 0.04, 'carte', 'valide', '2025-06-16 12:55:00'),
(55, 77, 0.01, 'carte', 'valide', '2025-06-16 12:57:41'),
(56, 78, 0.01, 'carte', 'valide', '2025-06-16 13:24:10'),
(57, 80, 0.00, 'carte', 'valide', '2025-06-16 13:25:44'),
(58, 81, 0.00, 'carte', 'valide', '2025-06-16 13:34:25'),
(59, 82, 0.00, 'carte', 'valide', '2025-06-16 13:54:55'),
(60, 83, 0.00, 'carte', 'valide', '2025-06-16 15:12:18'),
(61, 90, 2.35, 'carte', 'valide', '2025-06-16 16:30:54'),
(62, 91, 0.11, 'carte', 'valide', '2025-06-16 16:37:36'),
(63, 92, 0.24, 'carte', 'valide', '2025-06-16 16:48:43'),
(64, 93, 0.01, 'carte', 'valide', '2025-06-16 16:52:01'),
(65, 94, 1.50, 'carte', 'annule', '2025-06-16 16:59:22'),
(66, 96, 1.50, 'carte', 'valide', '2025-06-16 17:01:34'),
(67, 97, 0.01, 'carte', 'annule', '2025-06-16 17:05:14'),
(68, 98, 1.50, 'carte', 'annule', '2025-06-16 17:08:35'),
(69, 99, 1.50, 'carte', 'annule', '2025-06-16 17:37:08'),
(70, 101, 1.50, 'carte', 'annule', '2025-06-16 17:49:14'),
(71, 102, 1.50, 'carte', 'valide', '2025-06-16 18:24:13'),
(72, 104, 0.03, 'carte', 'valide', '2025-06-16 18:25:23'),
(73, 105, 1.50, 'carte', 'annule', '2025-06-16 19:58:47'),
(74, 106, 0.01, 'carte', 'valide', '2025-06-16 20:41:38'),
(75, 107, 1.50, 'carte', 'annule', '2025-06-17 08:42:26'),
(76, 108, 0.00, 'carte', 'valide', '2025-06-17 10:09:01'),
(77, 109, 1.50, 'carte', 'annule', '2025-06-17 10:09:20'),
(78, 110, 1.50, 'carte', 'valide', '2025-06-17 14:17:04'),
(79, 111, 0.01, 'carte', 'valide', '2025-06-17 14:17:29'),
(80, 112, 0.58, 'carte', 'valide', '2025-06-17 16:50:40'),
(81, 113, 0.00, 'carte', 'valide', '2025-06-18 11:01:50'),
(82, 114, 0.01, 'carte', 'valide', '2025-06-18 11:15:29'),
(83, 115, 0.01, 'carte', 'valide', '2025-06-18 12:26:17'),
(84, 116, 0.02, 'carte', 'valide', '2025-06-18 18:34:49'),
(85, 117, 0.45, 'carte', 'valide', '2025-06-18 20:55:43'),
(86, 118, 0.45, 'carte', 'annule', '2025-06-18 20:57:19'),
(87, 119, 0.08, 'carte', 'valide', '2025-06-18 21:03:18'),
(88, 120, 0.00, 'paypal', 'valide', '2025-06-19 11:07:20'),
(89, 122, 0.64, 'carte', 'valide', '2025-06-19 11:41:03'),
(90, 123, 0.19, 'paypal', 'valide', '2025-06-19 12:16:49'),
(91, 124, 0.64, 'paypal', 'annule', '2025-06-19 15:47:46'),
(92, 125, 0.19, 'paypal', 'annule', '2025-06-19 15:48:21'),
(93, 131, 1.50, 'carte', 'valide', '2025-06-19 18:32:54'),
(94, 132, 1.19, 'carte', 'annule', '2025-06-19 18:37:42'),
(95, 133, 0.00, 'carte', 'valide', '2025-06-19 23:51:15'),
(96, 136, 0.32, 'carte', 'valide', '2025-06-20 00:08:32'),
(97, 144, 0.32, 'carte', 'valide', '2025-06-20 01:36:40'),
(98, 147, 0.64, 'carte', 'valide', '2025-06-20 01:46:36'),
(99, 151, 0.19, 'carte', 'valide', '2025-06-20 01:52:04'),
(100, 155, 0.64, 'carte', 'valide', '2025-06-20 01:54:56'),
(101, 164, 0.08, 'paypal', 'valide', '2025-06-21 16:34:18');

-- --------------------------------------------------------

--
-- Structure de la table `parking_spaces`
--

CREATE TABLE `parking_spaces` (
  `id` int(11) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `type` enum('standard','handicape','electrique','moto/scooter','velo') NOT NULL DEFAULT 'standard',
  `status` enum('libre','occupe','maintenance') NOT NULL DEFAULT 'libre',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `parking_spaces`
--

INSERT INTO `parking_spaces` (`id`, `numero`, `type`, `status`, `created_at`) VALUES
(1, 'A01', 'standard', 'libre', '2025-05-18 20:50:31'),
(2, 'A02', 'standard', 'libre', '2025-05-18 20:50:31'),
(4, 'A04', 'standard', 'libre', '2025-05-18 20:50:31'),
(5, 'A05', 'standard', 'libre', '2025-05-18 20:50:31'),
(6, 'A06', 'standard', 'libre', '2025-05-18 20:50:31'),
(7, 'A07', 'standard', 'libre', '2025-05-18 20:50:31'),
(8, 'A08', 'standard', 'libre', '2025-05-18 20:50:31'),
(9, 'A09', 'standard', 'libre', '2025-05-18 20:50:31'),
(10, 'A10', 'standard', 'libre', '2025-05-18 20:50:31'),
(11, 'A11', 'standard', 'libre', '2025-05-18 20:50:31'),
(12, 'A12', 'standard', 'libre', '2025-05-18 20:50:31'),
(13, 'A13', 'standard', 'libre', '2025-05-18 20:50:31'),
(14, 'A14', 'standard', 'libre', '2025-05-18 20:50:31'),
(15, 'A15', 'standard', 'libre', '2025-05-18 20:50:31'),
(16, 'A16', 'standard', 'libre', '2025-05-18 20:50:31'),
(17, 'A17', 'standard', 'libre', '2025-05-18 20:50:31'),
(18, 'A18', 'standard', 'libre', '2025-05-18 20:50:31'),
(19, 'A19', 'standard', 'libre', '2025-05-18 20:50:31'),
(20, 'A20', 'standard', 'libre', '2025-05-18 20:50:31'),
(21, 'H01', 'standard', 'libre', '2025-05-18 20:50:31'),
(22, 'H02', 'handicape', 'libre', '2025-05-18 20:50:31'),
(23, 'H03', 'handicape', 'libre', '2025-05-18 20:50:31'),
(24, 'H04', 'handicape', 'libre', '2025-05-18 20:50:31'),
(25, 'H05', 'handicape', 'libre', '2025-05-18 20:50:31'),
(26, 'E01', 'electrique', 'libre', '2025-05-18 20:50:31'),
(27, 'E02', 'electrique', 'libre', '2025-05-18 20:50:31'),
(28, 'E03', 'electrique', 'libre', '2025-05-18 20:50:31'),
(29, 'E04', 'electrique', 'libre', '2025-05-18 20:50:31'),
(30, 'E05', 'electrique', 'libre', '2025-05-18 20:50:31'),
(35, '2', 'electrique', 'libre', '2025-06-10 19:29:38'),
(37, '921', 'handicape', 'libre', '2025-06-13 15:12:04'),
(38, '301', 'handicape', 'libre', '2025-06-16 17:39:01'),
(39, '302', 'handicape', 'libre', '2025-06-16 17:39:01'),
(40, '303', 'handicape', 'libre', '2025-06-16 17:39:01'),
(41, '304', 'standard', 'libre', '2025-06-16 17:39:01'),
(42, '305', 'velo', 'libre', '2025-06-16 17:39:01'),
(43, 'u05', 'handicape', 'libre', '2025-06-17 10:33:32'),
(44, '9', 'electrique', 'libre', '2025-06-17 10:34:17'),
(46, '7', 'moto/scooter', 'libre', '2025-06-17 10:39:04'),
(47, '123', 'moto/scooter', 'libre', '2025-06-17 16:54:15');

-- --------------------------------------------------------

--
-- Structure de la table `remboursements`
--

CREATE TABLE `remboursements` (
  `id` int(11) NOT NULL,
  `paiement_id` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `raison` text DEFAULT NULL,
  `status` enum('en_cours','effectué','refusé') DEFAULT 'en_cours',
  `date_demande` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `remboursements`
--

INSERT INTO `remboursements` (`id`, `paiement_id`, `montant`, `raison`, `status`, `date_demande`) VALUES
(2, 20, 4.00, 'Annulation par l\'utilisateur', 'effectué', '2025-05-21 14:28:32'),
(3, 21, 4.00, 'Annulation par l\'utilisateur', 'effectué', '2025-05-21 14:36:34'),
(4, 22, 3.00, 'Annulation par l\'utilisateur', 'en_cours', '2025-05-21 18:01:47'),
(5, 1, 8.00, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-13 19:51:51'),
(6, 23, 3.00, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-13 20:02:55'),
(7, 24, 6.00, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-13 20:37:35'),
(8, 25, 3.00, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-14 11:28:22'),
(9, 26, 3.00, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-14 11:28:36'),
(10, 27, 3.00, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-14 11:28:39'),
(11, 28, 3.00, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-14 14:49:13'),
(12, 31, 1.50, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-14 19:38:07'),
(13, 30, 1.50, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-14 19:38:11'),
(14, 34, 1.50, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-15 12:33:58'),
(15, 33, 1.50, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-15 12:34:00'),
(16, 32, 1.50, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-15 12:34:02'),
(17, 29, 1.50, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-15 12:34:05'),
(18, 43, 1.50, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-15 15:18:58'),
(19, 45, 1.50, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-15 19:24:21'),
(20, 49, 0.01, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-16 12:09:42'),
(21, 50, 0.75, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-16 12:09:44'),
(22, 48, 0.01, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-16 12:09:46'),
(23, 47, 0.73, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-16 12:09:50'),
(24, 46, 7.50, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-16 12:09:51'),
(25, 44, 0.47, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-16 12:09:58'),
(26, 65, 1.50, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-16 17:04:13'),
(27, 67, 0.01, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-16 17:05:37'),
(28, 68, 1.50, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-16 17:08:50'),
(29, 69, 1.50, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-16 17:37:27'),
(30, 70, 1.50, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-16 17:49:30'),
(31, 73, 1.50, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-16 19:59:23'),
(32, 75, 1.50, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-17 08:42:38'),
(33, 77, 1.50, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-17 10:09:27'),
(34, 86, 0.45, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-18 21:00:58'),
(35, 91, 0.64, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-19 15:47:54'),
(36, 92, 0.19, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-19 15:48:33'),
(37, 94, 1.19, 'Annulation par l\'utilisateur', 'en_cours', '2025-06-20 00:08:40');

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `place_id` int(11) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime DEFAULT NULL,
  `status` enum('en_attente','confirmée','en_cours','terminee','annulée','expirée','en_cours_immediat','en_attente_paiement') NOT NULL DEFAULT 'en_attente',
  `code_acces` varchar(10) DEFAULT NULL,
  `code_sortie` varchar(10) DEFAULT NULL,
  `montant_total` decimal(10,2) DEFAULT 0.00,
  `reduction_abonnement` decimal(5,2) DEFAULT 0.00,
  `notification_sent` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiration_time` datetime DEFAULT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `guest_email` varchar(255) DEFAULT NULL,
  `guest_phone` varchar(20) DEFAULT NULL,
  `guest_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `place_id`, `date_debut`, `date_fin`, `status`, `code_acces`, `code_sortie`, `montant_total`, `reduction_abonnement`, `notification_sent`, `created_at`, `expiration_time`, `guest_name`, `guest_email`, `guest_phone`, `guest_token`) VALUES
(1, 5, 16, '2025-05-08 22:50:31', '2025-05-09 02:50:31', 'annulée', 'CA1C2E', NULL, 8.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(2, 6, 23, '2025-05-09 22:50:31', '2025-05-10 02:50:31', '', 'EFE330', NULL, 6.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(3, 3, 4, '2025-05-10 22:50:31', '2025-05-11 02:50:31', '', '2761B0', NULL, 8.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(4, 4, 2, '2025-05-11 22:50:31', '2025-05-12 02:50:31', '', '01A436', NULL, 8.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(5, 2, 25, '2025-05-12 22:50:31', '2025-05-13 02:50:31', '', 'DF57B2', NULL, 6.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(6, 3, 24, '2025-05-13 22:50:31', '2025-05-14 02:50:31', '', '14AC6C', NULL, 6.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(7, 6, 24, '2025-05-14 22:50:31', '2025-05-15 02:50:31', '', 'AC74BB', NULL, 6.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(8, 2, 15, '2025-05-15 22:50:31', '2025-05-16 02:50:31', '', '5B573D', NULL, 8.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(9, 5, 13, '2025-05-16 22:50:31', '2025-05-17 02:50:31', '', 'CA857E', NULL, 8.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(10, 2, 14, '2025-05-17 22:50:31', '2025-05-18 02:50:31', '', 'D29BF6', NULL, 8.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(11, 2, 22, '2025-05-18 20:50:31', '2025-05-19 00:50:31', '', 'EBA751', NULL, 6.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(12, 4, 1, '2025-05-18 19:50:31', '2025-05-19 01:50:31', '', '10EEE2', NULL, 10.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(13, 2, 10, '2025-05-18 18:50:31', '2025-05-19 02:50:31', '', '136A00', NULL, 12.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(14, 4, 12, '2025-05-19 22:50:31', '2025-05-20 06:50:31', '', '82C530', NULL, 16.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(16, 5, 1, '2025-05-21 22:50:31', '2025-05-22 06:50:31', 'terminee', '08FB7A', NULL, 16.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(17, 4, 14, '2025-05-22 22:50:31', '2025-05-23 06:50:31', 'annulée', NULL, NULL, 0.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(18, 4, 21, '2025-05-23 22:50:31', '2025-05-24 06:50:31', 'terminee', '818558', NULL, 12.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(19, 5, 10, '2025-05-24 22:50:31', '2025-05-25 06:50:31', 'annulée', NULL, NULL, 0.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(20, 5, 19, '2025-05-25 22:50:31', '2025-05-26 06:50:31', 'terminee', '44B79D', NULL, 16.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(21, 5, 1, '2025-05-26 22:50:31', '2025-05-27 06:50:31', 'annulée', NULL, NULL, 0.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(22, 2, 19, '2025-05-27 22:50:31', '2025-05-28 06:50:31', 'terminee', 'A15DBF', NULL, 16.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(23, 2, 22, '2025-05-28 22:50:31', '2025-05-29 06:50:31', 'annulée', NULL, NULL, 0.00, 0.00, 0, '2025-05-18 20:50:31', NULL, NULL, NULL, NULL, NULL),
(25, 7, 2, '2025-05-21 17:27:00', '2025-05-21 19:27:00', 'annulée', NULL, NULL, 0.00, 0.00, 0, '2025-05-21 14:27:51', NULL, NULL, NULL, NULL, NULL),
(26, 7, 2, '2025-05-21 17:35:00', '2025-05-21 19:35:00', 'annulée', NULL, NULL, 0.00, 0.00, 0, '2025-05-21 14:35:55', NULL, NULL, NULL, NULL, NULL),
(27, 7, 24, '2025-05-21 20:55:00', '2025-05-21 22:55:00', 'annulée', NULL, NULL, 0.00, 0.00, 0, '2025-05-21 17:55:49', NULL, NULL, NULL, NULL, NULL),
(28, 7, 35, '2025-06-13 16:00:00', '2025-06-13 17:00:00', 'annulée', '8F1EDF', NULL, 3.00, 0.00, 0, '2025-06-13 13:38:11', NULL, NULL, NULL, NULL, NULL),
(31, 7, 35, '2025-06-13 23:00:00', '2025-06-14 01:00:00', 'annulée', '14D8A2', NULL, 6.00, 0.00, 0, '2025-06-13 20:03:19', NULL, NULL, NULL, NULL, NULL),
(32, 7, 35, '2025-06-14 00:46:00', '2025-06-14 01:46:00', 'annulée', '09743E', NULL, 3.00, 0.00, 0, '2025-06-13 22:37:25', NULL, NULL, NULL, NULL, NULL),
(33, 7, 37, '2025-06-14 00:40:00', '2025-06-14 01:40:00', 'annulée', '7C6163', NULL, 1.50, 0.00, 0, '2025-06-13 22:39:20', NULL, NULL, NULL, NULL, NULL),
(34, 9, 35, '2025-06-14 13:24:00', '2025-06-14 14:24:00', 'annulée', '4D347D', NULL, 3.00, 0.00, 0, '2025-06-14 10:24:36', '2025-06-14 12:39:36', 'Sami LABIDI', 'labidi.neeth@gmail.com', '0663895379', '7f6c3c5ea4f0f74f34f52dc690b7f555'),
(35, 9, 35, '2025-06-14 13:24:00', '2025-06-14 14:24:00', 'annulée', 'F6CFD2', NULL, 3.00, 0.00, 0, '2025-06-14 10:29:04', '2025-06-14 12:44:04', 'Sami LABIDI', 'labidi.neeth@gmail.com', '0663895379', '80ae1178e50f48e2037e3a84e8db7804'),
(36, 10, 35, '2025-06-14 14:30:00', '2025-06-14 15:30:00', 'annulée', 'D65830', NULL, 3.00, 0.00, 0, '2025-06-14 11:25:35', '2025-06-14 13:40:35', NULL, NULL, NULL, NULL),
(37, 7, 35, '2025-06-14 17:00:00', '2025-06-14 18:00:00', 'annulée', '37D798', NULL, 3.00, 0.00, 0, '2025-06-14 14:47:01', '2025-06-14 17:02:01', NULL, NULL, NULL, NULL),
(38, 7, 35, '2025-06-14 21:00:00', '2025-06-14 21:30:00', 'annulée', '4989D4', NULL, 1.50, 0.00, 0, '2025-06-14 18:51:24', '2025-06-14 21:06:24', NULL, NULL, NULL, NULL),
(39, 7, 35, '2025-06-14 23:00:00', '2025-06-15 00:30:00', 'annulée', '270293', NULL, 4.50, 0.00, 0, '2025-06-14 18:59:04', '2025-06-14 21:14:04', NULL, NULL, NULL, NULL),
(40, 7, 37, '2025-06-14 21:00:00', '2025-06-14 21:30:00', 'annulée', 'A1C7E5', NULL, 0.75, 0.00, 0, '2025-06-14 18:59:14', '2025-06-14 21:14:14', NULL, NULL, NULL, NULL),
(41, 7, 35, '2025-06-14 21:45:00', '2025-06-14 22:15:00', 'annulée', '46D2BD', NULL, 1.50, 0.00, 0, '2025-06-14 19:37:02', '2025-06-14 21:52:02', NULL, NULL, NULL, NULL),
(42, 7, 35, '2025-06-14 22:45:00', '2025-06-14 23:15:00', 'annulée', '2E7F07', NULL, 1.50, 0.00, 0, '2025-06-14 19:37:37', '2025-06-14 21:52:37', NULL, NULL, NULL, NULL),
(43, 7, 35, '2025-06-14 22:00:00', '2025-06-14 22:30:00', 'annulée', '7ED0D5', NULL, 1.50, 0.00, 0, '2025-06-14 19:49:07', '2025-06-14 22:04:07', NULL, NULL, NULL, NULL),
(44, 9, 37, '2025-06-15 12:22:00', '2025-06-15 13:22:00', 'annulée', '613865', NULL, 1.50, 0.00, 0, '2025-06-15 10:20:56', '2025-06-15 12:35:56', 'Sami LABIDI', 'labidi.neeth@gmail.com', '0663895379', 'a2e1c41a7b1d0e2d272423ad88058c44'),
(45, 9, 37, '2025-06-15 14:31:00', '2025-06-15 15:31:00', 'annulée', '5EAA3F', NULL, 1.50, 0.00, 0, '2025-06-15 10:31:55', '2025-06-15 12:46:55', 'Sami LABIDI', 'labidi.neeth@gmail.com', '0663895379', '39f0ad5cef1340dccc34dfeab721be35'),
(46, 7, 35, '2025-06-15 14:31:01', NULL, 'annulée', 'A8A444', NULL, 0.00, 0.00, 0, '2025-06-15 12:31:01', NULL, NULL, NULL, NULL, NULL),
(47, 7, 35, '2025-06-15 14:34:20', NULL, 'annulée', 'E3A8BF', NULL, 0.00, 0.00, 0, '2025-06-15 12:34:20', NULL, NULL, NULL, NULL, NULL),
(48, 7, 35, '2025-06-15 14:38:03', NULL, 'annulée', '821E2E', NULL, 0.00, 0.00, 0, '2025-06-15 12:38:03', NULL, NULL, NULL, NULL, NULL),
(49, 7, 35, '2025-06-15 14:41:03', '2025-06-15 14:54:24', 'terminee', '01FE4B', NULL, 0.67, 0.00, 0, '2025-06-15 12:41:03', NULL, NULL, NULL, NULL, NULL),
(50, 7, 35, '2025-06-15 14:57:22', '2025-06-15 14:57:48', 'terminee', '0A25E4', NULL, 0.02, 0.00, 0, '2025-06-15 12:57:22', NULL, NULL, NULL, NULL, NULL),
(51, 7, 35, '2025-06-15 15:09:19', '2025-06-15 15:09:45', 'terminee', '7B78B9', 'FB0D5C15', 0.02, 0.00, 0, '2025-06-15 13:09:19', NULL, NULL, NULL, NULL, NULL),
(52, 7, 35, '2025-06-15 15:10:22', '2025-06-15 15:10:27', 'terminee', '0DF722', '9E473073', 0.00, 0.00, 0, '2025-06-15 13:10:22', NULL, NULL, NULL, NULL, NULL),
(53, 7, 35, '2025-06-15 15:11:52', '2025-06-15 15:12:09', 'terminee', '07919E', '940675CB', 0.01, 0.00, 0, '2025-06-15 13:11:52', NULL, NULL, NULL, NULL, NULL),
(54, 7, 35, '2025-06-15 15:22:06', '2025-06-15 15:22:15', 'terminee', '73D855', '212870C3', 0.01, 0.00, 0, '2025-06-15 13:22:06', NULL, NULL, NULL, NULL, NULL),
(55, 7, 35, '2025-06-15 15:24:05', '2025-06-15 15:24:09', 'terminee', '90BD70', '9F7E1BA4', 0.00, 0.00, 0, '2025-06-15 13:24:05', NULL, NULL, NULL, NULL, NULL),
(56, 7, 35, '2025-06-15 15:34:18', '2025-06-15 15:34:28', 'terminee', 'A748B4', '2A902D4E', 0.01, 0.00, 0, '2025-06-15 13:34:18', NULL, NULL, NULL, NULL, NULL),
(57, 7, 2, '2025-06-15 17:07:43', '2025-06-15 17:07:55', 'terminee', '5DD546', 'B22E13F7', 0.01, 0.00, 0, '2025-06-15 15:07:43', NULL, NULL, NULL, NULL, NULL),
(58, 7, 35, '2025-06-15 17:11:48', '2025-06-15 17:12:09', 'terminee', '27AB52', '64582999', 0.02, 0.00, 0, '2025-06-15 15:11:48', NULL, NULL, NULL, NULL, NULL),
(59, 7, 35, '2025-06-15 17:12:12', '2025-06-15 17:12:25', 'terminee', 'CF509D', '64DD8A77', 0.01, 0.00, 0, '2025-06-15 15:12:12', NULL, NULL, NULL, NULL, NULL),
(60, 7, 35, '2025-06-15 17:30:00', '2025-06-15 18:00:00', 'annulée', 'ABC973', NULL, 1.50, 0.00, 0, '2025-06-15 15:16:09', '2025-06-15 17:31:09', NULL, NULL, NULL, NULL),
(61, 7, 35, '2025-06-15 17:24:44', '2025-06-15 17:34:11', 'annulée', '140F0C', 'CC8EB9DB', 0.47, 0.00, 0, '2025-06-15 15:24:44', NULL, NULL, NULL, NULL, NULL),
(62, 7, 35, '2025-06-15 21:30:00', '2025-06-15 22:00:00', 'annulée', '502CEC', NULL, 1.50, 0.00, 0, '2025-06-15 19:23:52', '2025-06-15 21:38:52', NULL, NULL, NULL, NULL),
(63, 7, 35, '2025-06-15 21:24:24', '2025-06-15 21:24:34', 'annulée', '83ADA2', 'E3C4D39C', 0.01, 0.00, 0, '2025-06-15 19:24:24', NULL, NULL, NULL, NULL, NULL),
(64, 7, 35, '2025-06-15 22:51:53', '2025-06-15 22:55:05', 'annulée', 'E409B2', '2951E1BE', 0.16, 0.00, 0, '2025-06-15 20:51:53', NULL, NULL, NULL, NULL, NULL),
(65, 7, 35, '2025-06-16 00:32:00', '2025-06-16 03:02:00', 'annulée', '6FD49F', NULL, 7.50, 0.00, 0, '2025-06-15 21:32:21', '2025-06-15 23:47:21', NULL, NULL, NULL, NULL),
(66, 7, 35, '2025-06-15 23:32:39', '2025-06-15 23:47:14', 'annulée', 'C69D2C', '0DA90F26', 0.73, 0.00, 0, '2025-06-15 21:32:39', NULL, NULL, NULL, NULL, NULL),
(67, 7, 35, '2025-06-15 23:49:54', '2025-06-15 23:50:53', 'annulée', '82FB6C', 'FEDC945B', 0.05, 0.00, 0, '2025-06-15 21:49:54', NULL, NULL, NULL, NULL, NULL),
(68, 7, 37, '2025-06-16 00:40:42', '2025-06-16 00:41:00', 'annulée', 'ABA8C8', 'F8EA9E6F', 0.01, 0.00, 0, '2025-06-15 22:40:42', NULL, NULL, NULL, NULL, NULL),
(69, 7, 37, '2025-06-16 00:44:02', '2025-06-16 00:44:06', 'annulée', '804783', '8EAC4416', 0.00, 0.00, 0, '2025-06-15 22:44:02', NULL, NULL, NULL, NULL, NULL),
(70, 7, 37, '2025-06-16 00:44:14', '2025-06-16 00:44:32', 'annulée', 'B4C62F', 'CC253A90', 0.01, 0.00, 0, '2025-06-15 22:44:14', NULL, NULL, NULL, NULL, NULL),
(71, 7, 37, '2025-06-16 01:10:43', '2025-06-16 01:10:47', 'annulée', 'CCB411', '5BAD7DAD', 0.00, 0.00, 0, '2025-06-15 23:10:43', NULL, NULL, NULL, NULL, NULL),
(72, 7, 37, '2025-06-16 01:43:00', '2025-06-16 02:13:00', 'annulée', '644ED0', NULL, 0.75, 0.00, 0, '2025-06-15 23:42:15', '2025-06-16 01:57:15', NULL, NULL, NULL, NULL),
(73, 7, 35, '2025-06-16 14:15:15', '2025-06-16 14:15:24', 'terminee', '854DE7', '069164DE', 0.01, 0.00, 0, '2025-06-16 12:15:15', NULL, NULL, NULL, NULL, NULL),
(74, 7, 35, '2025-06-16 14:15:48', '2025-06-16 14:16:00', 'terminee', '4B32EF', 'F3A3D1C6', 0.01, 0.00, 0, '2025-06-16 12:15:48', NULL, NULL, NULL, NULL, NULL),
(75, 7, 35, '2025-06-16 14:49:51', '2025-06-16 14:52:27', 'terminee', '984A7F', 'E8FEB760', 0.13, 0.00, 0, '2025-06-16 12:49:51', NULL, NULL, NULL, NULL, NULL),
(76, 7, 35, '2025-06-16 14:54:14', '2025-06-16 14:54:57', 'terminee', '403749', '18CA2001', 0.04, 0.00, 0, '2025-06-16 12:54:14', NULL, NULL, NULL, NULL, NULL),
(77, 7, 35, '2025-06-16 14:57:26', '2025-06-16 14:57:37', 'terminee', '3C70E6', '5275DA99', 0.01, 0.00, 0, '2025-06-16 12:57:26', NULL, NULL, NULL, NULL, NULL),
(78, 7, 35, '2025-06-16 15:23:55', '2025-06-16 15:24:08', 'terminee', 'EE702B', '869938CB', 0.01, 0.00, 0, '2025-06-16 13:23:55', NULL, NULL, NULL, NULL, NULL),
(79, 7, 35, '2025-06-16 15:25:31', '2025-06-16 15:25:33', 'terminee', '5D03EF', '308690BF', 0.00, 0.00, 0, '2025-06-16 13:25:31', NULL, NULL, NULL, NULL, NULL),
(80, 7, 35, '2025-06-16 15:25:40', '2025-06-16 15:25:42', 'terminee', '152295', '6AC6A5A8', 0.00, 0.00, 0, '2025-06-16 13:25:40', NULL, NULL, NULL, NULL, NULL),
(81, 7, 35, '2025-06-16 15:34:18', '2025-06-16 15:34:22', 'terminee', 'EC2AE9', '903B83BF', 0.00, 0.00, 0, '2025-06-16 13:34:18', NULL, NULL, NULL, NULL, NULL),
(82, 7, 35, '2025-06-16 15:54:46', '2025-06-16 15:54:49', 'terminee', '16B124', 'B5F9E039', 0.00, 0.00, 0, '2025-06-16 13:54:46', NULL, NULL, NULL, NULL, NULL),
(83, 7, 37, '2025-06-16 17:12:11', '2025-06-16 17:12:16', 'terminee', 'F64388', '8CB50A3C', 0.00, 0.00, 0, '2025-06-16 15:12:11', NULL, NULL, NULL, NULL, NULL),
(84, 7, 35, '2025-06-16 17:31:17', '2025-06-16 17:31:51', 'terminee', '4A7445', 'ED4FE161', 0.03, 0.00, 0, '2025-06-16 15:31:17', NULL, NULL, NULL, NULL, NULL),
(85, 7, 35, '2025-06-16 17:31:58', '2025-06-16 17:32:12', 'terminee', 'A70E59', 'E8E82D55', 0.01, 0.00, 0, '2025-06-16 15:31:58', NULL, NULL, NULL, NULL, NULL),
(86, 7, 35, '2025-06-16 17:32:34', '2025-06-16 17:32:54', 'terminee', '2E0B3B', 'F2BADB0E', 0.02, 0.00, 0, '2025-06-16 15:32:34', NULL, NULL, NULL, NULL, NULL),
(87, 7, 37, '2025-06-16 17:33:00', '2025-06-16 17:33:30', 'terminee', '9F54D8', 'D28C1DD6', 0.01, 0.00, 0, '2025-06-16 15:33:00', NULL, NULL, NULL, NULL, NULL),
(88, 7, 35, '2025-06-16 17:39:51', '2025-06-16 17:40:46', 'terminee', 'C0D23D', '07D2313F', 0.05, 0.00, 0, '2025-06-16 15:39:51', NULL, NULL, NULL, NULL, NULL),
(89, 7, 35, '2025-06-16 17:41:09', '2025-06-16 17:41:45', 'terminee', '8A862E', '1B532070', 0.03, 0.00, 0, '2025-06-16 15:41:09', NULL, NULL, NULL, NULL, NULL),
(90, 7, 35, '2025-06-16 17:43:53', '2025-06-16 18:30:49', 'terminee', '3C31A9', 'E1311A04', 2.35, 0.00, 0, '2025-06-16 15:43:53', NULL, NULL, NULL, NULL, NULL),
(91, 7, 35, '2025-06-16 18:35:23', '2025-06-16 18:37:31', 'terminee', '7D6E98', '3BCC2E18', 0.11, 0.00, 0, '2025-06-16 16:35:23', NULL, NULL, NULL, NULL, NULL),
(92, 7, 35, '2025-06-16 18:43:54', '2025-06-16 18:48:41', 'terminee', 'B3F347', 'EFB2ABE5', 0.24, 0.00, 0, '2025-06-16 16:43:54', NULL, NULL, NULL, NULL, NULL),
(93, 7, 2, '2025-06-16 18:51:45', '2025-06-16 18:51:57', 'terminee', '8418D4', '6A74DD03', 0.01, 0.00, 0, '2025-06-16 16:51:45', NULL, NULL, NULL, NULL, NULL),
(94, 7, 35, '2025-06-16 19:59:00', '2025-06-16 20:29:00', 'annulée', 'F656CD', NULL, 1.50, 0.00, 0, '2025-06-16 16:59:19', '2025-06-16 19:14:19', NULL, NULL, NULL, NULL),
(95, 7, 35, '2025-06-16 19:07:00', '2025-06-16 19:37:00', 'annulée', 'E6EF57', NULL, 1.50, 0.00, 0, '2025-06-16 17:01:15', '2025-06-16 19:16:15', NULL, NULL, NULL, NULL),
(96, 7, 35, '2025-06-16 19:04:00', '2025-06-16 19:34:00', 'terminee', '78A687', NULL, 1.50, 0.00, 0, '2025-06-16 17:01:31', '2025-06-16 19:16:31', NULL, NULL, NULL, NULL),
(97, 7, 37, '2025-06-16 19:04:57', '2025-06-16 19:05:11', 'annulée', 'F64FEB', 'C695626B', 0.01, 0.00, 0, '2025-06-16 17:04:57', NULL, NULL, NULL, NULL, NULL),
(98, 7, 35, '2025-06-16 20:08:00', '2025-06-16 20:38:00', 'annulée', 'BC5A61', NULL, 1.50, 0.00, 0, '2025-06-16 17:08:33', '2025-06-16 19:23:33', NULL, NULL, NULL, NULL),
(99, 7, 35, '2025-06-16 19:38:00', '2025-06-16 20:08:00', 'annulée', '723825', NULL, 1.50, 0.00, 0, '2025-06-16 17:37:05', '2025-06-16 19:52:05', NULL, NULL, NULL, NULL),
(100, 7, 35, '2025-06-16 19:41:14', '2025-06-16 19:41:37', 'terminee', 'DB0F0B', 'CE1D1957', 0.02, 0.00, 0, '2025-06-16 17:41:14', NULL, NULL, NULL, NULL, NULL),
(101, 7, 35, '2025-06-16 19:51:00', '2025-06-16 20:21:00', 'annulée', '22B5B7', NULL, 1.50, 0.00, 0, '2025-06-16 17:49:12', '2025-06-16 20:04:12', NULL, NULL, NULL, NULL),
(102, 12, 35, '2025-06-16 20:25:00', '2025-06-16 20:55:00', 'terminee', 'AB3669', NULL, 1.50, 0.00, 0, '2025-06-16 18:24:10', '2025-06-16 20:39:10', NULL, NULL, NULL, NULL),
(103, 12, 38, '2025-06-16 20:24:25', '2025-06-16 20:24:36', 'terminee', 'FDCB31', 'A07B80C5', 0.00, 0.00, 0, '2025-06-16 18:24:25', NULL, NULL, NULL, NULL, NULL),
(104, 12, 35, '2025-06-16 20:24:40', '2025-06-16 20:25:20', 'terminee', '5DDB9A', 'A9F8EBC5', 0.03, 0.00, 0, '2025-06-16 18:24:40', NULL, NULL, NULL, NULL, NULL),
(105, 7, 35, '2025-06-16 22:00:00', '2025-06-16 22:30:00', 'annulée', '2E03F6', NULL, 1.50, 0.00, 0, '2025-06-16 19:58:43', '2025-06-16 22:13:43', NULL, NULL, NULL, NULL),
(106, 7, 35, '2025-06-16 22:36:43', '2025-06-16 22:36:53', 'terminee', 'A78CA5', '9432BD03', 0.01, 0.00, 0, '2025-06-16 20:36:43', NULL, NULL, NULL, NULL, NULL),
(107, 7, 35, '2025-06-17 10:45:00', '2025-06-17 11:15:00', 'annulée', '6D7C62', NULL, 1.50, 0.00, 0, '2025-06-17 08:42:23', '2025-06-17 10:57:23', NULL, NULL, NULL, NULL),
(108, 7, 35, '2025-06-17 12:08:53', '2025-06-17 12:08:58', 'terminee', '9B12CB', '6790EC8A', 0.00, 0.00, 0, '2025-06-17 10:08:53', NULL, NULL, NULL, NULL, NULL),
(109, 7, 35, '2025-06-17 12:11:00', '2025-06-17 12:41:00', 'annulée', 'C5060D', NULL, 1.50, 0.00, 0, '2025-06-17 10:09:18', '2025-06-17 12:24:18', NULL, NULL, NULL, NULL),
(110, 7, 35, '2025-06-17 16:19:00', '2025-06-17 16:49:00', 'terminee', 'C7F38D', NULL, 1.50, 0.00, 0, '2025-06-17 14:17:02', '2025-06-17 16:32:02', NULL, NULL, NULL, NULL),
(111, 7, 35, '2025-06-17 16:17:12', '2025-06-17 16:17:26', 'terminee', '4C7C92', '2CD65EA4', 0.01, 0.00, 0, '2025-06-17 14:17:12', NULL, NULL, NULL, NULL, NULL),
(112, 7, 35, '2025-06-17 18:39:04', '2025-06-17 18:50:36', 'terminee', '8B2F6E', '058AB9A4', 0.58, 0.00, 0, '2025-06-17 16:39:04', NULL, NULL, NULL, NULL, NULL),
(113, 7, 35, '2025-06-18 13:01:42', '2025-06-18 13:01:46', 'terminee', '1E5325', 'BD7D185B', 0.00, 0.00, 0, '2025-06-18 11:01:42', NULL, NULL, NULL, NULL, NULL),
(114, 7, 35, '2025-06-18 13:15:17', '2025-06-18 13:15:26', 'terminee', '93604B', '3EBFAD14', 0.01, 0.00, 0, '2025-06-18 11:15:17', NULL, NULL, NULL, NULL, NULL),
(115, 7, 35, '2025-06-18 14:26:08', '2025-06-18 14:26:14', 'terminee', '9154D5', 'A83B93B2', 0.01, 0.00, 0, '2025-06-18 12:26:08', NULL, NULL, NULL, NULL, NULL),
(116, 7, 35, '2025-06-18 20:34:20', '2025-06-18 20:34:46', 'terminee', '9D6AF9', '4092F7A3', 0.02, 0.00, 0, '2025-06-18 18:34:20', NULL, NULL, NULL, NULL, NULL),
(117, 7, 47, '2025-06-18 22:58:00', '2025-06-18 23:28:00', 'terminee', '2F27FD', NULL, 0.45, 0.00, 0, '2025-06-18 20:55:33', '2025-06-18 23:10:33', NULL, NULL, NULL, NULL),
(118, 7, 47, '2025-06-19 22:56:00', '2025-06-19 23:26:00', 'annulée', 'A94BA2', NULL, 0.45, 0.00, 0, '2025-06-18 20:57:16', '2025-06-18 23:12:16', NULL, NULL, NULL, NULL),
(119, 7, 47, '2025-06-18 22:57:25', '2025-06-18 23:03:02', 'terminee', 'D7F4FD', 'EFD03F7E', 0.08, 0.00, 0, '2025-06-18 20:57:25', NULL, NULL, NULL, NULL, NULL),
(120, 7, 38, '2025-06-19 13:07:13', '2025-06-19 13:07:17', 'terminee', '54CADB', '2DC66593', 0.00, 0.00, 0, '2025-06-19 11:07:13', NULL, NULL, NULL, NULL, NULL),
(121, 7, 35, '2025-06-19 13:22:15', '2025-06-19 13:22:23', 'terminee', 'E941C7', '5F96361A', 0.00, 0.00, 0, '2025-06-19 11:22:15', NULL, NULL, NULL, NULL, NULL),
(122, 7, 35, '2025-06-19 13:42:00', '2025-06-19 14:12:00', 'terminee', 'B994C3', NULL, 0.64, 0.00, 0, '2025-06-19 11:40:59', '2025-06-19 13:55:59', NULL, NULL, NULL, NULL),
(123, 7, 47, '2025-06-19 14:19:00', '2025-06-19 14:49:00', 'terminee', '8C504F', NULL, 0.19, 0.00, 0, '2025-06-19 12:16:43', '2025-06-19 14:31:43', NULL, NULL, NULL, NULL),
(124, 7, 35, '2025-06-19 17:49:00', '2025-06-19 18:19:00', 'annulée', '87657F', NULL, 0.64, 0.00, 0, '2025-06-19 15:47:41', '2025-06-19 18:02:41', NULL, NULL, NULL, NULL),
(125, 7, 47, '2025-06-19 17:50:00', '2025-06-19 18:20:00', 'annulée', '70E83C', NULL, 0.19, 0.00, 0, '2025-06-19 15:48:14', '2025-06-19 18:03:14', NULL, NULL, NULL, NULL),
(126, 7, 35, '2025-06-19 19:46:00', '2025-06-19 20:16:00', 'annulée', '2BFA2B', NULL, 1.50, 0.00, 0, '2025-06-19 17:44:38', '2025-06-19 19:59:38', NULL, NULL, NULL, NULL),
(127, 7, 35, '2025-06-19 23:53:00', '2025-06-20 00:23:00', 'annulée', '27E99E', NULL, 1.50, 0.00, 0, '2025-06-19 17:53:10', '2025-06-19 20:08:10', NULL, NULL, NULL, NULL),
(128, 7, 47, '2025-06-19 20:04:00', '2025-06-19 20:34:00', 'annulée', '56382C', NULL, 0.45, 0.00, 0, '2025-06-19 18:01:58', '2025-06-19 20:16:58', NULL, NULL, NULL, NULL),
(129, 7, 35, '2025-06-19 20:25:00', '2025-06-19 20:55:00', 'annulée', 'B7D83D', NULL, 1.50, 0.00, 0, '2025-06-19 18:22:50', '2025-06-19 20:37:50', NULL, NULL, NULL, NULL),
(130, 7, 47, '2025-06-19 20:23:58', '2025-06-19 20:43:20', 'terminee', NULL, '491DB1A2', 0.20, 0.00, 0, '2025-06-19 18:23:58', NULL, NULL, NULL, NULL, NULL),
(131, 7, 35, '2025-06-19 20:35:00', '2025-06-19 21:05:00', 'terminee', '91C986', NULL, 1.50, 0.00, 0, '2025-06-19 18:32:51', '2025-06-19 20:47:51', NULL, NULL, NULL, NULL),
(132, 7, 35, '2025-06-20 20:39:00', '2025-06-20 21:09:00', 'annulée', 'DCF874', NULL, 1.19, 0.00, 0, '2025-06-19 18:37:39', '2025-06-19 20:52:39', NULL, NULL, NULL, NULL),
(133, 7, 47, '2025-06-20 01:53:00', '2025-06-20 02:23:00', 'terminee', 'CDD3CA', NULL, 0.00, 0.00, 0, '2025-06-19 23:51:12', '2025-06-20 02:06:12', NULL, NULL, NULL, NULL),
(134, 7, 38, '2025-06-20 01:51:24', '2025-06-20 01:54:27', 'terminee', NULL, '80243D39', 0.00, 0.00, 0, '2025-06-19 23:51:24', NULL, NULL, NULL, NULL, NULL),
(135, 7, 38, '2025-06-20 02:00:44', '2025-06-20 02:04:37', 'terminee', NULL, '279204E9', 0.00, 0.00, 0, '2025-06-20 00:00:44', NULL, NULL, NULL, NULL, NULL),
(136, 7, 38, '2025-06-20 02:13:00', '2025-06-20 02:43:00', 'terminee', '95FA93', NULL, 0.32, 0.00, 0, '2025-06-20 00:08:29', '2025-06-20 02:23:29', NULL, NULL, NULL, NULL),
(139, 2, 1, '2025-06-20 02:25:50', '2025-06-20 03:41:42', '', 'TST252', '3DE2DF12', 3.15, 0.00, 0, '2025-06-20 00:33:50', NULL, NULL, NULL, NULL, NULL),
(140, 7, 35, '2025-06-20 02:56:51', '2025-06-20 03:11:25', 'terminee', NULL, '1C79D004', 0.00, 0.00, 0, '2025-06-20 00:56:51', NULL, NULL, NULL, NULL, NULL),
(141, 7, 35, '2025-06-20 03:11:34', '2025-06-20 03:21:59', 'terminee', NULL, '6664399F', 0.00, 0.00, 0, '2025-06-20 01:11:34', NULL, NULL, NULL, NULL, NULL),
(142, 7, 47, '2025-06-20 03:27:15', '2025-06-20 03:28:22', 'terminee', NULL, 'AC04BCBA', 0.00, 0.00, 0, '2025-06-20 01:27:15', NULL, NULL, NULL, NULL, NULL),
(143, 7, 35, '2025-06-20 03:30:31', '2025-06-20 03:33:37', '', NULL, '13997E0D', 0.64, 0.00, 0, '2025-06-20 01:30:31', NULL, NULL, NULL, NULL, NULL),
(144, 7, 38, '2025-06-20 03:36:07', '2025-06-20 03:36:29', 'terminee', NULL, '179AF285', 0.32, 0.00, 0, '2025-06-20 01:36:07', NULL, NULL, NULL, NULL, NULL),
(146, 2, 1, '2025-06-20 03:15:08', '2025-06-20 03:45:08', 'en_attente_paiement', 'F3B0F72B', 'B4EEF637', 1.25, 0.00, 0, '2025-06-20 01:45:08', NULL, NULL, NULL, NULL, NULL),
(147, 7, 35, '2025-06-20 03:46:18', '2025-06-20 03:46:26', 'terminee', NULL, '9CCF91B1', 0.64, 0.00, 0, '2025-06-20 01:46:18', NULL, NULL, NULL, NULL, NULL),
(148, 7, 38, '2025-06-20 03:47:10', '2025-06-20 03:49:47', 'en_attente_paiement', NULL, 'B8439112', 0.32, 0.00, 0, '2025-06-20 01:47:10', NULL, NULL, NULL, NULL, NULL),
(149, 2, 1, '2025-06-20 03:37:38', '2025-06-20 03:47:38', 'en_attente_paiement', '4441D15C', '8C535BF6', 0.62, 0.00, 0, '2025-06-20 01:47:38', NULL, NULL, NULL, NULL, NULL),
(150, 2, 1, '2025-06-20 03:44:04', '2025-06-20 03:51:33', 'en_attente_paiement', 'C451E6A5', 'C42C3AE1', 0.62, 0.00, 0, '2025-06-20 01:49:04', NULL, NULL, NULL, NULL, NULL),
(151, 7, 47, '2025-06-20 03:53:00', '2025-06-20 04:23:00', 'terminee', '9BAEC6', NULL, 0.19, 0.00, 0, '2025-06-20 01:52:00', '2025-06-20 04:07:00', NULL, NULL, NULL, NULL),
(152, 7, 35, '2025-06-20 03:52:30', '2025-06-20 03:52:37', 'en_attente_paiement', 'B43274', 'B3FDFF4D', 0.64, 0.00, 0, '2025-06-20 01:52:30', NULL, NULL, NULL, NULL, NULL),
(153, 2, 1, '2025-06-20 03:49:45', NULL, 'terminee', '9EEA3236', '8B6425BB', 0.00, 0.00, 0, '2025-06-20 01:52:45', NULL, NULL, NULL, NULL, NULL),
(154, 2, 1, '2025-06-20 03:51:12', NULL, 'terminee', '7EBC0C39', '6453ED35', 0.00, 0.00, 0, '2025-06-20 01:54:12', NULL, NULL, NULL, NULL, NULL),
(155, 7, 35, '2025-06-20 03:54:41', '2025-06-20 03:54:45', 'terminee', '92D84F', 'BC88449D', 0.64, 0.00, 0, '2025-06-20 01:54:41', NULL, NULL, NULL, NULL, NULL),
(156, 7, 35, '2025-06-20 03:58:47', '2025-06-20 03:59:26', 'en_attente_paiement', NULL, 'FAC0BB34', 0.64, 0.00, 0, '2025-06-20 01:58:47', NULL, NULL, NULL, NULL, NULL),
(158, 7, 47, '2025-06-21 19:26:00', '2025-06-21 19:56:00', 'annulée', '961254', NULL, 0.19, 0.00, 0, '2025-06-21 16:27:05', '2025-06-21 18:42:05', NULL, NULL, NULL, NULL),
(159, 7, 47, '2025-06-21 20:28:00', '2025-06-21 20:58:00', 'annulée', '7B5B51', NULL, 0.19, 0.00, 0, '2025-06-21 16:28:33', '2025-06-21 18:43:33', NULL, NULL, NULL, NULL),
(161, 7, 47, '2025-06-21 19:30:00', '2025-06-21 20:00:00', 'annulée', '10C6FA', NULL, 0.19, 0.00, 0, '2025-06-21 16:30:24', '2025-06-21 18:45:24', NULL, NULL, NULL, NULL),
(162, 7, 47, '2025-06-21 21:31:00', '2025-06-21 22:01:00', 'annulée', '4469BA', NULL, 0.19, 0.00, 0, '2025-06-21 16:31:17', '2025-06-21 18:46:17', NULL, NULL, NULL, NULL),
(163, 7, 47, '2025-06-21 22:32:00', '2025-06-21 23:02:00', 'annulée', '97EF2E', NULL, 0.19, 0.00, 0, '2025-06-21 16:32:07', '2025-06-21 18:47:07', NULL, NULL, NULL, NULL),
(164, 7, 47, '2025-06-21 23:34:00', '2025-06-22 00:04:00', 'confirmée', 'ACBBA1', NULL, 0.19, 0.00, 0, '2025-06-21 16:34:12', '2025-06-21 18:49:12', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `tarifs`
--

CREATE TABLE `tarifs` (
  `id` int(11) NOT NULL,
  `type_place` varchar(50) NOT NULL DEFAULT 'standard',
  `free_minutes` int(11) NOT NULL DEFAULT 0,
  `prix_heure` decimal(10,2) NOT NULL,
  `prix_journee` decimal(10,2) NOT NULL,
  `prix_mois` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tarifs`
--

INSERT INTO `tarifs` (`id`, `type_place`, `free_minutes`, `prix_heure`, `prix_journee`, `prix_mois`) VALUES
(1, 'standard', 0, 2.49, 27.00, 250.00),
(2, 'handicape', 15, 1.50, 15.00, 150.00),
(3, 'electrique', 15, 3.00, 25.00, 250.00),
(6, 'velo', 0, 0.48, 5.00, 30.00),
(11, 'moto/scooter', 0, 0.90, 21.60, 669.60),
(15, '101', 0, 0.01, 0.01, 0.01);

-- --------------------------------------------------------

--
-- Structure de la table `tarifs_historique`
--

CREATE TABLE `tarifs_historique` (
  `id` int(11) NOT NULL,
  `tarif_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `type_place` varchar(50) NOT NULL,
  `champ_modifie` varchar(50) NOT NULL,
  `ancien_prix` decimal(10,2) DEFAULT NULL,
  `nouveau_prix` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tarifs_historique`
--

INSERT INTO `tarifs_historique` (`id`, `tarif_id`, `admin_id`, `type_place`, `champ_modifie`, `ancien_prix`, `nouveau_prix`, `created_at`) VALUES
(1, 4, 7, 'velo', 'prix_journee', 5.00, 2.30, '2025-06-16 23:50:13'),
(2, 4, 7, 'velo', 'prix_mois', 30.00, 25.00, '2025-06-16 23:50:13'),
(3, 5, 7, 'velo', 'type_place', 0.00, 0.00, '2025-06-17 00:13:48'),
(4, 6, 7, 'velo', 'prix_heure', 0.50, 0.48, '2025-06-17 00:18:45');

-- --------------------------------------------------------

--
-- Structure de la table `tarif_historique`
--

CREATE TABLE `tarif_historique` (
  `id` int(11) NOT NULL,
  `tarif_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `type_place` varchar(50) NOT NULL,
  `champ_modifie` enum('type_place','prix_heure','prix_journee','prix_mois','free_minutes') NOT NULL,
  `ancien_prix` decimal(10,2) DEFAULT 0.00,
  `nouveau_prix` decimal(10,2) DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `is_subscribed` tinyint(1) NOT NULL DEFAULT 0,
  `notifications_active` tinyint(1) DEFAULT 1,
  `status` enum('actif','inactif') NOT NULL DEFAULT 'actif',
  `payment_preferences` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `telephone`, `password`, `nom`, `prenom`, `role`, `is_subscribed`, `notifications_active`, `status`, `payment_preferences`, `created_at`) VALUES
(2, 'jean.dupont@example.com', '0612345678', '$2y$10$jC8wYduNNQfwQwXdqoL09euiCLkgyNXQJYiHBN7CV1fcA4Vq8tyHi', 'Jean', 'Dupont', 'user', 0, 1, 'inactif', NULL, '2025-05-18 20:50:31'),
(3, 'marie.martin@example.com', '0687654321', '$2y$10$jC8wYduNNQfwQwXdqoL09euiCLkgyNXQJYiHBN7CV1fcA4Vq8tyHi', 'Marie', 'Martin', 'user', 0, 1, 'actif', NULL, '2025-05-18 20:50:31'),
(4, 'pierre.durand@example.com', '0698765432', '$2y$10$jC8wYduNNQfwQwXdqoL09euiCLkgyNXQJYiHBN7CV1fcA4Vq8tyHi', 'Pierre', 'Durand', 'user', 0, 1, 'inactif', NULL, '2025-05-18 20:50:31'),
(5, 'sophie.lefebvre@example.com', '0654321098', '$2y$10$jC8wYduNNQfwQwXdqoL09euiCLkgyNXQJYiHBN7CV1fcA4Vq8tyHi', 'Sophie', 'Lefebvre', 'user', 0, 1, 'actif', NULL, '2025-05-18 20:50:31'),
(6, 'thomas.moreau@example.com', '0678901234', '$2y$10$jC8wYduNNQfwQwXdqoL09euiCLkgyNXQJYiHBN7CV1fcA4Vq8tyHi', 'Thomas', 'Moreau', 'user', 0, 1, 'actif', NULL, '2025-05-18 20:50:31'),
(7, 'sasa@gmail.com', '0663895379', '$2y$10$94cu8Yv6DsJjrqfp6cFbIOzBuVhvU.ES6/gxDTNlcoSW3uKBwC51m', 'LABIDI', 'Sami', 'admin', 1, 1, 'actif', NULL, '2025-05-21 14:16:32'),
(9, 'guest@parkme.in', '', 'NO_LOGIN', 'Guestd', 'User', 'user', 0, 0, 'actif', NULL, '2025-06-13 18:10:23'),
(10, 'labidi.neeth@gmail.com', '0663895379', '$2y$10$AXcj1Or4dHOXtZKKV7kIGumuzZXrquL3av.yt/twa0bvxTYe66rp2', 'LABIDI', 'Sami', 'user', 0, 1, 'actif', NULL, '2025-06-14 10:25:39'),
(12, 'labidi.neeth45@gmail.com', '0663895379', '$2y$10$RxBf7XAptnckbeLD0VGbGOU056KxHQBgpPEmibdDg5C0xoI.VtgwG', 'LABIDI', 'Sami', 'user', 0, 1, 'actif', NULL, '2025-06-16 18:23:45'),
(13, 'test@example.com', '0123456789', '$2y$10$4xWdGTgEFyvm.FznwPuRAuKuhzoLU.bXIF9wYAG/YqVev8WFoTjEe', 'Test', 'User', 'user', 0, 1, 'actif', NULL, '2025-06-20 01:19:20');

-- --------------------------------------------------------

--
-- Structure de la table `user_abonnements`
--

CREATE TABLE `user_abonnements` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `abonnement_id` int(11) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `status` varchar(20) DEFAULT 'actif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_abonnements`
--

INSERT INTO `user_abonnements` (`id`, `user_id`, `abonnement_id`, `date_debut`, `date_fin`, `status`, `created_at`, `payment_id`) VALUES
(1, 7, 1, '2025-06-18 20:25:00', '2025-06-25 20:25:00', 'résilié', '2025-06-18 18:25:00', NULL),
(2, 7, 3, '2025-06-18 20:41:13', '2026-06-18 20:41:13', 'résilié', '2025-06-18 18:41:13', NULL),
(3, 7, 3, '2025-06-18 21:36:35', '2026-06-18 21:36:35', 'résilié', '2025-06-18 19:36:35', NULL),
(4, 7, 3, '2025-06-18 22:58:32', '2026-06-18 22:58:32', 'résilié', '2025-06-18 20:58:32', NULL),
(5, 7, 2, '2025-06-18 23:27:01', '2025-07-18 23:27:01', 'résilié', '2025-06-18 21:27:01', NULL),
(6, 7, 1, '2025-06-19 02:18:33', '2025-06-26 02:18:33', 'résilié', '2025-06-19 00:18:33', NULL),
(7, 7, 2, '2025-06-19 02:18:56', '2025-07-19 02:18:56', 'résilié', '2025-06-19 00:18:56', NULL),
(8, 7, 2, '2025-06-19 13:03:17', '2025-07-19 13:03:17', 'résilié', '2025-06-19 11:03:17', NULL),
(9, 7, 1, '2025-06-19 20:33:27', '2025-06-26 20:33:27', 'résilié', '2025-06-19 18:33:27', NULL),
(10, 7, 3, '2025-06-19 20:50:21', '2026-06-19 20:50:21', 'résilié', '2025-06-19 18:50:21', NULL),
(11, 7, 2, '2025-06-20 01:54:16', '2025-07-20 01:54:16', 'actif', '2025-06-19 23:54:16', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `abonnements`
--
ALTER TABLE `abonnements`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `alertes_disponibilite`
--
ALTER TABLE `alertes_disponibilite`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `place_id` (`place_id`);

--
-- Index pour la table `availability_alerts`
--
ALTER TABLE `availability_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `place_id` (`place_id`);

--
-- Index pour la table `factures`
--
ALTER TABLE `factures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_facture` (`numero_facture`),
  ADD KEY `paiement_id` (`paiement_id`);

--
-- Index pour la table `horaires_ouverture`
--
ALTER TABLE `horaires_ouverture`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Index pour la table `parking_spaces`
--
ALTER TABLE `parking_spaces`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`);

--
-- Index pour la table `remboursements`
--
ALTER TABLE `remboursements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paiement_id` (`paiement_id`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `guest_token` (`guest_token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `place_id` (`place_id`);

--
-- Index pour la table `tarifs`
--
ALTER TABLE `tarifs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `tarifs_historique`
--
ALTER TABLE `tarifs_historique`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `tarif_historique`
--
ALTER TABLE `tarif_historique`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tarif_id` (`tarif_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `abonnements`
--
ALTER TABLE `abonnements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `alertes_disponibilite`
--
ALTER TABLE `alertes_disponibilite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `availability_alerts`
--
ALTER TABLE `availability_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `factures`
--
ALTER TABLE `factures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT pour la table `horaires_ouverture`
--
ALTER TABLE `horaires_ouverture`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=338;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT pour la table `paiements`
--
ALTER TABLE `paiements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT pour la table `parking_spaces`
--
ALTER TABLE `parking_spaces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT pour la table `remboursements`
--
ALTER TABLE `remboursements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT pour la table `tarifs`
--
ALTER TABLE `tarifs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `tarifs_historique`
--
ALTER TABLE `tarifs_historique`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `tarif_historique`
--
ALTER TABLE `tarif_historique`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
