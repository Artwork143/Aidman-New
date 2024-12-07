-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2024 at 03:37 PM
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
-- Database: `aidman-db`
--

-- --------------------------------------------------------

--
-- Table structure for table `criteria`
--

CREATE TABLE `criteria` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `weight` decimal(3,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `criteria`
--

INSERT INTO `criteria` (`id`, `name`, `weight`) VALUES
(1, 'Damage Severity', 0.40),
(2, 'Number of Occupants', 0.20),
(3, 'Vulnerability', 0.20),
(4, 'Income Level', 0.10),
(5, 'Special Needs', 0.10);

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `image_path`) VALUES
(61, 'uploads/670f5259e998e.jpg'),
(64, 'uploads/670f53cea1890.jpg'),
(65, 'uploads/670f56186db81.jpg'),
(66, 'uploads/670f56237aaf5.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `initial_quantity` int(11) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `expiry_date` date NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `threshold_quantity` int(11) GENERATED ALWAYS AS (`initial_quantity` * 0.4) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `name`, `quantity`, `initial_quantity`, `unit`, `expiry_date`, `image_path`) VALUES
(8, 'Rice', 5, 100, 'kg', '2024-01-01', 'uploads/RICES.jpg'),
(10, 'Sardines Can', 88, 100, 'piece/s', '2025-01-01', 'uploads/Sardines.jpg'),
(16, 'Lucky Me Noodles', 88, 100, 'piece/s', '2024-01-01', 'uploads/noodles.png'),
(18, 'Pancit Canton', 87, 100, 'piece/s', '2025-01-01', 'uploads/pancit cantoon.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `residents`
--

CREATE TABLE `residents` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `damage_severity` int(11) NOT NULL,
  `number_of_occupants` int(11) NOT NULL,
  `vulnerability` int(11) NOT NULL,
  `income_level` int(11) NOT NULL,
  `special_needs` int(11) NOT NULL,
  `total_score` decimal(5,2) NOT NULL,
  `distribution_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`id`, `name`, `damage_severity`, `number_of_occupants`, `vulnerability`, `income_level`, `special_needs`, `total_score`, `distribution_date`) VALUES
(77, 'Neil Rechie Consolacion', 10, 5, 5, 6, 4, 7.00, NULL),
(78, 'Patricia Mae Rosales', 8, 3, 5, 4, 6, 5.80, NULL),
(79, 'John Paul Asoy', 5, 8, 8, 5, 5, 6.20, NULL),
(80, 'Janice Sitchon', 5, 6, 7, 5, 5, 5.60, NULL),
(113, 'Lea Canete', 3, 8, 5, 4, 5, 4.70, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `scheduled_assistance`
--

CREATE TABLE `scheduled_assistance` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `pickup_date` datetime NOT NULL,
  `notification_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scheduled_assistance`
--

INSERT INTO `scheduled_assistance` (`id`, `resident_id`, `pickup_date`, `notification_message`) VALUES
(12, 81, '2024-11-20 15:04:00', 'Scheduled for pickup on 2024-11-20T15:04'),
(15, 80, '2024-11-20 16:41:00', 'Scheduled for pickup on 2024-11-20T16:41'),
(17, 82, '2024-11-21 14:50:00', 'Scheduled for pickup on 2024-11-21T14:50'),
(18, 77, '2024-11-21 22:10:00', 'Scheduled for pickup on 2024-11-21T22:10');

-- --------------------------------------------------------

--
-- Table structure for table `scheduled_assistance_items`
--

CREATE TABLE `scheduled_assistance_items` (
  `id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scheduled_assistance_items`
--

INSERT INTO `scheduled_assistance_items` (`id`, `schedule_id`, `item_id`, `quantity`) VALUES
(89, 12, 8, 1),
(90, 12, 10, 1),
(91, 12, 16, 1),
(92, 12, 18, 1),
(101, 15, 8, 1),
(102, 15, 10, 1),
(103, 15, 16, 1),
(104, 15, 18, 1),
(113, 17, 8, 1),
(114, 17, 10, 1),
(115, 17, 16, 1),
(116, 17, 18, 1),
(117, 18, 8, 1),
(118, 18, 10, 1),
(119, 18, 16, 1),
(120, 18, 18, 1);

-- --------------------------------------------------------

--
-- Table structure for table `schedule_residents`
--

CREATE TABLE `schedule_residents` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `assistance_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule_residents`
--

INSERT INTO `schedule_residents` (`id`, `fullname`, `created_at`, `updated_at`, `assistance_status`) VALUES
(77, 'jonnas d. barlas', '2024-11-20 14:10:31', '2024-11-20 14:10:31', 'for pickup'),
(80, 'ben', '2024-11-19 08:41:35', '2024-11-19 08:41:35', 'for pickup'),
(81, 'ken', '2024-11-19 07:04:23', '2024-11-19 07:58:08', 'received'),
(82, 'cong', '2024-11-20 06:50:12', '2024-11-20 06:50:23', 'received');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Official','Resident') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `username`, `password`, `role`) VALUES
(20, 'Patricia Mae Rosales', 'patriciamae@gmail.com', 'Patty', '$2y$10$Fr83o8wSKGYk7os2qdc8/.qLUFrMpDfbmQPW0O87iTJs3EhMVBd2.', 'Admin'),
(21, 'John Paul Asoy', 'Johnpaul@gmail.com', 'John', '$2y$10$LDXjNaHI2MoJiXFNkd8xWuzQ0wjVG7krx8A/9MZS6tZT6iVP5bfWa', 'Official'),
(63, 'Janice Sitchon', 'Janicesitchon@gmail.com', 'Janice', '$2y$10$wO/3Ini//2GKNER88UXq8OW9uqKW1LXWu6WgWl1yFnqVY4Wy7ZwvW', 'Official'),
(74, 'Admin', 'narutoharaz2@gmail.com', 'Admin', '$2y$10$r7oYsYbknyVYN4Og3KNuuONxc9vjsd2b0SYef6Mb9OolAg44aKmj2', 'Admin'),
(75, 'Neil Rechie M. Consolacion', 'nielrechiecons@gmail.com', 'NeilR', '$2y$10$SNNIpY4zHZ90JdQe0lp8gulrzu1J2iNc1VrVXTIufPcgEdE5/Mmpq', 'Resident'),
(77, 'jonnas d. barlas', 'jonas@gmail.com', 'jonas', '$2y$10$Bkmx4rV2aeBGnq24bjCuQe44DraG6IBiLOeYVi9Qles/wpVTwxJRa', 'Resident'),
(80, 'ben', 'ben@gmail.com', 'ben', '$2y$10$XywBJy6y36vYxdz1Iuet4OB5k/cEiBKJuLtOvMci048obnLZlELTO', 'Resident'),
(81, 'ken', 'ken@gmail.com', 'ken', '$2y$10$YkEP0mHqsrjFFdivOgdlVOFMRq9.bjQJK6xKYibFdIvwpu.L/.Epm', 'Resident'),
(82, 'cong', 'cong@gmail.com', 'cong', '$2y$10$ockI75vLRLaJyQgVZk/QEupz5MTweiJTF/UuYNCNjGEm218AEX8tq', 'Resident');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `criteria`
--
ALTER TABLE `criteria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `residents`
--
ALTER TABLE `residents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scheduled_assistance`
--
ALTER TABLE `scheduled_assistance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resident_id` (`resident_id`);

--
-- Indexes for table `scheduled_assistance_items`
--
ALTER TABLE `scheduled_assistance_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `schedule_residents`
--
ALTER TABLE `schedule_residents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `criteria`
--
ALTER TABLE `criteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `scheduled_assistance`
--
ALTER TABLE `scheduled_assistance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `scheduled_assistance_items`
--
ALTER TABLE `scheduled_assistance_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `schedule_residents`
--
ALTER TABLE `schedule_residents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `scheduled_assistance`
--
ALTER TABLE `scheduled_assistance`
  ADD CONSTRAINT `scheduled_assistance_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `schedule_residents` (`id`);

--
-- Constraints for table `scheduled_assistance_items`
--
ALTER TABLE `scheduled_assistance_items`
  ADD CONSTRAINT `scheduled_assistance_items_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `scheduled_assistance` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
