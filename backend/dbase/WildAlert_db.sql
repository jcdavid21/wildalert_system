-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 07, 2025 at 09:40 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `WildAlert_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_account`
--

CREATE TABLE `tbl_account` (
  `acc_id` int(11) NOT NULL,
  `email` varchar(70) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_account`
--

INSERT INTO `tbl_account` (`acc_id`, `email`, `password`, `role_id`) VALUES
(1, 'jcdavid@gmail.com', '$2y$10$QzEm/PHvgnjaNbQVEoMIsuBseBqVOIxZiVIHmd/woROtFRykcIuOO', 1),
(2, 'ranger@wildalert.org', '$2y$10$SFE/5z/GlwL2ythvA7pDBOyh/bJhJoxPMqc3GMd99kcQ18qu2ljFS', 1),
(3, 'scientist@gmail.com', '$2y$10$XT4pBEG90nfPbR9i96EYsO4CC.HjdjUd9n1CUeQVfgrnu/oz2P4XG', 1),
(4, 'volunteer@gmail.com', '$2y$10$7jj7RvaaZJh.Ji/H95kVxugE3gFNWYovAhEjTVlgskuFRCcHJd50m', 1),
(5, 'admin@gmail.com', '$2y$10$TOC6wLSvOX9akwa8BPGTlOtspXBOiPbCutLa5RiSXNTw5hEqMrU2y', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_audit_trail`
--

CREATE TABLE `tbl_audit_trail` (
  `trail_id` int(11) NOT NULL,
  `trail_username` varchar(50) NOT NULL,
  `trail_activity` varchar(100) NOT NULL,
  `trail_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_audit_trail`
--

INSERT INTO `tbl_audit_trail` (`trail_id`, `trail_username`, `trail_activity`, `trail_date`) VALUES
(10, 'admin@gmail.com', 'Added new species: Waling-waling Orchid (uploaded) (ID: 16)', '2025-05-07 18:04:18'),
(11, 'admin@gmail.com', 'Deleted species ID: 16', '2025-05-07 18:05:41'),
(12, 'admin@gmail.com', 'Updated report #1 status to Surveillance', '2025-05-07 18:57:30'),
(13, 'admin@gmail.com', 'Viewed audit trail page', '2025-05-07 13:19:28'),
(14, 'admin@gmail.com', 'Viewed audit trail page', '2025-05-07 13:20:59'),
(15, 'admin@gmail.com', 'Viewed audit trail page', '2025-05-07 13:21:01'),
(16, 'admin@gmail.com', 'Viewed audit trail page', '2025-05-07 13:21:05'),
(17, 'admin@gmail.com', 'Viewed audit trail page', '2025-05-07 13:21:27'),
(18, 'admin@gmail.com', 'Viewed audit trail page', '2025-05-07 13:21:47'),
(19, 'admin@gmail.com', 'Viewed audit trail page', '2025-05-07 13:21:52'),
(20, 'admin@gmail.com', 'Viewed audit trail page', '2025-05-07 13:21:58'),
(21, 'admin@gmail.com', 'Viewed audit trail page', '2025-05-07 13:21:59'),
(22, 'admin@gmail.com', 'Viewed audit trail page', '2025-05-07 13:22:07'),
(23, 'admin@gmail.com', 'Viewed audit trail page', '2025-05-07 13:22:08'),
(24, 'admin@gmail.com', 'Viewed audit trail page', '2025-05-07 13:22:10'),
(25, 'admin@gmail.com', 'Viewed audit trail page', '2025-05-07 13:22:11'),
(26, 'admin@gmail.com', 'Viewed audit trail page', '2025-05-07 13:22:40'),
(27, 'admin@gmail.com', 'Viewed audit trail page', '2025-05-07 13:23:22'),
(28, 'admin@gmail.com', 'Exported audit trail to CSV', '2025-05-07 13:23:27'),
(29, 'admin@gmail.com', 'Updated species: Waling-waling Orchid1 (ID: 15)', '2025-05-08 01:14:08'),
(30, 'admin@gmail.com', 'Updated species: Waling-waling Orchid (ID: 15)', '2025-05-08 01:14:14'),
(31, 'admin@gmail.com', 'Viewed audit trail page', '2025-05-07 20:58:44'),
(32, 'admin@gmail.com', 'Deleted species ID: 15', '2025-05-08 03:03:45'),
(33, 'admin@gmail.com', 'Deleted species ID: 14', '2025-05-08 03:03:47'),
(34, 'admin@gmail.com', 'Deleted species ID: 12', '2025-05-08 03:03:49'),
(35, 'admin@gmail.com', 'Deleted species ID: 11', '2025-05-08 03:03:51'),
(36, 'admin@gmail.com', 'Deleted species ID: 10', '2025-05-08 03:03:54'),
(37, 'admin@gmail.com', 'Deleted species ID: 9', '2025-05-08 03:03:55'),
(38, 'admin@gmail.com', 'Deleted species ID: 7', '2025-05-08 03:03:58'),
(39, 'admin@gmail.com', 'Deleted species ID: 6', '2025-05-08 03:04:00'),
(40, 'admin@gmail.com', 'Deleted species ID: 5', '2025-05-08 03:04:01'),
(41, 'admin@gmail.com', 'Deleted species ID: 4', '2025-05-08 03:04:03'),
(42, 'admin@gmail.com', 'Deleted species ID: 3', '2025-05-08 03:04:05'),
(43, 'admin@gmail.com', 'Deleted species ID: 2', '2025-05-08 03:04:06'),
(44, 'admin@gmail.com', 'Deleted species ID: 1', '2025-05-08 03:04:08'),
(45, 'admin@gmail.com', 'Added new species: African Bullfrog (ID: 17)', '2025-05-08 03:05:22'),
(46, 'admin@gmail.com', 'Updated species: African Bullfrog (ID: 17)', '2025-05-08 03:06:42'),
(47, 'admin@gmail.com', 'Added new species: Harlequin Tree Frog (ID: 20)', '2025-05-08 03:09:51'),
(48, 'admin@gmail.com', 'Added new species: Oriental Fire-Bellied Newt (ID: 21)', '2025-05-08 03:10:18'),
(49, 'admin@gmail.com', 'Added new species: Oriental Fire-Bellied Toad (ID: 22)', '2025-05-08 03:10:55'),
(50, 'admin@gmail.com', 'Added new species: Luzon Rufous Hornbill (ID: 24)', '2025-05-08 03:13:19'),
(51, 'admin@gmail.com', 'Added new species: Negros Bleeding Heart (ID: 25)', '2025-05-08 03:13:42'),
(52, 'admin@gmail.com', 'Added new species: Orange-winged Amazon (ID: 26)', '2025-05-08 03:14:07'),
(53, 'admin@gmail.com', 'Added new species: Palawan Peacock-Pheasant (ID: 27)', '2025-05-08 03:14:37'),
(54, 'admin@gmail.com', 'Added new species: Philippine Eagle Owl (ID: 28)', '2025-05-08 03:15:07'),
(55, 'admin@gmail.com', 'Added new species: Philippine Eagle (ID: 29)', '2025-05-08 03:15:27'),
(56, 'admin@gmail.com', 'Added new species: Red Bird-of-Paradise (ID: 30)', '2025-05-08 03:15:50'),
(57, 'admin@gmail.com', 'Added new species: Red-keeled Flowerpecker (ID: 31)', '2025-05-08 03:16:18'),
(58, 'admin@gmail.com', 'Added new species: Southern Crowned Pigeon (ID: 32)', '2025-05-08 03:16:53'),
(59, 'admin@gmail.com', 'Added new species: White-collared Kingfisher (ID: 33)', '2025-05-08 03:17:18'),
(60, 'admin@gmail.com', 'Added new species: Chaco Golden Knee Tarantula (ID: 34)', '2025-05-08 03:18:26'),
(61, 'admin@gmail.com', 'Added new species: Chilean Rose Hair Tarantula (ID: 35)', '2025-05-08 03:18:58'),
(62, 'admin@gmail.com', 'Added new species: Curlyhair Tarantula (ID: 36)', '2025-05-08 03:19:20'),
(63, 'admin@gmail.com', 'Added new species: Asian small-clawed otter (ID: 37)', '2025-05-08 03:20:36'),
(64, 'admin@gmail.com', 'Added new species: Golden-Crowned Flying Fox (ID: 38)', '2025-05-08 03:21:02'),
(65, 'admin@gmail.com', 'Added new species: Malayan Civet (ID: 39)', '2025-05-08 03:21:35'),
(66, 'admin@gmail.com', 'Added new species: Diamondback terrapin (ID: 40)', '2025-05-08 03:22:51'),
(67, 'admin@gmail.com', 'Added new species: Earless monitor lizard (ID: 41)', '2025-05-08 03:23:18'),
(68, 'admin@gmail.com', 'Added new species: Green tree python (ID: 42)', '2025-05-08 03:23:58'),
(69, 'admin@gmail.com', 'Added new species: Alocasia sanderiana (ID: 43)', '2025-05-08 03:28:23'),
(70, 'admin@gmail.com', 'Added new species: Alocasia zebrina (ID: 44)', '2025-05-08 03:28:57'),
(71, 'admin@gmail.com', 'Added new species: Elmer’s Jade Vine (ID: 45)', '2025-05-08 03:30:53'),
(72, 'admin@gmail.com', 'Added new species: Emerald Vine (ID: 46)', '2025-05-08 03:31:28'),
(73, 'admin@gmail.com', 'Added new species: Nepenthes philippinensis (ID: 47)', '2025-05-08 03:32:36'),
(74, 'admin@gmail.com', 'Added new species: Nepenthes sumagaya (ID: 48)', '2025-05-08 03:32:59'),
(75, 'admin@gmail.com', 'Added new species: Nepenthes truncata (ID: 49)', '2025-05-08 03:33:15'),
(76, 'admin@gmail.com', 'Added new species: Nepenthes ultra (ID: 50)', '2025-05-08 03:33:40'),
(77, 'admin@gmail.com', 'Added new species: Nepenthes ventricosa (ID: 51)', '2025-05-08 03:33:55'),
(78, 'admin@gmail.com', 'Added new species: Aerides quinquevulnera (ID: 52)', '2025-05-08 03:35:59'),
(79, 'admin@gmail.com', 'Added new species: Aerides monticola (ID: 53)', '2025-05-08 03:36:15'),
(80, 'admin@gmail.com', 'Added new species: Elkhorn Fern (ID: 54)', '2025-05-08 03:37:18'),
(81, 'admin@gmail.com', 'Added new species: Staghorn Fern (ID: 55)', '2025-05-08 03:37:37');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_category`
--

CREATE TABLE `tbl_category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `topic_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_category`
--

INSERT INTO `tbl_category` (`category_id`, `category_name`, `topic_id`) VALUES
(1, 'Animalia', 1),
(2, 'Fungi', 1),
(3, 'Monera', 1),
(4, 'Protista', 1),
(5, 'Plantae', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_contact`
--

CREATE TABLE `tbl_contact` (
  `contact_id` int(11) NOT NULL,
  `acc_id` int(11) NOT NULL,
  `concern` varchar(255) NOT NULL,
  `date_submitted` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_reports`
--

CREATE TABLE `tbl_reports` (
  `report_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `reporter_name` varchar(100) NOT NULL,
  `species_name` varchar(50) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `date_time` datetime NOT NULL,
  `status_name` varchar(50) NOT NULL,
  `status_report_id` int(11) NOT NULL DEFAULT 1,
  `comments` varchar(255) DEFAULT NULL,
  `acc_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_reports`
--

INSERT INTO `tbl_reports` (`report_id`, `category_id`, `reporter_name`, `species_name`, `image_path`, `location`, `date_time`, `status_name`, `status_report_id`, `comments`, `acc_id`) VALUES
(3, 1, 'comia', 'Philippine Eagle (Pithecophaga jefferyi)', '/images/reported_images/681baaee9006e.jpeg', 'Tropical rainforest', '2025-05-08 02:47:49', 'Endangered', 1, '', 1),
(4, 1, 'comia', 'Seychelles fruit bat (Pteropus seychellensis)', '/images/reported_images/681babf752f80.jpg', 'Tropical forest, likely in the Seychelles or surrounding islands, hanging from a tree branch amongst large green leaves', '2025-05-08 02:51:00', 'Healthy', 1, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_role`
--

CREATE TABLE `tbl_role` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_role`
--

INSERT INTO `tbl_role` (`role_id`, `role_name`) VALUES
(1, 'User'),
(2, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_species`
--

CREATE TABLE `tbl_species` (
  `species_id` int(11) NOT NULL,
  `species_name` varchar(50) NOT NULL,
  `scientific_name` varchar(50) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `kingdom` varchar(50) NOT NULL,
  `group_name` varchar(50) NOT NULL,
  `date_created` datetime NOT NULL,
  `category_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `acc_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_species`
--

INSERT INTO `tbl_species` (`species_id`, `species_name`, `scientific_name`, `image_path`, `kingdom`, `group_name`, `date_created`, `category_id`, `type_id`, `acc_id`) VALUES
(17, 'African Bullfrog', 'Pyxicephalus adspersus', '/images/species/681baef28d448.jpg', 'Animalia', 'Amphibian', '2025-05-08 03:05:22', 1, 7, 5),
(18, 'Budgett\'s Frog', 'Lepidobatrachus laevis', '/images/species/681baf907422a.jpg', 'Animalia', 'Amphibian', '2025-05-08 03:07:23', 1, 7, 5),
(19, 'Cranwell\'s Horned Frog', 'Ceratophrys cranwelli', '/images/species/681bafb53a64c.jpg', 'Animalia', 'Amphibian', '2025-05-08 03:08:37', 1, 7, 5),
(20, 'Harlequin Tree Frog', 'Rhacophorus pardalis', '/images/species/681bafff4fed0.jpg', 'Animalia', 'Amphibian', '2025-05-08 03:09:51', 1, 7, 5),
(21, 'Oriental Fire-Bellied Newt', 'Cynops orientalis', '/images/species/681bb01aa8aed.jpg', 'Animalia', 'Angiosperms', '2025-05-08 03:10:18', 1, 7, 5),
(22, 'Oriental Fire-Bellied Toad', 'Bombina orientalis', '/images/species/681bb03fcd50a.jpg', 'Animalia', 'Amphibian', '2025-05-08 03:10:55', 1, 7, 5),
(23, 'White\'s Treefrog', 'Litoria caerulea', '/images/species/681bb05bd5015.jpg', 'Animalia', 'Amphibian', '2025-05-08 03:11:23', 1, 7, 5),
(24, 'Luzon Rufous Hornbill', 'Buceros hydrocorax', '/images/species/681bb0cf3fcdc.jpg', 'Animalia', 'Birds', '2025-05-08 03:13:19', 1, 1, 5),
(25, 'Negros Bleeding Heart', 'Gallicolumba keayi', '/images/species/681bb0e616868.jpg', 'Animalia', 'Birds', '2025-05-08 03:13:42', 1, 1, 5),
(26, 'Orange-winged Amazon', 'Amazona amazonica', '/images/species/681bb0ffcbae0.jpg', 'Animalia', 'Birds', '2025-05-08 03:14:07', 1, 1, 5),
(27, 'Palawan Peacock-Pheasant', 'Polyplectron napoleonis', '/images/species/681bb11d6f0ed.jpg', 'Animalia', 'Birds', '2025-05-08 03:14:37', 1, 1, 5),
(28, 'Philippine Eagle Owl', 'Bubo philippensis', '/images/species/681bb13b80b59.jpg', 'Animalia', 'Birds', '2025-05-08 03:15:07', 1, 1, 5),
(29, 'Philippine Eagle', 'Pithecophaga jefferyi', '/images/species/681bb14fb581e.jpg', 'Animalia', 'Birds', '2025-05-08 03:15:27', 1, 1, 5),
(30, 'Red Bird-of-Paradise', 'Paradisaea rubra', '/images/species/681bb166b98c5.jpg', 'Animalia', 'Birds', '2025-05-08 03:15:50', 1, 1, 5),
(31, 'Red-keeled Flowerpecker', 'Dicaeum australe', '/images/species/681bb18242ac0.jpg', 'Animalia', 'Birds', '2025-05-08 03:16:18', 1, 1, 5),
(32, 'Southern Crowned Pigeon', 'Goura scheepmakeri', '/images/species/681bb1a536a55.jpg', 'Animalia', 'Birds', '2025-05-08 03:16:53', 1, 1, 5),
(33, 'White-collared Kingfisher', 'Todiramphus chloris', '/images/species/681bb1be47f09.jpg', 'Animalia', 'Birds', '2025-05-08 03:17:18', 1, 1, 5),
(34, 'Chaco Golden Knee Tarantula', 'Grammostola pulchripes', '/images/species/681bb2024c709.jpg', 'Animalia', 'Tarantulas', '2025-05-08 03:18:26', 1, 8, 5),
(35, 'Chilean Rose Hair Tarantula', 'Grammostola rosea', '/images/species/681bb222b902c.jpg', 'Animalia', 'Tarantulas', '2025-05-08 03:18:58', 1, 8, 5),
(36, 'Curlyhair Tarantula', 'Tliltocatl albopilosus', '/images/species/681bb238b214d.jpg', 'Animalia', 'Tarantulas', '2025-05-08 03:19:20', 1, 8, 5),
(37, 'Asian small-clawed otter', 'Aonyx cinereus', '/images/species/681bb284482dc.jpg', 'Animalia', 'Pack or romp', '2025-05-08 03:20:36', 1, 2, 5),
(38, 'Golden-Crowned Flying Fox', 'Acerodon jubatus', '/images/species/681bb29ec4da5.jpg', 'Animalia', 'Colony or camp', '2025-05-08 03:21:02', 1, 2, 5),
(39, 'Malayan Civet', 'Viverra tangalunga', '/images/species/681bb2bfdb631.jpg', 'Animalia', 'Unknown', '2025-05-08 03:21:35', 1, 2, 5),
(40, 'Diamondback terrapin', 'Malaclemys terrapin', '/images/species/681bb30b52f7b.jpg', 'Animalia', 'Reptilse', '2025-05-08 03:22:51', 1, 3, 5),
(41, 'Earless monitor lizard', 'Lanthanotus borneensis', '/images/species/681bb32657769.jpg', 'Animalia', 'Reptilse', '2025-05-08 03:23:18', 1, 3, 5),
(42, 'Green tree python', 'Morelia viridis', '/images/species/681bb34ef314c.jpg', 'Animalia', 'Reptiles', '2025-05-08 03:23:58', 1, 3, 5),
(43, 'Alocasia sanderiana', 'Alocasia sanderiana W.Bull', '/images/species/681bb457b84b3.jpg', 'Plantae', 'Aroid', '2025-05-08 03:28:23', 5, 9, 5),
(44, 'Alocasia zebrina', 'Alocasia zebrina Schott ex Van Houtte', '/images/species/681bb4792063d.jpg', 'Plantae', 'Aroid', '2025-05-08 03:28:57', 5, 9, 5),
(45, 'Elmer’s Jade Vine', 'Strongylodon elmeri Merr.', '/images/species/681bb4ed9728e.jpg', 'Plantae', 'Fabaceae', '2025-05-08 03:30:53', 5, 10, 5),
(46, 'Emerald Vine', 'Strongylodon macrobotrys A.Gray', '/images/species/681bb5102a6a3.jpg', 'Plantae', 'Fabaceae', '2025-05-08 03:31:28', 1, 10, 5),
(47, 'Nepenthes philippinensis', 'Nepenthes philippinensis Macfarl.', '/images/species/681bb554ec7eb.jpg', 'Plantae', 'Pitcher Plant', '2025-05-08 03:32:36', 5, 11, 5),
(48, 'Nepenthes sumagaya', 'Nepenthes sumagaya Cheek', '/images/species/681bb56b35b25.jpg', 'Plantae', 'Pitcher Plant', '2025-05-08 03:32:59', 5, 11, 5),
(49, 'Nepenthes truncata', 'Nepenthes truncata Macfarl', '/images/species/681bb57b5eb8c.jpg', 'Plantae', 'Pitcher Plant', '2025-05-08 03:33:15', 5, 11, 5),
(50, 'Nepenthes ultra', 'Nepenthes ultra Jebb & Cheek', '/images/species/681bb5948e42c.jpg', 'Plantae', 'Pitcher Plant', '2025-05-08 03:33:40', 5, 11, 5),
(51, 'Nepenthes ventricosa', 'Nepenthes ventricosa Blanco', '/images/species/681bb5a34cd5f.jpg', 'Plantae', 'Pitcher Plant', '2025-05-08 03:33:55', 5, 11, 5),
(52, 'Aerides quinquevulnera', 'Aerides quinquevulnera Lindl', '/images/species/681bb61f1dc35.jpg', 'Plantae', 'Orchid', '2025-05-08 03:35:59', 5, 12, 5),
(53, 'Aerides monticola', 'Amesiella monticola Cootes & D.P.Banks', '/images/species/681bb62f72494.jpg', 'Plantae', 'Orchid', '2025-05-08 03:36:15', 5, 12, 5),
(54, 'Elkhorn Fern', 'Platycerium coronarium', '/images/species/681bb66ede30a.jpg', 'Plantae', 'Ferns', '2025-05-08 03:37:18', 1, 13, 5),
(55, 'Staghorn Fern', 'Platycerium grande', '/images/species/681bb68136563.jpg', 'Plantae', 'Ferns', '2025-05-08 03:37:37', 1, 13, 5);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_species_type`
--

CREATE TABLE `tbl_species_type` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_species_type`
--

INSERT INTO `tbl_species_type` (`type_id`, `type_name`) VALUES
(1, 'Birds'),
(2, 'Mamals'),
(3, 'Reptiles'),
(4, 'Fungi'),
(5, 'Monera'),
(6, 'Protista'),
(7, 'Amphibians'),
(8, 'Invertebrate'),
(9, 'Araceae'),
(10, 'Fabaceae'),
(11, 'Nepenthaceae'),
(12, 'Orchidaceae'),
(13, 'Polypodiaceaev');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_status`
--

CREATE TABLE `tbl_status` (
  `status_id` int(11) NOT NULL,
  `status_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_status`
--

INSERT INTO `tbl_status` (`status_id`, `status_name`) VALUES
(1, 'Alive'),
(2, 'Endangared');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_status_report`
--

CREATE TABLE `tbl_status_report` (
  `status_rp_id` int(11) NOT NULL,
  `status_rp_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_status_report`
--

INSERT INTO `tbl_status_report` (`status_rp_id`, `status_rp_name`) VALUES
(1, 'Pending'),
(2, 'Surveillance'),
(3, 'Investigation'),
(4, 'Verified'),
(5, 'No Action Needed'),
(6, 'Closed Report');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_topic`
--

CREATE TABLE `tbl_topic` (
  `topic_id` int(11) NOT NULL,
  `topic_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_topic`
--

INSERT INTO `tbl_topic` (`topic_id`, `topic_name`) VALUES
(1, 'Fauna'),
(2, 'Flora');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_account`
--
ALTER TABLE `tbl_account`
  ADD PRIMARY KEY (`acc_id`);

--
-- Indexes for table `tbl_audit_trail`
--
ALTER TABLE `tbl_audit_trail`
  ADD PRIMARY KEY (`trail_id`);

--
-- Indexes for table `tbl_category`
--
ALTER TABLE `tbl_category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `tbl_contact`
--
ALTER TABLE `tbl_contact`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `tbl_reports`
--
ALTER TABLE `tbl_reports`
  ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `tbl_role`
--
ALTER TABLE `tbl_role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `tbl_species`
--
ALTER TABLE `tbl_species`
  ADD PRIMARY KEY (`species_id`);

--
-- Indexes for table `tbl_species_type`
--
ALTER TABLE `tbl_species_type`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `tbl_status`
--
ALTER TABLE `tbl_status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `tbl_status_report`
--
ALTER TABLE `tbl_status_report`
  ADD PRIMARY KEY (`status_rp_id`);

--
-- Indexes for table `tbl_topic`
--
ALTER TABLE `tbl_topic`
  ADD PRIMARY KEY (`topic_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_account`
--
ALTER TABLE `tbl_account`
  MODIFY `acc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_audit_trail`
--
ALTER TABLE `tbl_audit_trail`
  MODIFY `trail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `tbl_category`
--
ALTER TABLE `tbl_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_contact`
--
ALTER TABLE `tbl_contact`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_reports`
--
ALTER TABLE `tbl_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_role`
--
ALTER TABLE `tbl_role`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_species`
--
ALTER TABLE `tbl_species`
  MODIFY `species_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `tbl_species_type`
--
ALTER TABLE `tbl_species_type`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tbl_status`
--
ALTER TABLE `tbl_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_status_report`
--
ALTER TABLE `tbl_status_report`
  MODIFY `status_rp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_topic`
--
ALTER TABLE `tbl_topic`
  MODIFY `topic_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
