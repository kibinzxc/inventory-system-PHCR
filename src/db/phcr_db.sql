-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 21, 2024 at 10:46 PM
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
  `remarks` text NOT NULL,
  `usage_count` double NOT NULL,
  `status` text NOT NULL,
  `last_update` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_inventory`
--

INSERT INTO `daily_inventory` (`inventoryID`, `itemID`, `name`, `uom`, `beginning`, `deliveries`, `transfers_in`, `transfers_out`, `spoilage`, `ending`, `remarks`, `usage_count`, `status`, `last_update`, `updated_by`) VALUES
(38, 'BPDX40', 'blend pan dough (165gms x 45)', 'pac', 2, 0, 0, 0, 0, 2, '', 0, 'low stock', '2024-11-21 13:14:30', ''),
(44, 'A65658', 'adobo', 'kg', 27, 0, 0, 0, 0, 27, '', 0, 'in stock', '2024-11-21 13:14:30', ''),
(45, 'S50891', 'sinigang', 'kg', 10, 0, 0, 0, 0, 10, '', 0, 'in stock', '2024-11-21 13:14:30', ''),
(46, 'HSS761', 'hot sauce sachet', 'pc', 2000, 0, 0, 0, 0, 2000, '', 0, 'in stock', '2024-11-22 04:05:22', 'SUPER ADMIN'),
(47, 'H29878', 'ham', 'kg', 2, 0, 0, 1.8, 0, 0.2, '', 0, 'in stock', '2024-11-22 05:12:16', 'SUPER ADMIN');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `prodID` int(50) NOT NULL,
  `name` text NOT NULL,
  `category` varchar(250) NOT NULL,
  `ingredients` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`ingredients`)),
  `slogan` text NOT NULL,
  `size` varchar(250) NOT NULL,
  `price` double NOT NULL,
  `status` varchar(250) NOT NULL,
  `img` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`prodID`, `name`, `category`, `ingredients`, `slogan`, `size`, `price`, `status`, `img`) VALUES
(1, 'Cheese Lovers', 'Pizza', '[{\"ingredient_name\": \"HAM\", \"quantity\": 2050, \"measurement\": \"grams\"}, {\"ingredient_name\": \"PIZZA SAUCE\", \"quantity\": 90, \"measurement\": \"grams\"}, {\"ingredient_name\": \"MOZZARELLA\", \"quantity\": 50, \"measurement\": \"grams\"}, {\"ingredient_name\": \"QUICKMELT CHEESE\", \"quantity\": 50, \"measurement\": \"grams\"}, {\"ingredient_name\": \"FLOUR\", \"quantity\": 180, \"measurement\": \"grams\"}, {\"ingredient_name\": \"SOYA OIL\", \"quantity\": 20, \"measurement\": \"grams\"}, {\"ingredient_name\": \"Dough Blend\", \"quantity\": 10, \"measurement\": \"grams\"}, {\"ingredient_name\": \"Pizza Box\", \"quantity\": 1, \"measurement\": \"pc\"}, {\"ingredient_name\": \"Boxliner\", \"quantity\": 1, \"measurement\": \"pc\"}, {\"ingredient_name\": \"hot sauce sachet\", \"quantity\": 2, \"measurement\": \"pcs\"}, {\"ingredient_name\": \"Beef topping\", \"quantity\": 50, \"measurement\": \"grams\"}, {\"ingredient_name\": \"Onions\", \"quantity\": 50, \"measurement\": \"grams\"}, {\"ingredient_name\": \"Pork topping\", \"quantity\": 50, \"measurement\": \"grams\"}, {\"ingredient_name\": \"Mushroom\", \"quantity\": 50, \"measurement\": \"grams\"}]', 'Mozzarella, parmesan and cheddar cheeses. A cheese lover\'s delight.', '9inch Pan Pizza', 299, 'not available', 'cheese_lovers.png'),
(8, 'Cheese Lovers', 'Pizza', '[{\"ingredient_name\": \"ham\", \"quantity\": 200, \"measurement\": \"grams\"},\r\n    {\"ingredient_name\": \"hot sauce sachet\", \"quantity\": 2, \"measurement\": \"pcs\"}]', 'Mozzarella, parmesan and cheddar cheeses. A cheese lover\'s delight.', '9inch Pan Pizza', 299, 'available', 'cheese_lovers.png'),
(9, 'Cheese Lovers', 'beverages', '[{\"ingredient_name\": \"HAM\", \"quantity\": 2050, \"measurement\": \"grams\"}, {\"ingredient_name\": \"PIZZA SAUCE\", \"quantity\": 90, \"measurement\": \"grams\"}, {\"ingredient_name\": \"MOZZARELLA\", \"quantity\": 50, \"measurement\": \"grams\"}, {\"ingredient_name\": \"QUICKMELT CHEESE\", \"quantity\": 50, \"measurement\": \"grams\"}, {\"ingredient_name\": \"FLOUR\", \"quantity\": 180, \"measurement\": \"grams\"}, {\"ingredient_name\": \"SOYA OIL\", \"quantity\": 20, \"measurement\": \"grams\"}, {\"ingredient_name\": \"Dough Blend\", \"quantity\": 10, \"measurement\": \"grams\"}, {\"ingredient_name\": \"Pizza Box\", \"quantity\": 1, \"measurement\": \"pc\"}, {\"ingredient_name\": \"Boxliner\", \"quantity\": 1, \"measurement\": \"pc\"}, {\"ingredient_name\": \"hot sauce sachet\", \"quantity\": 2, \"measurement\": \"pcs\"}, {\"ingredient_name\": \"Beef topping\", \"quantity\": 50, \"measurement\": \"grams\"}, {\"ingredient_name\": \"Onions\", \"quantity\": 50, \"measurement\": \"grams\"}, {\"ingredient_name\": \"Pork topping\", \"quantity\": 50, \"measurement\": \"grams\"}, {\"ingredient_name\": \"Mushroom\", \"quantity\": 50, \"measurement\": \"grams\"}]', 'Mozzarella, parmesan and cheddar cheeses. A cheese lover\'s delight.', '9inch Pan Pizza', 299, 'not available', 'cheese_lovers.png');

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
(78, 'BPDX40', 'blend pan dough (165gms x 45)', 'pac', 5, 0, 5, 5, 0, 2, 3, '', 'SUPER ADMIN', '2024-11-17'),
(79, 'A65658', 'adobo', 'kg', 6, 6, 7, 3, 3, 8, 5, '', 'SUPER ADMIN', '2024-11-17'),
(80, 'BPDX40', 'blend pan dough (165gms x 45)', 'pac', 1, 5, 5, 5, 5, 0, 1, '', 'SUPER ADMIN', '2024-11-18'),
(81, 'A65658', 'adobo', 'kg', 8, 3, 5, 3, 1, 7, 5, '', 'SUPER ADMIN', '2024-11-18'),
(83, 'BPDX40', 'blend pan dough (165gms x 45)', 'pac', 1, 46, 4, 6, 2, 39, 4, '', 'SUPER ADMIN', '2024-11-19'),
(84, 'A65658', 'adobo', 'kg', 6, 5, 0, 0, 1, 8, 2, '', 'SUPER ADMIN', '2024-11-19'),
(85, 'S50891', 'sinigang', 'kg', 5, 6, 3, 6, 2, 3, 3, '', 'SUPER ADMIN', '2024-11-19'),
(86, 'S50891', 'sinigang', 'kg', 7, 6, 3, 6, 2, 2, 4, '', 'SUPER ADMIN', '2024-12-18'),
(87, 'BPDX40', 'blend pan dough (165gms x 45)', 'pac', 1, 0, 6, 5, 0, 2, 0, '', 'SUPER ADMIN', '2024-11-20'),
(88, 'A65658', 'adobo', 'kg', 2, 14, 14, 6, 9, 10, 5, 'ninakaw bhie', 'SUPER ADMIN', '2024-11-20'),
(89, 'S50891', 'sinigang', 'kg', 2, 5, 10, 7, 0, 10, 0, '', 'SUPER ADMIN', '2024-11-20');

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
  MODIFY `inventoryID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `prodID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `records_inventory`
--
ALTER TABLE `records_inventory`
  MODIFY `recordID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
