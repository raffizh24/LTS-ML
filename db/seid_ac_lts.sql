-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 15, 2026 at 09:43 AM
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
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `action_master`
--

INSERT INTO `action_master` (`action_id`, `action_name`, `created_at`) VALUES
(1, 'Change Coupler, Revacuum, Recharging', '2026-05-26 10:21:15');

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
(1, 'Part', '2026-05-26 10:18:47');

-- --------------------------------------------------------

--
-- Table structure for table `defect_master`
--

CREATE TABLE `defect_master` (
  `defect_id` int(11) NOT NULL,
  `defect_name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `defect_master`
--

INSERT INTO `defect_master` (`defect_id`, `defect_name`, `created_at`) VALUES
(1, 'Noise', '2026-05-26 10:17:33');

-- --------------------------------------------------------

--
-- Table structure for table `line_drop_transaction`
--

CREATE TABLE `line_drop_transaction` (
  `transaction_id` int(11) NOT NULL,
  `product_id` varchar(100) NOT NULL,
  `model_code` varchar(20) NOT NULL,
  `defect_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `rootcause_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `remark` text DEFAULT NULL,
  `created_by` varchar(30) DEFAULT NULL,
  `created_name` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `line_drop_transaction`
--

INSERT INTO `line_drop_transaction` (`transaction_id`, `product_id`, `model_code`, `defect_id`, `category_id`, `rootcause_id`, `action_id`, `remark`, `created_by`, `created_name`, `created_at`) VALUES
(1, '5195DBZ3AHA5BEY2', 'AHA5BEY2', 1, 1, 1, 1, 'Bocor dibagian seal dan chuck', 'operator01', 'Operator IDU', '2026-05-26 10:34:42'),
(2, '5195DBZ3AHA5BEY2', 'AHA5BEY2', 1, 1, 1, 1, 'Test', 'repair01', 'Repair', '2026-05-28 13:38:18');

-- --------------------------------------------------------

--
-- Table structure for table `model_master`
--

CREATE TABLE `model_master` (
  `model_id` int(11) NOT NULL,
  `model_name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `model_master`
--

INSERT INTO `model_master` (`model_id`, `model_name`, `created_at`) VALUES
(1, 'AHA5BEY2', '2026-05-26 10:07:07'),
(2, 'AHA5BMY2', '2026-05-26 10:07:15');

-- --------------------------------------------------------

--
-- Table structure for table `rootcause_master`
--

CREATE TABLE `rootcause_master` (
  `rootcause_id` int(11) NOT NULL,
  `rootcause_name` varchar(150) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rootcause_master`
--

INSERT INTO `rootcause_master` (`rootcause_id`, `rootcause_name`, `created_at`) VALUES
(1, 'Leak Coupler', '2026-05-26 10:20:54');

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
  `process` varchar(32) NOT NULL,
  `role` enum('OPERATOR','REPAIRMAN','LEADER','ADMIN') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_master`
--

INSERT INTO `user_master` (`user_id`, `username`, `password`, `name`, `area`, `process`, `role`, `created_at`) VALUES
(1, 'admin01', 'SeidMail01', 'Admin', 'IDU', 'Structural Inspection', 'ADMIN', '2026-05-26 08:46:46'),
(3, 'leader01', 'SeidMail01', 'Leader 1', 'IDU', 'ALL', 'LEADER', '2026-05-26 10:00:44'),
(4, 'operator01', 'SeidMail01', 'Operator IDU', 'IDU', 'Structural Inspection', 'OPERATOR', '2026-05-26 10:33:36'),
(6, 'repair01', 'SeidMail01', 'Repair', 'IDU', 'All', 'REPAIRMAN', '2026-05-28 13:36:00');

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
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `category_master`
--
ALTER TABLE `category_master`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `defect_master`
--
ALTER TABLE `defect_master`
  MODIFY `defect_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `line_drop_transaction`
--
ALTER TABLE `line_drop_transaction`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `model_master`
--
ALTER TABLE `model_master`
  MODIFY `model_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rootcause_master`
--
ALTER TABLE `rootcause_master`
  MODIFY `rootcause_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_master`
--
ALTER TABLE `user_master`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
