-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 17, 2025 at 07:32 AM
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
-- Database: `room_reservation`
--

-- --------------------------------------------------------

--
-- Table structure for table `accepting_reasons`
--

CREATE TABLE `accepting_reasons` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `personal_info_id` int(11) NOT NULL,
  `reason` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accepting_reasons`
--

INSERT INTO `accepting_reasons` (`id`, `user_id`, `personal_info_id`, `reason`) VALUES
(4, 73, 6, 'related_to_course'),
(5, 75, 8, 'related_to_course'),
(6, 76, 9, 'salaryBenefits'),
(7, 76, 9, 'careerChallenge');

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity_logs`
--

CREATE TABLE `admin_activity_logs` (
  `log_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `action_description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_permissions`
--

CREATE TABLE `admin_permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_permissions`
--

INSERT INTO `admin_permissions` (`permission_id`, `permission_name`, `description`, `created_at`) VALUES
(1, 'manage_users', 'Can create, edit, and delete users', '2025-01-09 12:02:34'),
(2, 'view_logs', 'Can view system logs', '2025-01-09 12:02:34'),
(3, 'manage_settings', 'Can modify system settings', '2025-01-09 12:02:34'),
(4, 'manage_content', 'Can manage website content', '2025-01-09 12:02:34');

-- --------------------------------------------------------

--
-- Table structure for table `admin_roles`
--

CREATE TABLE `admin_roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_roles`
--

INSERT INTO `admin_roles` (`role_id`, `role_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 'Full system access with all privileges', '2025-01-09 12:02:34', '2025-01-09 12:02:34'),
(2, 'admin', 'General administrative access', '2025-01-09 12:02:34', '2025-01-09 12:02:34'),
(3, 'moderator', 'Limited administrative access', '2025-01-09 12:02:34', '2025-01-09 12:02:34');

-- --------------------------------------------------------

--
-- Table structure for table `admin_role_permissions`
--

CREATE TABLE `admin_role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_role_permissions`
--

INSERT INTO `admin_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES
(1, 1, '2025-01-09 12:02:34'),
(1, 2, '2025-01-09 12:02:34'),
(1, 3, '2025-01-09 12:02:34'),
(1, 4, '2025-01-09 12:02:34');

-- --------------------------------------------------------

--
-- Table structure for table `admin_sessions`
--

CREATE TABLE `admin_sessions` (
  `session_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `session_token` varchar(100) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','moderator') NOT NULL DEFAULT 'admin',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`admin_id`, `username`, `first_name`, `last_name`, `email`, `password_hash`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(4, 'keith joshua.bungalso', 'Keith Joshua', 'Bungalso', 'keithjoshuabungalso123@gmail.com', '$2y$10$G7XzE6o4lsMaR.9t.xHLAuOUU9t5Kyp2vbWmTunUqftEMHeK/TOJq', 'admin', 1, '2025-03-07 17:29:24', '2025-01-09 12:21:29', '2025-03-07 17:29:24');

-- --------------------------------------------------------

--
-- Table structure for table `alumni`
--

CREATE TABLE `alumni` (
  `alumni_id` int(11) NOT NULL,
  `alumni_id_card_no` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `membership_type` varchar(255) NOT NULL,
  `verify` enum('used','unused') DEFAULT 'unused'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumni`
--

INSERT INTO `alumni` (`alumni_id`, `alumni_id_card_no`, `last_name`, `first_name`, `middle_name`, `membership_type`, `verify`) VALUES
(9, '001238', 'Bungalso', 'Keith Joshua', 'D', 'Lifetime', 'used'),
(10, '001237', 'Bungalso', 'Keith Joshua', 'D', 'Premium', 'used'),
(11, '001236', 'Bungalso', 'Keith Joshua', 'D', 'Premium', 'used'),
(12, '001231', 'Bungalso', 'Keith Joshua', 'LARIOSA', 'Premium', 'used');

-- --------------------------------------------------------

--
-- Table structure for table `alumni_id_cards`
--

CREATE TABLE `alumni_id_cards` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `course` varchar(255) NOT NULL,
  `year_graduated` int(11) NOT NULL,
  `highschool_graduated` varchar(255) NOT NULL,
  `membership_type` enum('5_years','lifetime') NOT NULL,
  `status` enum('pending','confirmed','declined','paid') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumni_id_cards`
--

INSERT INTO `alumni_id_cards` (`id`, `user_id`, `last_name`, `first_name`, `middle_name`, `email`, `course`, `year_graduated`, `highschool_graduated`, `membership_type`, `status`, `created_at`, `price`) VALUES
(71, 75, 'Bungalso', 'Keith Joshua', 'D', 'bungalsokeith@gmail.com', 'qwqeqwe', 2000, '123', 'lifetime', 'paid', '2025-02-23 12:35:23', 1500.00);

--
-- Triggers `alumni_id_cards`
--
DELIMITER $$
CREATE TRIGGER `before_delete_alumni_id_card` BEFORE DELETE ON `alumni_id_cards` FOR EACH ROW BEGIN
    -- You could add any additional logging or validation here
    INSERT INTO `audit_log` (
        `table_name`,
        `action`,
        `record_id`,
        `user_id`,
        `old_data`,
        `created_at`
    ) VALUES (
        'alumni_id_cards',
        'DELETE',
        OLD.id,
        OLD.user_id,
        JSON_OBJECT(
            'last_name', OLD.last_name,
            'first_name', OLD.first_name,
            'email', OLD.email,
            'status', OLD.status
        ),
        NOW()
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `badge` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `badge`, `title`, `content`, `created_at`, `status`) VALUES
(1, 'Event', 'SIRA ULO', 'news update po', '2025-01-12 12:32:45', 1),
(4, 'Event', 'SIRA ULO', 'Class Notes: Updates from alumni about marriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\r\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\r\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university dawdhistory, or ways alumni can give back.\\r\\nWould you like tips on how to structure content for a specific alumni', '2025-01-12 12:39:57', 1),
(7, 'Update', 'SIRA ULO', '1wadwadaw', '2025-01-27 13:08:47', 1),
(8, 'Academic', 'Meta', 'Class Notes: Updates from alumni about marriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\\\r\\\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\\\r\\\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university dawdhistory, or ways alumni can give back.\\\\r\\\\nWould you like tips on how to structure content for a specific alumni', '2025-02-03 09:00:41', 1);

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `action` varchar(10) NOT NULL,
  `record_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `table_name`, `action`, `record_id`, `user_id`, `old_data`, `created_at`) VALUES
(12, 'alumni_id_cards', 'DELETE', 39, 73, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"keithjoshuabungalso123@gmail.com\", \"status\": \"pending\"}', '2025-02-12 05:22:08'),
(13, 'alumni_id_cards', 'DELETE', 40, 73, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"keithjoshuabungalso123@gmail.com\", \"status\": \"pending\"}', '2025-02-12 05:25:56'),
(14, 'alumni_id_cards', 'DELETE', 41, 73, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"keithjoshuabungalso123@gmail.com\", \"status\": \"pending\"}', '2025-02-12 05:40:01'),
(15, 'alumni_id_cards', 'DELETE', 42, 73, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"keithjoshuabungalso123@gmail.com\", \"status\": \"pending\"}', '2025-02-12 05:40:32'),
(16, 'alumni_id_cards', 'DELETE', 43, 73, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"keithjoshuabungalso123@gmail.com\", \"status\": \"pending\"}', '2025-02-12 05:42:54'),
(17, 'alumni_id_cards', 'DELETE', 45, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"pending\"}', '2025-02-20 11:40:37'),
(18, 'alumni_id_cards', 'DELETE', 46, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"pending\"}', '2025-02-20 12:08:58'),
(19, 'alumni_id_cards', 'DELETE', 38, 63, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"keithjoshuabungalso123@gmail.com\", \"status\": \"pending\"}', '2025-02-20 12:15:35'),
(20, 'alumni_id_cards', 'DELETE', 44, 73, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"keithjoshuabungalso123@gmail.com\", \"status\": \"pending\"}', '2025-02-20 12:15:35'),
(21, 'alumni_id_cards', 'DELETE', 47, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"confirmed\"}', '2025-02-21 12:53:08'),
(22, 'alumni_id_cards', 'DELETE', 48, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"declined\"}', '2025-02-22 09:50:12'),
(23, 'alumni_id_cards', 'DELETE', 49, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"paid\"}', '2025-02-22 10:09:13'),
(24, 'alumni_id_cards', 'DELETE', 50, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"confirmed\"}', '2025-02-22 10:12:14'),
(25, 'alumni_id_cards', 'DELETE', 51, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"paid\"}', '2025-02-22 10:22:00'),
(26, 'alumni_id_cards', 'DELETE', 52, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"pending\"}', '2025-02-22 10:26:44'),
(27, 'alumni_id_cards', 'DELETE', 53, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"paid\"}', '2025-02-22 10:28:53'),
(28, 'alumni_id_cards', 'DELETE', 54, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"confirmed\"}', '2025-02-22 10:31:37'),
(29, 'alumni_id_cards', 'DELETE', 55, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"pending\"}', '2025-02-22 10:32:23'),
(30, 'alumni_id_cards', 'DELETE', 56, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"pending\"}', '2025-02-22 10:32:50'),
(31, 'alumni_id_cards', 'DELETE', 57, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"pending\"}', '2025-02-22 10:33:33'),
(32, 'alumni_id_cards', 'DELETE', 58, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"pending\"}', '2025-02-22 10:37:55'),
(33, 'alumni_id_cards', 'DELETE', 59, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"pending\"}', '2025-02-22 10:38:26'),
(34, 'alumni_id_cards', 'DELETE', 60, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"pending\"}', '2025-02-22 10:39:21'),
(35, 'alumni_id_cards', 'DELETE', 61, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"pending\"}', '2025-02-22 10:39:54'),
(36, 'alumni_id_cards', 'DELETE', 62, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"pending\"}', '2025-02-22 10:44:43'),
(37, 'alumni_id_cards', 'DELETE', 63, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"paid\"}', '2025-02-23 11:47:04'),
(38, 'alumni_id_cards', 'DELETE', 64, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"confirmed\"}', '2025-02-23 11:53:43'),
(39, 'alumni_id_cards', 'DELETE', 65, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"confirmed\"}', '2025-02-23 11:57:47'),
(40, 'alumni_id_cards', 'DELETE', 66, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"paid\"}', '2025-02-23 12:19:35'),
(41, 'alumni_id_cards', 'DELETE', 67, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"declined\"}', '2025-02-23 12:20:11'),
(42, 'alumni_id_cards', 'DELETE', 68, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"paid\"}', '2025-02-23 12:23:05'),
(43, 'alumni_id_cards', 'DELETE', 69, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"confirmed\"}', '2025-02-23 12:32:33'),
(44, 'alumni_id_cards', 'DELETE', 70, 75, '{\"last_name\": \"Bungalso\", \"first_name\": \"Keith Joshua\", \"email\": \"bungalsokeith@gmail.com\", \"status\": \"paid\"}', '2025-02-23 12:35:14');

-- --------------------------------------------------------

--
-- Table structure for table `backup_codes`
--

CREATE TABLE `backup_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(6) NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `used_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `board_pricing`
--

CREATE TABLE `board_pricing` (
  `id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `occupancy` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `board_pricing`
--

INSERT INTO `board_pricing` (`id`, `price`, `occupancy`) VALUES
(5, 5000.00, '20');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `reference_number` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_number` int(11) NOT NULL,
  `occupancy` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `price_per_day` decimal(10,2) DEFAULT NULL,
  `arrival_date` date NOT NULL,
  `arrival_time` time NOT NULL,
  `departure_date` date NOT NULL,
  `departure_time` time NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `reference_number`, `user_id`, `room_number`, `occupancy`, `price`, `price_per_day`, `arrival_date`, `arrival_time`, `departure_date`, `departure_time`, `status`, `created_at`) VALUES
(4, 'REF890123', 77, 3, 2, 5000.00, 2500.00, '2025-02-15', '14:00:00', '2025-02-17', '12:00:00', 'completed', '2025-02-10 01:00:00'),
(5, 'REF890124', 73, 6, 1, 2500.00, 2500.00, '2025-03-03', '12:30:00', '2025-03-04', '11:00:00', 'cancelled', '2025-03-01 08:45:00'),
(6, 'REF890125', 75, 9, 3, 7500.00, 2500.00, '2025-02-20', '15:00:00', '2025-02-23', '10:30:00', 'completed', '2025-02-15 06:20:00'),
(7, 'REF890126', 76, 2, 2, 5000.00, 2500.00, '2025-01-28', '13:00:00', '2025-01-30', '12:00:00', 'completed', '2025-01-20 00:15:00'),
(8, 'REF890127', 77, 10, 1, 2500.00, 2500.00, '2025-03-08', '14:00:00', '2025-03-09', '11:30:00', 'cancelled', '2025-03-06 02:40:00'),
(9, 'REF890128', 73, 1, 2, 5000.00, 2500.00, '2025-02-05', '15:30:00', '2025-02-07', '12:00:00', 'completed', '2025-02-01 03:10:00'),
(10, 'REF890129', 75, 4, 3, 7500.00, 2500.00, '2025-02-25', '14:45:00', '2025-02-28', '10:45:00', 'completed', '2025-02-20 09:30:00'),
(11, 'REF890130', 76, 7, 2, 5000.00, 2500.00, '2025-03-02', '12:00:00', '2025-03-04', '12:00:00', 'cancelled', '2025-02-28 05:00:00'),
(12, 'REF890131', 77, 11, 1, 2500.00, 2500.00, '2025-02-12', '14:30:00', '2025-02-13', '11:00:00', 'completed', '2025-02-10 01:50:00'),
(13, 'REF890132', 73, 5, 3, 7500.00, 2500.00, '2025-01-15', '15:15:00', '2025-01-18', '10:30:00', 'completed', '2025-01-10 04:00:00'),
(14, 'REF890133', 75, 8, 2, 5000.00, 2500.00, '2025-03-06', '13:30:00', '2025-03-07', '12:00:00', 'cancelled', '2025-03-04 06:25:00'),
(15, 'REF890134', 76, 12, 1, 2500.00, 2500.00, '2025-02-10', '14:00:00', '2025-02-11', '11:30:00', 'completed', '2025-02-05 01:20:00'),
(16, 'REF890135', 77, 6, 3, 7500.00, 2500.00, '2025-03-12', '12:45:00', '2025-03-15', '12:00:00', 'cancelled', '2025-03-09 07:10:00'),
(17, 'REF890136', 73, 9, 2, 5000.00, 2500.00, '2025-01-20', '13:10:00', '2025-01-22', '11:15:00', 'completed', '2025-01-15 02:55:00'),
(18, 'REF890137', 75, 2, 1, 2500.00, 2500.00, '2025-02-28', '14:00:00', '2025-03-01', '10:45:00', 'cancelled', '2025-02-25 08:35:00'),
(19, 'REF890138', 76, 7, 3, 7500.00, 2500.00, '2025-02-05', '15:45:00', '2025-02-08', '12:00:00', 'completed', '2025-02-01 00:30:00'),
(20, 'REF890139', 77, 1, 2, 5000.00, 2500.00, '2025-03-15', '12:20:00', '2025-03-17', '11:30:00', 'cancelled', '2025-03-12 06:50:00'),
(21, 'REF890140', 73, 3, 1, 2500.00, 2500.00, '2025-01-08', '14:10:00', '2025-01-09', '12:00:00', 'completed', '2025-01-05 01:15:00'),
(22, 'REF890141', 75, 4, 3, 7500.00, 2500.00, '2025-02-18', '15:00:00', '2025-02-21', '11:30:00', 'completed', '2025-02-13 04:40:00'),
(23, 'REF890142', 76, 10, 2, 5000.00, 2500.00, '2025-03-09', '13:45:00', '2025-03-10', '10:30:00', 'cancelled', '2025-03-06 03:25:00'),
(24, 'REF890143', 77, 5, 1, 2500.00, 2500.00, '2025-01-25', '14:30:00', '2025-01-26', '12:00:00', 'completed', '2025-01-20 02:00:00'),
(25, 'REF890025', 73, 2, 1, 2500.00, 2500.00, '2024-11-15', '14:00:00', '2024-11-16', '12:00:00', 'completed', '2024-11-10 00:30:00'),
(26, 'REF890026', 75, 5, 2, 5000.00, 2500.00, '2025-01-05', '13:30:00', '2025-01-07', '11:30:00', 'completed', '2024-12-30 01:45:00'),
(27, 'REF890027', 76, 10, 3, 7500.00, 2500.00, '2024-12-20', '15:15:00', '2024-12-23', '10:30:00', 'cancelled', '2024-12-18 03:20:00'),
(28, 'REF890028', 77, 8, 1, 2500.00, 2500.00, '2024-10-12', '14:45:00', '2024-10-13', '12:00:00', 'completed', '2024-10-07 23:50:00'),
(29, 'REF890029', 73, 1, 2, 5000.00, 2500.00, '2025-02-08', '12:30:00', '2025-02-10', '11:00:00', 'completed', '2025-02-03 02:00:00'),
(30, 'REF890030', 75, 6, 3, 7500.00, 2500.00, '2025-03-01', '15:00:00', '2025-03-04', '10:45:00', 'cancelled', '2025-02-25 06:10:00'),
(31, 'REF890031', 76, 4, 1, 2500.00, 2500.00, '2024-09-10', '14:10:00', '2024-09-11', '12:00:00', 'completed', '2024-09-06 01:20:00'),
(32, 'REF890032', 77, 9, 2, 5000.00, 2500.00, '2024-12-05', '13:50:00', '2024-12-07', '10:30:00', 'completed', '2024-12-01 03:15:00'),
(33, 'REF890033', 73, 3, 3, 7500.00, 2500.00, '2025-01-25', '15:30:00', '2025-01-28', '11:00:00', 'cancelled', '2025-01-22 04:30:00'),
(34, 'REF890034', 75, 7, 1, 2500.00, 2500.00, '2025-02-18', '14:30:00', '2025-02-19', '11:45:00', 'completed', '2025-02-15 01:40:00'),
(35, 'REF890035', 76, 12, 2, 5000.00, 2500.00, '2024-11-27', '13:00:00', '2024-11-29', '10:30:00', 'cancelled', '2024-11-22 07:20:00'),
(36, 'REF890036', 77, 11, 3, 7500.00, 2500.00, '2025-03-10', '12:45:00', '2025-03-13', '12:00:00', 'completed', '2025-03-06 06:50:00'),
(37, 'REF890037', 73, 5, 1, 2500.00, 2500.00, '2024-10-01', '14:00:00', '2024-10-02', '12:00:00', 'completed', '2024-09-28 00:40:00'),
(38, 'REF890038', 75, 2, 2, 5000.00, 2500.00, '2024-12-15', '15:10:00', '2024-12-17', '10:45:00', 'cancelled', '2024-12-12 04:25:00'),
(39, 'REF890039', 76, 8, 1, 2500.00, 2500.00, '2025-01-10', '14:20:00', '2025-01-11', '11:00:00', 'completed', '2025-01-07 01:30:00'),
(40, 'REF890040', 77, 6, 3, 7500.00, 2500.00, '2024-09-15', '13:30:00', '2024-09-18', '10:15:00', 'completed', '2024-09-09 23:20:00'),
(41, 'REF890041', 73, 10, 2, 5000.00, 2500.00, '2025-02-22', '12:10:00', '2025-02-24', '12:00:00', 'cancelled', '2025-02-18 02:10:00'),
(42, 'REF890042', 75, 1, 1, 2500.00, 2500.00, '2024-08-08', '14:45:00', '2024-08-09', '11:30:00', 'completed', '2024-08-04 01:00:00'),
(43, 'REF890043', 76, 4, 3, 7500.00, 2500.00, '2024-10-20', '15:50:00', '2024-10-23', '12:00:00', 'cancelled', '2024-10-18 04:00:00'),
(44, 'REF890044', 77, 9, 1, 2500.00, 2500.00, '2025-03-05', '14:10:00', '2025-03-06', '10:45:00', 'completed', '2025-03-01 03:30:00'),
(45, 'REF890045', 73, 7, 2, 5000.00, 2500.00, '2024-09-25', '12:20:00', '2024-09-27', '12:00:00', 'completed', '2024-09-21 02:15:00'),
(46, 'REF890046', 75, 3, 3, 7500.00, 2500.00, '2025-01-15', '13:45:00', '2025-01-18', '12:00:00', 'cancelled', '2025-01-12 06:50:00'),
(47, 'REF890047', 76, 11, 1, 2500.00, 2500.00, '2024-07-30', '14:30:00', '2024-07-31', '11:15:00', 'completed', '2024-07-26 00:40:00'),
(48, 'REF890048', 77, 5, 2, 5000.00, 2500.00, '2025-02-05', '12:15:00', '2025-02-07', '11:30:00', 'completed', '2025-02-02 01:55:00'),
(50, 'REF890050', 75, 12, 1, 2500.00, 2500.00, '2024-11-05', '14:10:00', '2024-11-06', '11:00:00', 'completed', '2024-11-02 01:15:00'),
(51, 'REF890051', 73, 1, 1, 2500.00, 2500.00, '2024-01-10', '14:00:00', '2024-01-11', '11:00:00', 'completed', '2024-01-05 01:00:00'),
(52, 'REF890052', 75, 5, 2, 5000.00, 2500.00, '2024-02-15', '13:30:00', '2024-02-17', '12:00:00', 'cancelled', '2024-02-10 02:15:00'),
(53, 'REF890053', 76, 8, 3, 7500.00, 2500.00, '2024-03-20', '15:00:00', '2024-03-23', '11:30:00', 'completed', '2024-03-15 06:30:00'),
(54, 'REF890054', 77, 10, 1, 2500.00, 2500.00, '2024-04-05', '12:45:00', '2024-04-06', '11:00:00', 'completed', '2024-03-30 00:45:00'),
(55, 'REF890055', 73, 4, 2, 5000.00, 2500.00, '2024-05-12', '14:30:00', '2024-05-14', '10:45:00', 'cancelled', '2024-05-07 03:50:00'),
(56, 'REF890056', 75, 7, 3, 7500.00, 2500.00, '2024-06-25', '15:15:00', '2024-06-28', '11:30:00', 'completed', '2024-06-20 04:10:00'),
(57, 'REF890057', 76, 12, 1, 2500.00, 2500.00, '2024-07-18', '14:00:00', '2024-07-19', '11:00:00', 'cancelled', '2024-07-15 02:30:00'),
(58, 'REF890058', 77, 2, 2, 5000.00, 2500.00, '2024-08-08', '12:30:00', '2024-08-10', '11:45:00', 'completed', '2024-08-03 01:50:00'),
(59, 'REF890059', 73, 6, 3, 7500.00, 2500.00, '2024-09-14', '15:45:00', '2024-09-17', '10:30:00', 'cancelled', '2024-09-10 05:20:00'),
(60, 'REF890060', 75, 9, 1, 2500.00, 2500.00, '2024-10-22', '14:15:00', '2024-10-23', '11:00:00', 'completed', '2024-10-18 03:40:00'),
(61, 'REF890061', 76, 3, 2, 5000.00, 2500.00, '2024-11-09', '12:50:00', '2024-11-11', '12:00:00', 'completed', '2024-11-05 02:45:00'),
(62, 'REF890062', 77, 11, 3, 7500.00, 2500.00, '2024-12-27', '15:30:00', '2024-12-30', '10:15:00', 'cancelled', '2024-12-22 06:00:00'),
(63, 'REF890063', 73, 4, 1, 2500.00, 2500.00, '2024-01-28', '14:00:00', '2024-01-29', '11:30:00', 'completed', '2024-01-23 01:10:00'),
(64, 'REF890064', 75, 8, 2, 5000.00, 2500.00, '2024-02-10', '13:45:00', '2024-02-12', '11:00:00', 'completed', '2024-02-06 02:20:00'),
(65, 'REF890065', 76, 10, 3, 7500.00, 2500.00, '2024-03-05', '12:15:00', '2024-03-08', '10:45:00', 'cancelled', '2024-02-28 03:30:00'),
(66, 'REF890066', 77, 1, 1, 2500.00, 2500.00, '2024-04-18', '14:30:00', '2024-04-19', '11:15:00', 'completed', '2024-04-14 00:55:00'),
(67, 'REF890067', 73, 7, 2, 5000.00, 2500.00, '2024-05-21', '12:50:00', '2024-05-23', '11:30:00', 'completed', '2024-05-16 02:10:00'),
(68, 'REF890068', 75, 4, 3, 7500.00, 2500.00, '2024-06-09', '15:10:00', '2024-06-12', '12:00:00', 'cancelled', '2024-06-05 05:40:00'),
(69, 'REF890069', 76, 6, 1, 2500.00, 2500.00, '2024-07-30', '14:00:00', '2024-07-31', '11:00:00', 'completed', '2024-07-26 01:30:00'),
(70, 'REF890070', 77, 3, 2, 5000.00, 2500.00, '2024-08-12', '12:40:00', '2024-08-14', '10:30:00', 'completed', '2024-08-08 00:45:00'),
(71, 'REF890071', 73, 9, 3, 7500.00, 2500.00, '2024-09-17', '15:25:00', '2024-09-20', '11:00:00', 'cancelled', '2024-09-12 06:00:00'),
(72, 'REF890072', 75, 11, 1, 2500.00, 2500.00, '2024-10-07', '14:10:00', '2024-10-08', '12:00:00', 'completed', '2024-10-03 01:40:00'),
(73, 'REF890073', 76, 2, 2, 5000.00, 2500.00, '2024-11-25', '12:55:00', '2024-11-27', '11:15:00', 'cancelled', '2024-11-21 03:20:00'),
(74, 'REF890074', 77, 12, 3, 7500.00, 2500.00, '2024-12-10', '15:35:00', '2024-12-13', '12:00:00', 'completed', '2024-12-06 05:50:00'),
(75, 'REF890075', 73, 5, 1, 2500.00, 2500.00, '2024-06-14', '14:00:00', '2024-06-15', '11:30:00', 'completed', '2024-06-10 01:20:00'),
(76, 'REF890076', 75, 8, 2, 5000.00, 2500.00, '2024-07-22', '13:30:00', '2024-07-24', '11:15:00', 'completed', '2024-07-17 02:40:00'),
(77, 'REF890077', 76, 10, 3, 7500.00, 2500.00, '2024-08-05', '12:10:00', '2024-08-08', '10:30:00', 'cancelled', '2024-07-30 03:50:00'),
(78, 'REF890078', 77, 1, 1, 2500.00, 2500.00, '2024-09-09', '14:20:00', '2024-09-10', '11:00:00', 'completed', '2024-09-05 01:50:00'),
(79, 'BK14195846', 73, 4, 1, 1800.00, 1800.00, '2025-02-22', '12:00:00', '2025-02-23', '12:00:00', 'completed', '2025-02-22 08:49:55'),
(82, 'BK78054867', 76, 2, 3, 10500.00, 3500.00, '2025-03-08', '12:00:00', '2025-03-15', '15:00:00', 'completed', '2025-03-06 16:20:54'),
(88, 'REF123461', 76, 12, 2, 6000.00, 1200.00, '2025-03-21', '14:45:00', '2025-03-28', '11:00:00', 'completed', '2025-03-08 04:28:52'),
(95, 'REF123473', 76, 4, 1, 2800.00, 1400.00, '2025-01-10', '13:30:00', '2025-01-12', '10:30:00', 'completed', '2025-03-08 04:35:03'),
(146, 'REF-023', 76, 3, 3, 7200.00, 3600.00, '2024-05-05', '12:30:00', '2024-05-10', '10:00:00', 'cancelled', '2024-10-04 23:45:00'),
(147, 'REF-024', 75, 7, 4, 8800.00, 4400.00, '2024-06-10', '11:15:00', '2024-06-15', '09:45:00', 'completed', '2024-11-09 22:30:00'),
(148, 'REF-025', 73, 5, 2, 5400.00, 2700.00, '2024-07-18', '13:00:00', '2024-07-23', '10:30:00', 'completed', '2025-02-18 01:00:00'),
(177, 'ee028541-fc43-11ef-9', 76, 4, 4, 10000.00, 2500.00, '2024-03-14', '14:00:00', '2024-03-17', '12:00:00', 'completed', '2025-03-08 17:36:52'),
(178, 'ee0285a4-fc43-11ef-9', 76, 4, 3, 7500.00, 2500.00, '2024-04-20', '14:00:00', '2024-04-22', '12:00:00', 'completed', '2025-03-08 17:36:52'),
(179, 'ee02860d-fc43-11ef-9', 76, 5, 2, 5000.00, 2500.00, '2024-01-01', '14:00:00', '2024-01-03', '12:00:00', 'completed', '2025-03-08 17:36:52'),
(180, 'ee028668-fc43-11ef-9', 73, 6, 3, 7500.00, 2500.00, '2024-02-05', '14:00:00', '2024-02-07', '12:00:00', 'completed', '2025-03-08 17:36:52');

-- --------------------------------------------------------

--
-- Table structure for table `booking_status_logs`
--

CREATE TABLE `booking_status_logs` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `old_status` varchar(20) NOT NULL,
  `new_status` varchar(20) NOT NULL,
  `changed_by` int(11) NOT NULL,
  `changed_at` datetime NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tracks all booking status changes';

-- --------------------------------------------------------

--
-- Table structure for table `building_pricing`
--

CREATE TABLE `building_pricing` (
  `id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `occupancy` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `building_pricing`
--

INSERT INTO `building_pricing` (`id`, `price`, `occupancy`) VALUES
(2, 70000.00, '20'),
(3, 7000.00, '2000');

-- --------------------------------------------------------

--
-- Table structure for table `cancelled_alumni_applications`
--

CREATE TABLE `cancelled_alumni_applications` (
  `id` int(11) NOT NULL,
  `original_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `course` varchar(255) NOT NULL,
  `year_graduated` int(11) NOT NULL,
  `highschool_graduated` varchar(255) NOT NULL,
  `membership_type` varchar(50) NOT NULL,
  `status` varchar(20) DEFAULT 'cancelled',
  `cancellation_reason` text NOT NULL,
  `cancelled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `original_created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cancelled_alumni_applications`
--

INSERT INTO `cancelled_alumni_applications` (`id`, `original_id`, `user_id`, `last_name`, `first_name`, `middle_name`, `email`, `course`, `year_graduated`, `highschool_graduated`, `membership_type`, `status`, `cancellation_reason`, `cancelled_at`, `original_created_at`) VALUES
(16, 45, 75, 'Bungalso', 'Keith Joshua', 'D', 'bungalsokeith@gmail.com', 'qwqeqwe', 2000, '123', 'lifetime', 'cancelled', 'dawfwaf', '2025-02-20 11:40:37', '2025-02-20 11:36:43'),
(17, 46, 75, 'Bungalso', 'Keith Joshua', 'D', 'bungalsokeith@gmail.com', 'qwqeqwe', 2000, '123', '5_years', 'cancelled', 'wfgawfgwa', '2025-02-20 12:08:58', '2025-02-20 11:41:04'),
(18, 47, 75, 'Bungalso', 'Keith Joshua', 'D', 'bungalsokeith@gmail.com', 'qwqeqwe', 2000, '123', 'lifetime', 'cancelled', 'dadawd', '2025-02-21 12:53:08', '2025-02-20 12:15:14'),
(19, 48, 75, 'Bungalso', 'Keith Joshua', 'D', 'bungalsokeith@gmail.com', 'qwqeqwe', 2000, '123', 'lifetime', 'cancelled', 'dawdawfwafwaf', '2025-02-22 09:50:12', '2025-02-21 12:53:17'),
(20, 49, 75, 'Bungalso', 'Keith Joshua', 'D', 'bungalsokeith@gmail.com', 'qwqeqwe', 2000, '123', 'lifetime', 'cancelled', 'dwagawgwaawg', '2025-02-22 10:09:13', '2025-02-22 09:50:36'),
(21, 54, 75, 'Bungalso', 'Keith Joshua', 'D', 'bungalsokeith@gmail.com', 'qwqeqwe', 2000, '123', 'lifetime', 'cancelled', 'dwadwafwaf', '2025-02-22 10:31:37', '2025-02-22 10:29:00'),
(22, 55, 75, 'Bungalso', 'Keith Joshua', 'D', 'bungalsokeith@gmail.com', 'qwqeqwe', 2000, '123', '', 'cancelled', 'dawfawfaf', '2025-02-22 10:32:23', '2025-02-22 10:31:51'),
(23, 59, 75, 'Bungalso', 'Keith Joshua', 'D', 'bungalsokeith@gmail.com', 'qwqeqwe', 2000, '123', 'lifetime', 'cancelled', 'wafwafwaf', '2025-02-22 10:38:26', '2025-02-22 10:38:01'),
(24, 64, 75, 'Bungalso', 'Keith Joshua', 'D', 'bungalsokeith@gmail.com', 'qwqeqwe', 2000, '123', 'lifetime', 'cancelled', 'wadawdwad', '2025-02-23 11:53:43', '2025-02-23 11:47:12'),
(25, 65, 75, 'Bungalso', 'Keith Joshua', 'D', 'bungalsokeith@gmail.com', 'qwqeqwe', 2000, '123', 'lifetime', 'cancelled', 'dwadwad', '2025-02-23 11:57:47', '2025-02-23 11:53:51'),
(26, 69, 75, 'Bungalso', 'Keith Joshua', 'D', 'bungalsokeith@gmail.com', 'qwqeqwe', 2000, '123', 'lifetime', 'cancelled', 'egsgesg', '2025-02-23 12:32:33', '2025-02-23 12:23:18');

-- --------------------------------------------------------

--
-- Table structure for table `cancelled_bookings`
--

CREATE TABLE `cancelled_bookings` (
  `id` int(11) NOT NULL,
  `original_booking_id` int(11) DEFAULT NULL,
  `reference_number` varchar(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_number` int(11) DEFAULT NULL,
  `occupancy` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `price_per_day` decimal(10,2) DEFAULT NULL,
  `arrival_date` date DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `departure_date` date DEFAULT NULL,
  `departure_time` time DEFAULT NULL,
  `cancellation_reason` text NOT NULL,
  `cancelled_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cancelled_bookings`
--

INSERT INTO `cancelled_bookings` (`id`, `original_booking_id`, `reference_number`, `user_id`, `room_number`, `occupancy`, `price`, `price_per_day`, `arrival_date`, `arrival_time`, `departure_date`, `departure_time`, `cancellation_reason`, `cancelled_at`) VALUES
(72, 71, 'BK59952943', 73, 5, 2, 40000.00, 2500.00, '2025-02-04', '12:00:00', '2025-02-20', '12:00:00', 'fawfawf', '2025-02-05 17:52:46'),
(73, 72, 'BK05575414', 73, 1, 3, 49000.00, 3500.00, '2025-02-07', '12:00:00', '2025-02-21', '12:00:00', 'gawggwaga', '2025-02-07 13:21:20'),
(74, 74, 'BK82040699', 73, 1, 2, 2500.00, 2500.00, '2025-02-18', '09:00:00', '2025-02-19', '09:00:00', 'fwafwafwa', '2025-02-18 20:39:18');

-- --------------------------------------------------------

--
-- Table structure for table `competencies`
--

CREATE TABLE `competencies` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `personal_info_id` int(11) NOT NULL,
  `competency` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competencies`
--

INSERT INTO `competencies` (`id`, `user_id`, `personal_info_id`, `competency`) VALUES
(7, 73, 6, 'human_relations'),
(8, 74, 7, 'problem_solving'),
(9, 75, 8, 'problem_solving'),
(10, 76, 9, 'communication'),
(11, 76, 9, 'entrepreneurial');

-- --------------------------------------------------------

--
-- Table structure for table `conference_pricing`
--

CREATE TABLE `conference_pricing` (
  `id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `occupancy` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conference_pricing`
--

INSERT INTO `conference_pricing` (`id`, `price`, `occupancy`) VALUES
(18, 5000.00, '25');

-- --------------------------------------------------------

--
-- Table structure for table `device_history`
--

CREATE TABLE `device_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `device_type` varchar(50) NOT NULL,
  `operating_system` varchar(50) NOT NULL,
  `browser` varchar(50) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `last_active` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `educational_background`
--

CREATE TABLE `educational_background` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `personal_info_id` int(11) NOT NULL,
  `professional_exams` varchar(50) DEFAULT NULL,
  `highest_education` varchar(50) DEFAULT NULL,
  `reason_for_taking` varchar(50) DEFAULT NULL,
  `degree_specialization` varchar(255) NOT NULL,
  `college_university` varchar(255) NOT NULL,
  `year_graduated` int(11) NOT NULL,
  `honors_or_awards` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `educational_background`
--

INSERT INTO `educational_background` (`id`, `user_id`, `personal_info_id`, `professional_exams`, `highest_education`, `reason_for_taking`, `degree_specialization`, `college_university`, `year_graduated`, `honors_or_awards`) VALUES
(6, 73, 6, 'licensure', 'post_doctorate', 'good_grades_hs', 'BS Computer', 'Cavite State', 2000, '0'),
(7, 74, 7, 'licensure', 'doctorate', 'peer_influence', 'BS Com', 'Cavite City', 2000, '0'),
(8, 75, 8, 'licensure', 'bachelors', 'good_grades_hs', 'BS Computer', 'Cavite State', 2000, '0'),
(9, 76, 9, 'none', 'bachelors', 'high_grades_course', 'BS Computer Science', 'Cavite State University - Cavite City Campus', 2021, '0');

-- --------------------------------------------------------

--
-- Table structure for table `employment_data`
--

CREATE TABLE `employment_data` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `personal_info_id` int(11) NOT NULL,
  `employment_status` varchar(5) DEFAULT NULL,
  `present_employment_status` varchar(50) DEFAULT NULL,
  `self_employed_skills` text DEFAULT NULL,
  `present_occupation` varchar(100) DEFAULT NULL,
  `business_line` varchar(100) DEFAULT NULL,
  `work_place` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employment_data`
--

INSERT INTO `employment_data` (`id`, `user_id`, `personal_info_id`, `employment_status`, `present_employment_status`, `self_employed_skills`, `present_occupation`, `business_line`, `work_place`) VALUES
(6, 73, 6, 'yes', 'contractual', '', 'dwadwa', 'Public Administration and Defense', 'abroad'),
(7, 74, 7, 'yes', 'contractual', '', 'jsis', 'Electricity, Gas and Water Supply', 'work_from_home'),
(8, 75, 8, 'yes', 'temporary', '', 'dwadwa', 'Transport Storage and Communication', 'abroad'),
(9, 76, 9, 'no', 'regular', '', 'Software Engineer', 'Hotels and Restaurants', 'abroad');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `month` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `venue` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `day`, `month`, `title`, `venue`, `description`, `created_at`) VALUES
(1, 1, 'April', 'SIRA ULO', 'Cavite City', 'hala', '2025-01-13 09:11:16');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `room_rating` int(11) NOT NULL,
  `staff_rating` int(11) NOT NULL,
  `cleanliness_rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_email`, `room_rating`, `staff_rating`, `cleanliness_rating`, `comment`, `created_at`) VALUES
(15, 'keithjoshuabungalso123@gmail.com', 4, 5, 4, 'Cavite State University (CvSU) is required by law to process your personal information and sensitive personal information in order to safeguard academic freedom, uphold your right to quality education, and protect your right to data privacy in conformity with Republic Act No. 10173, otherwise known as the Data Privacy Act of 2012, and its implementing rules and regulations.', '2024-11-25 11:51:28'),
(16, 'bungalsokeith@gmail.com', 5, 5, 5, 'Cavite State University (CvSU) is required by law to process your personal information and sensitive personal information in order to safeguard academic freedom, uphold your right to quality education, and protect your right to data privacy in conformity with Republic Act No. 10173, otherwise known as the Data Privacy Act of 2012, and its implementing rules and regulations.', '2024-11-25 11:53:22'),
(17, 'keithjoshuabungalso123@gmail.com', 2, 4, 5, 'Booked a room at Ocean View Hotel on October 1, 2024.\r\nUpdated your profile information on September 28, 2024.\r\nMade a payment of $150 for your last booking on September 25, 2024.', '2024-11-25 12:07:27'),
(18, 'keithjoshuabungalso123@gmail.com', 5, 5, 5, 'Booked a room at Ocean View Hotel on October 1, 2024.\r\nUpdated your profile information on September 28, 2024.\r\nMade a payment of $150 for your last booking on September 25, 2024.', '2024-11-25 12:08:28'),
(19, 'bungalsokeith@gmail.com', 3, 3, 3, 'Cavite State University (CvSU) is required by law to process your personal information and sensitive personal information in order to safeguard academic freedom, uphold your right to quality education, and protect your right to data privacy in conformity with Republic Act No. 10173, otherwise known as the Data Privacy Act of 2012, and its implementing rules and regulations.', '2024-11-25 12:11:19'),
(20, 'keithjoshuabungalso123@gmail.com', 1, 1, 1, 'wew', '2024-11-25 12:12:44'),
(21, 'keithjoshuabungalso123@gmail.com', 1, 2, 3, '1', '2024-11-26 23:10:54');

-- --------------------------------------------------------

--
-- Table structure for table `feedback_replies`
--

CREATE TABLE `feedback_replies` (
  `id` int(11) NOT NULL,
  `feedback_id` int(11) DEFAULT NULL,
  `admin_name` varchar(255) DEFAULT NULL,
  `reply` text DEFAULT NULL,
  `is_admin_reply` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback_replies`
--

INSERT INTO `feedback_replies` (`id`, `feedback_id`, `admin_name`, `reply`, `is_admin_reply`, `created_at`) VALUES
(42, 15, 'adminbaito', 'Cavite State University (CvSU) is required by law to process your personal information and sensitive personal information in order to safeguard academic freedom, uphold your right to quality education, and protect your right to data privacy in conformity with Republic Act No. 10173, otherwise known as the Data Privacy Act of 2012, and its implementing rules and regulations.', 1, '2024-11-25 11:52:29'),
(43, 16, 'admin', 'Cavite State University (CvSU) is required by law to process your personal information and sensitive personal information in order to safeguard academic freedom, uphold your right to quality education, and protect your right to data privacy in conformity with Republic Act No. 10173, otherwise known as the Data Privacy Act of 2012, and its implementing rules and regulations.', 1, '2024-11-25 11:53:33'),
(44, 16, 'admin', 'epublic Act No. 10173, otherwise known as the Data Privacy Act of 2012, and its implementing rules and regulations.', 1, '2024-11-25 11:56:11'),
(45, 17, 'admin', 'Booked a room at Ocean View Hotel on October 1, 2024.\r\nUpdated your profile information on September 28, 2024.\r\nMade a payment of $150 for your last booking on September 25, 2024.', 1, '2024-11-25 12:07:43'),
(46, 18, 'admin', 'Booked a room at Ocean View Hotel on October 1, 2024.\r\nUpdated your profile information on September 28, 2024.\r\nMade a payment of $150 for your last booking on September 25, 2024.', 1, '2024-11-25 12:08:40'),
(47, 19, 'admin', 'Cavite State University (CvSU) is required by law to process your personal information and sensitive personal information in order to safeguard academic freedom, uphold your right to quality education, and protect your right to data privacy in conformity with Republic Act No. 10173, otherwise known as the Data Privacy Act of 2012, and its implementing rules and regulations.', 1, '2024-11-25 12:11:31'),
(48, 20, 'admin', 'wew', 1, '2024-11-25 12:12:55'),
(49, 21, 'keithjoshuabungalso123@gmail.com', 'wew', 0, '2024-11-26 23:12:57'),
(50, 21, 'keithjoshuabungalso123@gmail.com', 'was', 0, '2024-12-16 06:23:56');

-- --------------------------------------------------------

--
-- Table structure for table `it_support`
--

CREATE TABLE `it_support` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('it_support','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_duration`
--

CREATE TABLE `job_duration` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `personal_info_id` int(11) NOT NULL,
  `first_job_duration` varchar(50) DEFAULT NULL,
  `job_finding_method` varchar(50) DEFAULT NULL,
  `time_to_land` varchar(50) DEFAULT NULL,
  `job_level` varchar(50) DEFAULT NULL,
  `current_job` varchar(50) DEFAULT NULL,
  `initial_earning` varchar(50) DEFAULT NULL,
  `curriculum_relevant` varchar(5) DEFAULT NULL,
  `suggestions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_duration`
--

INSERT INTO `job_duration` (`id`, `user_id`, `personal_info_id`, `first_job_duration`, `job_finding_method`, `time_to_land`, `job_level`, `current_job`, `initial_earning`, `curriculum_relevant`, `suggestions`) VALUES
(6, 73, 6, '6months_1year', 'recommendation', '1_6months', 'mid', 'contractual', '21k_30k', 'no', 'WALA'),
(7, 74, 7, '1_2years', 'walk_in', '7_11months', 'senior', 'Self-employed', 'above_40k', 'no', ''),
(8, 75, 8, 'less_than_6months', 'advertisement', '1_6months', 'junior', 'project_based', '10k_20k', 'no', ''),
(9, 76, 9, '6months_1year', 'advertisement', '1_6months', 'entry', 'permanent', '31k_40k', 'yes', 'ASDASDASDASD');

-- --------------------------------------------------------

--
-- Table structure for table `job_experience`
--

CREATE TABLE `job_experience` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `personal_info_id` int(11) NOT NULL,
  `first_job` varchar(5) DEFAULT NULL,
  `course_related` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_experience`
--

INSERT INTO `job_experience` (`id`, `user_id`, `personal_info_id`, `first_job`, `course_related`) VALUES
(6, 73, 6, 'yes', 'yes'),
(7, 74, 7, 'no', 'no'),
(8, 75, 8, 'yes', 'yes'),
(9, 76, 9, 'yes', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `lobby_pricing`
--

CREATE TABLE `lobby_pricing` (
  `id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `occupancy` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lobby_pricing`
--

INSERT INTO `lobby_pricing` (`id`, `price`, `occupancy`) VALUES
(10, 5000.00, '25');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `category`, `title`, `description`, `date`, `image_path`, `created_at`, `updated_at`) VALUES
(27, 'Academic', 'wala', 'Class Notes: Updates from alumni about marriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\\\r\\\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\\\r\\\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university dawdhistory, or ways alumni can give back.\\\\r\\\\nWould you like tips on how to structure content for a specific alumni', '2025-03-02', '1740900541_67c408bda9bb8.jpg', '2025-03-02 07:29:01', '2025-03-02 07:29:01');

-- --------------------------------------------------------

--
-- Table structure for table `news_likes`
--

CREATE TABLE `news_likes` (
  `id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news_posts`
--

CREATE TABLE `news_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news_posts`
--

INSERT INTO `news_posts` (`id`, `title`, `description`, `author`, `date`) VALUES
(23, 'Articles in English - Learn What It Is, Definition, Types, Uses and Examples', 'Have you ever been wondering what part of speech the articles belong to? Do you think they are pronouns, adverbs or adjectives? Well, this article will help you with all that you need to know. Learn what articles are, their definition, types, how to use them, and uses, along with examples. Also, try out the practice questions given to check how far you have understood the same.<img src=\"../.cache/uploads/674671371b384.png\" alt=\"Uploaded Image\" style=\"max-width: 100%; display: block;\">\r\nHave you ever been wondering what part of speech the articles belong to? Do you think they are pronouns, adverbs or adjectives? Well, this article will help you with all that you need to know. Learn what articles are, their definition, types, how to use them, and uses, along with examples. Also, try out the practice questions given to check how far you have understood the same.', 'Keith Joshua', '2024-11-27 01:09:15');

-- --------------------------------------------------------

--
-- Table structure for table `other_alumni`
--

CREATE TABLE `other_alumni` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `personal_info_id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `other_alumni`
--

INSERT INTO `other_alumni` (`id`, `user_id`, `personal_info_id`, `name`, `address`, `contact_number`) VALUES
(6, 73, 6, 'Ney Anne Bungalso', '#140 Bliss Site Homer Homes', '09615334858'),
(7, 75, 8, 'Keith Joshua D Bungalso', 'WAS', '09156036419'),
(8, 76, 9, 'Sac Macross Abaño', '1112 Cabuco Street Caridad', '09915594654'),
(9, 76, 9, 'Sac Macross Abaño', '1112 Cabuco Street Caridad', '09915594654'),
(10, 76, 9, 'Sac Macross Abaño', '1112 Cabuco Street Caridad', '09915594654'),
(11, 76, 9, 'Sac Macross Abaño', '1112 Cabuco Street Caridad', '09915594654');

-- --------------------------------------------------------

--
-- Table structure for table `password_history`
--

CREATE TABLE `password_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `change_date` datetime NOT NULL,
  `action` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_history`
--

INSERT INTO `password_history` (`id`, `user_id`, `change_date`, `action`) VALUES
(26, 73, '2025-02-17 20:34:10', 'Password changed');

-- --------------------------------------------------------

--
-- Table structure for table `personal_info`
--

CREATE TABLE `personal_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `civil_status` varchar(20) DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `campus` varchar(100) DEFAULT NULL,
  `residence` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personal_info`
--

INSERT INTO `personal_info` (`id`, `user_id`, `civil_status`, `sex`, `birthday`, `course`, `campus`, `residence`, `created_at`) VALUES
(6, 73, 'widowed', 'male', '2025-02-04', 'BS Business Administration', 'Bacoor Campus', 'maragondon', '2025-02-04 10:02:15'),
(7, 74, 'married', 'female', '2025-02-04', 'BS Agricultural Engineering', 'Tanza Campus', 'trece_martires', '2025-02-04 12:36:50'),
(8, 75, 'married', 'female', '2025-02-05', 'BS Electrical Engineering', 'Gen. Mariano Alvarez Campus', 'mendez', '2025-02-17 13:13:52'),
(9, 76, 'single', 'male', '2003-01-25', 'BS Computer Science', 'Cavite City Campus', 'cavite_city', '2025-03-06 01:46:48');

-- --------------------------------------------------------

--
-- Table structure for table `recovery_emails`
--

CREATE TABLE `recovery_emails` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recovery_email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `issue_type` varchar(255) NOT NULL,
  `reference_number` varchar(255) NOT NULL,
  `report_details` varchar(255) NOT NULL,
  `tracking_number` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `issue_status` varchar(20) DEFAULT 'in progress'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `user_name`, `user_email`, `issue_type`, `reference_number`, `report_details`, `tracking_number`, `created_at`, `issue_status`) VALUES
(14, 'Keith Joshua Bungalso', 'keithjoshuabungalso123@gmail.com', 'Booking Issue', 'b824dfb6788b', 'weae', 'RPT-DFE3CDB6', '2024-11-18 08:21:56', 'Solved');

-- --------------------------------------------------------

--
-- Table structure for table `room_images`
--

CREATE TABLE `room_images` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_images`
--

INSERT INTO `room_images` (`id`, `room_id`, `image_path`, `created_at`) VALUES
(42, 1, '1737703798_safasfsaf.png', '2025-01-24 07:29:58'),
(43, 1, '1737703862_progress.png', '2025-01-24 07:31:02'),
(44, 1, '1737703869_trash-can.png', '2025-01-24 07:31:09'),
(45, 2, '1737718362_images.png', '2025-01-24 11:32:42'),
(46, 2, '1737718370_musical-note.png', '2025-01-24 11:32:50'),
(47, 2, '1737718377_plus.png', '2025-01-24 11:32:57'),
(48, 3, '1737718387_lock.png', '2025-01-24 11:33:07'),
(49, 3, '1737718395_like.png', '2025-01-24 11:33:15'),
(50, 3, '1737718404_speech-bubble.png', '2025-01-24 11:33:24'),
(51, 4, '1737718412_greater-than-symbol.png', '2025-01-24 11:33:32'),
(52, 4, '1737718421_home-button (1).png', '2025-01-24 11:33:41'),
(53, 5, '1737718429_trash-can.png', '2025-01-24 11:33:49'),
(54, 5, '1737718436_copy.png', '2025-01-24 11:33:56'),
(55, 6, '1737718445_user (4).png', '2025-01-24 11:34:05'),
(56, 7, '1737718452_plus.png', '2025-01-24 11:34:12'),
(57, 7, '1737718458_user (4).png', '2025-01-24 11:34:18'),
(58, 8, '1737718465_greater-than-symbol.png', '2025-01-24 11:34:25'),
(59, 8, '1737718474_speech-bubble.png', '2025-01-24 11:34:34'),
(60, 9, '1737718480_user (3).png', '2025-01-24 11:34:40'),
(61, 9, '1737718486_dadaf.png', '2025-01-24 11:34:46'),
(62, 10, '1737718493_Screenshot 2025-01-08 202606.png', '2025-01-24 11:34:53'),
(63, 10, '1737718499_aa1cc914-0eea-45a9-8438-26d6d64b01a4.jpg', '2025-01-24 11:34:59'),
(64, 11, '1737718506_copy.png', '2025-01-24 11:35:06'),
(65, 11, '1737718512_plus.png', '2025-01-24 11:35:12'),
(66, 6, '1737723548_Screenshot 2025-01-24 071936.png', '2025-01-24 12:59:08'),
(68, 12, '1739607978_CvSU-Front-09-2023-scaled.jpg', '2025-02-15 08:26:18');

-- --------------------------------------------------------

--
-- Table structure for table `room_price`
--

CREATE TABLE `room_price` (
  `id` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `occupancy` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_price`
--

INSERT INTO `room_price` (`id`, `price`, `occupancy`) VALUES
(3, 1800, 1),
(4, 2500, 2),
(5, 3500, 3),
(6, 7, 2000);

-- --------------------------------------------------------

--
-- Table structure for table `security_logs`
--

CREATE TABLE `security_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staying_reasons`
--

CREATE TABLE `staying_reasons` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `personal_info_id` int(11) NOT NULL,
  `reason` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staying_reasons`
--

INSERT INTO `staying_reasons` (`id`, `user_id`, `personal_info_id`, `reason`) VALUES
(4, 73, 6, 'career_growth'),
(5, 75, 8, 'career_growth'),
(6, 76, 9, 'salary'),
(7, 76, 9, 'work_life_balance');

-- --------------------------------------------------------

--
-- Table structure for table `support_chats`
--

CREATE TABLE `support_chats` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `status` enum('active','closed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_messages`
--

CREATE TABLE `support_messages` (
  `id` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `training_studies`
--

CREATE TABLE `training_studies` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `personal_info_id` int(11) NOT NULL,
  `training_title` varchar(200) DEFAULT NULL,
  `duration_credits` varchar(100) DEFAULT NULL,
  `institution` varchar(200) DEFAULT NULL,
  `advance_reason` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_studies`
--

INSERT INTO `training_studies` (`id`, `user_id`, `personal_info_id`, `training_title`, `duration_credits`, `institution`, `advance_reason`) VALUES
(6, 73, 6, 'dwdwad', '1 month', 'Ney Anne Bungalso', 'academic_interest'),
(7, 74, 7, 'na', '6 months', 'Cavite City', 'job_requirement'),
(8, 75, 8, 'dwdwad', '1 month', 'NEY-ANNE LARIOSA TADOY', 'personal_growth'),
(9, 76, 9, 'None', 'None', 'None', 'career_advancement');

-- --------------------------------------------------------

--
-- Table structure for table `unemployment_reasons`
--

CREATE TABLE `unemployment_reasons` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `personal_info_id` int(11) NOT NULL,
  `reason` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `unemployment_reasons`
--

INSERT INTO `unemployment_reasons` (`id`, `user_id`, `personal_info_id`, `reason`) VALUES
(3, 76, 9, 'advanced_study');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `alumni_id_card_no` varchar(50) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `second_address` text DEFAULT NULL,
  `accompanying_persons` text DEFAULT NULL,
  `user_status` varchar(50) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `user_id`, `alumni_id_card_no`, `first_name`, `last_name`, `middle_name`, `position`, `address`, `telephone`, `phone_number`, `second_address`, `accompanying_persons`, `user_status`, `verified`) VALUES
(100, 73, '001237', 'Keith Joshua', 'Bungalso', 'D', 'n/a', 'awdwa', '09156036419', '09156036419', 'dawdwa', 'awdawd', 'Alumni', 1),
(120, 75, '001231', 'Keith Joshua', 'Bungalso', 'D', 'n/a', '#140 Bliss Site Homer Homes', '09156036419', '09156036419', 'dawd', 'dwaad', 'Alumni', 1),
(121, 76, NULL, 'Sac Macross', 'Abaño', NULL, 'Software Engineer', '1112 Cabuco Street Caridad', '09915594654', '09915594654', NULL, 'Angelica Surio', 'Guest', 1),
(122, 77, NULL, 'Sac Macross', 'Abaño', NULL, 'AI Engineer', '1112 Cabuco Street Caridad', '09915594654', '09915594654', NULL, NULL, 'Guest', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `first_login` tinyint(1) DEFAULT 1,
  `session_token` varchar(255) DEFAULT NULL,
  `two_factor_auth` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `first_login`, `session_token`, `two_factor_auth`) VALUES
(73, 'ZeDelicious', 'keithjoshuabungalso123@gmail.com', '$2y$10$e4MwzYohq1TarW/F8gyzjOJiM6Bgy5HPTF4bJ3s6uIpswazqYZXyK', '2025-01-18 05:30:12', 0, '33ba2ff12123885818d01796f95020913e2d4ff8f85a9f2bc5af894041ee2bc4', 1),
(74, 'Yow', 'lyricflow123@gmail.com', '$2y$10$oqk9IYK9t.CSZqp1swaeuOE.yW1e.kxNms.3xw6cMCpz4XGwzSsd.', '2025-01-20 13:24:49', 0, NULL, 0),
(75, 'qwe', 'bungalsokeith@gmail.com', '$2y$10$SC//JVDOCxKS7ZT.JAsihe4CK.uiHcIgur0lvzf4mRZTZPVpVbd8K', '2025-02-01 03:45:55', 0, NULL, 0),
(76, 'Sac Macross', 'sacmacrossxxv@gmail.com', '$2y$10$aXW/w28aMWRiqK2LZP4g2eFpnVTHC7J7C1mAH8762xDgvK.R7qYUO', '2025-03-02 16:15:30', 0, NULL, 0),
(77, 'Macross', 'sac.macross1@gmail.com', '$2y$10$dPoaEBMLzr4Y.VWhp3Rw2OTtIHJQ2q9PW2ffpEvf8lSDSItpT3pCa', '2025-03-05 02:14:26', 0, '3211d53a22ad09b5c0e062be39ecc845746c55cb835d54f5fac7f5a7c9e30cea', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accepting_reasons`
--
ALTER TABLE `accepting_reasons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `personal_info_id` (`personal_info_id`),
  ADD KEY `idx_user_personal` (`user_id`,`personal_info_id`);

--
-- Indexes for table `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_activity_logs_admin` (`admin_id`),
  ADD KEY `idx_activity_logs_date` (`created_at`);

--
-- Indexes for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`);

--
-- Indexes for table `admin_roles`
--
ALTER TABLE `admin_roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `admin_role_permissions`
--
ALTER TABLE `admin_role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `idx_admin_sessions_token` (`session_token`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_admin_email` (`email`),
  ADD KEY `idx_admin_username` (`username`);

--
-- Indexes for table `alumni`
--
ALTER TABLE `alumni`
  ADD PRIMARY KEY (`alumni_id`);

--
-- Indexes for table `alumni_id_cards`
--
ALTER TABLE `alumni_id_cards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_table_record` (`table_name`,`record_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `backup_codes`
--
ALTER TABLE `backup_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `board_pricing`
--
ALTER TABLE `board_pricing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_number` (`reference_number`);

--
-- Indexes for table `booking_status_logs`
--
ALTER TABLE `booking_status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_booking_id` (`booking_id`),
  ADD KEY `idx_changed_by` (`changed_by`),
  ADD KEY `idx_changed_at` (`changed_at`);

--
-- Indexes for table `building_pricing`
--
ALTER TABLE `building_pricing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cancelled_alumni_applications`
--
ALTER TABLE `cancelled_alumni_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_original_id` (`original_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_cancelled_at` (`cancelled_at`);

--
-- Indexes for table `cancelled_bookings`
--
ALTER TABLE `cancelled_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `competencies`
--
ALTER TABLE `competencies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `personal_info_id` (`personal_info_id`),
  ADD KEY `idx_user_personal` (`user_id`,`personal_info_id`);

--
-- Indexes for table `conference_pricing`
--
ALTER TABLE `conference_pricing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `device_history`
--
ALTER TABLE `device_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `educational_background`
--
ALTER TABLE `educational_background`
  ADD PRIMARY KEY (`id`),
  ADD KEY `personal_info_id` (`personal_info_id`),
  ADD KEY `idx_user_personal` (`user_id`,`personal_info_id`);

--
-- Indexes for table `employment_data`
--
ALTER TABLE `employment_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `personal_info_id` (`personal_info_id`),
  ADD KEY `idx_user_personal` (`user_id`,`personal_info_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback_replies`
--
ALTER TABLE `feedback_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedback_id` (`feedback_id`);

--
-- Indexes for table `it_support`
--
ALTER TABLE `it_support`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_unique` (`email`);

--
-- Indexes for table `job_duration`
--
ALTER TABLE `job_duration`
  ADD PRIMARY KEY (`id`),
  ADD KEY `personal_info_id` (`personal_info_id`),
  ADD KEY `idx_user_personal` (`user_id`,`personal_info_id`);

--
-- Indexes for table `job_experience`
--
ALTER TABLE `job_experience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `personal_info_id` (`personal_info_id`),
  ADD KEY `idx_user_personal` (`user_id`,`personal_info_id`);

--
-- Indexes for table `lobby_pricing`
--
ALTER TABLE `lobby_pricing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news_likes`
--
ALTER TABLE `news_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`news_id`,`user_id`),
  ADD KEY `idx_news_likes_news_id` (`news_id`),
  ADD KEY `idx_news_likes_user_id` (`user_id`);

--
-- Indexes for table `news_posts`
--
ALTER TABLE `news_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `other_alumni`
--
ALTER TABLE `other_alumni`
  ADD PRIMARY KEY (`id`),
  ADD KEY `personal_info_id` (`personal_info_id`),
  ADD KEY `idx_user_personal` (`user_id`,`personal_info_id`);

--
-- Indexes for table `password_history`
--
ALTER TABLE `password_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `personal_info`
--
ALTER TABLE `personal_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `recovery_emails`
--
ALTER TABLE `recovery_emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_images`
--
ALTER TABLE `room_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_price`
--
ALTER TABLE `room_price`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `security_logs`
--
ALTER TABLE `security_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `staying_reasons`
--
ALTER TABLE `staying_reasons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `personal_info_id` (`personal_info_id`),
  ADD KEY `idx_user_personal` (`user_id`,`personal_info_id`);

--
-- Indexes for table `support_chats`
--
ALTER TABLE `support_chats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_chats` (`user_id`);

--
-- Indexes for table `support_messages`
--
ALTER TABLE `support_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `idx_chat_messages` (`chat_id`);

--
-- Indexes for table `training_studies`
--
ALTER TABLE `training_studies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `personal_info_id` (`personal_info_id`),
  ADD KEY `idx_user_personal` (`user_id`,`personal_info_id`);

--
-- Indexes for table `unemployment_reasons`
--
ALTER TABLE `unemployment_reasons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `personal_info_id` (`personal_info_id`),
  ADD KEY `idx_user_personal` (`user_id`,`personal_info_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accepting_reasons`
--
ALTER TABLE `accepting_reasons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admin_roles`
--
ALTER TABLE `admin_roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `alumni`
--
ALTER TABLE `alumni`
  MODIFY `alumni_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `alumni_id_cards`
--
ALTER TABLE `alumni_id_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `backup_codes`
--
ALTER TABLE `backup_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT for table `board_pricing`
--
ALTER TABLE `board_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=190;

--
-- AUTO_INCREMENT for table `booking_status_logs`
--
ALTER TABLE `booking_status_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `building_pricing`
--
ALTER TABLE `building_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cancelled_alumni_applications`
--
ALTER TABLE `cancelled_alumni_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `cancelled_bookings`
--
ALTER TABLE `cancelled_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `competencies`
--
ALTER TABLE `competencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `conference_pricing`
--
ALTER TABLE `conference_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `device_history`
--
ALTER TABLE `device_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `educational_background`
--
ALTER TABLE `educational_background`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `employment_data`
--
ALTER TABLE `employment_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `it_support`
--
ALTER TABLE `it_support`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_duration`
--
ALTER TABLE `job_duration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `job_experience`
--
ALTER TABLE `job_experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `lobby_pricing`
--
ALTER TABLE `lobby_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `news_likes`
--
ALTER TABLE `news_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `other_alumni`
--
ALTER TABLE `other_alumni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `password_history`
--
ALTER TABLE `password_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `personal_info`
--
ALTER TABLE `personal_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `recovery_emails`
--
ALTER TABLE `recovery_emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `room_images`
--
ALTER TABLE `room_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `room_price`
--
ALTER TABLE `room_price`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `security_logs`
--
ALTER TABLE `security_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staying_reasons`
--
ALTER TABLE `staying_reasons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `support_chats`
--
ALTER TABLE `support_chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `support_messages`
--
ALTER TABLE `support_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT for table `training_studies`
--
ALTER TABLE `training_studies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `unemployment_reasons`
--
ALTER TABLE `unemployment_reasons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accepting_reasons`
--
ALTER TABLE `accepting_reasons`
  ADD CONSTRAINT `accepting_reasons_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `accepting_reasons_ibfk_2` FOREIGN KEY (`personal_info_id`) REFERENCES `personal_info` (`id`);

--
-- Constraints for table `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  ADD CONSTRAINT `admin_activity_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL;

--
-- Constraints for table `admin_role_permissions`
--
ALTER TABLE `admin_role_permissions`
  ADD CONSTRAINT `admin_role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `admin_roles` (`role_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admin_role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `admin_permissions` (`permission_id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  ADD CONSTRAINT `admin_sessions_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE CASCADE;

--
-- Constraints for table `backup_codes`
--
ALTER TABLE `backup_codes`
  ADD CONSTRAINT `backup_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `booking_status_logs`
--
ALTER TABLE `booking_status_logs`
  ADD CONSTRAINT `booking_status_logs_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_status_logs_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `cancelled_alumni_applications`
--
ALTER TABLE `cancelled_alumni_applications`
  ADD CONSTRAINT `cancelled_alumni_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `competencies`
--
ALTER TABLE `competencies`
  ADD CONSTRAINT `competencies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `competencies_ibfk_2` FOREIGN KEY (`personal_info_id`) REFERENCES `personal_info` (`id`);

--
-- Constraints for table `device_history`
--
ALTER TABLE `device_history`
  ADD CONSTRAINT `device_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `educational_background`
--
ALTER TABLE `educational_background`
  ADD CONSTRAINT `educational_background_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `educational_background_ibfk_2` FOREIGN KEY (`personal_info_id`) REFERENCES `personal_info` (`id`);

--
-- Constraints for table `employment_data`
--
ALTER TABLE `employment_data`
  ADD CONSTRAINT `employment_data_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `employment_data_ibfk_2` FOREIGN KEY (`personal_info_id`) REFERENCES `personal_info` (`id`);

--
-- Constraints for table `job_duration`
--
ALTER TABLE `job_duration`
  ADD CONSTRAINT `job_duration_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `job_duration_ibfk_2` FOREIGN KEY (`personal_info_id`) REFERENCES `personal_info` (`id`);

--
-- Constraints for table `job_experience`
--
ALTER TABLE `job_experience`
  ADD CONSTRAINT `job_experience_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `job_experience_ibfk_2` FOREIGN KEY (`personal_info_id`) REFERENCES `personal_info` (`id`);

--
-- Constraints for table `news_likes`
--
ALTER TABLE `news_likes`
  ADD CONSTRAINT `news_likes_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `news_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `other_alumni`
--
ALTER TABLE `other_alumni`
  ADD CONSTRAINT `other_alumni_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `other_alumni_ibfk_2` FOREIGN KEY (`personal_info_id`) REFERENCES `personal_info` (`id`);

--
-- Constraints for table `password_history`
--
ALTER TABLE `password_history`
  ADD CONSTRAINT `password_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `personal_info`
--
ALTER TABLE `personal_info`
  ADD CONSTRAINT `personal_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `recovery_emails`
--
ALTER TABLE `recovery_emails`
  ADD CONSTRAINT `recovery_emails_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `security_logs`
--
ALTER TABLE `security_logs`
  ADD CONSTRAINT `security_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `staying_reasons`
--
ALTER TABLE `staying_reasons`
  ADD CONSTRAINT `staying_reasons_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `staying_reasons_ibfk_2` FOREIGN KEY (`personal_info_id`) REFERENCES `personal_info` (`id`);

--
-- Constraints for table `support_chats`
--
ALTER TABLE `support_chats`
  ADD CONSTRAINT `support_chats_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `support_messages`
--
ALTER TABLE `support_messages`
  ADD CONSTRAINT `support_messages_ibfk_1` FOREIGN KEY (`chat_id`) REFERENCES `support_chats` (`id`);

--
-- Constraints for table `training_studies`
--
ALTER TABLE `training_studies`
  ADD CONSTRAINT `training_studies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `training_studies_ibfk_2` FOREIGN KEY (`personal_info_id`) REFERENCES `personal_info` (`id`);

--
-- Constraints for table `unemployment_reasons`
--
ALTER TABLE `unemployment_reasons`
  ADD CONSTRAINT `unemployment_reasons_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `unemployment_reasons_ibfk_2` FOREIGN KEY (`personal_info_id`) REFERENCES `personal_info` (`id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
