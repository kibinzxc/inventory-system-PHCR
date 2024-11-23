-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 23, 2024 at 10:33 PM
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
  `usage_count` double NOT NULL,
  `status` text NOT NULL,
  `last_update` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_inventory`
--

INSERT INTO `daily_inventory` (`inventoryID`, `itemID`, `name`, `uom`, `beginning`, `deliveries`, `transfers_in`, `transfers_out`, `spoilage`, `ending`, `usage_count`, `status`, `last_update`, `updated_by`) VALUES
(46, 'HSS761', 'hot sauce sachet', 'pc', 2000, 0, 0, 0, 0, 2000, 0, 'in stock', '2024-11-22 04:05:22', 'SUPER ADMIN'),
(47, 'H29878', 'ham', 'kg', 2, 0, 0, 1.8, 0, 0.2, 0, 'low stock', '2024-11-22 18:37:28', 'SUPER ADMIN'),
(48, 'P18818', 'pepsi 1.5l', 'bt', 5, 0, 0, 0, 0, 5, 0, 'low stock', '2024-11-22 18:37:28', 'SUPER ADMIN'),
(51, 'PB4659', 'pizza box', 'pc', 20, 0, 0, 0, 0, 20, 0, 'in stock', '2024-11-24 03:53:00', 'SUPER ADMIN'),
(52, 'B87177', 'bacon', 'kg', 1, 0, 0, 0, 0, 1, 0, 'in stock', '2024-11-24 04:23:55', 'SUPER ADMIN'),
(53, 'BT1363', 'beef topping', 'kg', 35, 0, 0, 0, 0, 35, 0, 'in stock', '2024-11-24 04:24:12', 'SUPER ADMIN'),
(54, 'DB3050', 'dough blend', 'kg', 50, 0, 0, 0, 0, 50, 0, 'in stock', '2024-11-24 04:24:34', 'SUPER ADMIN'),
(55, 'B55959', 'boxliner', 'pc', 100, 0, 0, 0, 0, 100, 0, 'in stock', '2024-11-24 04:30:46', 'SUPER ADMIN'),
(56, 'PS7276', 'pizza sauce', 'kg', 25, 0, 0, 0, 0, 25, 0, 'in stock', '2024-11-24 04:31:00', 'SUPER ADMIN'),
(57, 'SO9727', 'soya oil', 'kg', 1, 0, 0, 0, 0, 1, 0, 'in stock', '2024-11-24 04:31:09', 'SUPER ADMIN'),
(58, 'QC9243', 'quickmelt cheese', 'kg', 2, 0, 0, 0, 0, 2, 0, 'in stock', '2024-11-24 04:31:24', 'SUPER ADMIN'),
(59, 'M26649', 'mozzarella', 'kg', 25, 0, 0, 0, 0, 25, 0, 'in stock', '2024-11-24 04:31:35', 'SUPER ADMIN'),
(60, 'P39089', 'parmesan', 'kg', 20, 0, 0, 0, 0, 20, 0, 'in stock', '2024-11-24 04:31:49', 'SUPER ADMIN'),
(61, 'F30735', 'flour', 'kg', 25, 0, 0, 0, 0, 25, 0, 'in stock', '2024-11-24 04:33:30', 'SUPER ADMIN'),
(62, 'C54769', 'cheddar', 'kg', 15, 0, 0, 0, 0, 15, 0, 'in stock', '2024-11-24 04:34:55', 'SUPER ADMIN'),
(63, 'SP8315', 'spaghetti pasta', 'kg', 5, 0, 0, 0, 0, 5, 0, 'in stock', '2024-11-24 04:35:24', 'SUPER ADMIN'),
(64, 'BS6169', 'bolognese sauce', 'kg', 3, 0, 0, 0, 0, 3, 0, 'in stock', '2024-11-24 04:36:07', 'SUPER ADMIN'),
(65, 'CS4175', 'carbonara sauce', 'kg', 2, 0, 0, 0, 0, 2, 0, 'in stock', '2024-11-24 04:36:12', 'SUPER ADMIN'),
(66, 'M46300', 'meatballs', 'pc', 160, 0, 0, 0, 0, 160, 0, 'in stock', '2024-11-24 04:38:11', 'SUPER ADMIN'),
(67, '718588', '7-up 1.5l', 'bt', 50, 0, 0, 0, 0, 50, 0, 'in stock', '2024-11-24 04:38:57', 'SUPER ADMIN');

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `id` int(11) NOT NULL,
  `invID` varchar(250) NOT NULL,
  `orders` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`orders`)),
  `total_amount` double NOT NULL,
  `amount_received` double NOT NULL,
  `amount_change` double NOT NULL,
  `order_type` text NOT NULL,
  `transaction_date` datetime NOT NULL DEFAULT current_timestamp(),
  `cashier` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`id`, `invID`, `orders`, `total_amount`, `amount_received`, `amount_change`, `order_type`, `transaction_date`, `cashier`) VALUES
(1, '11232024001', '[{\"name\":\"TEST\",\"size\":\"9inch Pan Pizza\",\"price\":299,\"quantity\":2}]', 598, 5000, 4402, 'walk-in', '2024-11-24 03:16:36', 'SUPER ADMIN'),
(2, '11232024002', '[{\"name\":\"TEST\",\"size\":\"9inch Pan Pizza\",\"price\":299,\"quantity\":2}]', 598, 600, 2, 'walk-in', '2024-11-24 03:17:22', 'SUPER ADMIN'),
(3, '11232024003', '[{\"name\":\"TEST\",\"size\":\"9inch Pan Pizza\",\"price\":299,\"quantity\":1}]', 299, 900, 601, 'walk-in', '2024-11-24 03:19:01', 'SUPER ADMIN'),
(4, '11232024004', '[{\"name\":\"TEST\",\"size\":\"9inch Pan Pizza\",\"price\":299,\"quantity\":1}]', 299, 535, 236, 'walk-in', '2024-11-24 03:21:11', 'SUPER ADMIN');

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
(9, 'Pepsi', 'beverages', '[{\"ingredient_name\": \"pepsi 1.5l\", \"quantity\": 1, \"measurement\": \"bottle\"}]', '', '1.5L', 139, 'available', 'pepsi.png'),
(10, 'Supreme', 'pizza', '[\r\n  { \"ingredient_name\": \"ham\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"pizza sauce\", \"quantity\": 90, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mozzarella\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"quickmelt cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"flour\", \"quantity\": 180, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"soya oil\", \"quantity\": 20, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"dough blend\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"pizza box\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"boxliner\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"hot sauce sachet\", \"quantity\": 2, \"measurement\": \"pcs\" },\r\n  { \"ingredient_name\": \"beef topping\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"onions\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"pork topping\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mushroom\", \"quantity\": 50, \"measurement\": \"grams\" }\r\n]', 'Six delightful toppings - beef, pepperoni, seasoned pork, bell pepper, onions and mushrooms.', '9inch Pan Pizza', 409, 'not available', 'supreme.png'),
(11, 'Bacon Supreme', 'pizza', '[\r\n  { \"ingredient_name\": \"pizza sauce\", \"quantity\": 90, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mozzarella\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"quickmelt cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"flour\", \"quantity\": 180, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"soya oil\", \"quantity\": 20, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"dough blend\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"pizza box\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"boxliner\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"hot sauce sachet\", \"quantity\": 2, \"measurement\": \"pcs\" },\r\n  { \"ingredient_name\": \"onions\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mushroom\", \"quantity\": 20, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"bell pepper green\", \"quantity\": 25, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"bell pepper red\", \"quantity\": 25, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"bacon\", \"quantity\": 100, \"measurement\": \"grams\" }\r\n]', 'Two layers of toasted bacon with bell peppers, onions and mushrooms on our signature pizza sauce.', '9inch Pan Pizza', 409, 'not available', 'bacon_supreme.png'),
(12, 'Bacon Margherita', 'pizza', '[\r\n  { \"ingredient_name\": \"mozzarella\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"quickmelt cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"flour\", \"quantity\": 180, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"soya oil\", \"quantity\": 20, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"dough blend\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"tomato\", \"quantity\": 30, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"basil\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"cheddar\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"carbonara sauce\", \"quantity\": 100, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"bacon\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"parmesan\", \"quantity\": 40, \"measurement\": \"grams\" }\r\n]\r\n', 'Tomatoes, basil, cheddar and mozzarella cheese topped with bacon and parmesan.', '9inch Pan Pizza', 409, 'not available', 'bacon_margherita.png'),
(13, 'BBQ Chicken Supreme', 'pizza', '[\r\n  { \"ingredient_name\": \"mozzarella\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"quickmelt cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"flour\", \"quantity\": 180, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"soya oil\", \"quantity\": 20, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"dough blend\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"pizza box\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"boxliner\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"hot sauce sachet\", \"quantity\": 2, \"measurement\": \"pcs\" },\r\n  { \"ingredient_name\": \"bbq sauce\", \"quantity\": 100, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"chicken chunks\", \"quantity\": 100, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mushrooms\", \"quantity\": 40, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"onions\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"parsley\", \"quantity\": 10, \"measurement\": \"grams\" }\r\n]\r\n', 'BBQ sauce, mozzarella, BBQ-coated chicken chunks, mushrooms, onions and parsley.', '9inch Pan Pizza', 409, 'not available', 'bbq_supreme.jpg'),
(14, 'Bacon Cheeseburger', 'pizza', '[\r\n  { \"ingredient_name\": \"pizza sauce\", \"quantity\": 90, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mozzarella\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"quickmelt cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"flour\", \"quantity\": 180, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"soya oil\", \"quantity\": 20, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"dough blend\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"pizza box\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"boxliner\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"hot sauce sachet\", \"quantity\": 2, \"measurement\": \"pcs\" },\r\n  { \"ingredient_name\": \"beef topping\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"bacon\", \"quantity\": 52, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"cheddar\", \"quantity\": 52, \"measurement\": \"grams\" }\r\n]\r\n', 'Beef, bacon and a triple layer of cheddar and mozzarella on our signature pizza sauce', '9inch Pan Pizza', 409, 'available', 'bacon_cheeseburger.png'),
(15, 'Pepperoni Lovers', 'pizza', '[\r\n  { \"ingredient_name\": \"pizza sauce\", \"quantity\": 90, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mozzarella\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"quickmelt cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"flour\", \"quantity\": 180, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"soya oil\", \"quantity\": 20, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"dough blend\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"pizza box\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"boxliner\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"hot sauce sachet\", \"quantity\": 2, \"measurement\": \"pcs\" },\r\n  { \"ingredient_name\": \"pepperoni\", \"quantity\": 75, \"measurement\": \"grams\" }\r\n]\r\n', 'A true classic pepperoni and mozzarella cheese on our signature pizza sauce.', '9inch Pan Pizza', 379, 'not available', 'pepperoni_lovers.jpg'),
(16, 'Cheese Lovers', 'pizza', '[\r\n  { \"ingredient_name\": \"pizza sauce\", \"quantity\": 90, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mozzarella\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"quickmelt cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"flour\", \"quantity\": 180, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"soya oil\", \"quantity\": 20, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"dough blend\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"pizza box\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"boxliner\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"hot sauce sachet\", \"quantity\": 2, \"measurement\": \"pcs\" },\r\n  { \"ingredient_name\": \"cheddar\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"parmesan\", \"quantity\": 50, \"measurement\": \"grams\" }\r\n]\r\n', 'Mozzarella, parmesan and cheddar cheeses. A cheese lover\'s delight.', '9inch Pan Pizza', 379, 'available', 'cheese_lovers.png'),
(17, 'Veggie Lovers', 'pizza', '[\r\n  { \"ingredient_name\": \"pizza sauce\", \"quantity\": 90, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mozzarella\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"quickmelt cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"flour\", \"quantity\": 180, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"soya oil\", \"quantity\": 20, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"dough blend\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"pizza box\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"boxliner\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"hot sauce sachet\", \"quantity\": 2, \"measurement\": \"pcs\" },\r\n  { \"ingredient_name\": \"pineapple tidbits\", \"quantity\": 70, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"bell pepper red\", \"quantity\": 25, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"bell pepper green\", \"quantity\": 25, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"onions\", \"quantity\": 50, \"measurement\": \"grams\" }\r\n]\r\n', 'Crunchy bell peppers, mushrooms, onions and juicy pineapples on a double layer of mozzarella cheese.', '9inch Pan Pizza', 379, 'not available', 'veggie_lovers.jpg'),
(18, 'Baked Ziti', 'pasta', '[\r\n  { \"ingredient_name\": \"ziti noodles\", \"quantity\": 120, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"bolognese sauce\", \"quantity\": 25, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"carbonara sauce\", \"quantity\": 25, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mozzarella\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"parmesan\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"parsley\", \"quantity\": 5, \"measurement\": \"grams\" }\r\n]\r\n', 'Ziti noodles, Bolognese sauce, white sauce, mozzarella cheese, parmesan cheese, dried parsley', 'Regular', 199, 'not available', 'baked_ziti.jpg'),
(19, 'Baked Carbonara', 'pasta', '[\r\n  { \"ingredient_name\": \"carbonara sauce\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"ham\", \"quantity\": 5, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"spaghetti pasta\", \"quantity\": 120, \"measurement\": \"grams\" }\r\n]\r\n', 'Baked spaghetti in the classic creamy white sauce with ham and mushrooms.', 'Regular', 359, 'available', 'baked_carbonara.jpg'),
(20, 'Baked Bolognese', 'pasta', '[\r\n  { \"ingredient_name\": \"bolognese sauce\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"spaghetti pasta\", \"quantity\": 120, \"measurement\": \"grams\" }\r\n]\r\n', 'Baked spaghetti pasta cooked al dente with savory sweet Bolognese sauce.', 'Regular', 349, 'available', 'baked_bolognese.jpg'),
(21, 'Baked Bolognese with Meatballs', 'pasta', '[\r\n  { \"ingredient_name\": \"bolognese sauce\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"spaghetti pasta\", \"quantity\": 120, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"meatballs\", \"quantity\": 3, \"measurement\": \"pcs\" }\r\n]\r\n', 'Baked pasta in savory-sweet Bolognese sauce and Italian meatballs. An all-time fave!', 'Regular', 679, 'available', 'baked_bolognese-meatballs.jpg'),
(22, '7-UP', 'beverages', '[{\"ingredient_name\": \"7-up 1.5l\", \"quantity\": 1, \"measurement\": \"bottle\"}]', '', '1.5L', 139, 'available', '7-up.png'),
(23, 'Bottled Water', 'beverages', '[{\"ingredient_name\": \"bottled water 500ml\", \"quantity\": 1, \"measurement\": \"bottle\"}]', '', '500ml', 39, 'not available', 'bottled_water.jpg');

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
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `inventoryID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `prodID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `records_inventory`
--
ALTER TABLE `records_inventory`
  MODIFY `recordID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
