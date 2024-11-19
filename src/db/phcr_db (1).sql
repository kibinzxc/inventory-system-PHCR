-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2024 at 07:06 AM
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
-- Table structure for table `daily_inventory`
--

CREATE TABLE `daily_inventory` (
  `inventoryID` int(50) NOT NULL,
  `itemID` varchar(250) NOT NULL,
  `name` text NOT NULL,
  `uom` varchar(50) NOT NULL,
  `beginning` double NOT NULL,
  `deliveries` double NOT NULL,
  `transfers_in` double NOT NULL,
  `transfers_out` double NOT NULL,
  `spoilage` double NOT NULL,
  `ending` double NOT NULL,
  `variance` double NOT NULL,
  `remarks` text NOT NULL,
  `usage_count` double NOT NULL,
  `status` text NOT NULL,
  `last_update` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_inventory`
--

INSERT INTO `daily_inventory` (`inventoryID`, `itemID`, `name`, `uom`, `beginning`, `deliveries`, `transfers_in`, `transfers_out`, `spoilage`, `ending`, `variance`, `remarks`, `usage_count`, `status`, `last_update`, `updated_by`) VALUES
(23, 'CM4303', 'cajun mix', 'kg', 0, 0, 0, 0, 0, 0, 0, '', 0, 'out of stock', '2024-11-20 06:39:40', ''),
(24, 'L38795', 'lasagna', 'kg', 0, 0, 0, 0, 0, 0, 0, '', 0, 'out of stock', '2024-11-20 06:39:40', ''),
(27, 'PT1239', 'pineapple tidbits', 'kg', 0, 0, 0, 0, 0, 0, 0, '', 0, 'out of stock', '2024-11-20 06:39:40', ''),
(29, 'TRW406', 'tomato red whole', 'kg', 0, 0, 0, 0, 0, 0, 0, '', 0, 'out of stock', '2024-11-20 06:39:40', ''),
(30, 'CPS362', 'cheese parmesan shredded', 'kg', 0, 0, 0, 0, 0, 0, 0, '', 0, 'out of stock', '2024-11-20 06:39:40', ''),
(31, 'CM5024', 'cheese mozarella', 'kg', 0, 0, 0, 0, 0, 0, 0, '', 0, 'out of stock', '2024-11-20 06:39:40', ''),
(32, 'PBG560', 'pepper bell green', 'kg', 0, 0, 0, 0, 0, 0, 0, '', 0, 'out of stock', '2024-11-20 06:39:40', ''),
(33, 'PBR549', 'pepper bell red', 'kg', 0, 0, 0, 0, 0, 0, 0, '', 0, 'out of stock', '2024-11-20 06:39:40', ''),
(38, 'BPDX40', 'blend pan dough (165gms x 45)', 'pac', 34, 0, 0, 0, 0, 34, 0, '', 0, 'in stock', '2024-11-20 06:39:40', ''),
(39, 'F5280', 'flour (10kgs/bag)', 'bag', 0, 0, 0, 0, 0, 0, 0, '', 0, 'out of stock', '2024-11-20 06:39:40', '');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `prodID` int(50) NOT NULL,
  `name` text NOT NULL,
  `ingredients` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`ingredients`)),
  `status` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`prodID`, `name`, `ingredients`, `status`) VALUES
(1, 'Supreme', '[{\"ingredient_name\": \"HAM\", \"quantity\": 50, \"measurement\": \"grams\"}, {\"ingredient_name\": \"PIZZA SAUCE\", \"quantity\": 90, \"measurement\": \"grams\"}, {\"ingredient_name\": \"MOZZARELLA\", \"quantity\": 50, \"measurement\": \"grams\"}, {\"ingredient_name\": \"QUICKMELT CHEESE\", \"quantity\": 50, \"measurement\": \"grams\"}, {\"ingredient_name\": \"FLOUR\", \"quantity\": 180, \"measurement\": \"grams\"}, {\"ingredient_name\": \"SOYA OIL\", \"quantity\": 20, \"measurement\": \"grams\"}, {\"ingredient_name\": \"Dough Blend\", \"quantity\": 10, \"measurement\": \"grams\"}, {\"ingredient_name\": \"Pizza Box\", \"quantity\": 1, \"measurement\": \"pc\"}, {\"ingredient_name\": \"Boxliner\", \"quantity\": 1, \"measurement\": \"pc\"}, {\"ingredient_name\": \"HOT SAUCE SACHET\", \"quantity\": 2, \"measurement\": \"pcs\"}, {\"ingredient_name\": \"Beef topping\", \"quantity\": 50, \"measurement\": \"grams\"}, {\"ingredient_name\": \"Onions\", \"quantity\": 50, \"measurement\": \"grams\"}, {\"ingredient_name\": \"Pork topping\", \"quantity\": 50, \"measurement\": \"grams\"}, {\"ingredient_name\": \"Mushroom\", \"quantity\": 50, \"measurement\": \"grams\"}]', 'not available');

-- --------------------------------------------------------

--
-- Table structure for table `records_inventory`
--

CREATE TABLE `records_inventory` (
  `recordID` int(50) NOT NULL,
  `itemID` varchar(250) NOT NULL,
  `name` text NOT NULL,
  `uom` varchar(50) NOT NULL,
  `beginning` double NOT NULL,
  `deliveries` double NOT NULL,
  `transfers_in` double NOT NULL,
  `transfers_out` double NOT NULL,
  `spoilage` double NOT NULL,
  `ending` double NOT NULL,
  `usage_count` double NOT NULL,
  `remarks` text NOT NULL,
  `submitted_by` varchar(250) NOT NULL,
  `inventory_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `records_inventory`
--

INSERT INTO `records_inventory` (`recordID`, `itemID`, `name`, `uom`, `beginning`, `deliveries`, `transfers_in`, `transfers_out`, `spoilage`, `ending`, `usage_count`, `remarks`, `submitted_by`, `inventory_date`) VALUES
(16, 'CM4303', 'cajun mix', 'kg', 0, 0, 0, 0, 0, 0, 0, '', '', '2024-11-19'),
(17, 'L38795', 'lasagna', 'kg', 0, 0, 0, 0, 0, 0, 0, '', '', '2024-11-19'),
(18, 'PT1239', 'pineapple tidbits', 'kg', 0, 0, 0, 0, 0, 0, 0, '', '', '2024-11-19'),
(19, 'TRW406', 'tomato red whole', 'kg', 0, 0, 0, 0, 0, 0, 0, '', '', '2024-11-19'),
(20, 'CPS362', 'cheese parmesan shredded', 'kg', 0, 0, 0, 0, 0, 0, 0, '', '', '2024-11-19'),
(21, 'CM5024', 'cheese mozarella', 'kg', 0, 0, 0, 0, 0, 0, 0, '', '', '2024-11-19'),
(22, 'PBG560', 'pepper bell green', 'kg', 0, 0, 0, 0, 0, 0, 0, '', '', '2024-11-19'),
(23, 'PBR549', 'pepper bell red', 'kg', 0, 0, 0, 0, 0, 0, 0, '', '', '2024-11-19'),
(24, 'BPDX40', 'blend pan dough (165gms x 45)', 'pac', 34, 0, 0, 0, 0, 34, 0, '', '', '2024-11-19'),
(25, 'F5280', 'flour (10kgs/bag)', 'bag', 0, 0, 0, 0, 0, 0, 0, '', '', '2024-11-19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `daily_inventory`
--
ALTER TABLE `daily_inventory`
  ADD PRIMARY KEY (`inventoryID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`prodID`);

--
-- Indexes for table `records_inventory`
--
ALTER TABLE `records_inventory`
  ADD PRIMARY KEY (`recordID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `uid` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2024046;

--
-- AUTO_INCREMENT for table `daily_inventory`
--
ALTER TABLE `daily_inventory`
  MODIFY `inventoryID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `prodID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `records_inventory`
--
ALTER TABLE `records_inventory`
  MODIFY `recordID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
