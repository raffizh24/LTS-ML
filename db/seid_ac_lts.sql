-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 08, 2026 at 03:18 AM
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
-- Database: `seid_ac_lts`
--

-- --------------------------------------------------------

--
-- Table structure for table `action_master`
--

CREATE TABLE `action_master` (
  `action_id` int(11) NOT NULL,
  `action_name` varchar(150) NOT NULL,
  `area` enum('IDU','ODU') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `action_master`
--

INSERT INTO `action_master` (`action_id`, `action_name`, `area`, `created_at`) VALUES
(2, 'Change PWB', 'IDU', '2026-07-08 08:06:24'),
(3, 'Change Part Inhouse', 'IDU', '2026-07-08 08:06:30'),
(4, 'Repair Part', 'IDU', '2026-07-08 08:06:52'),
(5, 'Retry EPROM', 'IDU', '2026-07-08 08:07:12'),
(6, 'Change Part Vendor', 'IDU', '2026-07-08 08:07:57'),
(7, 'Change Part', 'ODU', '2026-07-08 08:17:02'),
(8, 'Repair Part', 'ODU', '2026-07-08 08:17:07'),
(9, 'Change Coupler', 'ODU', '2026-07-08 08:17:31');

-- --------------------------------------------------------

--
-- Table structure for table `category_master`
--

CREATE TABLE `category_master` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_master`
--

INSERT INTO `category_master` (`category_id`, `category_name`, `created_at`) VALUES
(1, 'Machine', '2026-07-08 07:48:40'),
(2, 'Work', '2026-07-08 07:48:44'),
(3, 'Equipment', '2026-07-08 07:48:48'),
(4, 'Part', '2026-07-08 07:48:53');

-- --------------------------------------------------------

--
-- Table structure for table `defect_master`
--

CREATE TABLE `defect_master` (
  `defect_id` int(11) NOT NULL,
  `defect_name` varchar(100) NOT NULL,
  `area` enum('IDU','ODU') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `defect_master`
--

INSERT INTO `defect_master` (`defect_id`, `defect_name`, `area`, `created_at`) VALUES
(2, 'EGWV - ACW NG', 'IDU', '2026-07-08 07:50:11'),
(3, 'EGWV - IR NG', 'IDU', '2026-07-08 07:50:21'),
(4, 'EGWV - EC NG', 'IDU', '2026-07-08 07:50:27'),
(5, 'PWB - OFF', 'IDU', '2026-07-08 07:50:47'),
(6, 'PWB - Auto Start', 'IDU', '2026-07-08 07:51:17'),
(7, 'PWB - Fan Motor OFF', 'IDU', '2026-07-08 07:51:39'),
(8, 'PWB - Blinking', 'IDU', '2026-07-08 07:51:47'),
(9, 'PWB - LED OFF', 'IDU', '2026-07-08 07:52:06'),
(10, 'PWB - Cable Broken', 'IDU', '2026-07-08 07:52:30'),
(11, 'PWB - EPROM Error', 'IDU', '2026-07-08 07:53:39'),
(12, 'Fan Motor - Low Speed RPM', 'IDU', '2026-07-08 07:55:35'),
(13, 'Front Panel', 'IDU', '2026-07-08 07:56:10'),
(14, 'Cabinet', 'IDU', '2026-07-08 07:56:17'),
(15, 'Open Panel', 'IDU', '2026-07-08 07:56:28'),
(16, 'PWB - Horizontal Louver OFF', 'IDU', '2026-07-08 07:57:15'),
(17, 'Leak - Condensor', 'ODU', '2026-07-08 08:08:28'),
(18, 'Leak - Capillary', 'ODU', '2026-07-08 08:09:20'),
(19, 'Leak - Coupler Capillary', 'ODU', '2026-07-08 08:10:18'),
(20, 'Leak - Coupler 2 Way', 'ODU', '2026-07-08 08:10:25'),
(21, 'Leak - Coupler 3 Way', 'ODU', '2026-07-08 08:10:32'),
(22, 'Leak - Ultrasonic Welding', 'ODU', '2026-07-08 08:10:46'),
(23, 'High Wattage', 'ODU', '2026-07-08 08:11:15'),
(24, 'Low Wattage', 'ODU', '2026-07-08 08:11:24'),
(25, 'High Pressure', 'ODU', '2026-07-08 08:11:42'),
(26, 'Leak - U Bend', 'ODU', '2026-07-08 08:12:09'),
(27, 'Leak - Outlet Pipe', 'ODU', '2026-07-08 08:12:15'),
(28, 'Leak - Inlet Pipe', 'ODU', '2026-07-08 08:12:23'),
(29, 'Tube Blockage - Capillary', 'ODU', '2026-07-08 08:15:10');

-- --------------------------------------------------------

--
-- Table structure for table `line_drop_transaction`
--

CREATE TABLE `line_drop_transaction` (
  `transaction_id` int(11) NOT NULL,
  `product_id` varchar(100) NOT NULL,
  `model_code` varchar(20) NOT NULL,
  `area` enum('IDU','ODU') NOT NULL,
  `defect_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `rootcause_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `remark` text DEFAULT NULL,
  `created_by` varchar(30) DEFAULT NULL,
  `created_name` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_master`
--

CREATE TABLE `model_master` (
  `model_id` int(11) NOT NULL,
  `model_name` varchar(100) NOT NULL,
  `area` enum('IDU','ODU') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `model_master`
--

INSERT INTO `model_master` (`model_id`, `model_name`, `area`, `created_at`) VALUES
(1, 'AHA5BEY2', 'IDU', '2026-07-08 07:47:26'),
(2, 'AHA5BBY2', 'IDU', '2026-07-08 07:47:26'),
(3, 'AHAP5BMY2', 'IDU', '2026-07-08 07:47:26'),
(4, 'AHA5DEY', 'IDU', '2026-07-08 07:47:26'),
(5, 'AHA5CAY', 'IDU', '2026-07-08 07:47:26'),
(6, 'AHA5DHY', 'IDU', '2026-07-08 07:47:26'),
(7, 'AHA7BEY', 'IDU', '2026-07-08 07:47:26'),
(8, 'AHAP7BMY', 'IDU', '2026-07-08 07:47:26'),
(9, 'AHA7BEY2', 'IDU', '2026-07-08 07:47:26'),
(10, 'AHAP7BMY2', 'IDU', '2026-07-08 07:47:26'),
(11, 'AHA7DEY', 'IDU', '2026-07-08 07:47:26'),
(12, 'AHA9BEY2', 'IDU', '2026-07-08 07:47:26'),
(13, 'AHA9BBY2', 'IDU', '2026-07-08 07:47:26'),
(14, 'AHAP9BMY2', 'IDU', '2026-07-08 07:47:26'),
(15, 'AHA9BEYT', 'IDU', '2026-07-08 07:47:26'),
(16, 'AHA9DEY', 'IDU', '2026-07-08 07:47:26'),
(17, 'AHA9CAY', 'IDU', '2026-07-08 07:47:26'),
(18, 'AHA9DHY', 'IDU', '2026-07-08 07:47:26'),
(19, 'AHX6BEY', 'IDU', '2026-07-08 07:47:26'),
(20, 'AHXP6BMY', 'IDU', '2026-07-08 07:47:26'),
(21, 'AHX8BEY', 'IDU', '2026-07-08 07:47:26'),
(22, 'AHXP8BMY', 'IDU', '2026-07-08 07:47:26'),
(23, 'AHX10BEY', 'IDU', '2026-07-08 07:47:26'),
(24, 'AHXP10BMY', 'IDU', '2026-07-08 07:47:26'),
(25, 'AHX13BEY', 'IDU', '2026-07-08 07:47:26'),
(26, 'AHXP13BMY', 'IDU', '2026-07-08 07:47:26'),
(27, 'AHX1DEW', 'IDU', '2026-07-08 07:47:26'),
(28, 'AHXP1DSW', 'IDU', '2026-07-08 07:47:26'),
(29, 'AHX3DEW', 'IDU', '2026-07-08 07:47:26'),
(30, 'AHXP3DSW', 'IDU', '2026-07-08 07:47:26'),
(31, 'AUA5BEY2', 'ODU', '2026-07-08 07:47:34'),
(32, 'AUA5BBY2', 'ODU', '2026-07-08 07:47:34'),
(33, 'AUA5BMY2', 'ODU', '2026-07-08 07:47:34'),
(34, 'AUA5DEY', 'ODU', '2026-07-08 07:47:34'),
(35, 'AUA5CAY', 'ODU', '2026-07-08 07:47:34'),
(36, 'AUA5DHY', 'ODU', '2026-07-08 07:47:34'),
(37, 'AUA7BEY', 'ODU', '2026-07-08 07:47:34'),
(38, 'AUAP7BMY', 'ODU', '2026-07-08 07:47:34'),
(39, 'AUA7BEY2', 'ODU', '2026-07-08 07:47:34'),
(40, 'AUA7BMY2', 'ODU', '2026-07-08 07:47:34'),
(41, 'AUA7DEY', 'ODU', '2026-07-08 07:47:34'),
(42, 'AUA9BEY2', 'ODU', '2026-07-08 07:47:34'),
(43, 'AUA9BBY2', 'ODU', '2026-07-08 07:47:34'),
(44, 'AUA9BMY2', 'ODU', '2026-07-08 07:47:34'),
(45, 'AUA9DEY', 'ODU', '2026-07-08 07:47:34'),
(46, 'AUA9CAY', 'ODU', '2026-07-08 07:47:34'),
(47, 'AUA9DHY', 'ODU', '2026-07-08 07:47:34'),
(48, 'AUX6BEY', 'ODU', '2026-07-08 07:47:34'),
(49, 'AUX6BMY', 'ODU', '2026-07-08 07:47:34'),
(50, 'AUX8BEY', 'ODU', '2026-07-08 07:47:34'),
(51, 'AUX8BMY', 'ODU', '2026-07-08 07:47:34'),
(52, 'AUX10BEY', 'ODU', '2026-07-08 07:47:34'),
(53, 'AUX10BMY', 'ODU', '2026-07-08 07:47:34'),
(54, 'AUX13BEY', 'ODU', '2026-07-08 07:47:34'),
(55, 'AUX13BMY', 'ODU', '2026-07-08 07:47:34'),
(56, 'AUX1DEW', 'ODU', '2026-07-08 07:47:34'),
(57, 'AUX3DEW', 'ODU', '2026-07-08 07:47:34'),
(58, 'AUX1DSW', 'ODU', '2026-07-08 07:47:34'),
(59, 'AUX3DSW', 'ODU', '2026-07-08 07:47:34');

-- --------------------------------------------------------

--
-- Table structure for table `rootcause_master`
--

CREATE TABLE `rootcause_master` (
  `rootcause_id` int(11) NOT NULL,
  `rootcause_name` varchar(150) NOT NULL,
  `area` enum('IDU','ODU') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rootcause_master`
--

INSERT INTO `rootcause_master` (`rootcause_id`, `rootcause_name`, `area`, `created_at`) VALUES
(2, 'PWB NG', 'IDU', '2026-07-08 08:04:19'),
(3, 'NG Part Inhouse', 'IDU', '2026-07-08 08:05:35'),
(4, 'NG Part Vendor', 'IDU', '2026-07-08 08:05:44'),
(5, 'Slow Charging', 'ODU', '2026-07-08 08:15:52'),
(6, 'Dented - U Bend', 'ODU', '2026-07-08 08:16:20'),
(7, 'Touching - Piping Part', 'ODU', '2026-07-08 08:16:43');

-- --------------------------------------------------------

--
-- Table structure for table `user_master`
--

CREATE TABLE `user_master` (
  `user_id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `area` enum('IDU','ODU') NOT NULL,
  `role` enum('REPAIRMAN','LEADER','ADMIN') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_master`
--

INSERT INTO `user_master` (`user_id`, `username`, `password`, `name`, `area`, `role`, `created_at`) VALUES
(1, 'admin01', 'SeidMail01', 'Admin', 'IDU', 'ADMIN', '2026-05-26 08:46:46'),
(6, 'repairman_idu_a', 'SeidMail01', 'Repairman IDU A', 'IDU', 'REPAIRMAN', '2026-05-28 13:36:00'),
(7, 'leader_idu_a', 'SeidMail01', 'Dewa & Rifki', 'IDU', 'LEADER', '2026-07-07 14:00:15'),
(8, 'leader_odu_a', 'SeidMail01', 'Irwanto', 'ODU', 'LEADER', '2026-07-07 14:00:36'),
(9, 'leader_idu_b', 'SeidMail01', 'Irfan & Rohman', 'IDU', 'LEADER', '2026-07-08 07:38:31'),
(10, 'leader_idu_c', 'SeidMail01', 'Haris & Mufti', 'IDU', 'LEADER', '2026-07-08 07:38:51'),
(11, 'leader_odu_b', 'SeidMail01', 'Adi & Nurdin', 'ODU', 'LEADER', '2026-07-08 07:39:21'),
(12, 'leader_odu_c', 'SeidMail01', 'Romly & Mufti', 'ODU', 'LEADER', '2026-07-08 07:39:50'),
(13, 'repairman_idu_b', 'SeidMail01', 'Repairman IDU B', 'IDU', 'REPAIRMAN', '2026-07-08 07:40:26'),
(14, 'repairman_idu_c', 'SeidMail01', 'Repairman IDU C', 'IDU', 'REPAIRMAN', '2026-07-08 07:40:53'),
(15, 'repairman_odu_b', 'SeidMail01', 'Repairman ODU B', 'ODU', 'REPAIRMAN', '2026-07-08 07:41:11'),
(16, 'repairman_odu_c', 'SeidMail01', 'Repairman ODU C', 'ODU', 'REPAIRMAN', '2026-07-08 07:41:26'),
(17, 'repairman_odu_a', 'SeidMail01', 'Repairman ODU A', 'ODU', 'REPAIRMAN', '2026-07-08 07:41:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `action_master`
--
ALTER TABLE `action_master`
  ADD PRIMARY KEY (`action_id`);

--
-- Indexes for table `category_master`
--
ALTER TABLE `category_master`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `defect_master`
--
ALTER TABLE `defect_master`
  ADD PRIMARY KEY (`defect_id`);

--
-- Indexes for table `line_drop_transaction`
--
ALTER TABLE `line_drop_transaction`
  ADD PRIMARY KEY (`transaction_id`);

--
-- Indexes for table `model_master`
--
ALTER TABLE `model_master`
  ADD PRIMARY KEY (`model_id`);

--
-- Indexes for table `rootcause_master`
--
ALTER TABLE `rootcause_master`
  ADD PRIMARY KEY (`rootcause_id`);

--
-- Indexes for table `user_master`
--
ALTER TABLE `user_master`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `action_master`
--
ALTER TABLE `action_master`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `category_master`
--
ALTER TABLE `category_master`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `defect_master`
--
ALTER TABLE `defect_master`
  MODIFY `defect_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `line_drop_transaction`
--
ALTER TABLE `line_drop_transaction`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `model_master`
--
ALTER TABLE `model_master`
  MODIFY `model_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `rootcause_master`
--
ALTER TABLE `rootcause_master`
  MODIFY `rootcause_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_master`
--
ALTER TABLE `user_master`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
