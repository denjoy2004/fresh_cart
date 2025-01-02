-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 02, 2025 at 06:07 PM
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
-- Database: `fresh_cart`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_table`
--

CREATE TABLE `admin_table` (
  `admin_name` varchar(50) NOT NULL,
  `admin_username` varchar(50) NOT NULL,
  `admin_mbno` varchar(10) NOT NULL,
  `admin_password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_table`
--

INSERT INTO `admin_table` (`admin_name`, `admin_username`, `admin_mbno`, `admin_password`) VALUES
('Adarsh S Kumar', 'adarsgskumar@gmail.com', '8590399437', 'adarsg123'),
('Den Joy', 'denjoykunnini@gmail.com', '9539658310', 'den123');

-- --------------------------------------------------------

--
-- Table structure for table `buyer_table`
--

CREATE TABLE `buyer_table` (
  `buyer_name` varchar(50) NOT NULL,
  `buyer_mbno` varchar(10) NOT NULL,
  `buyer_username` varchar(50) NOT NULL,
  `buyer_password` varchar(50) NOT NULL,
  `buyer_house_name` varchar(15) NOT NULL,
  `buyer_area` varchar(15) NOT NULL,
  `buyer_city` varchar(15) NOT NULL,
  `buyer_state` varchar(15) NOT NULL,
  `buyer_pincode` varchar(6) NOT NULL,
  `status` enum('active','removed') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buyer_table`
--

INSERT INTO `buyer_table` (`buyer_name`, `buyer_mbno`, `buyer_username`, `buyer_password`, `buyer_house_name`, `buyer_area`, `buyer_city`, `buyer_state`, `buyer_pincode`, `status`) VALUES
('Anoop Kumar', '9876543210', 'anoop.kumar@example.com', 'password123', 'Green Villa', 'Poojappura', 'Thiruvananthapu', 'Kerala', '695012', 'active'),
('Arya S', '9567112233', 'arya.s@example.com', 'password123', 'Blue Hills', 'Feroke', 'Kozhikode', 'Kerala', '673631', 'active'),
('Bince Benny', '6238133610', 'binzbenny@gmail.com', 'binz123', 'Thekkekara', 'Chemmalamattam', 'Kottayam', 'kerala', '688845', 'active'),
('Devika R', '9845126789', 'devika.r@example.com', 'password123', 'Ocean View', 'Palarivattom', 'Kochi', 'Kerala', '682025', 'active'),
('Kiran Jose', '9745123456', 'kiran.jose@example.com', 'password123', 'St. Mary’s', 'Mananchira', 'Kozhikode', 'Kerala', '673001', 'active'),
('Lekshmi Menon', '9561234567', 'lekshmi.menon@example.com', 'password123', 'Rose Cottage', 'Kadavanthra', 'Kochi', 'Kerala', '682020', 'active'),
('Meera Nair', '9447123456', 'meera.nair@example.com', 'password123', 'Lakshmi Bhavan', 'Kowdiar', 'Thiruvananthapu', 'Kerala', '695003', 'active'),
('Nandana P', '9446547890', 'nandana.p@example.com', 'password123', 'Harmony House', 'Palayam', 'Thrissur', 'Kerala', '680001', 'active'),
('Rahul Krishna', '9870012345', 'rahul.krishna@example.com', 'password123', 'Krishna Bhavan', 'Mavoor', 'Kozhikode', 'Kerala', '673661', 'active'),
('Sreejith K', '9496234512', 'sreejith.k@example.com', 'password123', 'Golden Nest', 'East Fort', 'Thrissur', 'Kerala', '680005', 'active'),
('test test', '1234567890', 'test@gmail.com', '12', 'testh', 'testa', 'testc', 'testss', '123456', 'removed'),
('Vishnu Raj', '9998765432', 'vishnu.raj@example.com', 'password123', 'Sunrise Villa', 'Kaloor', 'Kochi', 'Kerala', '682017', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `cart_table`
--

CREATE TABLE `cart_table` (
  `cart_id` int(11) NOT NULL,
  `buyer_id` varchar(50) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items_table`
--

CREATE TABLE `order_items_table` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `seller_id` varchar(50) NOT NULL,
  `buyer_id` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `order_status` enum('pending','shipped','delivered','cancelled') DEFAULT 'pending',
  `ordered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items_table`
--

INSERT INTO `order_items_table` (`order_item_id`, `order_id`, `product_id`, `seller_id`, `buyer_id`, `quantity`, `price`, `order_status`, `ordered_at`, `order_updated_at`) VALUES
(27, 32, 20, 'test2@gmail.com', 'test@gmail.com', 2, 10.00, 'cancelled', '2024-11-01 16:31:59', '2024-11-07 15:51:25'),
(28, 32, 21, 'test2@gmail.com', 'test@gmail.com', 1, 8.00, 'delivered', '2024-11-01 16:31:59', '2024-11-04 17:22:15'),
(29, 33, 22, 'test2@gmail.com', 'test@gmail.com', 1, 15.00, 'pending', '2024-11-01 16:52:30', '2024-11-01 16:52:30'),
(30, 34, 18, 'test2@gmail.com', 'test@gmail.com', 1, 80.00, 'pending', '2024-11-01 17:04:26', '2024-11-01 17:04:26'),
(31, 35, 20, 'test2@gmail.com', 'test@gmail.com', 1, 10.00, 'pending', '2024-11-01 17:06:26', '2024-11-01 17:06:26'),
(32, 36, 23, 'test2@gmail.com', 'test@gmail.com', 1, 5.00, 'delivered', '2024-11-01 17:07:52', '2024-11-04 17:21:38'),
(33, 37, 16, 'test2@gmail.com', 'test@gmail.com', 1, 120.00, 'shipped', '2024-11-04 15:21:14', '2024-11-04 17:21:13'),
(34, 37, 17, 'test2@gmail.com', 'test@gmail.com', 1, 150.00, 'pending', '2024-11-04 15:21:14', '2024-11-04 15:21:14'),
(35, 37, 20, 'test2@gmail.com', 'test@gmail.com', 1, 10.00, 'pending', '2024-11-04 15:21:14', '2024-11-04 15:21:14'),
(36, 37, 21, 'test2@gmail.com', 'test@gmail.com', 1, 8.00, 'pending', '2024-11-04 15:21:14', '2024-11-04 15:21:14'),
(37, 37, 28, 'test2@gmail.com', 'test@gmail.com', 1, 10.00, 'pending', '2024-11-04 15:21:14', '2024-11-04 15:21:14'),
(38, 38, 20, 'test2@gmail.com', 'test@gmail.com', 1, 10.00, 'pending', '2024-11-04 15:27:09', '2024-11-04 15:27:09'),
(39, 38, 18, 'test2@gmail.com', 'test@gmail.com', 1, 80.00, 'pending', '2024-11-04 15:27:09', '2024-11-04 15:27:09'),
(40, 39, 18, 'test2@gmail.com', 'test@gmail.com', 2, 80.00, 'pending', '2024-11-27 08:53:34', '2024-11-27 08:53:34'),
(41, 39, 16, 'test2@gmail.com', 'test@gmail.com', 1, 120.00, 'pending', '2024-11-27 08:53:34', '2024-11-27 08:53:34'),
(42, 39, 18, 'test2@gmail.com', 'test@gmail.com', 1, 80.00, 'pending', '2024-11-27 08:53:34', '2024-11-27 08:53:34'),
(43, 39, 20, 'test2@gmail.com', 'test@gmail.com', 1, 10.00, 'pending', '2024-11-27 08:53:34', '2024-11-27 08:53:34'),
(44, 39, 20, 'test2@gmail.com', 'test@gmail.com', 10, 10.00, 'pending', '2024-11-27 08:53:34', '2024-11-27 08:53:34'),
(45, 40, 21, 'test2@gmail.com', 'test@gmail.com', 10, 8.00, 'pending', '2024-11-27 09:07:47', '2024-11-27 09:07:47'),
(46, 41, 18, 'test2@gmail.com', 'test@gmail.com', 1, 80.00, 'pending', '2024-11-27 09:14:16', '2024-11-27 09:14:16'),
(47, 41, 18, 'test2@gmail.com', 'test@gmail.com', 1, 80.00, 'pending', '2024-11-27 09:14:16', '2024-11-27 09:14:16'),
(48, 42, 21, 'test2@gmail.com', 'test@gmail.com', 10, 8.00, 'pending', '2024-11-27 09:35:29', '2024-11-27 09:35:29'),
(49, 43, 21, 'test2@gmail.com', 'test@gmail.com', 2, 16.00, 'pending', '2024-12-14 09:55:24', '2024-12-14 09:55:24'),
(50, 44, 33, 'santhosh@gmail.com', 'binzbenny@gmail.com', 2, 400.00, 'pending', '2024-12-17 09:51:55', '2024-12-17 09:51:55'),
(51, 45, 32, 'santhosh@gmail.com', 'binzbenny@gmail.com', 3, 240.00, 'pending', '2024-12-17 15:44:31', '2024-12-17 15:44:31'),
(52, 45, 30, 'santhosh@gmail.com', 'binzbenny@gmail.com', 2, 100.00, 'pending', '2024-12-17 15:44:31', '2024-12-17 15:44:31'),
(53, 46, 33, 'santhosh@gmail.com', 'binzbenny@gmail.com', 2, 400.00, 'pending', '2024-12-18 06:43:20', '2024-12-18 06:43:20');

-- --------------------------------------------------------

--
-- Table structure for table `order_table`
--

CREATE TABLE `order_table` (
  `order_id` int(11) NOT NULL,
  `buyer_id` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_status` enum('pending','in progress','shipped','delivered','cancelled') DEFAULT 'pending',
  `payment_method` enum('upi','credit card / debit card','cash on delivery') NOT NULL,
  `ordered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_table`
--

INSERT INTO `order_table` (`order_id`, `buyer_id`, `total_amount`, `order_status`, `payment_method`, `ordered_at`, `order_updated_at`) VALUES
(32, 'test@gmail.com', 150.00, '', '', '2024-11-01 16:31:59', '2024-11-07 15:51:25'),
(33, 'test@gmail.com', 15.00, 'pending', '', '2024-11-01 16:52:30', '2024-11-01 16:52:30'),
(34, 'test@gmail.com', 80.00, 'pending', '', '2024-11-01 17:04:26', '2024-11-01 17:04:26'),
(35, 'test@gmail.com', 10.00, 'pending', '', '2024-11-01 17:06:26', '2024-11-01 17:06:26'),
(36, 'test@gmail.com', 5.00, 'delivered', 'upi', '2024-11-01 17:07:52', '2024-11-04 17:21:38'),
(37, 'test@gmail.com', 298.00, 'in progress', 'upi', '2024-11-04 15:21:14', '2024-11-04 17:20:43'),
(38, 'test@gmail.com', 90.00, 'cancelled', '', '2024-11-04 15:27:09', '2024-11-04 15:45:06'),
(39, 'test@gmail.com', 470.00, 'pending', 'upi', '2024-11-27 08:53:34', '2024-11-27 08:53:34'),
(40, 'test@gmail.com', 80.00, 'pending', '', '2024-11-27 09:07:47', '2024-11-27 09:07:47'),
(41, 'test@gmail.com', 160.00, 'pending', '', '2024-11-27 09:14:16', '2024-11-27 09:14:16'),
(42, 'test@gmail.com', 80.00, 'pending', '', '2024-11-27 09:35:29', '2024-11-27 09:35:29'),
(43, 'test@gmail.com', 16.00, 'pending', 'upi', '2024-12-14 09:55:24', '2024-12-14 09:55:24'),
(44, 'binzbenny@gmail.com', 400.00, 'pending', 'upi', '2024-12-17 09:51:55', '2024-12-17 09:51:55'),
(45, 'binzbenny@gmail.com', 340.00, 'cancelled', '', '2024-12-17 15:44:31', '2024-12-18 06:43:37'),
(46, 'binzbenny@gmail.com', 400.00, 'pending', '', '2024-12-18 06:43:20', '2024-12-18 06:43:20');

-- --------------------------------------------------------

--
-- Table structure for table `product_table`
--

CREATE TABLE `product_table` (
  `product_id` int(50) NOT NULL,
  `seller_Id` varchar(50) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL,
  `min_quantity` varchar(20) NOT NULL,
  `price` varchar(15) NOT NULL,
  `stock_quantity` varchar(20) NOT NULL,
  `image_path` varchar(100) NOT NULL,
  `product_added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','removed') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_table`
--

INSERT INTO `product_table` (`product_id`, `seller_Id`, `product_name`, `description`, `min_quantity`, `price`, `stock_quantity`, `image_path`, `product_added_at`, `updated_at`, `status`) VALUES
(16, 'test2@gmail.com', 'Apple', 'Fresh organic apples', '1 kg', '120', '96', 'apple.jpg', '2024-10-06 17:01:04', '2024-12-17 05:44:11', 'removed'),
(17, 'test2@gmail.com', 'Orange', 'Sweet and juicy oranges', '1 kg', '150', '147', 'orange.jpg', '2024-10-06 17:01:04', '2024-12-17 05:44:11', 'removed'),
(18, 'test2@gmail.com', 'Grape', 'Fresh grapes from the vineyard', '1 kg', '80', '17', 'grape.jpg', '2024-10-06 17:01:04', '2024-12-17 05:44:11', 'removed'),
(19, 'test2@gmail.com', 'Cheese', 'Creamy homemade cheese', '', '700', '0', 'cheese.jpg', '2024-10-06 17:01:04', '2024-12-17 05:44:11', 'removed'),
(20, 'test2@gmail.com', 'Beef', 'Grass-fed beef cuts', '', '10.00', '46', 'beef.jpg', '2024-10-06 17:01:04', '2024-12-17 05:44:11', 'removed'),
(21, 'test2@gmail.com', 'Chicken', 'Organic free-range chicken', '1 kg', '8', '88', 'chicken.jpg', '2024-10-06 17:01:04', '2024-12-17 05:44:11', 'removed'),
(22, 'test2@gmail.com', 'Wine', 'Premium red wine', '', '15.00', '30', 'wine.jpg', '2024-10-06 17:01:04', '2024-12-17 05:44:11', 'removed'),
(23, 'test2@gmail.com', 'Cinnamon', 'Ground cinnamon spice', '', '5.00', '59', 'cinnamon.jpg', '2024-10-06 17:03:46', '2024-12-17 05:44:11', 'removed'),
(28, 'test2@gmail.com', 'Orange', 'Sweet and juicy oranges', '', '10.00', '150', 'orange.jpg', '2024-10-06 17:01:04', '2024-11-12 03:50:02', 'removed'),
(30, 'santhosh@gmail.com', 'Redchilly', 'Fresh and spicy redchillies', '250 gm', '50', '98', 'redchilly.jpg', '2024-12-17 09:40:13', '2024-12-17 15:44:31', 'active'),
(31, 'santhosh@gmail.com', 'Mushroom', 'Fresh mushrooms for cooking', '500 gm', '120', '100', 'mushroom.jpg', '2024-12-17 09:40:13', '2024-12-17 09:41:14', 'active'),
(32, 'santhosh@gmail.com', 'Carrot', 'Fresh, organic carrots', '1 kg', '80', '97', 'carrot.jpg', '2024-12-17 09:40:13', '2024-12-17 15:44:31', 'active'),
(33, 'santhosh@gmail.com', 'Blackberry', 'Sweet and juicy blackberries', '500 gm', '200', '96', 'blackberry.jpg', '2024-12-17 09:40:13', '2024-12-18 06:43:20', 'active'),
(34, 'santhosh@gmail.com', 'Banana', 'Fresh bananas from Kerala', '1 kg', '80', '100', 'banana.jpg', '2024-12-17 09:40:13', '2024-12-17 09:41:34', 'active'),
(35, 'santhosh@gmail.com', 'Strawberry', 'Fresh strawberries', '500 gm', '250', '100', 'strawberry.jpg', '2024-12-17 09:40:13', '2024-12-17 09:41:40', 'active'),
(36, 'santhosh@gmail.com', 'Tomato', 'Fresh tomatoes for cooking', '500 gm', '60', '100', 'tomato.jpg', '2024-12-17 09:40:13', '2024-12-17 09:41:45', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `seller_table`
--

CREATE TABLE `seller_table` (
  `seller_name` varchar(50) NOT NULL,
  `seller_username` varchar(50) NOT NULL,
  `seller_mbno` varchar(10) NOT NULL,
  `seller_password` varchar(255) NOT NULL,
  `business_name` varchar(50) NOT NULL,
  `seller_area` varchar(15) NOT NULL,
  `seller_city` varchar(15) NOT NULL,
  `seller_state` varchar(15) NOT NULL,
  `seller_pincode` varchar(6) NOT NULL,
  `status` enum('active','removed') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seller_table`
--

INSERT INTO `seller_table` (`seller_name`, `seller_username`, `seller_mbno`, `seller_password`, `business_name`, `seller_area`, `seller_city`, `seller_state`, `seller_pincode`, `status`) VALUES
('Arya S', 'arya.s@example.com', '9567112233', 'password12', 'Arya Farms', 'Feroke', 'Kozhikode', 'Kerala', '673631', 'active'),
('Devika R', 'devika.r@example.com', '9845126789', 'password12', 'Devika Vegetables', 'Palarivattom', 'Kochi', 'Kerala', '682025', 'active'),
('Kiran Jose', 'kiran.jose@example.com', '9745123456', 'password12', 'Kiran Fruits', 'Mananchira', 'Kozhikode', 'Kerala', '673001', 'active'),
('Lekshmi Menon', 'lekshmi.menon@example.com', '9561234567', 'password12', 'Lekshmi Dairy', 'Kadavanthra', 'Kochi', 'Kerala', '682020', 'active'),
('Maya Nair', 'maya.nair@example.com', '9447123456', 'password12', 'Maya Spices', 'Kowdiar', 'Thiruvananthapu', 'Kerala', '695003', 'active'),
('Nandana P', 'nandana.p@example.com', '9446547890', 'password12', 'Nandana Grocery', 'Palayam', 'Thrissur', 'Kerala', '680001', 'active'),
('Rahul Krishna', 'rahul.krishna@example.com', '9870012345', 'password12', 'Krishna Fruits', 'Mavoor', 'Kozhikode', 'Kerala', '673661', 'active'),
('Ravi Kumar', 'ravi.kumar@example.com', '9876543210', 'password12', 'Ravi Agro', 'Poojappura', 'Thiruvananthapu', 'Kerala', '695012', 'active'),
('Santhosh Kumar', 'santhosh@gmail.com', '8111965831', 'santhosh123', 'Ajay Stores', 'Konni', 'Patthanamthitta', 'Kerala', '689692', 'active'),
('Sreejith K', 'sreejith.k@example.com', '9496234512', 'password12', 'Sreejith Enterprises', 'East Fort', 'Thrissur', 'Kerala', '680005', 'active'),
('test2', 'test2@gmail.com', '55656', '12', 'test2', 'fefn', 'kfjrnfj', 'enfnjkrjknf', '123456', 'removed'),
('Vijay Raj', 'vijay.raj@example.com', '9998765432', 'password12', 'Vijay Vegetables', 'Kaloor', 'Kochi', 'Kerala', '682017', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_table`
--
ALTER TABLE `admin_table`
  ADD PRIMARY KEY (`admin_username`);

--
-- Indexes for table `buyer_table`
--
ALTER TABLE `buyer_table`
  ADD PRIMARY KEY (`buyer_username`);

--
-- Indexes for table `cart_table`
--
ALTER TABLE `cart_table`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_items_table`
--
ALTER TABLE `order_items_table`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `buyer_id` (`buyer_id`);

--
-- Indexes for table `order_table`
--
ALTER TABLE `order_table`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `buyer_id` (`buyer_id`);

--
-- Indexes for table `product_table`
--
ALTER TABLE `product_table`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `seller_id` (`seller_Id`);

--
-- Indexes for table `seller_table`
--
ALTER TABLE `seller_table`
  ADD PRIMARY KEY (`seller_username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_table`
--
ALTER TABLE `cart_table`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `order_items_table`
--
ALTER TABLE `order_items_table`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `order_table`
--
ALTER TABLE `order_table`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `product_table`
--
ALTER TABLE `product_table`
  MODIFY `product_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_table`
--
ALTER TABLE `cart_table`
  ADD CONSTRAINT `cart_table_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `buyer_table` (`buyer_username`),
  ADD CONSTRAINT `cart_table_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product_table` (`product_id`);

--
-- Constraints for table `order_items_table`
--
ALTER TABLE `order_items_table`
  ADD CONSTRAINT `order_items_table_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order_table` (`order_id`),
  ADD CONSTRAINT `order_items_table_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product_table` (`product_id`),
  ADD CONSTRAINT `order_items_table_ibfk_3` FOREIGN KEY (`seller_id`) REFERENCES `seller_table` (`seller_username`),
  ADD CONSTRAINT `order_items_table_ibfk_4` FOREIGN KEY (`buyer_id`) REFERENCES `buyer_table` (`buyer_username`);

--
-- Constraints for table `order_table`
--
ALTER TABLE `order_table`
  ADD CONSTRAINT `order_table_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `buyer_table` (`buyer_username`);

--
-- Constraints for table `product_table`
--
ALTER TABLE `product_table`
  ADD CONSTRAINT `product_table_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `seller_table` (`seller_username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
