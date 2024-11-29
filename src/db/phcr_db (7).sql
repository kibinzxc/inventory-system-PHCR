-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2024 at 01:50 AM
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
  `password` varchar(100) NOT NULL,
  `number` varchar(20) DEFAULT '+63'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`uid`, `name`, `email`, `userType`, `password`, `number`) VALUES
(2024014, 'Theresa Soriano', 'phcr.inventory@gmail.com', 'super_admin', '2c103f2c4ed1e59c0b4e2e01821770fa', '+63'),
(2024043, 'Kevin Almirante', 'almirantekevindaniel26@gmail.com', 'rider', '2c103f2c4ed1e59c0b4e2e01821770fa', '+63'),
(2024045, 'Maricar Glorioso', 'maricar.glorioso@cvsu.edu.ph', 'cashier', '2c103f2c4ed1e59c0b4e2e01821770fa', '+63'),
(2024047, 'Mark Basmayor', 'markanthony.basmayor@cvsu.edu.ph', 'admin', '2c103f2c4ed1e59c0b4e2e01821770fa', '+63'),
(2024048, 'Alden Richards', 'stockman@gmail.com', 'stockman', '2c103f2c4ed1e59c0b4e2e01821770fa', '+63');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(100) NOT NULL,
  `dish_id` int(100) NOT NULL,
  `uid` int(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `size` varchar(100) NOT NULL,
  `qty` int(50) NOT NULL,
  `price` int(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `totalprice` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customerinfo`
--

CREATE TABLE `customerinfo` (
  `customerID` int(100) NOT NULL,
  `uid` int(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contactNum` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customerinfo`
--

INSERT INTO `customerinfo` (`customerID`, `uid`, `name`, `email`, `contactNum`, `address`) VALUES
(1, 131810009, 'MAGNO, JOHN PAUL', 'magno123@gmail.com', '09474797762', 'B2, Camachile, Panapaan 4, Bacoor, Cavite, 4102');

-- --------------------------------------------------------

--
-- Table structure for table `daily_inventory`
--

CREATE TABLE `daily_inventory` (
  `inventoryID` int(50) NOT NULL,
  `itemID` varchar(250) NOT NULL,
  `name` text NOT NULL,
  `uom` varchar(50) NOT NULL,
  `beginning` double(11,2) NOT NULL,
  `deliveries` double(11,2) NOT NULL,
  `transfers_in` double(11,2) NOT NULL,
  `transfers_out` double(11,2) NOT NULL,
  `spoilage` double(11,2) NOT NULL,
  `remarks` text NOT NULL,
  `ending` double(11,2) NOT NULL,
  `usage_count` double(11,2) NOT NULL,
  `status` text NOT NULL,
  `last_update` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_inventory`
--

INSERT INTO `daily_inventory` (`inventoryID`, `itemID`, `name`, `uom`, `beginning`, `deliveries`, `transfers_in`, `transfers_out`, `spoilage`, `remarks`, `ending`, `usage_count`, `status`, `last_update`, `updated_by`) VALUES
(46, 'HSS761', 'hot sauce sachet', 'pc', 14828.00, 0.00, 0.00, 0.00, 0.00, '', 14818.00, 10.00, 'in stock', '2024-11-29 08:26:07', ''),
(47, 'H29878', 'ham', 'kg', 148.98, 0.00, 0.00, 0.00, 0.00, '', 148.98, 0.00, 'in stock', '2024-11-28 08:22:47', ''),
(48, 'P18818', 'pepsi 1.5l', 'bt', 473.00, 0.00, 0.00, 0.00, 0.00, '', 471.00, 2.00, 'in stock', '2024-11-28 08:25:32', ''),
(51, 'PB4659', 'pizza box', 'pc', 908.00, 0.00, 0.00, 0.00, 0.00, '', 903.00, 5.00, 'in stock', '2024-11-29 08:26:07', ''),
(52, 'B87177', 'bacon', 'kg', 68.28, 0.00, 0.00, 0.00, 2.00, '', 66.13, 0.15, 'in stock', '2024-11-29 08:33:33', 'Theresa Soriano'),
(53, 'BT1363', 'beef topping', 'kg', 88.00, 0.00, 0.00, 0.00, 0.00, '', 87.95, 0.05, 'in stock', '2024-11-28 08:25:32', ''),
(54, 'DB3050', 'dough blend', 'kg', 73.08, 0.00, 0.00, 0.00, 0.00, '', 73.03, 0.05, 'in stock', '2024-11-29 08:26:07', ''),
(55, 'B55959', 'boxliner', 'pc', 4908.00, 0.00, 0.00, 0.00, 0.00, '', 4903.00, 5.00, 'in stock', '2024-11-29 08:26:07', ''),
(56, 'PS7276', 'pizza sauce', 'kg', 493.52, 0.00, 0.00, 0.00, 0.00, '', 493.34, 0.18, 'in stock', '2024-11-28 08:25:32', ''),
(57, 'SO9727', 'soya oil', 'kg', 288.16, 0.00, 0.00, 0.00, 0.00, '', 288.06, 0.10, 'in stock', '2024-11-29 08:26:07', ''),
(58, 'QC9243', 'quickmelt cheese', 'kg', 595.40, 0.00, 0.00, 0.00, 0.00, '', 595.15, 0.25, 'in stock', '2024-11-29 08:26:07', ''),
(59, 'MC8936', 'mozzarella cheese', 'kg', 310.00, 0.00, 0.00, 0.00, 0.00, '', 309.55, 0.45, 'in stock', '2024-11-19 08:36:52', ''),
(60, 'PC5093', 'parmesan cheese', 'kg', 18.73, 0.00, 0.00, 0.00, 0.00, '', 18.69, 0.04, 'in stock', '2024-11-19 08:36:52', ''),
(61, 'F30735', 'flour', 'kg', 693.44, 0.00, 0.00, 0.00, 0.00, '', 692.54, 0.90, 'in stock', '2024-11-29 08:26:07', ''),
(62, 'C54769', 'cheddar', 'kg', 47.88, 0.00, 0.00, 0.00, 0.00, '', 47.83, 0.05, 'in stock', '2024-11-28 08:25:32', ''),
(63, 'SP8315', 'spaghetti pasta', 'kg', 297.24, 0.00, 0.00, 0.00, 0.00, '', 297.24, 0.00, 'in stock', '2024-11-28 08:22:47', ''),
(64, 'BS6169', 'bolognese sauce', 'kg', 149.04, 0.00, 0.00, 0.00, 0.00, '', 148.94, 0.10, 'in stock', '2024-11-19 08:36:52', ''),
(65, 'CS4175', 'carbonara sauce', 'kg', 13.80, 0.00, 0.00, 0.00, 0.00, '', 13.70, 0.10, 'in stock', '2024-11-19 08:36:52', ''),
(66, 'M46300', 'meatballs', 'pc', 82.00, 0.00, 0.00, 0.00, 0.00, '', 82.00, 0.00, 'in stock', '2024-11-28 08:22:47', ''),
(67, '718588', '7-up 1.5l', 'bt', 493.00, 0.00, 0.00, 0.00, 4.00, 'spilled', 489.00, 0.00, 'in stock', '2024-11-29 08:29:17', 'Theresa Soriano'),
(68, 'P64928', 'pepperoni', 'kg', 5.23, 0.00, 0.00, 0.00, 0.00, '', 5.23, 0.00, 'in stock', '2024-11-28 08:22:47', ''),
(70, 'B58256', 'basil', 'kg', 199.94, 0.00, 0.00, 0.00, 0.00, '', 199.94, 0.00, 'in stock', '2024-11-28 08:22:47', ''),
(71, 'T84140', 'tomato', 'kg', 399.82, 0.00, 0.00, 0.00, 0.00, '', 399.82, 0.00, 'in stock', '2024-11-28 08:22:47', ''),
(72, 'BPG668', 'bell pepper green', 'kg', 199.78, 0.00, 0.00, 0.00, 0.00, '', 199.75, 0.02, 'in stock', '2024-11-28 08:25:10', ''),
(73, 'BPR743', 'bell pepper red', 'kg', 199.78, 0.00, 0.00, 0.00, 0.00, '', 199.75, 0.02, 'in stock', '2024-11-28 08:25:10', ''),
(74, 'O07558', 'onions', 'kg', 397.90, 0.00, 0.00, 0.00, 0.00, '', 397.70, 0.20, 'in stock', '2024-11-29 08:26:07', ''),
(75, 'M04399', 'mushroom', 'kg', 428.36, 0.00, 0.00, 0.00, 0.00, '', 428.22, 0.14, 'in stock', '2024-11-29 08:26:07', ''),
(76, 'BS3890', 'bbq sauce', 'kg', 98.60, 0.00, 0.00, 0.00, 0.00, '', 98.30, 0.30, 'in stock', '2024-11-29 08:26:07', ''),
(77, 'P59966', 'parsley', 'kg', 229.83, 0.00, 0.00, 0.00, 0.00, '', 229.78, 0.05, 'in stock', '2024-11-19 08:36:52', ''),
(78, 'CC4083', 'chicken chunks', 'kg', 698.60, 0.00, 0.00, 0.00, 0.00, '', 698.30, 0.30, 'in stock', '2024-11-29 08:26:07', ''),
(79, 'PT4200', 'pork topping', 'kg', 299.00, 0.00, 0.00, 0.00, 0.00, '', 299.00, 0.00, 'in stock', '2024-11-28 08:22:47', ''),
(80, 'PT6853', 'pineapple tidbits', 'kg', 529.72, 0.00, 0.00, 0.00, 0.00, '', 529.72, 0.00, 'in stock', '2024-11-28 08:22:47', ''),
(81, 'ZN7784', 'ziti noodles', 'kg', 529.04, 0.00, 0.00, 0.00, 0.00, '', 528.56, 0.48, 'in stock', '2024-11-19 08:36:52', ''),
(82, 'BW5195', 'bottled water 500ml', 'bt', 485.00, 0.00, 0.00, 0.00, 0.00, '', 485.00, 0.00, 'in stock', '2024-11-28 08:22:47', '');

-- --------------------------------------------------------

--
-- Table structure for table `dishes`
--

CREATE TABLE `dishes` (
  `dish_id` int(100) NOT NULL,
  `categoryID` int(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slogan` varchar(255) NOT NULL,
  `size` varchar(100) NOT NULL,
  `price` int(100) NOT NULL,
  `img` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dishes`
--

INSERT INTO `dishes` (`dish_id`, `categoryID`, `name`, `slogan`, `size`, `price`, `img`) VALUES
(4, 1, 'BBQ Chicken Supreme', 'BBQ sauce, mozzarella, BBQ-coated chicken chunks, mushrooms, onions and parsley.', '9inch', 409, 'bbq_supreme.jpg'),
(6, 2, 'Baked Ziti', 'Ziti noodles, Bolognese sauce, white sauce, mozzarella cheese, parmesan cheese, dried parsley', 'Regular', 199, 'baked_ziti.jpg'),
(7, 2, 'Baked Carbonara', 'Baked spaghetti in the classic creamy white sauce with ham and mushrooms.', 'Regular', 359, 'baked_carbonara.jpg'),
(8, 2, 'Baked Bolognese', 'Baked spaghetti pasta cooked al dente with savory sweet Bolognese sauce.', 'Regular', 349, 'baked_bolognese.jpg'),
(9, 2, 'Baked Bolognese with Meatballs', 'Baked pasta in savory-sweet Bolognese sauce and Italian meatballs. An all-time fave!', 'Regular', 679, 'baked_bolognese-meatballs.jpg'),
(10, 3, 'Pepsi', '', '1.5L', 139, 'pepsi.png'),
(11, 3, '7-UP', '', '1.5L', 139, '7-up.png'),
(12, 3, 'Mountain Dew', '', '1.5L', 139, 'mt_dew.png'),
(14, 1, 'Hawaiian Supreme', 'Say \'Aloha\' to our all-time favorite with double layers of ham and pineapple!', '9inch', 409, 'hawaiian_supreme.png'),
(15, 1, 'Pepperoni Lovers', 'A true classic- pepperoni and mozzarella cheese on our signature pizza sauce.', '9inch', 379, 'pepperoni_lovers.jpg'),
(16, 1, 'Veggie Lovers', 'Crunchy bell peppers, mushrooms, onions and juicy pineapples on a double layer of mozzarella cheese.', '9inch', 379, 'veggie_lovers.jpg'),
(17, 1, 'Cheese Lovers', 'Mozzarella, parmesan and cheddar cheeses. A cheese lover\'s delight.', '9inch', 379, 'cheese_lovers.png'),
(18, 1, 'Bacon Cheeseburger', 'Beef, bacon and a triple layer of cheddar and mozzarella on our signature pizza sauce', '9inch', 409, 'bacon_cheeseburger.png'),
(19, 1, 'Bacon Supreme', 'Two layers of toasted bacon with bell peppers, onions and mushrooms on our signature pizza sauce.', '9inch', 409, 'bacon_supreme.png'),
(21, 1, 'Supreme', 'Six delightful toppings - beef, pepperoni, seasoned pork, bell pepper, onions and mushrooms.', '9inch', 409, 'supreme.png'),
(22, 1, 'Bacon Margherita', 'Tomatoes, basil, cheddar and mozzarella cheese topped with bacon and parmesan.', '9inch', 409, 'bacon_margherita.png'),
(23, 3, 'Bottled Water', '', '500ML', 39, 'bottled_water.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `food_category`
--

CREATE TABLE `food_category` (
  `categoryID` int(100) NOT NULL,
  `cName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_category`
--

INSERT INTO `food_category` (`categoryID`, `cName`) VALUES
(1, 'Pizza'),
(2, 'Pasta'),
(3, 'Beverages');

-- --------------------------------------------------------

--
-- Table structure for table `forgot_user`
--

CREATE TABLE `forgot_user` (
  `id` int(50) NOT NULL,
  `uid` int(250) NOT NULL,
  `date_notified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forgot_user`
--

INSERT INTO `forgot_user` (`id`, `uid`, `date_notified`) VALUES
(0, 2024014, '2024-11-28 21:51:12'),
(0, 2024014, '2024-11-29 01:44:26'),
(0, 2024014, '2024-11-18 07:10:22'),
(0, 2024014, '2024-11-21 07:31:37'),
(0, 2024014, '2024-11-25 07:48:50'),
(0, 2024014, '2024-11-11 07:52:31'),
(0, 2024014, '2024-11-12 07:53:02'),
(0, 2024014, '2024-11-14 08:01:02'),
(0, 2024014, '2024-11-15 08:01:37'),
(0, 2024014, '2024-11-16 08:08:35'),
(0, 2024014, '2024-11-17 08:09:09'),
(0, 2024014, '2024-11-27 08:10:39'),
(0, 2024014, '2024-11-26 08:18:51'),
(0, 2024014, '2024-11-19 08:36:40');

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
  `mop` text NOT NULL,
  `transaction_date` datetime NOT NULL DEFAULT current_timestamp(),
  `cashier` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`id`, `invID`, `orders`, `total_amount`, `amount_received`, `amount_change`, `order_type`, `mop`, `transaction_date`, `cashier`) VALUES
(259, '11182024001', '[{\"name\":\"BBQ Chicken Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":2},{\"name\":\"Bottled Water\",\"size\":\"500ml\",\"price\":39,\"quantity\":1},{\"name\":\"Pepsi\",\"size\":\"1.5L\",\"price\":139,\"quantity\":1}]', 996, 1000, 4, 'walk-in', 'cash', '2024-11-18 07:27:35', 'Theresa Soriano'),
(260, '11182024002', '[{\"name\":\"Baked Bolognese with Meatballs\",\"size\":\"Regular\",\"price\":679,\"quantity\":1},{\"name\":\"Pepsi\",\"size\":\"1.5L\",\"price\":139,\"quantity\":1}]', 818, 820, 2, 'walk-in', 'cash', '2024-11-18 07:27:58', 'Theresa Soriano'),
(261, '11182024003', '[{\"name\":\"Baked Ziti\",\"size\":\"Regular\",\"price\":199,\"quantity\":1},{\"name\":\"Bacon Cheeseburger\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":2}]', 1017, 1200, 183, 'walk-in', 'cash', '2024-11-18 07:29:40', 'Theresa Soriano'),
(262, '11192024001', '[{\"name\":\"Pepsi\",\"size\":\"1.5L\",\"price\":139,\"quantity\":2},{\"name\":\"Baked Bolognese\",\"size\":\"Regular\",\"price\":349,\"quantity\":2},{\"name\":\"Baked Carbonara\",\"size\":\"Regular\",\"price\":359,\"quantity\":1}]', 1335, 1335, 0, 'walk-in', 'cash', '2024-11-19 07:30:11', 'Theresa Soriano'),
(263, '11192024002', '[{\"name\":\"BBQ Chicken Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":1},{\"name\":\"Veggie Lovers\",\"size\":\"9inch Pan Pizza\",\"price\":379,\"quantity\":1}]', 788, 788, 0, 'walk-in', 'cash', '2024-11-19 07:30:24', 'Theresa Soriano'),
(264, '11202024001', '[{\"name\":\"Bacon Margherita\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":1},{\"name\":\"Pepperoni Lovers\",\"size\":\"9inch Pan Pizza\",\"price\":379,\"quantity\":1},{\"name\":\"Veggie Lovers\",\"size\":\"9inch Pan Pizza\",\"price\":379,\"quantity\":1},{\"name\":\"Bottled Water\",\"size\":\"500ml\",\"price\":39,\"quantity\":4}]', 1323, 1323, 0, 'walk-in', 'cash', '2024-11-20 07:30:43', 'Theresa Soriano'),
(265, '11202024002', '[{\"name\":\"Baked Carbonara\",\"size\":\"Regular\",\"price\":359,\"quantity\":2},{\"name\":\"Cheese Lovers\",\"size\":\"9inch Pan Pizza\",\"price\":379,\"quantity\":1}]', 1097, 1100, 3, 'walk-in', 'cash', '2024-11-20 07:31:00', 'Theresa Soriano'),
(266, '11212024001', '[{\"name\":\"Cheese Lovers\",\"size\":\"9inch Pan Pizza\",\"price\":379,\"quantity\":2}]', 758, 800, 42, 'walk-in', 'cash', '2024-11-21 07:31:17', 'Theresa Soriano'),
(267, '11212024002', '[{\"name\":\"Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":3},{\"name\":\"7-UP\",\"size\":\"1.5L\",\"price\":139,\"quantity\":1},{\"name\":\"Pepsi\",\"size\":\"1.5L\",\"price\":139,\"quantity\":1}]', 1505, 1510, 5, 'walk-in', 'cash', '2024-11-21 07:32:14', 'Theresa Soriano'),
(268, '11222024001', '[{\"name\":\"Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":2},{\"name\":\"Bacon Cheeseburger\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":2},{\"name\":\"7-UP\",\"size\":\"1.5L\",\"price\":139,\"quantity\":1}]', 1775, 1800, 25, 'walk-in', 'cash', '2024-11-22 07:32:45', 'Theresa Soriano'),
(269, '11222024002', '[{\"name\":\"Bottled Water\",\"size\":\"500ml\",\"price\":39,\"quantity\":2},{\"name\":\"Baked Ziti\",\"size\":\"Regular\",\"price\":199,\"quantity\":1}]', 277, 277, 0, 'walk-in', 'cash', '2024-11-22 07:32:55', 'Theresa Soriano'),
(270, '11222024003', '[{\"name\":\"Bottled Water\",\"size\":\"500ml\",\"price\":39,\"quantity\":4},{\"name\":\"Pepperoni Lovers\",\"size\":\"9inch Pan Pizza\",\"price\":379,\"quantity\":1},{\"name\":\"Bacon Cheeseburger\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":2}]', 1353, 1500, 147, 'walk-in', 'cash', '2024-11-22 07:38:41', 'Theresa Soriano'),
(271, '11232024001', '[{\"name\":\"Bacon Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":1},{\"name\":\"BBQ Chicken Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":1},{\"name\":\"Cheese Lovers\",\"size\":\"9inch Pan Pizza\",\"price\":379,\"quantity\":1}]', 1197, 2000, 803, 'walk-in', 'cash', '2024-11-23 07:47:10', 'Theresa Soriano'),
(272, '11232024002', '[{\"name\":\"Pepsi\",\"size\":\"1.5L\",\"price\":139,\"quantity\":3},{\"name\":\"Baked Bolognese\",\"size\":\"Regular\",\"price\":349,\"quantity\":1},{\"name\":\"Bacon Cheeseburger\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":4}]', 2402, 4000, 1598, 'walk-in', 'cash', '2024-11-23 07:47:23', 'Theresa Soriano'),
(273, '11242024001', '[{\"name\":\"Bacon Cheeseburger\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":2}]', 818, 890, 72, 'walk-in', 'cash', '2024-11-24 07:47:32', 'Theresa Soriano'),
(274, '11242024002', '[{\"name\":\"Baked Carbonara\",\"size\":\"Regular\",\"price\":359,\"quantity\":1},{\"name\":\"Baked Bolognese with Meatballs\",\"size\":\"Regular\",\"price\":679,\"quantity\":1},{\"name\":\"Pepsi\",\"size\":\"1.5L\",\"price\":139,\"quantity\":2}]', 1316, 1400, 84, 'walk-in', 'cash', '2024-11-24 07:47:48', 'Theresa Soriano'),
(275, '11242024003', '[{\"name\":\"Baked Carbonara\",\"size\":\"Regular\",\"price\":359,\"quantity\":3},{\"name\":\"Baked Bolognese with Meatballs\",\"size\":\"Regular\",\"price\":679,\"quantity\":1}]', 1756, 1800, 44, 'walk-in', 'cash', '2024-11-24 07:48:04', 'Theresa Soriano'),
(276, '11242024004', '[{\"name\":\"Cheese Lovers\",\"size\":\"9inch Pan Pizza\",\"price\":379,\"quantity\":3},{\"name\":\"Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":3}]', 2364, 2500, 136, 'walk-in', 'cash', '2024-11-24 07:48:19', 'Theresa Soriano'),
(277, '11252024001', '[{\"name\":\"Veggie Lovers\",\"size\":\"9inch Pan Pizza\",\"price\":379,\"quantity\":1},{\"name\":\"Bacon Margherita\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":1}]', 788, 800, 12, 'walk-in', 'cash', '2024-11-25 07:48:30', 'Theresa Soriano'),
(278, '11252024002', '[{\"name\":\"Bacon Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":2},{\"name\":\"Cheese Lovers\",\"size\":\"9inch Pan Pizza\",\"price\":379,\"quantity\":3}]', 1955, 2000, 45, 'walk-in', 'cash', '2024-11-25 07:49:02', 'Theresa Soriano'),
(279, '11252024003', '[{\"name\":\"Pepsi\",\"size\":\"1.5L\",\"price\":139,\"quantity\":3},{\"name\":\"Pepperoni Lovers\",\"size\":\"9inch Pan Pizza\",\"price\":379,\"quantity\":2},{\"name\":\"Cheese Lovers\",\"size\":\"9inch Pan Pizza\",\"price\":379,\"quantity\":2}]', 1933, 2000, 67, 'walk-in', 'cash', '2024-11-25 07:49:19', 'Theresa Soriano'),
(280, '11262024001', '[{\"name\":\"BBQ Chicken Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":2}]', 818, 1000, 182, 'walk-in', 'cash', '2024-11-26 07:49:45', 'Theresa Soriano'),
(281, '11112024001', '[{\"name\":\"BBQ Chicken Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":2},{\"name\":\"Bacon Cheeseburger\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":1}]', 1227, 1300, 73, 'walk-in', 'cash', '2024-11-11 07:50:06', 'Theresa Soriano'),
(282, '11112024002', '[{\"name\":\"Pepsi\",\"size\":\"1.5L\",\"price\":139,\"quantity\":2},{\"name\":\"7-UP\",\"size\":\"1.5L\",\"price\":139,\"quantity\":1},{\"name\":\"Baked Ziti\",\"size\":\"Regular\",\"price\":199,\"quantity\":2}]', 815, 820, 5, 'walk-in', 'cash', '2024-11-11 07:50:20', 'Theresa Soriano'),
(283, '11112024003', '[{\"name\":\"Cheese Lovers\",\"size\":\"9inch Pan Pizza\",\"price\":379,\"quantity\":1},{\"name\":\"Pepperoni Lovers\",\"size\":\"9inch Pan Pizza\",\"price\":379,\"quantity\":1}]', 758, 800, 42, 'walk-in', 'cash', '2024-11-11 07:50:28', 'Theresa Soriano'),
(284, '11122024001', '[{\"name\":\"BBQ Chicken Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":1},{\"name\":\"Bacon Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":1}]', 818, 818, 0, 'walk-in', 'cash', '2024-11-12 07:56:19', 'Theresa Soriano'),
(285, '11122024002', '[{\"name\":\"Baked Bolognese with Meatballs\",\"size\":\"Regular\",\"price\":679,\"quantity\":1},{\"name\":\"Baked Bolognese\",\"size\":\"Regular\",\"price\":349,\"quantity\":1}]', 1028, 1030, 2, 'walk-in', 'cash', '2024-11-12 07:56:29', 'Theresa Soriano'),
(286, '11122024003', '[{\"name\":\"Pepsi\",\"size\":\"1.5L\",\"price\":139,\"quantity\":1},{\"name\":\"Bottled Water\",\"size\":\"500ml\",\"price\":39,\"quantity\":1}]', 178, 180, 2, 'walk-in', 'cash', '2024-11-12 07:56:41', 'Theresa Soriano'),
(287, '11132024001', '[{\"name\":\"Bacon Margherita\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":2},{\"name\":\"Bacon Cheeseburger\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":1}]', 1227, 1300, 73, 'walk-in', 'cash', '2024-11-13 07:56:56', 'Theresa Soriano'),
(288, '11132024002', '[{\"name\":\"Baked Bolognese with Meatballs\",\"size\":\"Regular\",\"price\":679,\"quantity\":1},{\"name\":\"Baked Bolognese\",\"size\":\"Regular\",\"price\":349,\"quantity\":1},{\"name\":\"Baked Carbonara\",\"size\":\"Regular\",\"price\":359,\"quantity\":1}]', 1387, 1400, 13, 'walk-in', 'cash', '2024-11-13 07:57:08', 'Theresa Soriano'),
(289, '11142024001', '[{\"name\":\"Baked Ziti\",\"size\":\"Regular\",\"price\":199,\"quantity\":2},{\"name\":\"Cheese Lovers\",\"size\":\"9inch Pan Pizza\",\"price\":379,\"quantity\":2},{\"name\":\"Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":2},{\"name\":\"Pepsi\",\"size\":\"1.5L\",\"price\":139,\"quantity\":1}]', 2113, 2200, 87, 'walk-in', 'cash', '2024-11-14 08:00:57', 'Theresa Soriano'),
(290, '11142024002', '[{\"name\":\"BBQ Chicken Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":1},{\"name\":\"Pepsi\",\"size\":\"1.5L\",\"price\":139,\"quantity\":1}]', 548, 600, 52, 'walk-in', 'cash', '2024-11-14 08:01:18', 'Theresa Soriano'),
(291, '11152024001', '[{\"name\":\"Bacon Cheeseburger\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":2},{\"name\":\"BBQ Chicken Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":1}]', 1227, 1300, 73, 'walk-in', 'cash', '2024-11-15 08:01:32', 'Theresa Soriano'),
(292, '11152024002', '[{\"name\":\"Pepsi\",\"size\":\"1.5L\",\"price\":139,\"quantity\":2},{\"name\":\"Baked Bolognese\",\"size\":\"Regular\",\"price\":349,\"quantity\":2}]', 976, 1000, 24, 'walk-in', 'cash', '2024-11-15 08:01:46', 'Theresa Soriano'),
(293, '11152024003', '[{\"name\":\"Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":3}]', 1227, 1300, 73, 'walk-in', 'cash', '2024-11-15 08:07:06', 'Theresa Soriano'),
(294, '11152024004', '[{\"name\":\"Baked Ziti\",\"size\":\"Regular\",\"price\":199,\"quantity\":1},{\"name\":\"Baked Bolognese\",\"size\":\"Regular\",\"price\":349,\"quantity\":1},{\"name\":\"Baked Bolognese with Meatballs\",\"size\":\"Regular\",\"price\":679,\"quantity\":1}]', 1227, 1400, 173, 'walk-in', 'cash', '2024-11-15 08:07:19', 'Theresa Soriano'),
(295, '11152024005', '[{\"name\":\"Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":1},{\"name\":\"Veggie Lovers\",\"size\":\"9inch Pan Pizza\",\"price\":379,\"quantity\":1}]', 788, 800, 12, 'walk-in', 'cash', '2024-11-15 08:08:04', 'Theresa Soriano'),
(296, '11152024006', '[{\"name\":\"Pepperoni Lovers\",\"size\":\"9inch Pan Pizza\",\"price\":379,\"quantity\":2},{\"name\":\"7-UP\",\"size\":\"1.5L\",\"price\":139,\"quantity\":1},{\"name\":\"Pepsi\",\"size\":\"1.5L\",\"price\":139,\"quantity\":2}]', 1175, 1200, 25, 'walk-in', 'cash', '2024-11-15 08:08:15', 'Theresa Soriano'),
(297, '11162024001', '[{\"name\":\"Pepsi\",\"size\":\"1.5L\",\"price\":139,\"quantity\":2},{\"name\":\"Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":3}]', 1505, 2000, 495, 'walk-in', 'cash', '2024-11-16 08:08:31', 'Theresa Soriano'),
(298, '11162024002', '[{\"name\":\"Bacon Cheeseburger\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":4},{\"name\":\"Pepsi\",\"size\":\"1.5L\",\"price\":139,\"quantity\":1}]', 1775, 1800, 25, 'walk-in', 'cash', '2024-11-16 08:08:47', 'Theresa Soriano'),
(299, '11172024001', '[{\"name\":\"Pepperoni Lovers\",\"size\":\"9inch Pan Pizza\",\"price\":379,\"quantity\":1},{\"name\":\"Cheese Lovers\",\"size\":\"9inch Pan Pizza\",\"price\":379,\"quantity\":1}]', 758, 800, 42, 'walk-in', 'cash', '2024-11-17 08:09:05', 'Theresa Soriano'),
(300, '11172024002', '[{\"name\":\"BBQ Chicken Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":2},{\"name\":\"Bottled Water\",\"size\":\"500ml\",\"price\":39,\"quantity\":2}]', 896, 1000, 104, 'walk-in', 'cash', '2024-11-17 08:09:20', 'Theresa Soriano'),
(301, '11272024001', '[{\"name\":\"Bacon Margherita\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":1},{\"name\":\"BBQ Chicken Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":1}]', 818, 900, 82, 'walk-in', 'cash', '2024-11-27 08:10:49', 'Theresa Soriano'),
(302, '11272024002', '[{\"name\":\"Baked Bolognese\",\"size\":\"Regular\",\"price\":349,\"quantity\":1},{\"name\":\"Baked Ziti\",\"size\":\"Regular\",\"price\":199,\"quantity\":1},{\"name\":\"Pepsi\",\"size\":\"1.5L\",\"price\":139,\"quantity\":1},{\"name\":\"Bottled Water\",\"size\":\"500ml\",\"price\":39,\"quantity\":1}]', 726, 1000, 274, 'walk-in', 'cash', '2024-11-27 08:11:04', 'Theresa Soriano'),
(303, '11262024002', '[{\"name\":\"Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":1},{\"name\":\"Pepsi\",\"size\":\"1.5L\",\"price\":139,\"quantity\":1}]', 548, 600, 52, 'walk-in', 'cash', '2024-11-26 08:19:02', 'Theresa Soriano'),
(304, '11262024003', '[{\"name\":\"Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":2}]', 818, 1000, 182, 'walk-in', 'cash', '2024-11-26 08:19:27', 'Theresa Soriano'),
(305, '11272024003', '[{\"name\":\"Bacon Margherita\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":1}]', 409, 500, 91, 'walk-in', 'cash', '2024-11-27 08:20:40', 'Theresa Soriano'),
(306, '11272024004', '[{\"name\":\"7-UP\",\"size\":\"1.5L\",\"price\":139,\"quantity\":1}]', 139, 150, 11, 'walk-in', 'cash', '2024-11-27 08:20:49', 'Theresa Soriano'),
(307, '11282024001', '[{\"name\":\"BBQ Chicken Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":2},{\"name\":\"Bacon Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":1}]', 1227, 1227, 0, 'walk-in', 'cash', '2024-11-28 08:25:10', 'Theresa Soriano'),
(308, '11282024002', '[{\"name\":\"Baked Ziti\",\"size\":\"Regular\",\"price\":199,\"quantity\":2},{\"name\":\"Pepsi\",\"size\":\"1.5L\",\"price\":139,\"quantity\":2},{\"name\":\"Bacon Cheeseburger\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":1}]', 1085, 1100, 15, 'walk-in', 'cash', '2024-11-28 08:25:32', 'Theresa Soriano'),
(309, '11292024001', '[{\"name\":\"BBQ Chicken Supreme\",\"size\":\"9inch Pan Pizza\",\"price\":409,\"quantity\":1}]', 409, 500, 91, 'walk-in', 'cash', '2024-11-29 08:26:07', 'Theresa Soriano'),
(310, '11192024003', '[{\"name\":\"Baked Ziti\",\"size\":\"Regular\",\"price\":199,\"quantity\":2}]', 398, 500, 102, 'walk-in', 'cash', '2024-11-19 08:36:52', 'Theresa Soriano');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `msgID` int(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `image` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `msg_users`
--

CREATE TABLE `msg_users` (
  `user_msgID` int(50) NOT NULL,
  `uid` int(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` text NOT NULL,
  `status` varchar(255) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `msg_users`
--

INSERT INTO `msg_users` (`user_msgID`, `uid`, `title`, `category`, `description`, `image`, `status`, `date_created`) VALUES
(107, 131810009, 'Order ID#100 Status Update', 'Order status', 'Your order is now out for delivery. Our team is on the way to bring you a tasty meal. We appreciate your patience and hope you enjoy your food. If you have any questions or need assistance, feel free to contact us. Thank you for choosing our delivery service!', 'delivery.png', 'read', '2024-11-28 16:55:22');

-- --------------------------------------------------------

--
-- Table structure for table `notify_user`
--

CREATE TABLE `notify_user` (
  `id` int(50) NOT NULL,
  `uid` int(250) NOT NULL,
  `date_notified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notify_user`
--

INSERT INTO `notify_user` (`id`, `uid`, `date_notified`) VALUES
(9, 2024045, '2024-11-29 06:14:23'),
(10, 2024047, '2024-11-29 06:14:26'),
(11, 2024014, '2024-11-29 06:14:29');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `orderID` int(100) NOT NULL,
  `uid` int(100) NOT NULL,
  `name` varchar(200) NOT NULL,
  `address` varchar(255) NOT NULL,
  `items` text NOT NULL,
  `totalPrice` int(255) NOT NULL,
  `payment` varchar(100) NOT NULL,
  `del_instruct` text NOT NULL,
  `orderPlaced` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(255) NOT NULL,
  `orderDelivered` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_count`
--

CREATE TABLE `order_count` (
  `count_id` int(100) NOT NULL,
  `dish_id` int(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `size` varchar(100) NOT NULL,
  `price` int(100) NOT NULL,
  `orders` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_count`
--

INSERT INTO `order_count` (`count_id`, `dish_id`, `name`, `size`, `price`, `orders`) VALUES
(1, 18, 'Bacon Cheeseburger', '9inch', 409, 0),
(2, 22, 'Bacon Margherita', '9inch', 409, 0),
(3, 19, 'Bacon Supreme', '9inch', 409, 0),
(4, 11, '7-UP', '1.5L', 139, 1),
(5, 8, 'Baked Bolognese', 'Regular', 349, 0),
(6, 9, 'Baked Bolognese with Meatballs', 'Regular', 679, 1),
(7, 7, 'Baked Carbonara', 'Regular', 359, 0),
(8, 6, 'Baked Ziti', 'Regular', 199, 0),
(9, 4, 'BBQ Chicken Supreme', '9inch', 409, 0),
(10, 23, 'Bottled Water', '500ML', 39, 1),
(11, 17, 'Cheese Lovers', '9inch', 379, 0),
(12, 14, 'Hawaiian Supreme', '9inch', 409, 0),
(13, 12, 'Mountain Dew', '1.5L', 139, 0),
(15, 15, 'Pepperoni Lovers', '9inch', 379, 1),
(16, 10, 'Pepsi', '1.5L', 139, 0),
(17, 21, 'Supreme', '9inch', 409, 0),
(18, 16, 'Veggie Lovers', '9inch', 379, 0);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `prodID` int(50) NOT NULL,
  `name` text NOT NULL,
  `dish_id` int(11) NOT NULL,
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

INSERT INTO `products` (`prodID`, `name`, `dish_id`, `category`, `ingredients`, `slogan`, `size`, `price`, `status`, `img`) VALUES
(9, 'Pepsi', 1, 'beverages', '[{\"ingredient_name\": \"pepsi 1.5l\", \"quantity\": 1, \"measurement\": \"bottle\"}]', '', '1.5L', 139, 'available', 'pepsi.png'),
(10, 'Supreme', 2, 'pizza', '[\r\n  { \"ingredient_name\": \"ham\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"pizza sauce\", \"quantity\": 90, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mozzarella cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"quickmelt cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"flour\", \"quantity\": 180, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"soya oil\", \"quantity\": 20, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"dough blend\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"pizza box\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"boxliner\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"hot sauce sachet\", \"quantity\": 2, \"measurement\": \"pcs\" },\r\n  { \"ingredient_name\": \"beef topping\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"onions\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"pork topping\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mushroom\", \"quantity\": 50, \"measurement\": \"grams\" }\r\n]', 'Six delightful toppings - beef, pepperoni, seasoned pork, bell pepper, onions and mushrooms.', '9inch Pan Pizza', 409, 'available', 'supreme.png'),
(11, 'Bacon Supreme', 3, 'pizza', '[\r\n  { \"ingredient_name\": \"pizza sauce\", \"quantity\": 90, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mozzarella cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"quickmelt cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"flour\", \"quantity\": 180, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"soya oil\", \"quantity\": 20, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"dough blend\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"pizza box\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"boxliner\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"hot sauce sachet\", \"quantity\": 2, \"measurement\": \"pcs\" },\r\n  { \"ingredient_name\": \"onions\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mushroom\", \"quantity\": 20, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"bell pepper green\", \"quantity\": 25, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"bell pepper red\", \"quantity\": 25, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"bacon\", \"quantity\": 100, \"measurement\": \"grams\" }\r\n]', 'Two layers of toasted bacon with bell peppers, onions and mushrooms on our signature pizza sauce.', '9inch Pan Pizza', 409, 'available', 'bacon_supreme.png'),
(12, 'Bacon Margherita', 4, 'pizza', '[\r\n  { \"ingredient_name\": \"mozzarella cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"quickmelt cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"flour\", \"quantity\": 180, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"soya oil\", \"quantity\": 20, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"dough blend\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n{ \"ingredient_name\": \"pizza box\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"tomato\", \"quantity\": 30, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"basil\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"boxliner\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"cheddar\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"carbonara sauce\", \"quantity\": 100, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"bacon\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"parmesan cheese\", \"quantity\": 40, \"measurement\": \"grams\" }\r\n]\r\n', 'Tomatoes, basil, cheddar and mozzarella cheese topped with bacon and parmesan.', '9inch Pan Pizza', 409, 'available', 'bacon_margherita.png'),
(13, 'BBQ Chicken Supreme', 5, 'pizza', '[\r\n  { \"ingredient_name\": \"mozzarella cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"quickmelt cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"flour\", \"quantity\": 180, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"soya oil\", \"quantity\": 20, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"dough blend\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"pizza box\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"boxliner\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"hot sauce sachet\", \"quantity\": 2, \"measurement\": \"pcs\" },\r\n  { \"ingredient_name\": \"bbq sauce\", \"quantity\": 100, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"chicken chunks\", \"quantity\": 100, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mushroom\", \"quantity\": 40, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"onions\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"parsley\", \"quantity\": 10, \"measurement\": \"grams\" }\r\n]\r\n', 'BBQ sauce, mozzarella, BBQ-coated chicken chunks, mushrooms, onions and parsley.', '9inch Pan Pizza', 409, 'available', 'bbq_supreme.jpg'),
(14, 'Bacon Cheeseburger', 6, 'pizza', '[\r\n  { \"ingredient_name\": \"pizza sauce\", \"quantity\": 90, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mozzarella cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"quickmelt cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"flour\", \"quantity\": 180, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"soya oil\", \"quantity\": 20, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"dough blend\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"pizza box\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"boxliner\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"hot sauce sachet\", \"quantity\": 2, \"measurement\": \"pcs\" },\r\n  { \"ingredient_name\": \"beef topping\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"bacon\", \"quantity\": 52, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"cheddar\", \"quantity\": 52, \"measurement\": \"grams\" }\r\n]\r\n', 'Beef, bacon and a triple layer of cheddar and mozzarella on our signature pizza sauce', '9inch Pan Pizza', 409, 'available', 'bacon_cheeseburger.png'),
(15, 'Pepperoni Lovers', 7, 'pizza', '[\r\n  { \"ingredient_name\": \"pizza sauce\", \"quantity\": 90, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mozzarella cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"quickmelt cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"flour\", \"quantity\": 180, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"soya oil\", \"quantity\": 20, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"dough blend\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"pizza box\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"boxliner\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"hot sauce sachet\", \"quantity\": 2, \"measurement\": \"pcs\" },\r\n  { \"ingredient_name\": \"pepperoni\", \"quantity\": 75, \"measurement\": \"grams\" }\r\n]\r\n', 'A true classic pepperoni and mozzarella cheese on our signature pizza sauce.', '9inch Pan Pizza', 379, 'available', 'pepperoni_lovers.jpg'),
(16, 'Cheese Lovers', 8, 'pizza', '[\r\n  { \"ingredient_name\": \"pizza sauce\", \"quantity\": 90, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mozzarella cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"quickmelt cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"flour\", \"quantity\": 180, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"soya oil\", \"quantity\": 20, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"dough blend\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"pizza box\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"boxliner\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"hot sauce sachet\", \"quantity\": 2, \"measurement\": \"pcs\" },\r\n  { \"ingredient_name\": \"cheddar\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"parmesan cheese\", \"quantity\": 50, \"measurement\": \"grams\" }\r\n]\r\n', 'Mozzarella, parmesan and cheddar cheeses. A cheese lover\'s delight.', '9inch Pan Pizza', 379, 'available', 'cheese_lovers.png'),
(17, 'Veggie Lovers', 9, 'pizza', '[\r\n  { \"ingredient_name\": \"pizza sauce\", \"quantity\": 90, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mozzarella cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"quickmelt cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"flour\", \"quantity\": 180, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"soya oil\", \"quantity\": 20, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"dough blend\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"pizza box\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"boxliner\", \"quantity\": 1, \"measurement\": \"pc\" },\r\n  { \"ingredient_name\": \"hot sauce sachet\", \"quantity\": 2, \"measurement\": \"pcs\" },\r\n  { \"ingredient_name\": \"pineapple tidbits\", \"quantity\": 70, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"bell pepper red\", \"quantity\": 25, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"bell pepper green\", \"quantity\": 25, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"onions\", \"quantity\": 50, \"measurement\": \"grams\" }\r\n]\r\n', 'Crunchy bell peppers, mushrooms, onions and juicy pineapples on a double layer of mozzarella cheese.', '9inch Pan Pizza', 379, 'available', 'veggie_lovers.jpg'),
(18, 'Baked Ziti', 10, 'pasta', '[\r\n  { \"ingredient_name\": \"ziti noodles\", \"quantity\": 120, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"bolognese sauce\", \"quantity\": 25, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"carbonara sauce\", \"quantity\": 25, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"mozzarella cheese\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"parmesan cheese\", \"quantity\": 10, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"parsley\", \"quantity\": 5, \"measurement\": \"grams\" }\r\n]\r\n', 'Ziti noodles, Bolognese sauce, white sauce, mozzarella cheese, parmesan cheese, dried parsley', 'Regular', 199, 'available', 'baked_ziti.jpg'),
(19, 'Baked Carbonara', 11, 'pasta', '[\r\n  { \"ingredient_name\": \"carbonara sauce\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"ham\", \"quantity\": 5, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"spaghetti pasta\", \"quantity\": 120, \"measurement\": \"grams\" }\r\n]\r\n', 'Baked spaghetti in the classic creamy white sauce with ham and mushrooms.', 'Regular', 359, 'available', 'baked_carbonara.jpg'),
(20, 'Baked Bolognese', 12, 'pasta', '[\r\n  { \"ingredient_name\": \"bolognese sauce\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"spaghetti pasta\", \"quantity\": 120, \"measurement\": \"grams\" }\r\n]\r\n', 'Baked spaghetti pasta cooked al dente with savory sweet Bolognese sauce.', 'Regular', 349, 'available', 'baked_bolognese.jpg'),
(21, 'Baked Bolognese with Meatballs', 13, 'pasta', '[\r\n  { \"ingredient_name\": \"bolognese sauce\", \"quantity\": 50, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"spaghetti pasta\", \"quantity\": 120, \"measurement\": \"grams\" },\r\n  { \"ingredient_name\": \"meatballs\", \"quantity\": 3, \"measurement\": \"pcs\" }\r\n]\r\n', 'Baked pasta in savory-sweet Bolognese sauce and Italian meatballs. An all-time fave!', 'Regular', 679, 'available', 'baked_bolognese-meatballs.jpg'),
(22, '7-UP', 14, 'beverages', '[{\"ingredient_name\": \"7-up 1.5l\", \"quantity\": 1, \"measurement\": \"bottle\"}]', '', '1.5L', 139, 'available', '7-up.png'),
(23, 'Bottled Water', 15, 'beverages', '[{\"ingredient_name\": \"bottled water 500ml\", \"quantity\": 1, \"measurement\": \"bottle\"}]', '', '500ml', 39, 'available', 'bottled_water.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `records_inventory`
--

CREATE TABLE `records_inventory` (
  `recordID` int(50) NOT NULL,
  `itemID` varchar(250) NOT NULL,
  `name` text NOT NULL,
  `uom` varchar(50) NOT NULL,
  `beginning` double(11,2) NOT NULL,
  `deliveries` double(11,2) NOT NULL,
  `transfers_in` double(11,2) NOT NULL,
  `transfers_out` double(11,2) NOT NULL,
  `spoilage` double(11,2) NOT NULL,
  `ending` double(11,2) NOT NULL,
  `usage_count` double(11,2) NOT NULL,
  `remarks` text NOT NULL,
  `submitted_by` varchar(250) NOT NULL,
  `inventory_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `records_inventory`
--

INSERT INTO `records_inventory` (`recordID`, `itemID`, `name`, `uom`, `beginning`, `deliveries`, `transfers_in`, `transfers_out`, `spoilage`, `ending`, `usage_count`, `remarks`, `submitted_by`, `inventory_date`) VALUES
(141, 'HSS761', 'hot sauce sachet', 'pc', 15000.00, 0.00, 0.00, 0.00, 0.00, 14828.00, 172.00, '', 'Theresa Soriano', '2024-11-27'),
(142, 'H29878', 'ham', 'kg', 150.00, 0.00, 0.00, 0.00, 0.00, 148.98, 1.04, '', 'Theresa Soriano', '2024-11-27'),
(143, 'P18818', 'pepsi 1.5l', 'bt', 500.00, 0.00, 0.00, 0.00, 0.00, 473.00, 27.00, '', 'Theresa Soriano', '2024-11-27'),
(144, 'PB4659', 'pizza box', 'pc', 1000.00, 0.00, 0.00, 0.00, 0.00, 908.00, 92.00, '', 'Theresa Soriano', '2024-11-27'),
(145, 'B87177', 'bacon', 'kg', 70.00, 0.00, 0.00, 0.00, 0.00, 68.28, 1.72, '', 'Theresa Soriano', '2024-11-27'),
(146, 'BT1363', 'beef topping', 'kg', 90.00, 0.00, 0.00, 0.00, 0.00, 88.00, 2.00, '', 'Theresa Soriano', '2024-11-27'),
(147, 'DB3050', 'dough blend', 'kg', 74.00, 0.00, 0.00, 0.00, 0.00, 73.08, 0.92, '', 'Theresa Soriano', '2024-11-27'),
(148, 'B55959', 'boxliner', 'pc', 5000.00, 0.00, 0.00, 0.00, 0.00, 4908.00, 92.00, '', 'Theresa Soriano', '2024-11-27'),
(149, 'PS7276', 'pizza sauce', 'kg', 500.00, 0.00, 0.00, 0.00, 0.00, 493.52, 6.48, '', 'Theresa Soriano', '2024-11-27'),
(150, 'SO9727', 'soya oil', 'kg', 290.00, 0.00, 0.00, 0.00, 0.00, 288.16, 1.84, '', 'Theresa Soriano', '2024-11-27'),
(151, 'QC9243', 'quickmelt cheese', 'kg', 600.00, 0.00, 0.00, 0.00, 0.00, 595.40, 4.60, '', 'Theresa Soriano', '2024-11-27'),
(152, 'MC8936', 'mozzarella cheese', 'kg', 315.00, 0.00, 0.00, 0.00, 0.00, 310.00, 5.00, '', 'Theresa Soriano', '2024-11-27'),
(153, 'PC5093', 'parmesan cheese', 'kg', 19.95, 0.00, 0.00, 0.00, 0.00, 18.73, 1.22, '', 'Theresa Soriano', '2024-11-27'),
(154, 'F30735', 'flour', 'kg', 710.00, 0.00, 0.00, 0.00, 0.00, 693.44, 16.56, '', 'Theresa Soriano', '2024-11-27'),
(155, 'C54769', 'cheddar', 'kg', 50.00, 0.00, 0.00, 0.00, 0.00, 47.88, 2.12, '', 'Theresa Soriano', '2024-11-27'),
(156, 'SP8315', 'spaghetti pasta', 'kg', 300.00, 0.00, 0.00, 0.00, 0.00, 297.24, 2.76, '', 'Theresa Soriano', '2024-11-27'),
(157, 'BS6169', 'bolognese sauce', 'kg', 150.00, 0.00, 0.00, 0.00, 0.00, 149.04, 0.96, '', 'Theresa Soriano', '2024-11-27'),
(158, 'CS4175', 'carbonara sauce', 'kg', 15.00, 0.00, 0.00, 0.00, 0.00, 13.80, 1.20, '', 'Theresa Soriano', '2024-11-27'),
(159, 'M46300', 'meatballs', 'pc', 100.00, 0.00, 0.00, 0.00, 0.00, 82.00, 18.00, '', 'Theresa Soriano', '2024-11-27'),
(160, '718588', '7-up 1.5l', 'bt', 500.00, 0.00, 0.00, 0.00, 2.00, 493.00, 5.00, '', 'Theresa Soriano', '2024-11-27'),
(161, 'P64928', 'pepperoni', 'kg', 6.00, 0.00, 0.00, 0.00, 0.00, 5.23, 0.76, '', 'Theresa Soriano', '2024-11-27'),
(162, 'B58256', 'basil', 'kg', 200.00, 0.00, 0.00, 0.00, 0.00, 199.94, 0.06, '', 'Theresa Soriano', '2024-11-27'),
(163, 'T84140', 'tomato', 'kg', 400.00, 0.00, 0.00, 0.00, 0.00, 399.82, 0.18, '', 'Theresa Soriano', '2024-11-27'),
(164, 'BPG668', 'bell pepper green', 'kg', 200.00, 0.00, 0.00, 0.00, 0.00, 199.78, 0.18, '', 'Theresa Soriano', '2024-11-27'),
(165, 'BPR743', 'bell pepper red', 'kg', 200.00, 0.00, 0.00, 0.00, 0.00, 199.78, 0.18, '', 'Theresa Soriano', '2024-11-27'),
(166, 'O07558', 'onions', 'kg', 400.00, 0.00, 0.00, 0.00, 0.00, 397.90, 2.10, '', 'Theresa Soriano', '2024-11-27'),
(167, 'M04399', 'mushroom', 'kg', 430.00, 0.00, 0.00, 0.00, 0.00, 428.36, 1.64, '', 'Theresa Soriano', '2024-11-27'),
(168, 'BS3890', 'bbq sauce', 'kg', 100.00, 0.00, 0.00, 0.00, 0.00, 98.60, 1.40, '', 'Theresa Soriano', '2024-11-27'),
(169, 'P59966', 'parsley', 'kg', 230.00, 0.00, 0.00, 0.00, 0.00, 229.83, 0.18, '', 'Theresa Soriano', '2024-11-27'),
(170, 'CC4083', 'chicken chunks', 'kg', 700.00, 0.00, 0.00, 0.00, 0.00, 698.60, 1.40, '', 'Theresa Soriano', '2024-11-27'),
(171, 'PT4200', 'pork topping', 'kg', 300.00, 0.00, 0.00, 0.00, 0.00, 299.00, 1.00, '', 'Theresa Soriano', '2024-11-27'),
(172, 'PT6853', 'pineapple tidbits', 'kg', 530.00, 0.00, 0.00, 0.00, 0.00, 529.72, 0.28, '', 'Theresa Soriano', '2024-11-27'),
(173, 'ZN7784', 'ziti noodles', 'kg', 530.00, 0.00, 0.00, 0.00, 0.00, 529.04, 0.96, '', 'Theresa Soriano', '2024-11-27'),
(174, 'BW5195', 'bottled water 500ml', 'bt', 500.00, 0.00, 0.00, 0.00, 0.00, 485.00, 15.00, '', 'Theresa Soriano', '2024-11-27');

-- --------------------------------------------------------

--
-- Table structure for table `remind_user`
--

CREATE TABLE `remind_user` (
  `id` int(50) NOT NULL,
  `uid` int(250) NOT NULL,
  `date_notified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `spoilage_reports`
--

CREATE TABLE `spoilage_reports` (
  `id` int(11) NOT NULL,
  `prod_description` varchar(250) NOT NULL,
  `qty` double NOT NULL,
  `measurement` varchar(250) NOT NULL,
  `remarks` text NOT NULL,
  `date_reported` datetime NOT NULL DEFAULT current_timestamp(),
  `submitted_by` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `success_orders`
--

CREATE TABLE `success_orders` (
  `s_orderID` int(100) NOT NULL,
  `orderID` int(100) NOT NULL,
  `uid` int(100) NOT NULL,
  `name` varchar(200) NOT NULL,
  `address` varchar(255) NOT NULL,
  `items` text NOT NULL,
  `totalPrice` int(255) NOT NULL,
  `payment` varchar(100) NOT NULL,
  `del_instruct` text NOT NULL,
  `orderPlaced` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(255) NOT NULL,
  `orderDelivered` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `success_orders`
--

INSERT INTO `success_orders` (`s_orderID`, `orderID`, `uid`, `name`, `address`, `items`, `totalPrice`, `payment`, `del_instruct`, `orderPlaced`, `status`, `orderDelivered`) VALUES
(21, 90, 131810002, 'GLORIOSO, MARICAR', 'B4 L23 Kimberton Ville, Cumberland, Niog 2, Bacoor, Cavite, 4102', '[{\"name\":\"7-UP\",\"size\":\"1.5L\",\"price\":\"139\",\"qty\":\"1\",\"totalPrice\":\"139\"},{\"name\":\"Baked Bolognese with Meatballs\",\"size\":\"Regular\",\"price\":\"679\",\"qty\":\"1\",\"totalPrice\":\"679\"}]', 868, 'COD', 'test', '2024-02-05 02:29:19', 'delivered', '2024-02-05 02:52:15'),
(22, 91, 131810002, 'GLORIOSO, MARICAR', 'B4 L23 Kimberton Ville, Cumberland, Niog 2, Bacoor, Cavite, 4102', '[{\"name\":\"Bottled Water\",\"size\":\"500ML\",\"price\":\"39\",\"qty\":\"1\",\"totalPrice\":\"39\"},{\"name\":\"Pepperoni Lovers\",\"size\":\"9inch Regular Pan Pizza\",\"price\":\"379\",\"qty\":\"1\",\"totalPrice\":\"379\"}]', 468, 'COD', '', '2024-02-05 03:02:14', 'delivered', '2024-02-05 03:09:27');

-- --------------------------------------------------------

--
-- Table structure for table `usage_count`
--

CREATE TABLE `usage_count` (
  `ingredientID` int(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `qty` varchar(255) NOT NULL,
  `measurement` varchar(100) NOT NULL,
  `usageCount` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usage_count`
--

INSERT INTO `usage_count` (`ingredientID`, `name`, `qty`, `measurement`, `usageCount`) VALUES
(1, 'Ham', '0', 'grams', 0),
(2, 'Pineapple Tidbits', '0', 'grams', 0),
(3, 'Pizza Sauce', '90', 'grams', 1),
(4, 'Mozzarella', '50', 'grams', 1),
(5, 'Quickmelt Cheese', '50', 'grams', 1),
(6, 'Flour', '180', 'grams', 1),
(7, 'Soya Oil', '20', 'grams', 1),
(8, 'Dough Blend', '10', 'grams', 1),
(9, 'Pizza Box', '1', 'pcs', 1),
(10, 'Boxliner', '1', 'pcs', 1),
(11, 'Hot Sauce Sachet', '2', 'pcs', 1),
(12, 'Beef topping', '0', 'grams', 0),
(13, 'Onions', '0', 'grams', 0),
(14, 'Pork topping', '0', 'grams', 0),
(15, 'Mushroom', '0', 'grams', 0),
(16, 'Bell Pepper Red', '0', 'grams', 0),
(17, 'Bell Pepper Green', '0', 'grams', 0),
(18, 'Bacon', '0', 'grams', 0),
(19, 'Tomato', '0', 'grams', 0),
(20, 'Basil', '0', 'grams', 0),
(21, 'Cheddar', '0', 'grams', 0),
(23, 'Parmesan ', '0', 'grams', 0),
(24, 'BBQ Sauce', '0', 'grams', 0),
(25, 'Chicken Chunks', '0', 'grams', 0),
(26, 'Parsley', '0', 'grams', 0),
(27, 'Pepperoni', '75', 'grams', 1),
(28, 'Ziti Noodles', '0', 'grams', 0),
(29, 'Bolognese Sauce', '50', 'grams', 1),
(30, 'Carbonara Sauce', '0', 'grams', 0),
(31, 'Spaghetti Pasta', '120', 'grams', 1),
(32, 'Meatballs', '3', 'pcs', 1),
(33, '7-UP', '1', 'pcs', 1),
(34, 'Mountain Dew', '0', 'pcs', 0),
(35, 'Bottled Water', '1', 'pcs', 1),
(36, 'Pepsi', '0', 'pcs', 0);

-- --------------------------------------------------------

--
-- Table structure for table `usage_reports`
--

CREATE TABLE `usage_reports` (
  `id` int(11) NOT NULL,
  `invID` int(50) NOT NULL,
  `name` varchar(250) NOT NULL,
  `size` varchar(250) NOT NULL,
  `price` double NOT NULL,
  `quantity` int(11) NOT NULL,
  `day_counted` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usage_reports`
--

INSERT INTO `usage_reports` (`id`, `invID`, `name`, `size`, `price`, `quantity`, `day_counted`) VALUES
(25, 2147483647, 'BBQ Chicken Supreme', '9inch Pan Pizza', 409, 2, '2024-11-18 07:27:35'),
(26, 2147483647, 'Bottled Water', '500ml', 39, 1, '2024-11-18 07:27:35'),
(27, 2147483647, 'Pepsi', '1.5L', 139, 1, '2024-11-18 07:27:35'),
(28, 2147483647, 'Baked Bolognese with Meatballs', 'Regular', 679, 1, '2024-11-18 07:27:58'),
(29, 2147483647, 'Pepsi', '1.5L', 139, 1, '2024-11-18 07:27:58'),
(30, 2147483647, 'Baked Ziti', 'Regular', 199, 1, '2024-11-18 07:29:40'),
(31, 2147483647, 'Bacon Cheeseburger', '9inch Pan Pizza', 409, 2, '2024-11-18 07:29:40'),
(32, 2147483647, 'Pepsi', '1.5L', 139, 2, '2024-11-19 07:30:11'),
(33, 2147483647, 'Baked Bolognese', 'Regular', 349, 2, '2024-11-19 07:30:11'),
(34, 2147483647, 'Baked Carbonara', 'Regular', 359, 1, '2024-11-19 07:30:11'),
(35, 2147483647, 'BBQ Chicken Supreme', '9inch Pan Pizza', 409, 1, '2024-11-19 07:30:24'),
(36, 2147483647, 'Veggie Lovers', '9inch Pan Pizza', 379, 1, '2024-11-19 07:30:24'),
(37, 2147483647, 'Bacon Margherita', '9inch Pan Pizza', 409, 1, '2024-11-20 07:30:43'),
(38, 2147483647, 'Pepperoni Lovers', '9inch Pan Pizza', 379, 1, '2024-11-20 07:30:43'),
(39, 2147483647, 'Veggie Lovers', '9inch Pan Pizza', 379, 1, '2024-11-20 07:30:43'),
(40, 2147483647, 'Bottled Water', '500ml', 39, 4, '2024-11-20 07:30:43'),
(41, 2147483647, 'Baked Carbonara', 'Regular', 359, 2, '2024-11-20 07:31:00'),
(42, 2147483647, 'Cheese Lovers', '9inch Pan Pizza', 379, 1, '2024-11-20 07:31:00'),
(43, 2147483647, 'Cheese Lovers', '9inch Pan Pizza', 379, 2, '2024-11-21 07:31:17'),
(44, 2147483647, 'Supreme', '9inch Pan Pizza', 409, 3, '2024-11-21 07:32:14'),
(45, 2147483647, '7-UP', '1.5L', 139, 1, '2024-11-21 07:32:14'),
(46, 2147483647, 'Pepsi', '1.5L', 139, 1, '2024-11-21 07:32:14'),
(47, 2147483647, 'Supreme', '9inch Pan Pizza', 409, 2, '2024-11-22 07:32:45'),
(48, 2147483647, 'Bacon Cheeseburger', '9inch Pan Pizza', 409, 2, '2024-11-22 07:32:45'),
(49, 2147483647, '7-UP', '1.5L', 139, 1, '2024-11-22 07:32:45'),
(50, 2147483647, 'Bottled Water', '500ml', 39, 2, '2024-11-22 07:32:55'),
(51, 2147483647, 'Baked Ziti', 'Regular', 199, 1, '2024-11-22 07:32:55'),
(52, 2147483647, 'Bottled Water', '500ml', 39, 4, '2024-11-22 07:38:41'),
(53, 2147483647, 'Pepperoni Lovers', '9inch Pan Pizza', 379, 1, '2024-11-22 07:38:41'),
(54, 2147483647, 'Bacon Cheeseburger', '9inch Pan Pizza', 409, 2, '2024-11-22 07:38:41'),
(55, 2147483647, 'Bacon Supreme', '9inch Pan Pizza', 409, 1, '2024-11-23 07:47:10'),
(56, 2147483647, 'BBQ Chicken Supreme', '9inch Pan Pizza', 409, 1, '2024-11-23 07:47:10'),
(57, 2147483647, 'Cheese Lovers', '9inch Pan Pizza', 379, 1, '2024-11-23 07:47:10'),
(58, 2147483647, 'Pepsi', '1.5L', 139, 3, '2024-11-23 07:47:23'),
(59, 2147483647, 'Baked Bolognese', 'Regular', 349, 1, '2024-11-23 07:47:23'),
(60, 2147483647, 'Bacon Cheeseburger', '9inch Pan Pizza', 409, 4, '2024-11-23 07:47:23'),
(61, 2147483647, 'Bacon Cheeseburger', '9inch Pan Pizza', 409, 2, '2024-11-24 07:47:32'),
(62, 2147483647, 'Baked Carbonara', 'Regular', 359, 1, '2024-11-24 07:47:48'),
(63, 2147483647, 'Baked Bolognese with Meatballs', 'Regular', 679, 1, '2024-11-24 07:47:48'),
(64, 2147483647, 'Pepsi', '1.5L', 139, 2, '2024-11-24 07:47:48'),
(65, 2147483647, 'Baked Carbonara', 'Regular', 359, 3, '2024-11-24 07:48:04'),
(66, 2147483647, 'Baked Bolognese with Meatballs', 'Regular', 679, 1, '2024-11-24 07:48:04'),
(67, 2147483647, 'Cheese Lovers', '9inch Pan Pizza', 379, 3, '2024-11-24 07:48:19'),
(68, 2147483647, 'Supreme', '9inch Pan Pizza', 409, 3, '2024-11-24 07:48:19'),
(69, 2147483647, 'Veggie Lovers', '9inch Pan Pizza', 379, 1, '2024-11-25 07:48:30'),
(70, 2147483647, 'Bacon Margherita', '9inch Pan Pizza', 409, 1, '2024-11-25 07:48:30'),
(71, 2147483647, 'Bacon Supreme', '9inch Pan Pizza', 409, 2, '2024-11-25 07:49:02'),
(72, 2147483647, 'Cheese Lovers', '9inch Pan Pizza', 379, 3, '2024-11-25 07:49:02'),
(73, 2147483647, 'Pepsi', '1.5L', 139, 3, '2024-11-25 07:49:19'),
(74, 2147483647, 'Pepperoni Lovers', '9inch Pan Pizza', 379, 2, '2024-11-25 07:49:19'),
(75, 2147483647, 'Cheese Lovers', '9inch Pan Pizza', 379, 2, '2024-11-25 07:49:19'),
(76, 2147483647, 'BBQ Chicken Supreme', '9inch Pan Pizza', 409, 2, '2024-11-26 07:49:45'),
(77, 2147483647, 'BBQ Chicken Supreme', '9inch Pan Pizza', 409, 2, '2024-11-11 07:50:06'),
(78, 2147483647, 'Bacon Cheeseburger', '9inch Pan Pizza', 409, 1, '2024-11-11 07:50:06'),
(79, 2147483647, 'Pepsi', '1.5L', 139, 2, '2024-11-11 07:50:20'),
(80, 2147483647, '7-UP', '1.5L', 139, 1, '2024-11-11 07:50:20'),
(81, 2147483647, 'Baked Ziti', 'Regular', 199, 2, '2024-11-11 07:50:20'),
(82, 2147483647, 'Cheese Lovers', '9inch Pan Pizza', 379, 1, '2024-11-11 07:50:28'),
(83, 2147483647, 'Pepperoni Lovers', '9inch Pan Pizza', 379, 1, '2024-11-11 07:50:28'),
(84, 2147483647, 'BBQ Chicken Supreme', '9inch Pan Pizza', 409, 1, '2024-11-12 07:56:19'),
(85, 2147483647, 'Bacon Supreme', '9inch Pan Pizza', 409, 1, '2024-11-12 07:56:19'),
(86, 2147483647, 'Baked Bolognese with Meatballs', 'Regular', 679, 1, '2024-11-12 07:56:29'),
(87, 2147483647, 'Baked Bolognese', 'Regular', 349, 1, '2024-11-12 07:56:29'),
(88, 2147483647, 'Pepsi', '1.5L', 139, 1, '2024-11-12 07:56:41'),
(89, 2147483647, 'Bottled Water', '500ml', 39, 1, '2024-11-12 07:56:41'),
(90, 2147483647, 'Bacon Margherita', '9inch Pan Pizza', 409, 2, '2024-11-13 07:56:56'),
(91, 2147483647, 'Bacon Cheeseburger', '9inch Pan Pizza', 409, 1, '2024-11-13 07:56:56'),
(92, 2147483647, 'Baked Bolognese with Meatballs', 'Regular', 679, 1, '2024-11-13 07:57:08'),
(93, 2147483647, 'Baked Bolognese', 'Regular', 349, 1, '2024-11-13 07:57:08'),
(94, 2147483647, 'Baked Carbonara', 'Regular', 359, 1, '2024-11-13 07:57:08'),
(95, 2147483647, 'Baked Ziti', 'Regular', 199, 2, '2024-11-14 08:00:57'),
(96, 2147483647, 'Cheese Lovers', '9inch Pan Pizza', 379, 2, '2024-11-14 08:00:57'),
(97, 2147483647, 'Supreme', '9inch Pan Pizza', 409, 2, '2024-11-14 08:00:57'),
(98, 2147483647, 'Pepsi', '1.5L', 139, 1, '2024-11-14 08:00:57'),
(99, 2147483647, 'BBQ Chicken Supreme', '9inch Pan Pizza', 409, 1, '2024-11-14 08:01:18'),
(100, 2147483647, 'Pepsi', '1.5L', 139, 1, '2024-11-14 08:01:18'),
(101, 2147483647, 'Bacon Cheeseburger', '9inch Pan Pizza', 409, 2, '2024-11-15 08:01:32'),
(102, 2147483647, 'BBQ Chicken Supreme', '9inch Pan Pizza', 409, 1, '2024-11-15 08:01:32'),
(103, 2147483647, 'Pepsi', '1.5L', 139, 2, '2024-11-15 08:01:46'),
(104, 2147483647, 'Baked Bolognese', 'Regular', 349, 2, '2024-11-15 08:01:46'),
(105, 2147483647, 'Supreme', '9inch Pan Pizza', 409, 3, '2024-11-15 08:07:06'),
(106, 2147483647, 'Baked Ziti', 'Regular', 199, 1, '2024-11-15 08:07:19'),
(107, 2147483647, 'Baked Bolognese', 'Regular', 349, 1, '2024-11-15 08:07:19'),
(108, 2147483647, 'Baked Bolognese with Meatballs', 'Regular', 679, 1, '2024-11-15 08:07:19'),
(109, 2147483647, 'Supreme', '9inch Pan Pizza', 409, 1, '2024-11-15 08:08:04'),
(110, 2147483647, 'Veggie Lovers', '9inch Pan Pizza', 379, 1, '2024-11-15 08:08:04'),
(111, 2147483647, 'Pepperoni Lovers', '9inch Pan Pizza', 379, 2, '2024-11-15 08:08:15'),
(112, 2147483647, '7-UP', '1.5L', 139, 1, '2024-11-15 08:08:15'),
(113, 2147483647, 'Pepsi', '1.5L', 139, 2, '2024-11-15 08:08:15'),
(114, 2147483647, 'Pepsi', '1.5L', 139, 2, '2024-11-16 08:08:31'),
(115, 2147483647, 'Supreme', '9inch Pan Pizza', 409, 3, '2024-11-16 08:08:31'),
(116, 2147483647, 'Bacon Cheeseburger', '9inch Pan Pizza', 409, 4, '2024-11-16 08:08:47'),
(117, 2147483647, 'Pepsi', '1.5L', 139, 1, '2024-11-16 08:08:47'),
(118, 2147483647, 'Pepperoni Lovers', '9inch Pan Pizza', 379, 1, '2024-11-17 08:09:05'),
(119, 2147483647, 'Cheese Lovers', '9inch Pan Pizza', 379, 1, '2024-11-17 08:09:05'),
(120, 2147483647, 'BBQ Chicken Supreme', '9inch Pan Pizza', 409, 2, '2024-11-17 08:09:20'),
(121, 2147483647, 'Bottled Water', '500ml', 39, 2, '2024-11-17 08:09:20'),
(122, 2147483647, 'Bacon Margherita', '9inch Pan Pizza', 409, 1, '2024-11-27 08:10:49'),
(123, 2147483647, 'BBQ Chicken Supreme', '9inch Pan Pizza', 409, 1, '2024-11-27 08:10:49'),
(124, 2147483647, 'Baked Bolognese', 'Regular', 349, 1, '2024-11-27 08:11:04'),
(125, 2147483647, 'Baked Ziti', 'Regular', 199, 1, '2024-11-27 08:11:04'),
(126, 2147483647, 'Pepsi', '1.5L', 139, 1, '2024-11-27 08:11:04'),
(127, 2147483647, 'Bottled Water', '500ml', 39, 1, '2024-11-27 08:11:04'),
(128, 2147483647, 'Supreme', '9inch Pan Pizza', 409, 1, '2024-11-26 08:19:02'),
(129, 2147483647, 'Pepsi', '1.5L', 139, 1, '2024-11-26 08:19:02'),
(130, 2147483647, 'Supreme', '9inch Pan Pizza', 409, 2, '2024-11-26 08:19:27'),
(131, 2147483647, 'Bacon Margherita', '9inch Pan Pizza', 409, 1, '2024-11-27 08:20:40'),
(132, 2147483647, '7-UP', '1.5L', 139, 1, '2024-11-27 08:20:49'),
(133, 2147483647, 'BBQ Chicken Supreme', '9inch Pan Pizza', 409, 2, '2024-11-28 08:25:10'),
(134, 2147483647, 'Bacon Supreme', '9inch Pan Pizza', 409, 1, '2024-11-28 08:25:10'),
(135, 2147483647, 'Baked Ziti', 'Regular', 199, 2, '2024-11-28 08:25:32'),
(136, 2147483647, 'Pepsi', '1.5L', 139, 2, '2024-11-28 08:25:32'),
(137, 2147483647, 'Bacon Cheeseburger', '9inch Pan Pizza', 409, 1, '2024-11-28 08:25:32'),
(138, 2147483647, 'BBQ Chicken Supreme', '9inch Pan Pizza', 409, 1, '2024-11-29 08:26:07'),
(139, 2147483647, 'Baked Ziti', 'Regular', 199, 2, '2024-11-19 08:36:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `user_type` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `email`, `user_type`, `password`) VALUES
(131810009, 'magno123@gmail.com', 'customer', '2c103f2c4ed1e59c0b4e2e01821770fa');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `dish_cart` (`dish_id`),
  ADD KEY `user_cart` (`uid`);

--
-- Indexes for table `customerinfo`
--
ALTER TABLE `customerinfo`
  ADD PRIMARY KEY (`customerID`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `daily_inventory`
--
ALTER TABLE `daily_inventory`
  ADD PRIMARY KEY (`inventoryID`),
  ADD UNIQUE KEY `itemID` (`itemID`);

--
-- Indexes for table `dishes`
--
ALTER TABLE `dishes`
  ADD PRIMARY KEY (`dish_id`),
  ADD KEY `dish_category` (`categoryID`);

--
-- Indexes for table `food_category`
--
ALTER TABLE `food_category`
  ADD PRIMARY KEY (`categoryID`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`msgID`);

--
-- Indexes for table `msg_users`
--
ALTER TABLE `msg_users`
  ADD PRIMARY KEY (`user_msgID`),
  ADD KEY `message_users` (`uid`);

--
-- Indexes for table `notify_user`
--
ALTER TABLE `notify_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orderID`);

--
-- Indexes for table `order_count`
--
ALTER TABLE `order_count`
  ADD PRIMARY KEY (`count_id`),
  ADD KEY `dish_id` (`dish_id`);

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
-- Indexes for table `remind_user`
--
ALTER TABLE `remind_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `spoilage_reports`
--
ALTER TABLE `spoilage_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `success_orders`
--
ALTER TABLE `success_orders`
  ADD PRIMARY KEY (`s_orderID`);

--
-- Indexes for table `usage_count`
--
ALTER TABLE `usage_count`
  ADD PRIMARY KEY (`ingredientID`);

--
-- Indexes for table `usage_reports`
--
ALTER TABLE `usage_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `uid` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2024049;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT for table `customerinfo`
--
ALTER TABLE `customerinfo`
  MODIFY `customerID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `daily_inventory`
--
ALTER TABLE `daily_inventory`
  MODIFY `inventoryID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `dishes`
--
ALTER TABLE `dishes`
  MODIFY `dish_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `food_category`
--
ALTER TABLE `food_category`
  MODIFY `categoryID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=311;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `msgID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `msg_users`
--
ALTER TABLE `msg_users`
  MODIFY `user_msgID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `notify_user`
--
ALTER TABLE `notify_user`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `orderID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `order_count`
--
ALTER TABLE `order_count`
  MODIFY `count_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `prodID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `records_inventory`
--
ALTER TABLE `records_inventory`
  MODIFY `recordID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT for table `remind_user`
--
ALTER TABLE `remind_user`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `spoilage_reports`
--
ALTER TABLE `spoilage_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `success_orders`
--
ALTER TABLE `success_orders`
  MODIFY `s_orderID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `usage_count`
--
ALTER TABLE `usage_count`
  MODIFY `ingredientID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `usage_reports`
--
ALTER TABLE `usage_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131810013;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `dish_cart` FOREIGN KEY (`dish_id`) REFERENCES `dishes` (`dish_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_cart` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `customerinfo`
--
ALTER TABLE `customerinfo`
  ADD CONSTRAINT `uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dishes`
--
ALTER TABLE `dishes`
  ADD CONSTRAINT `dish_category` FOREIGN KEY (`categoryID`) REFERENCES `food_category` (`categoryID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
