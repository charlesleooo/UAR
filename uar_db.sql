-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2025 at 05:16 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uar_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_requests`
--

CREATE TABLE `access_requests` (
  `id` int(11) NOT NULL,
  `requestor_name` varchar(255) NOT NULL,
  `business_unit` varchar(50) NOT NULL,
  `access_request_number` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `access_type` varchar(50) NOT NULL,
  `justification` text NOT NULL,
  `system_type` varchar(255) DEFAULT NULL,
  `other_system_type` varchar(255) DEFAULT NULL,
  `role_access_type` varchar(50) DEFAULT NULL,
  `duration_type` varchar(20) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `submission_date` datetime NOT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `review_date` datetime DEFAULT NULL,
  `review_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `access_requests`
--

INSERT INTO `access_requests` (`id`, `requestor_name`, `business_unit`, `access_request_number`, `department`, `email`, `contact_number`, `access_type`, `justification`, `system_type`, `other_system_type`, `role_access_type`, `duration_type`, `start_date`, `end_date`, `status`, `submission_date`, `reviewed_by`, `review_date`, `review_notes`) VALUES
(1, 'Charles Leo Palomares', 'AAC', 1, 'INFORMATION TECHNOLOGY (IT)', 'charlesleohermano@gmail.com', '09606072661', 'email_access', 'newly hired', NULL, NULL, NULL, NULL, NULL, NULL, 'pending', '2025-03-26 05:07:18', NULL, NULL, NULL),
(2, 'alven tampos', 'AAC', 1, 'INFORMATION TECHNOLOGY (IT)', 'alvinthenoob@gmail.com', '12345678901', 'system_application', 'hahahaah ', 'erp_nav', NULL, '', 'permanent', NULL, NULL, 'pending', '2025-03-26 05:10:14', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `created_at`) VALUES
(3, 'admin', '$2y$10$SA4aRMZAhyKQPxzFdI1w/uwT1Xf2VKciIzpraAAxQcaRu2DTDYyHG', '2025-03-26 01:56:34');

-- --------------------------------------------------------

--
-- Table structure for table `approval_history`
--

CREATE TABLE `approval_history` (
  `history_id` int(11) NOT NULL,
  `request_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` enum('approved','rejected') NOT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `request_id` int(11) DEFAULT NULL,
  `report_type` varchar(50) NOT NULL,
  `content` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_requests`
--
ALTER TABLE `access_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `approval_history`
--
ALTER TABLE `approval_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `idx_approval_request` (`request_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `idx_reports_request` (`request_id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `idx_requests_status` (`status`),
  ADD KEY `idx_requests_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_requests`
--
ALTER TABLE `access_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `approval_history`
--
ALTER TABLE `approval_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approval_history`
--
ALTER TABLE `approval_history`
  ADD CONSTRAINT `approval_history_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`),
  ADD CONSTRAINT `approval_history_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`);

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admin_users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
