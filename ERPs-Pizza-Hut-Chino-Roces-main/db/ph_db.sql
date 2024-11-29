-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 05, 2024 at 03:13 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ph_db`
--

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

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `dish_id`, `uid`, `name`, `size`, `qty`, `price`, `img`, `totalprice`) VALUES
(156, 17, 131810009, 'Cheese Lovers', '9inch', 1, 379, 'cheese_lovers.png', 379);

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
(131810001, 'admin01@gmail.com', 'admin', '08482610ff176fb7bb4e284a7766b036'),
(131810009, 'magno123@gmail.com', 'customer', '2c103f2c4ed1e59c0b4e2e01821770fa'),
(131810010, 'superadmin@gmail.com', 'super_admin', '08482610ff176fb7bb4e284a7766b036');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT for table `customerinfo`
--
ALTER TABLE `customerinfo`
  MODIFY `customerID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `msgID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `msg_users`
--
ALTER TABLE `msg_users`
  MODIFY `user_msgID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `orderID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `order_count`
--
ALTER TABLE `order_count`
  MODIFY `count_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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

--
-- Constraints for table `order_count`
--
ALTER TABLE `order_count`
  ADD CONSTRAINT `dish_id` FOREIGN KEY (`dish_id`) REFERENCES `dishes` (`dish_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
