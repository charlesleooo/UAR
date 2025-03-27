-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2025 at 03:16 AM
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
  `access_request_number` varchar(20) NOT NULL,
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
(28, 'alven tampos', 'AAC', 'REQ2025-004', 'INFORMATION TECHNOLOGY (IT)', 'charlesleohermano@gmail.com', '09606072661', 'System Application', 'qwe', 'ERP/NAV', NULL, '', 'permanent', NULL, NULL, 'pending', '2025-03-27 10:02:14', NULL, NULL, NULL);

--
-- Triggers `access_requests`
--
DELIMITER $$
CREATE TRIGGER `after_request_status_change` AFTER UPDATE ON `access_requests` FOR EACH ROW BEGIN
    IF NEW.status IN ('approved', 'rejected') THEN
        INSERT INTO approval_history (
            request_id, 
            admin_id, 
            action, 
            comments, 
            requestor_name, 
            business_unit, 
            department, 
            access_type, 
            system_type, 
            duration_type, 
            start_date, 
            end_date, 
            justification, 
            email, 
            contact_number, 
            access_request_number
        )
        VALUES (
            NEW.id,
            NEW.reviewed_by,
            NEW.status,
            NEW.review_notes,
            NEW.requestor_name,
            NEW.business_unit,
            NEW.department,
            NEW.access_type,
            NEW.system_type,
            NEW.duration_type,
            NEW.start_date,
            NEW.end_date,
            NEW.justification,
            NEW.email,
            NEW.contact_number,
            NEW.access_request_number
        );
        
        DELETE FROM access_requests WHERE id = NEW.id;
    END IF;
END
$$
DELIMITER ;

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
  `access_request_number` varchar(20) NOT NULL,
  `action` enum('approved','rejected') NOT NULL,
  `requestor_name` varchar(255) NOT NULL,
  `business_unit` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL,
  `access_type` varchar(50) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `system_type` varchar(255) DEFAULT NULL,
  `duration_type` varchar(20) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `justification` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approval_history`
--

INSERT INTO `approval_history` (`history_id`, `access_request_number`, `action`, `requestor_name`, `business_unit`, `department`, `access_type`, `admin_id`, `comments`, `system_type`, `duration_type`, `start_date`, `end_date`, `justification`, `email`, `contact_number`, `created_at`) VALUES
(4, 'REQ2025-001', 'approved', 'alven tampos', 'AAC', 'INFORMATION TECHNOLOGY (IT)', 'System Application', 3, '', 'ERP/NAV', 'permanent', NULL, NULL, 'qwe', 'alvintampus3@gmail.com', '09295219115', '2025-03-27 02:01:01'),
(5, 'REQ2025-002', 'rejected', 'qwe', 'ALDEV', 'ALD Cattle', 'PC Access - Network', 3, '', NULL, 'permanent', NULL, NULL, 'qwe', 'qwe@gmail.com', '09606072661', '2025-03-27 02:01:15'),
(6, 'REQ2025-003', 'approved', 'Charles Leo Palomares', 'AAC', 'INFORMATION TECHNOLOGY (IT)', 'Active Directory Access (MS ENTRA ID)', 3, '', NULL, 'temporary', '2025-03-27', '2025-03-28', 'qwe', 'qwe@gmail.com', '09606072661', '2025-03-27 02:01:48');

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
  ADD KEY `admin_id` (`admin_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `approval_history`
--
ALTER TABLE `approval_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
