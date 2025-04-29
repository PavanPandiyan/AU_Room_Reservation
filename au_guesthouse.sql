-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 29, 2025 at 04:54 PM
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
-- Database: `au_guesthouse`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(2, 'admin', '$2y$10$5ltK2ay2jEPkAl2s1ensQ.NrvC/eaFujVL5NHt7eUEbBxTjYft/Ei');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `reservation_id`, `payment_method`, `amount`, `payment_status`, `transaction_id`, `created_at`) VALUES
(1, 11, 'online', 5000.00, 'Pending', 'AUTO_GENERATED_ID', '2025-03-16 20:41:04'),
(2, 12, 'offline', 3200.00, 'Completed', 'N/A', '2025-03-20 05:20:46'),
(3, 13, 'offline', 8800.00, 'Completed', 'N/A', '2025-03-20 06:12:50'),
(4, 14, 'offline', 800.00, 'Completed', 'N/A', '2025-03-21 08:29:55'),
(5, 15, 'offline', 2000.00, 'Completed', 'N/A', '2025-03-24 10:28:27'),
(6, 16, 'offline', 400.00, 'Completed', 'N/A', '2025-03-31 15:57:45'),
(7, 17, 'online', 2.00, 'Pending', 'AUTO_GENERATED_ID', '2025-03-31 16:05:14'),
(8, 18, 'online', 2.00, 'Pending', 'AUTO_GENERATED_ID', '2025-03-31 16:10:45'),
(9, 19, 'online', 4.00, 'Pending', 'AUTO_GENERATED_ID', '2025-03-31 16:11:34'),
(10, 20, 'online', 1.00, 'Pending', 'AUTO_GENERATED_ID', '2025-03-31 16:25:38'),
(11, 21, 'online', 3600.00, 'Pending', 'AUTO_GENERATED_ID', '2025-03-31 16:28:04'),
(12, 22, 'online', 2000.00, 'Pending', 'AUTO_GENERATED_ID', '2025-03-31 16:33:59'),
(13, 23, 'online', 800.00, 'Pending', NULL, '2025-04-01 10:37:50'),
(14, 24, 'online', 800.00, 'Pending', NULL, '2025-04-01 10:38:38'),
(15, 25, 'online', 24000.00, 'Pending', NULL, '2025-04-01 11:21:10'),
(16, 26, 'online', 2000.00, 'Pending', NULL, '2025-04-01 11:38:49'),
(17, 27, 'online', 2000.00, 'Pending', NULL, '2025-04-01 11:53:01'),
(18, 28, 'online', 2000.00, 'Pending', NULL, '2025-04-03 04:21:33'),
(19, 29, 'online', 3000.00, 'Pending', NULL, '2025-04-24 16:21:36');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `applicant_name` varchar(255) NOT NULL,
  `applicant_address` text NOT NULL,
  `guest_name` varchar(255) NOT NULL,
  `guest_address` text NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `purpose` text NOT NULL,
  `room_type` varchar(100) NOT NULL,
  `num_rooms` int(11) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `checkin_date` date NOT NULL,
  `checkout_date` date NOT NULL,
  `cost_type` enum('fixed','variable') NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_status` enum('paid','unpaid') NOT NULL DEFAULT 'unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `applicant_name`, `applicant_address`, `guest_name`, `guest_address`, `staff_id`, `purpose`, `room_type`, `num_rooms`, `room_number`, `checkin_date`, `checkout_date`, `cost_type`, `total_cost`, `created_at`, `user_id`, `status`, `payment_status`) VALUES
(11, 'pavan', 'Chidambaram', 'Nandhan', 'Chidambaram', 23300037, 'Official', '0', 2, '91,92', '2025-03-17', '2025-03-18', '', 5000.00, '2025-03-16 20:41:04', 13, 'pending', 'unpaid'),
(12, 'pavan', 'Chidambaram', 'Nandhan', 'Chidambaram', 23300037, 'Official', '0', 4, '1,2,3,4', '2025-03-20', '2025-03-22', '', 3200.00, '2025-03-20 05:20:46', 13, 'pending', 'unpaid'),
(13, 'pavan', 'Chidambaram', 'guru', 'Chidambaram', 23300037, 'Personal', '0', 1, '29', '2025-03-27', '2025-04-18', '', 8800.00, '2025-03-20 06:12:50', 15, 'cancelled', 'unpaid'),
(14, 'pavan', 'cdm', 'arun', 'cdm', 1, 'Official', '0', 2, '1,2', '2025-03-21', '2025-03-22', '', 800.00, '2025-03-21 08:29:55', 13, 'confirmed', 'unpaid'),
(15, 'pavan', 'Chidambaram', 'Nandhan', 'Chidambaram', 23300037, 'Official', '0', 2, '81,82', '2025-03-24', '2025-03-25', '', 2000.00, '2025-03-24 10:28:27', 13, 'cancelled', 'unpaid'),
(16, 'pavan', 'Chidambaram', 'Nandhan', 'Chidambaram', 23300037, 'Official', '0', 1, '1', '2025-03-15', '2025-03-16', '', 400.00, '2025-03-31 15:57:45', 13, 'pending', 'unpaid'),
(17, 'pavan', 'Chidambaram', 'Nandhan', 'Chidambaram', 23300037, 'Official', '0', 1, '101', '2025-03-31', '2025-04-01', '', 2.00, '2025-03-31 16:05:14', 13, 'pending', 'unpaid'),
(18, 'pavan', 'cdm', 'Nandhan', 'Chidambaram', 23300037, 'Official', '0', 1, '102', '2025-03-31', '2025-04-01', '', 2.00, '2025-03-31 16:10:45', 13, 'pending', 'unpaid'),
(19, 'pavan', 'Chidambaram', 'Nandhan', 'Chidambaram', 23300037, 'Official', '0', 2, '101,102', '2025-04-02', '2025-04-03', '', 4.00, '2025-03-31 16:11:34', 13, 'pending', 'paid'),
(20, 'pavan', 'Chidambaram', 'Nandhan', 'Chidambaram', 23300037, 'Official', '0', 1, '101', '2025-04-21', '2025-04-22', '', 1.00, '2025-03-31 16:25:38', 13, 'pending', 'unpaid'),
(21, 'pavan', 'Chidambaram', 'Nandhan', 'Chidambaram', 23300037, 'Official', '0', 3, '31,32,33', '2025-03-31', '2025-04-01', '', 3600.00, '2025-03-31 16:28:04', 13, 'pending', 'unpaid'),
(22, 'pavan', 'Chidambaram', 'Nandhan', 'Chidambaram', 23300037, 'Official', '0', 2, '81,82', '2025-04-01', '2025-04-02', '', 2000.00, '2025-03-31 16:33:59', 13, 'pending', 'unpaid'),
(23, 'pavan', 'Chidambaram', 'Nandhan', 'Chidambaram', 23300037, 'Official', '0', 1, '31', '2025-04-01', '2025-04-02', '', 800.00, '2025-04-01 10:37:50', 13, 'pending', 'unpaid'),
(24, 'pavan', 'Chidambaram', 'Nandhan', 'Chidambaram', 23300037, 'Official', '0', 1, '32', '2025-04-01', '2025-04-02', '', 800.00, '2025-04-01 10:38:38', 13, 'pending', 'unpaid'),
(25, 'pavan', 'Chidambaram', 'Nandhan', 'Chidambaram', 23300037, 'Official', '0', 2, '91,92', '2025-04-01', '2025-04-09', '', 24000.00, '2025-04-01 11:21:10', 13, 'pending', 'unpaid'),
(26, 'pavan', 'Chidambaram', 'Nandhan', 'Chidambaram', 23300037, 'Official', '0', 2, '83,86', '2025-04-01', '2025-04-02', '', 2000.00, '2025-04-01 11:38:49', 13, 'pending', 'unpaid'),
(27, 'pavan', 'Chidambaram', 'Nandhan', 'Chidambaram', 23300037, 'Official', '0', 2, '81,82', '2025-04-09', '2025-04-10', '', 2000.00, '2025-04-01 11:53:01', 13, 'pending', 'unpaid'),
(28, 'pavan', 'Chidambaram', 'praveena', 'Chidambaram', 23300037, 'Official', '0', 2, '81,82', '2025-04-06', '2025-04-07', '', 2000.00, '2025-04-03 04:21:33', 13, 'pending', 'unpaid'),
(29, 'pavan', 'Chidambaram', 'Nandhan', 'Chidambaram', 23300037, 'Official', '0', 2, '82,83', '2025-04-24', '2025-04-25', '', 3000.00, '2025-04-24 16:21:36', 13, 'pending', 'unpaid');

-- --------------------------------------------------------

--
-- Table structure for table `room_list`
--

CREATE TABLE `room_list` (
  `id` int(11) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `room_number` int(11) NOT NULL,
  `cost_official` decimal(10,2) NOT NULL,
  `cost_others` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('Available','Booked') DEFAULT 'Available',
  `total_rooms` int(11) NOT NULL,
  `checkin_date` date DEFAULT NULL,
  `checkout_date` date DEFAULT NULL,
  `available_rooms` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_list`
--

INSERT INTO `room_list` (`id`, `room_type`, `room_number`, `cost_official`, `cost_others`, `image`, `status`, `total_rooms`, `checkin_date`, `checkout_date`, `available_rooms`) VALUES
(1, 'NON AC', 1, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(2, 'NON AC', 2, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(3, 'NON AC', 3, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(4, 'NON AC', 4, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(5, 'NON AC', 5, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(6, 'NON AC', 6, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(7, 'NON AC', 7, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(8, 'NON AC', 8, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(9, 'NON AC', 9, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(10, 'NON AC', 10, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(11, 'NON AC', 11, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(12, 'NON AC', 12, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(13, 'NON AC', 13, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(14, 'NON AC', 14, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(15, 'NON AC', 15, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(16, 'NON AC', 16, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(17, 'NON AC', 17, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(18, 'NON AC', 18, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(19, 'NON AC', 19, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(20, 'NON AC', 20, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(21, 'NON AC', 21, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(22, 'NON AC', 22, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(23, 'NON AC', 23, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(24, 'NON AC', 24, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(25, 'NON AC', 25, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(26, 'NON AC', 26, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(27, 'NON AC', 27, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(28, 'NON AC', 28, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(29, 'NON AC', 29, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(30, 'NON AC', 30, 400.00, 600.00, 'uploads/1742148844_h8.jpeg', 'Available', 0, NULL, NULL, 0),
(31, 'AC', 31, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(32, 'AC', 32, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(33, 'AC', 33, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(34, 'AC', 34, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(35, 'AC', 35, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(36, 'AC', 36, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(37, 'AC', 37, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(38, 'AC', 38, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(39, 'AC', 39, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(40, 'AC', 40, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(41, 'AC', 41, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(42, 'AC', 42, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(43, 'AC', 43, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(44, 'AC', 44, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(45, 'AC', 45, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(46, 'AC', 46, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(47, 'AC', 47, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(48, 'AC', 48, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(49, 'AC', 49, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(50, 'AC', 50, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(51, 'AC', 51, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(52, 'AC', 52, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(53, 'AC', 53, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(54, 'AC', 54, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(55, 'AC', 55, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(56, 'AC', 56, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(57, 'AC', 57, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(58, 'AC', 58, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(59, 'AC', 59, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(60, 'AC', 60, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(61, 'AC', 61, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(62, 'AC', 62, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(63, 'AC', 63, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(64, 'AC', 64, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(65, 'AC', 65, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(66, 'AC', 66, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(67, 'AC', 67, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(68, 'AC', 68, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(69, 'AC', 69, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(70, 'AC', 70, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(71, 'AC', 71, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(72, 'AC', 72, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(73, 'AC', 73, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(74, 'AC', 74, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(75, 'AC', 75, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(76, 'AC', 76, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(77, 'AC', 77, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(78, 'AC', 78, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(79, 'AC', 79, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(80, 'AC', 80, 800.00, 1200.00, 'uploads/1742148895_h6.jpeg', 'Available', 0, NULL, NULL, 0),
(81, 'AC WITH TV', 81, 1000.00, 1500.00, 'uploads/1742148943_h5.jpeg', 'Available', 0, NULL, NULL, 0),
(82, 'AC WITH TV', 82, 1000.00, 1500.00, 'uploads/1742148943_h5.jpeg', 'Available', 0, NULL, NULL, 0),
(83, 'AC WITH TV', 83, 1000.00, 1500.00, 'uploads/1742148943_h5.jpeg', 'Available', 0, NULL, NULL, 0),
(84, 'AC WITH TV', 84, 1000.00, 1500.00, 'uploads/1742148943_h5.jpeg', 'Available', 0, NULL, NULL, 0),
(85, 'AC WITH TV', 85, 1000.00, 1500.00, 'uploads/1742148943_h5.jpeg', 'Available', 0, NULL, NULL, 0),
(86, 'AC WITH TV', 86, 1000.00, 1500.00, 'uploads/1742148943_h5.jpeg', 'Available', 0, NULL, NULL, 0),
(87, 'AC WITH TV', 87, 1000.00, 1500.00, 'uploads/1742148943_h5.jpeg', 'Available', 0, NULL, NULL, 0),
(88, 'AC WITH TV', 88, 1000.00, 1500.00, 'uploads/1742148943_h5.jpeg', 'Available', 0, NULL, NULL, 0),
(89, 'AC WITH TV', 89, 1000.00, 1500.00, 'uploads/1742148943_h5.jpeg', 'Available', 0, NULL, NULL, 0),
(90, 'AC WITH TV', 90, 1000.00, 1500.00, 'uploads/1742148943_h5.jpeg', 'Available', 0, NULL, NULL, 0),
(91, 'SUIT', 91, 1500.00, 2500.00, 'uploads/1742148989_h7.jpeg', 'Available', 0, NULL, NULL, 0),
(92, 'SUIT', 92, 1500.00, 2500.00, 'uploads/1742148989_h7.jpeg', 'Available', 0, NULL, NULL, 0),
(93, 'SUIT', 93, 1500.00, 2500.00, 'uploads/1742148989_h7.jpeg', 'Available', 0, NULL, NULL, 0),
(94, 'SUIT', 94, 1500.00, 2500.00, 'uploads/1742148989_h7.jpeg', 'Available', 0, NULL, NULL, 0),
(95, 'SUIT', 95, 1500.00, 2500.00, 'uploads/1742148989_h7.jpeg', 'Available', 0, NULL, NULL, 0),
(96, 'delux', 101, 1.00, 2.00, 'uploads/1743436779_h4.jpeg', 'Available', 0, NULL, NULL, 0),
(97, 'delux', 102, 1.00, 2.00, 'uploads/1743436779_h4.jpeg', 'Available', 0, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `mailid` varchar(150) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `age` int(11) NOT NULL,
  `aadhar` varchar(16) NOT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `mailid`, `phone`, `gender`, `age`, `aadhar`, `otp`, `otp_expiry`, `status`) VALUES
(4, 'nanthan', '$2y$10$iO4o2GoYBy0gqoJs4.wr2Ofpfv2CxnulzGNWHarwLOriI53904Sx6', 'nanthan@gmail.com', '9361609280', 'Male', 22, '934985630939', NULL, NULL, 'active'),
(5, 'Kishore', '$2y$10$kHvTgVPalOONc9gppw887evvImTgdSJOrzj3r57iYe9CF5/KRujB2', 'kishorecsc007@gmail.com', '9150805236', 'Male', 23, '944936407216', NULL, NULL, 'active'),
(10, 'arjun', '$2y$10$Kq0wc0gl1hrP4wE379QyNepjVTwdHNZB2FTH6bLBYX0YvkX0GNuha', 'arjun@gmail.com', '8015331535', 'Male', 22, '934985630949', NULL, NULL, 'active'),
(11, 'praveena', '$2y$10$aqq.CrwjwEk6D3WZp83rJe7M842EDKD56pPvbAifMQCEdwHWxdzIS', 'praveena@gmail.com', '9791835729', 'Female', 22, '844645867365', NULL, NULL, 'active'),
(13, 'pavan', '$2y$10$uIqTJvQM1URyidu.3/qSnOwPd9YpMF7hoRNVkF0RMTOflQsSzuwCC', 'pavan@gmail.com', '8098202041', 'Male', 22, '934985630939', NULL, NULL, 'active'),
(14, 'praveen', '$2y$10$F4YbMJvOZ5QhQPudcWL0FOCrVb5Welq1VZtumhZKpSF77UHLGLc2e', 'praveen@gmail.com', '7373674204', 'Male', 24, '944946407216', NULL, NULL, 'active'),
(15, 'sriram', '$2y$10$tIUX/5w6Xgo8oR1secmoe.cjLTI6fKQSV0cRGsi/yYZCkSOlALP6u', 'sriram@gmail.com', '9874561230', 'Male', 22, '789456123012', NULL, NULL, 'active'),
(16, 'kabali', '$2y$10$nuVjJuluxi2sQSMdF3DiVubk3rcVk2c2j/qHZH2RbNWYuir1txjzm', 'kabali@gmail.com', '6789012345', 'Male', 22, '924985630935', NULL, NULL, 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `room_list`
--
ALTER TABLE `room_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_number` (`room_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mailid` (`mailid`),
  ADD UNIQUE KEY `unique_email` (`mailid`),
  ADD UNIQUE KEY `unique_username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `room_list`
--
ALTER TABLE `room_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
