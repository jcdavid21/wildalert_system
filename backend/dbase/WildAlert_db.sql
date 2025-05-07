-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 07, 2025 at 01:24 PM
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
(28, 'admin@gmail.com', 'Exported audit trail to CSV', '2025-05-07 13:23:27');

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
  `status_id` int(11) NOT NULL,
  `status_report_id` int(11) NOT NULL DEFAULT 1,
  `comments` varchar(255) DEFAULT NULL,
  `acc_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_reports`
--

INSERT INTO `tbl_reports` (`report_id`, `category_id`, `reporter_name`, `species_name`, `image_path`, `location`, `date_time`, `status_id`, `status_report_id`, `comments`, `acc_id`) VALUES
(1, 1, 'jcdavid', 'Parrot', '/images/reported_images/681afdf6ad331.jpeg', 'Mt APO', '2025-05-07 14:28:00', 2, 2, 'mamamatay na', 1);

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
(1, 'Philippine Eagle', 'Pithecophaga jefferyi', '/images/species/philippine_eagle.jpg', 'Animalia', 'Aves', '2025-01-15 10:30:00', 1, 1, 5),
(2, 'Palawan Peacock-pheasant', 'Polyplectron napoleonis', '/images/species/palawan_peacock.jpg', 'Animalia', 'Aves', '2025-01-20 14:15:00', 1, 1, 5),
(3, 'Visayan Hornbill', 'Penelopides panini', '/images/species/visayan_hornbill.jpg', 'Animalia', 'Aves', '2025-02-01 09:45:00', 1, 1, 5),
(4, 'Tamaraw', 'Bubalus mindorensis', '/images/species/tamaraw.jpg', 'Animalia', 'Mammalia', '2025-02-10 11:20:00', 1, 2, 5),
(5, 'Philippine Tarsier', 'Carlito syrichta', '/images/species/tarsier.webp', 'Animalia', 'Mammalia', '2025-02-15 13:40:00', 1, 2, 5),
(6, 'Visayan Spotted Deer', 'Rusa alfredi', '/images/species/spotted_deer.jpg', 'Animalia', 'Mammalia', '2025-02-28 16:30:00', 1, 2, 5),
(7, 'Philippine Crocodile', 'Crocodylus mindorensis', '/images/species/681b2bc53eee9.avif', 'Animalia', 'Reptilia', '2025-03-05 10:15:00', 1, 3, 5),
(9, 'Shiitake Mushroom', 'Lentinula edodes', '/images/species/shiitake.jpeg', 'Fungi', 'Basidiomycota', '2025-03-25 11:10:00', 2, 4, 5),
(10, 'Cyanobacteria', 'Nostoc commune', '/images/species/cyanobacteria.jpg', 'Monera', 'Bacteria', '2025-04-02 13:25:00', 3, 5, 5),
(11, 'Nitrogen-fixing Bacteria', 'Rhizobium leguminosarum', '/images/species/rhizobium.jpeg', 'Monera', 'Bacteria', '2025-04-10 15:40:00', 3, 5, 5),
(12, 'Giant Kelp', 'Macrocystis pyrifera', '/images/species/giant_kelp.jpg', 'Protista', 'Chromista', '2025-04-15 09:50:00', 4, 6, 5),
(14, 'Rafflesia', 'Rafflesia arnoldii', '/images/species/rafflesia.avif', 'Plantae', 'Angiosperms', '2025-05-01 10:45:00', 3, 5, 5),
(15, 'Waling-waling Orchid', 'Vanda sanderiana', '/images/species/waling_orchid.webp', 'Plantae', 'Angiosperms', '2025-05-05 13:20:00', 5, 5, 5);

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
(6, 'Protista');

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
  MODIFY `trail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_role`
--
ALTER TABLE `tbl_role`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_species`
--
ALTER TABLE `tbl_species`
  MODIFY `species_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tbl_species_type`
--
ALTER TABLE `tbl_species_type`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
