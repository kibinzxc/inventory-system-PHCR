-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2024 at 11:03 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `phcr_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `uid` int(100) NOT NULL,
  `name` text NOT NULL,
  `email` varchar(250) NOT NULL,
  `userType` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`uid`, `name`, `email`, `userType`, `password`) VALUES
(2024014, 'SUPER ADMIN', 'superadmin@phcr.com', 'super_admin', '2c103f2c4ed1e59c0b4e2e01821770fa'),
(2024043, 'TESTING LANG', 'tetesting@gmail.com', 'stockman', 'fd417c8eb41b3c16893d470d01ba2cd3'),
(2024045, 'TEST', 'test@gmail.com', 'admin', '2c103f2c4ed1e59c0b4e2e01821770fa');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `inventoryID` int(50) NOT NULL,
  `itemID` varchar(250) NOT NULL,
  `name` text NOT NULL,
  `uom` varchar(50) NOT NULL,
  `beginning` double NOT NULL,
  `purchases` double NOT NULL,
  `transfers_in` double NOT NULL,
  `transfers_out` double NOT NULL,
  `waste` double NOT NULL,
  `ending` double NOT NULL,
  `variance` double NOT NULL,
  `notes` text NOT NULL,
  `usage_count` double NOT NULL,
  `status` text NOT NULL,
  `last_update` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`inventoryID`, `itemID`, `name`, `uom`, `beginning`, `purchases`, `transfers_in`, `transfers_out`, `waste`, `ending`, `variance`, `notes`, `usage_count`, `status`, `last_update`, `updated_by`) VALUES
(23, 'CM4303', 'cajun mix', 'kg', 0, 0, 0, 0, 1, 0, 0, 'isa na nga lang ninakaw pa', 0, 'out of stock', '2024-11-09 04:34:53', 'SUPER ADMIN'),
(24, 'L38795', 'lasagna', 'kg', 5.5, 0, 0, 0, 0, 0, 0, '', 0, 'pending', '2024-11-08 19:57:57', 'SUPER ADMIN'),
(27, 'PT1239', 'pineapple tidbits', 'kg', 500, 0, 0, 0, 0, 0, 0, '', 0, 'pending', '2024-11-09 04:26:23', 'SUPER ADMIN'),
(29, 'TRW406', 'tomato red whole', 'kg', 4.2, 0, 0, 0, 0, 0, 0, '', 0, 'pending', '2024-11-09 04:30:20', 'SUPER ADMIN'),
(30, 'CPS362', 'cheese parmesan shredded', 'kg', 10, 0, 0, 0, 0, 0, 0, '', 0, 'pending', '2024-11-09 04:31:29', 'SUPER ADMIN'),
(31, 'CM5024', 'cheese mozarella', 'kg', 315, 0, 0, 0, 0, 0, 0, 'inubos ng staff', 0, 'low stock', '2024-11-09 04:35:14', 'SUPER ADMIN'),
(32, 'PBG560', 'pepper bell green', 'kg', 20, 0, 0, 0, 0, 0, 0, '', 0, 'pending', '2024-11-09 04:31:58', 'SUPER ADMIN'),
(33, 'PBR549', 'pepper bell red', 'kg', 15, 0, 0, 0, 0, 0, 0, '', 0, 'pending', '2024-11-09 04:32:08', 'SUPER ADMIN'),
(38, 'BPDX40', 'blend pan dough (165gms x 45)', 'pac', 353.79, 0, 0, 0, 0, 0, 0, 'ok', 0, 'in stock', '2024-11-09 05:06:01', 'SUPER ADMIN'),
(39, 'F5280', 'flour (10kgs/bag)', 'bag', 200, 0, 0, 0, 0, 0, 0, '', 0, 'pending', '2024-11-09 05:05:18', 'SUPER ADMIN');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventoryID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `uid` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2024046;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventoryID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
