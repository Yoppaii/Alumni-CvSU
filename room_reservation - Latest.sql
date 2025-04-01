-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 02, 2025 at 12:34 PM
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
(4, 'keith joshua.bungalso', 'Keith Joshua', 'Bungalso', 'keithjoshuabungalso123@gmail.com', '$2y$10$G7XzE6o4lsMaR.9t.xHLAuOUU9t5Kyp2vbWmTunUqftEMHeK/TOJq', 'admin', 1, '2025-02-02 11:32:30', '2025-01-09 12:21:29', '2025-02-02 11:32:30');

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
  `membership_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumni`
--

INSERT INTO `alumni` (`alumni_id`, `alumni_id_card_no`, `last_name`, `first_name`, `middle_name`, `membership_type`) VALUES
(0, '011111', 'Bungalso', 'Keith Joshua', 'D', 'Premium'),
(2, '001234', 'Bungalso', 'Keith Joshua', 'D', 'Renewable Membership'),
(3, '001235', 'Bungalso', 'Keith Joshua', 'Tadoy', 'Renewable Membership');

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
  `status` enum('pending','under_review','approved','ready_for_pickup') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumni_id_cards`
--

INSERT INTO `alumni_id_cards` (`id`, `user_id`, `last_name`, `first_name`, `middle_name`, `email`, `course`, `year_graduated`, `highschool_graduated`, `membership_type`, `status`, `created_at`) VALUES
(38, 63, 'Bungalso', 'Keith Joshua', 'D', 'keithjoshuabungalso123@gmail.com', 'qwqeqwe', 12312, '123', '5_years', 'pending', '2025-01-10 07:08:55');

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
(4, 'Event', 'SIRA ULO', 'Class Notes: Updates from alumni about marriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\r\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\r\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university history, or ways alumni can give back.\\r\\nWould you like tips on how to structure content for a specific alumni', '2025-01-12 12:39:57', 1),
(6, 'New', '\\\"RAMBULAN\\\" slated for November with barangay games theme', 'arriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\\\r\\\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\\\r\\\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university history, or ways alumni can give back.\\\\r\\\\nWould you like tips on how to structure content for a specific alumni', '2025-01-12 12:49:34', 1),
(7, 'Update', 'SIRA ULO', 'rriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\\\\\\\r\\\\\\\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\\\\\\\r\\\\\\\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university history, or ways alumni can give back.\\\\\\\\r\\\\\\\\nWould you like tips on how to structure content for a specific alumni\\r\\nrriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\\\\\\\r\\\\\\\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\\\\\\\r\\\\\\\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university history, or ways alumni can give back.\\\\\\\\r\\\\\\\\nWould you like tips on how to structure content for a specific alumni\\r\\n\\r\\nJanuary 13, 2025 4:49 AMrriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\\\\\\\r\\\\\\\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\\\\\\\r\\\\\\\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university history, or ways alumni can give back.\\\\\\\\r\\\\\\\\nWould you like tips on how to structure content for a specific alumni\\r\\n\\r\\nJanuary 13, 2025 4:49 AMrriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\\\\\\\r\\\\\\\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\\\\\\\r\\\\\\\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university history, or ways alumni can give back.\\\\\\\\r\\\\\\\\nWould you like tips on how to structure content for a specific alumni\\r\\n\\r\\nJanuary 13, 2025 4:49 AMrriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\\\\\\\r\\\\\\\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\\\\\\\r\\\\\\\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university history, or ways alumni can give back.\\\\\\\\r\\\\\\\\nWould you like tips on how to structure content for a specific alumni\\r\\n\\r\\nJanuary 13, 2025 4:49 AMrriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\\\\\\\r\\\\\\\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\\\\\\\r\\\\\\\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university history, or ways alumni can give back.\\\\\\\\r\\\\\\\\nWould you like tips on how to structure content for a specific alumni\\r\\n\\r\\nJanuary 13, 2025 4:49 AMrriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\\\\\\\r\\\\\\\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\\\\\\\r\\\\\\\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university history, or ways alumni can give back.\\\\\\\\r\\\\\\\\nWould you like tips on how to structure content for a specific alumni\\r\\n\\r\\nJanuary 13, 2025 4:49 AMrriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\\\\\\\r\\\\\\\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\\\\\\\r\\\\\\\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university history, or ways alumni can give back.\\\\\\\\r\\\\\\\\nWould you like tips on how to structure content for a specific alumni\\r\\n\\r\\nJanuary 13, 2025 4:49 AMrriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\\\\\\\r\\\\\\\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\\\\\\\r\\\\\\\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university history, or ways alumni can give back.\\\\\\\\r\\\\\\\\nWould you like tips on how to structure content for a specific alumni\\r\\n\\r\\nJanuary 13, 2025 4:49 AMrriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\\\\\\\r\\\\\\\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\\\\\\\r\\\\\\\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university history, or ways alumni can give back.\\\\\\\\r\\\\\\\\nWould you like tips on how to structure content for a specific alumni\\r\\n\\r\\nJanuary 13, 2025 4:49 AMrriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\\\\\\\r\\\\\\\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\\\\\\\r\\\\\\\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university history, or ways alumni can give back.\\\\\\\\r\\\\\\\\nWould you like tips on how to structure content for a specific alumni\\r\\n\\r\\nJanuary 13, 2025 4:49 AMrriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\\\\\\\r\\\\\\\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\\\\\\\r\\\\\\\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university history, or ways alumni can give back.\\\\\\\\r\\\\\\\\nWould you like tips on how to structure content for a specific alumni\\r\\n\\r\\nJanuary 13, 2025 4:49 AMrriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\\\\\\\r\\\\\\\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\\\\\\\r\\\\\\\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university history, or ways alumni can give back.\\\\\\\\r\\\\\\\\nWould you like tips on how to structure content for a specific alumni\\r\\n\\r\\nJanuary 13, 2025 4:49 AMrriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\\\\\\\r\\\\\\\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\\\\\\\r\\\\\\\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university history, or ways alumni can give back.\\\\\\\\r\\\\\\\\nWould you like tips on how to structure content for a specific alumni\\r\\n\\r\\nJanuary 13, 2025 4:49 AMrriages, births, career changes, or personal accomplishments that other alumni may find inspiring.\\\\\\\\r\\\\\\\\nUniversity Milestones: Celebrating anniversaries, major university-wide achievements, or the achievements of various departments or alumni associations.\\\\\\\\r\\\\\\\\nFeature Articles: Longer articles on issues important to the alumni community, such as global initiatives, university history, or ways alumni can give back.\\\\\\\\r\\\\\\\\nWould you like tips on how to structure content for a specific alumni\\r\\n\\r\\nJanuary 13, 2025 4:49 AM\\r\\n\\r\\nJanuary 13, 2025 4:49 AM', '2025-01-27 13:08:47', 1);

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
(4, 2500.00, '10');

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
(67, 'BK11018567', 74, 11, 25, 5000.00, 5000.00, '2025-01-31', '12:00:00', '2025-02-01', '12:00:00', 'cancelled', '2025-01-31 08:10:18'),
(68, 'BK99873828', 73, 1, 2, 67500.00, 2500.00, '2025-02-01', '12:00:00', '2025-02-28', '12:00:00', 'pending', '2025-02-01 08:51:14');

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
(69, 60, 'BK92087258', 73, 1, 3, 3500.00, 3500.00, '2025-01-31', '12:00:00', '2025-02-01', '12:00:00', 'gawwga', '2025-01-31 10:56:56'),
(70, 61, 'BK92283994', 73, 1, 3, 3500.00, 3500.00, '2025-01-31', '12:00:00', '2025-02-01', '12:00:00', 'fwafwaf', '2025-01-31 11:00:44'),
(71, 62, 'BK92462525', 73, 11, 25, 140000.00, 5000.00, '2025-01-31', '12:00:00', '2025-02-28', '12:00:00', 'gagagawg', '2025-01-31 11:42:58');

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

--
-- Dumping data for table `device_history`
--

INSERT INTO `device_history` (`id`, `user_id`, `device_type`, `operating_system`, `browser`, `ip_address`, `last_active`, `created_at`) VALUES
(37, 74, 'Mobile', 'Android', 'Chrome', '192.168.0.109', '2025-01-20 21:44:32', '2025-01-20 13:38:57'),
(38, 73, 'Desktop', 'Windows', 'Edge', '::1', '2025-02-01 22:33:57', '2025-01-21 05:09:23'),
(39, 74, 'Mobile', 'Android', 'Chrome', '192.168.0.108', '2025-02-01 16:06:15', '2025-02-01 08:06:15');

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
  `reason_for_taking` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 1, 'April', 'SIRA ULO', 'Cavite City', 'CvSU GymnasiumCvSU GymnasiumCvSU GymnasiumCvSU GymnasiumCvSU GymnasiumCvSU Gymnasium', '2025-01-13 09:11:16');

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
(67, 1, '1738383092_CvSU-Logo.png', '2025-02-01 04:11:32');

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
(5, 3500, 3);

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

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
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

INSERT INTO `user` (`id`, `user_id`, `first_name`, `last_name`, `middle_name`, `position`, `address`, `telephone`, `phone_number`, `second_address`, `accompanying_persons`, `user_status`, `verified`) VALUES
(61, 73, 'Keith Joshua', 'Bungalso', 'D', 'n/a', 'WAS', '09156036419', '09615334858', '', '', 'Alumni', 1),
(62, 74, 'keith Joshua', 'bungalso', 'Anne', 'Support', '#140 Bliss Site Homer Homes', '09615334858', '09615334858', '', '', 'Alumni', 1);

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
(73, 'ZeDelicious', 'keithjoshuabungalso123@gmail.com', '$2y$10$H4X9MMzL0DMeNkS753RXxO4GLVitv9IEahLq0gB5bJF4VRw4HyWDG', '2025-01-18 05:30:12', 0, '28b646dbd2139c6902de5c5c50108fa9cf1aaab2ad90e64b7f9e552d5f8d110e', 0),
(74, 'Yow', 'lyricflow123@gmail.com', '$2y$10$oqk9IYK9t.CSZqp1swaeuOE.yW1e.kxNms.3xw6cMCpz4XGwzSsd.', '2025-01-20 13:24:49', 0, '3c51ed23fb8c4096b276a2456cc58789cf0db49421d5f4908ea6bd2c3823f1d3', 0),
(75, 'qwe', 'bungalsokeith@gmail.com', '$2y$10$SC//JVDOCxKS7ZT.JAsihe4CK.uiHcIgur0lvzf4mRZTZPVpVbd8K', '2025-02-01 03:45:55', 0, '5f5904033a3a17dcd2ba1c34b0463e596a72c45f39c9f0dabcb66bac904c767a', 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT for table `alumni_id_cards`
--
ALTER TABLE `alumni_id_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `backup_codes`
--
ALTER TABLE `backup_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT for table `board_pricing`
--
ALTER TABLE `board_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `booking_status_logs`
--
ALTER TABLE `booking_status_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cancelled_alumni_applications`
--
ALTER TABLE `cancelled_alumni_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `cancelled_bookings`
--
ALTER TABLE `cancelled_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `competencies`
--
ALTER TABLE `competencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `conference_pricing`
--
ALTER TABLE `conference_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `device_history`
--
ALTER TABLE `device_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `educational_background`
--
ALTER TABLE `educational_background`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employment_data`
--
ALTER TABLE `employment_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `job_experience`
--
ALTER TABLE `job_experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lobby_pricing`
--
ALTER TABLE `lobby_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `other_alumni`
--
ALTER TABLE `other_alumni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `password_history`
--
ALTER TABLE `password_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `personal_info`
--
ALTER TABLE `personal_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `recovery_emails`
--
ALTER TABLE `recovery_emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `room_images`
--
ALTER TABLE `room_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `room_price`
--
ALTER TABLE `room_price`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `security_logs`
--
ALTER TABLE `security_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staying_reasons`
--
ALTER TABLE `staying_reasons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `support_chats`
--
ALTER TABLE `support_chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `support_messages`
--
ALTER TABLE `support_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT for table `training_studies`
--
ALTER TABLE `training_studies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `unemployment_reasons`
--
ALTER TABLE `unemployment_reasons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

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
