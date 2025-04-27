-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 25, 2025 at 02:14 PM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u552831403_iqra_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_logs`
--

CREATE TABLE `attendance_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` enum('teacher','student','admin','staff') NOT NULL,
  `punch_in_time` datetime DEFAULT NULL,
  `punch_out_time` datetime DEFAULT NULL,
  `punch_in_location_id` int(11) DEFAULT NULL,
  `punch_out_location_id` int(11) DEFAULT NULL,
  `punch_in_latitude` decimal(10,8) DEFAULT NULL,
  `punch_in_longitude` decimal(11,8) DEFAULT NULL,
  `punch_out_latitude` decimal(10,8) DEFAULT NULL,
  `punch_out_longitude` decimal(11,8) DEFAULT NULL,
  `punch_in_ip` varchar(45) DEFAULT NULL,
  `punch_out_ip` varchar(45) DEFAULT NULL,
  `total_hours` decimal(5,2) DEFAULT NULL,
  `status` enum('present','absent','leave','holiday','weekend','late','early_exit') DEFAULT 'present',
  `shift_start` time DEFAULT NULL,
  `shift_end` time DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_logs`
--

INSERT INTO `attendance_logs` (`id`, `user_id`, `user_type`, `punch_in_time`, `punch_out_time`, `punch_in_location_id`, `punch_out_location_id`, `punch_in_latitude`, `punch_in_longitude`, `punch_out_latitude`, `punch_out_longitude`, `punch_in_ip`, `punch_out_ip`, `total_hours`, `status`, `shift_start`, `shift_end`, `notes`, `created_at`, `updated_at`) VALUES
(8, 1, 'admin', '2025-04-24 19:11:47', '2025-04-24 19:18:06', 1, 1, 37.42209360, -122.08392200, 37.00000000, -122.08392200, '49.36.41.120', '49.36', 0.11, 'late', '09:00:00', '17:00:00', '0', '2025-04-24 19:11:47', '2025-04-24 19:18:06'),
(9, 1, 'admin', '2025-04-25 00:51:42', '2025-04-24 19:30:06', 1, 1, 37.42209360, -122.08392200, 37.00000000, -122.08392200, '49.36.41.120', '49.36', -5.36, 'early_exit', '09:00:00', '17:00:00', '0', '2025-04-24 19:21:42', '2025-04-24 19:30:06'),
(10, 1, 'admin', '2025-04-25 12:57:21', '2025-04-25 13:11:09', 1, 1, 18.71177760, 76.06885740, 18.72363520, 76.07418880, '2405:201:1059:9825:58fa:7611:75d7:a7f', '2405:201:1059:9825:44d:93db:96a2:fe7a', 0.23, 'early_exit', '09:00:00', '17:00:00', '', '2025-04-25 07:27:21', '2025-04-25 07:41:09'),
(11, 1, 'admin', '2025-04-25 16:19:41', '2025-04-25 17:05:08', 1, 1, 18.71177760, 76.06885720, 37.00000000, -122.08392200, '2405:201:1059:9825:58fa:7611:75d7:a7f', '49.36', 0.76, 'late', '09:00:00', '17:00:00', '0', '2025-04-25 10:49:41', '2025-04-25 11:35:08'),
(12, 2, 'admin', '2025-04-25 17:12:15', '2025-04-25 17:13:31', 1, 1, 18.71352920, 76.06518090, 18.00000000, 76.06518090, '2401:4900:7c71:19c1:a05e:bcff:fe81:6053', '2401', 0.02, 'late', '09:00:00', '17:00:00', '0', '2025-04-25 11:42:15', '2025-04-25 11:43:31'),
(13, 2, 'admin', '2025-04-25 19:15:22', NULL, 1, NULL, 18.71354270, 76.06514300, NULL, NULL, '2401:4900:7c6b:981b:9091:19ff:feae:9c16', NULL, NULL, 'late', '09:00:00', '17:00:00', NULL, '2025-04-25 13:45:22', '2025-04-25 13:45:22');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_settings`
--

CREATE TABLE `attendance_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_settings`
--

INSERT INTO `attendance_settings` (`id`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES
(1, 'work_shifts', '[{\"start\":\"09:00\",\"end\":\"17:00\",\"min_hours\":8}]', 'Multiple work shifts configuration', '2025-04-25 13:51:04', '2025-04-25 13:51:04'),
(2, 'late_threshold_minutes', '15', 'Attendance setting', '2025-04-25 13:51:04', '2025-04-25 13:51:04'),
(3, 'early_exit_threshold_minutes', '15', 'Attendance setting', '2025-04-25 13:51:04', '2025-04-25 13:51:04'),
(4, 'weekend_days', '0,6', 'Attendance setting', '2025-04-25 13:51:04', '2025-04-25 13:51:04'),
(5, 'geofencing_enabled', '1', 'Attendance setting', '2025-04-25 13:51:04', '2025-04-25 13:51:04'),
(6, 'auto_punch_out', '1', 'Attendance setting', '2025-04-25 13:51:04', '2025-04-25 13:51:04'),
(7, 'multiple_shifts_enabled', '1', 'Enable multiple punch in/out per day', '2025-04-25 13:51:04', '2025-04-25 13:51:04'),
(8, 'warn_incomplete_hours', '1', 'Warn users when punching out with incomplete hours', '2025-04-25 13:51:04', '2025-04-25 13:51:04');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_summary`
--

CREATE TABLE `attendance_summary` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` enum('teacher','student','admin','staff') NOT NULL,
  `summary_date` date NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `status` enum('present','absent','leave','holiday','weekend','late','early_exit') DEFAULT 'present',
  `work_hours` decimal(5,2) DEFAULT 0.00,
  `is_late` tinyint(1) DEFAULT 0,
  `is_early_exit` tinyint(1) DEFAULT 0,
  `leave_type_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_summary`
--

INSERT INTO `attendance_summary` (`id`, `user_id`, `user_type`, `summary_date`, `month`, `year`, `status`, `work_hours`, `is_late`, `is_early_exit`, `leave_type_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin', '2025-04-24', 4, 2025, 'early_exit', 0.11, 1, 1, NULL, '2025-04-24 19:11:47', '2025-04-24 19:30:06'),
(2, 1, 'admin', '2025-04-25', 4, 2025, 'late', -4.37, 0, 0, NULL, '2025-04-24 19:21:42', '2025-04-25 11:35:08'),
(3, 2, 'admin', '2025-04-25', 4, 2025, 'late', 0.02, 1, 0, NULL, '2025-04-25 11:42:15', '2025-04-25 11:43:31');

-- --------------------------------------------------------

--
-- Table structure for table `cheque_details`
--

CREATE TABLE `cheque_details` (
  `id` int(11) NOT NULL,
  `cheque_given_date` date NOT NULL,
  `cheque_year` int(11) NOT NULL,
  `cheque_month` int(11) NOT NULL,
  `cheque_number` varchar(100) NOT NULL,
  `cheque_amount` decimal(10,2) NOT NULL,
  `cheque_photo` varchar(255) NOT NULL,
  `cheque_handover_teacher` int(11) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_on` timestamp NULL DEFAULT NULL,
  `is_cleared` tinyint(1) DEFAULT 0,
  `is_bounced` tinyint(1) DEFAULT 0,
  `bank_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `daily_salary_calculations`
--

CREATE TABLE `daily_salary_calculations` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL COMMENT 'Teacher ID from users table',
  `calculation_date` date NOT NULL COMMENT 'Date of salary calculation',
  `base_amount` decimal(10,2) NOT NULL COMMENT 'Base salary amount for the day',
  `deduction_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Deduction amount for the day',
  `final_amount` decimal(10,2) NOT NULL COMMENT 'Final salary amount after deductions',
  `working_hours` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Actual hours worked',
  `required_hours` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Required minimum hours',
  `minutes_short` int(11) DEFAULT 0 COMMENT 'Minutes short of required hours',
  `notes` text DEFAULT NULL COMMENT 'Additional notes or explanation',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `month` int(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `Year` varchar(50) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_on` timestamp(6) NULL DEFAULT NULL,
  `status` enum('pending','paid','rejected') DEFAULT 'pending',
  `rejected_by` int(11) DEFAULT NULL,
  `rejected_on` timestamp NULL DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`id`, `student_id`, `amount`, `month`, `created_at`, `created_by`, `Year`, `approved_by`, `approved_on`, `status`, `rejected_by`, `rejected_on`, `reason`) VALUES
(1, 1, 100.00, 4, '2025-04-24 16:21:45', 2, '2025', 2, '2025-04-24 16:24:44.000000', 'paid', NULL, NULL, 'test '),
(2, 1, 100.00, 4, '2025-04-24 16:31:59', 2, '2025', 2, '2025-04-24 16:32:28.000000', 'paid', NULL, NULL, ''),
(3, 1, 200.00, 4, '2025-04-24 16:35:13', 2, '2025', NULL, NULL, 'rejected', 1, '2025-04-24 18:27:56', 'test'),
(4, 2, 200.00, 4, '2025-04-24 16:36:43', 1, '2025', 1, '2025-04-24 22:06:59.000000', 'paid', NULL, NULL, NULL),
(5, 2, 200.00, 4, '2025-04-24 16:37:28', 1, '2025', 2, '2025-04-24 16:37:45.000000', 'paid', NULL, NULL, NULL),
(6, 1, 150.00, 4, '2025-04-24 17:38:07', 2, '2025', NULL, NULL, 'rejected', 1, '2025-04-24 18:27:42', 'test'),
(7, 2, 50.00, 4, '2025-04-24 18:12:54', 1, '2025', NULL, NULL, 'rejected', 1, '2025-04-24 18:27:26', 'test'),
(8, 1, 120.00, 4, '2025-04-24 18:16:37', 1, '2025', NULL, NULL, 'rejected', 1, '2025-04-24 18:27:34', 'testq'),
(9, 1, 123.00, 4, '2025-04-24 18:26:40', 1, '2025', NULL, NULL, 'rejected', 1, '2025-04-24 18:27:49', 'test'),
(10, 2, 111.00, 4, '2025-04-24 18:33:20', 1, '2025', 1, '2025-04-24 18:33:49.000000', 'paid', NULL, NULL, ''),
(11, 1, 123.00, 4, '2025-04-24 18:35:35', 1, '2025', 1, '2025-04-24 18:37:03.000000', 'paid', NULL, NULL, ''),
(12, 2, 142.00, 4, '2025-04-24 18:50:15', 1, '2025', 1, '2025-04-24 18:51:12.000000', 'paid', NULL, NULL, ''),
(13, 1, 500.00, 4, '2025-04-25 12:28:45', 2, '2025', 2, '2025-04-25 17:59:25.000000', 'paid', NULL, NULL, ''),
(14, 1, 77.00, 4, '2025-04-25 13:50:27', 2, '2025', 2, '2025-04-25 19:22:18.000000', 'paid', NULL, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `id` int(11) NOT NULL,
  `holiday_name` varchar(100) NOT NULL,
  `holiday_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `is_full_day` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` enum('teacher','student','admin','staff') NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`id`, `type_name`, `description`, `is_paid`, `created_at`, `updated_at`) VALUES
(1, 'Sick Leave', 'Leave taken due to illness or medical appointments', 1, '2025-04-05 21:13:59', '2025-04-05 21:13:59'),
(2, 'Paid Leave', 'Leave taken for personal reasons with payment', 1, '2025-04-05 21:13:59', '2025-04-05 21:13:59'),
(3, 'Vacation', 'Annual vacation leave', 1, '2025-04-05 21:13:59', '2025-04-05 21:13:59'),
(4, 'Jamat Leave', 'Leave without pay', 0, '2025-04-05 21:13:59', '2025-04-05 21:13:59'),
(5, 'Paternity Leave', 'Leave for male employees after childbirth', 1, '2025-04-05 21:13:59', '2025-04-05 21:13:59'),
(6, 'Emergency Leave', 'Leave for family emergencies', 1, '2025-04-05 21:13:59', '2025-04-05 21:13:59');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--

CREATE TABLE `maintenance` (
  `id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `comment` varchar(250) DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_by` int(50) DEFAULT NULL,
  `deleted_on` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meeting_details`
--

CREATE TABLE `meeting_details` (
  `id` int(11) NOT NULL,
  `meeting_date` date NOT NULL,
  `student_responsibility` int(11) DEFAULT NULL,
  `namaz_responsibility` int(11) DEFAULT NULL,
  `daily_visit` enum('Fajar','Asar','Magrib') DEFAULT NULL,
  `fees_collection` varchar(255) DEFAULT NULL,
  `maktab_lock` int(11) DEFAULT NULL,
  `cleanliness_ethics` int(11) DEFAULT NULL,
  `food_responsibility` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `visit_fajar` int(11) NOT NULL,
  `visit_asar` int(11) NOT NULL,
  `visit_magrib` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meeting_fees_collection`
--

CREATE TABLE `meeting_fees_collection` (
  `id` int(11) NOT NULL,
  `meeting_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `office_locations`
--

CREATE TABLE `office_locations` (
  `id` int(11) NOT NULL,
  `location_name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `radius_meters` int(11) NOT NULL DEFAULT 100,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `office_locations`
--

INSERT INTO `office_locations` (`id`, `location_name`, `address`, `latitude`, `longitude`, `radius_meters`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Iqra', 'Roza Mohalla Moulana Azad Chowk.', 18.72363520, 76.07418880, 50, 1, '2025-04-24 19:09:12', '2025-04-24 19:09:12');

-- --------------------------------------------------------

--
-- Table structure for table `salary_calculation_logs`
--

CREATE TABLE `salary_calculation_logs` (
  `id` int(11) NOT NULL,
  `salary_id` int(11) NOT NULL,
  `log_type` enum('calculation','adjustment','payment','notification') NOT NULL,
  `log_details` text NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_deduction_rules`
--

CREATE TABLE `salary_deduction_rules` (
  `id` int(11) NOT NULL,
  `rule_name` varchar(100) NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `hours_threshold` decimal(5,2) DEFAULT NULL,
  `deduction_type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `fixed_amount` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_notifications`
--

CREATE TABLE `salary_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `salary_id` int(11) NOT NULL,
  `notification_title` varchar(100) NOT NULL,
  `notification_text` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_periods`
--

CREATE TABLE `salary_periods` (
  `id` int(11) NOT NULL,
  `period_name` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_processed` tinyint(1) NOT NULL DEFAULT 0,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_settings`
--

CREATE TABLE `salary_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_log`
--

CREATE TABLE `sms_log` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `class` varchar(50) DEFAULT NULL,
  `annual_fees` int(50) NOT NULL DEFAULT 2000,
  `phone` varchar(50) DEFAULT NULL,
  `student_address` varchar(255) DEFAULT NULL,
  `class_time` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `assigned_teacher` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `photo`, `class`, `annual_fees`, `phone`, `student_address`, `class_time`, `remarks`, `assigned_teacher`, `is_deleted`) VALUES
(1, 'Abed Farooqui', 'assets/images/1745510658_wagesh sir 2.jpg', '1', 1800, '9850041083', NULL, NULL, NULL, 3, 0),
(2, 'Zaheer Farooqui', 'assets/images/1745512145_aesthetic wallpaper.jpg', '2', 2000, '7218932313', NULL, NULL, NULL, 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `student_status_history`
--

CREATE TABLE `student_status_history` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(6) NOT NULL,
  `assigned_teacher` int(11) DEFAULT NULL,
  `salana_fees` decimal(10,2) DEFAULT NULL,
  `status` enum('active','inactive','transferred','graduated') NOT NULL DEFAULT 'active',
  `current_active_record` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_status_history`
--

INSERT INTO `student_status_history` (`id`, `student_id`, `year`, `month`, `assigned_teacher`, `salana_fees`, `status`, `current_active_record`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 1, 2025, 4, 3, 1800.00, 'active', 1, '2025-04-24 16:04:18', '2025-04-25 11:29:18', 2, 1),
(2, 2, 2025, 4, 3, 2000.00, 'active', 0, '2025-04-24 16:29:05', '2025-04-24 16:29:05', 1, NULL),
(3, 1, 2025, 4, 3, 1800.00, 'active', 0, '2025-04-25 11:29:18', '2025-04-25 11:29:18', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_class_assignments`
--

CREATE TABLE `teacher_class_assignments` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL COMMENT 'Teacher ID from users table',
  `class_name` varchar(100) NOT NULL COMMENT 'Name of the class',
  `subject` varchar(100) NOT NULL COMMENT 'Subject taught',
  `class_hours` decimal(5,2) NOT NULL DEFAULT 1.00 COMMENT 'Hours allocated for this class per day',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether this assignment is active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_salary_calculations`
--

CREATE TABLE `teacher_salary_calculations` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `period_id` int(11) NOT NULL,
  `base_salary` decimal(10,2) NOT NULL,
  `deductions` decimal(10,2) DEFAULT 0.00,
  `bonuses` decimal(10,2) DEFAULT 0.00,
  `total_working_hours` decimal(6,2) NOT NULL DEFAULT 0.00,
  `expected_working_hours` decimal(6,2) NOT NULL DEFAULT 0.00,
  `deduction_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bonus_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `final_salary` decimal(10,2) NOT NULL,
  `status` enum('draft','finalized','paid','processed') NOT NULL DEFAULT 'draft',
  `payment_date` date DEFAULT NULL,
  `payment_method` varchar(30) DEFAULT NULL,
  `reference_number` varchar(50) DEFAULT NULL,
  `payment_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `hourly_rate` varchar(50) DEFAULT NULL,
  `total_hours` decimal(10,2) DEFAULT 0.00,
  `expected_hours` decimal(10,2) DEFAULT 0.00,
  `notes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_salary_rates`
--

CREATE TABLE `teacher_salary_rates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `effective_date` date DEFAULT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `minimum_working_hours` decimal(5,2) DEFAULT 3.00 COMMENT 'Minimum working hours per day for the teacher'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `token` varchar(100) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher') NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `phone` varchar(30) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `token`, `token_expiry`, `last_login`, `name`, `email`, `password`, `role`, `is_deleted`, `phone`, `is_active`, `created_by`, `created_at`, `updated_by`, `updated_at`) VALUES
(1, 'c32f1c1d894867dcf588cd3fbdbc59e6432b2fced281dadddd0c7200223e8cc4', '2025-04-26 17:03:31', NULL, 'Shoaib Farooqui', '9595778797@gmail.com', '$2y$10$BCtAQclYS2dVc2sBg8w7w.WpFWaDDsWwgtTVdLNpbwHZSnDUq9ebO', 'admin', 0, '9595778797', 1, NULL, NULL, NULL, NULL),
(2, 'b0288f8e65eaf25f3e48c66be4ee155881f0d7378d66254bec4397e0f8b022f7', '2025-04-26 17:10:30', NULL, 'Rizwan Khureshi', '9960555762@gmail.com', '$2y$10$rnByJzKHf3Jr647QUVHhEOqRLsR/aIU/YlTbC5CNR2TAGph3RV3fO', 'admin', 0, '9960555762', 1, 1, '2025-04-24 15:58:49', NULL, NULL),
(3, 'a089f061abb7edfc830d15e16a3ca91104278e2506d47aa2d21a344403e586fc', '2025-04-25 16:06:41', NULL, 'Hafez Faruk', '9923242833@gmail.com', '$2y$10$.dcTF9ZoFtArLF8JaCtEmO8Nc3t7xp8BPQKdbRsvfJHZfyYVS3PRq', 'teacher', 0, '9923242833', 1, 13, '2025-04-24 16:00:25', 2, '2025-04-24 16:01:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `punch_in_location_id` (`punch_in_location_id`),
  ADD KEY `punch_out_location_id` (`punch_out_location_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_user_type` (`user_type`),
  ADD KEY `idx_attendance_date` (`punch_in_time`);

--
-- Indexes for table `attendance_settings`
--
ALTER TABLE `attendance_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_setting_key` (`setting_key`);

--
-- Indexes for table `attendance_summary`
--
ALTER TABLE `attendance_summary`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_date` (`user_id`,`user_type`,`summary_date`),
  ADD KEY `leave_type_id` (`leave_type_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_summary_date` (`summary_date`),
  ADD KEY `idx_month_year` (`month`,`year`);

--
-- Indexes for table `cheque_details`
--
ALTER TABLE `cheque_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `daily_salary_calculations`
--
ALTER TABLE `daily_salary_calculations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_teacher_date` (`teacher_id`,`calculation_date`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_holiday_date` (`holiday_date`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_type_id` (`leave_type_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_leave_dates` (`start_date`,`end_date`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_type_name` (`type_name`);

--
-- Indexes for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meeting_details`
--
ALTER TABLE `meeting_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_responsibility` (`student_responsibility`),
  ADD KEY `namaz_responsibility` (`namaz_responsibility`),
  ADD KEY `maktab_lock` (`maktab_lock`),
  ADD KEY `cleanliness_ethics` (`cleanliness_ethics`),
  ADD KEY `food_responsibility` (`food_responsibility`),
  ADD KEY `visit_fajar` (`visit_fajar`),
  ADD KEY `visit_asar` (`visit_asar`),
  ADD KEY `visit_magrib` (`visit_magrib`);

--
-- Indexes for table `meeting_fees_collection`
--
ALTER TABLE `meeting_fees_collection`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meeting_id` (`meeting_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `type_reference_id` (`type`,`reference_id`),
  ADD KEY `is_read` (`is_read`);

--
-- Indexes for table `office_locations`
--
ALTER TABLE `office_locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `salary_calculation_logs`
--
ALTER TABLE `salary_calculation_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_salary_log_calc` (`salary_id`);

--
-- Indexes for table `salary_deduction_rules`
--
ALTER TABLE `salary_deduction_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `salary_notifications`
--
ALTER TABLE `salary_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_salary_notif_user` (`user_id`),
  ADD KEY `fk_salary_notif_calc` (`salary_id`);

--
-- Indexes for table `salary_periods`
--
ALTER TABLE `salary_periods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_period_dates` (`start_date`,`end_date`);

--
-- Indexes for table `salary_settings`
--
ALTER TABLE `salary_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `sms_log`
--
ALTER TABLE `sms_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_teacher` (`assigned_teacher`);

--
-- Indexes for table `student_status_history`
--
ALTER TABLE `student_status_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teacher_class_assignments`
--
ALTER TABLE `teacher_class_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `teacher_salary_calculations`
--
ALTER TABLE `teacher_salary_calculations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_period` (`user_id`,`period_id`),
  ADD KEY `fk_salary_calc_period` (`period_id`);

--
-- Indexes for table `teacher_salary_rates`
--
ALTER TABLE `teacher_salary_rates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_salary_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `attendance_settings`
--
ALTER TABLE `attendance_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `attendance_summary`
--
ALTER TABLE `attendance_summary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cheque_details`
--
ALTER TABLE `cheque_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `daily_salary_calculations`
--
ALTER TABLE `daily_salary_calculations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `maintenance`
--
ALTER TABLE `maintenance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meeting_details`
--
ALTER TABLE `meeting_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meeting_fees_collection`
--
ALTER TABLE `meeting_fees_collection`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `office_locations`
--
ALTER TABLE `office_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `salary_calculation_logs`
--
ALTER TABLE `salary_calculation_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary_deduction_rules`
--
ALTER TABLE `salary_deduction_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary_notifications`
--
ALTER TABLE `salary_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary_periods`
--
ALTER TABLE `salary_periods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary_settings`
--
ALTER TABLE `salary_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sms_log`
--
ALTER TABLE `sms_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_status_history`
--
ALTER TABLE `student_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `teacher_class_assignments`
--
ALTER TABLE `teacher_class_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teacher_salary_calculations`
--
ALTER TABLE `teacher_salary_calculations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teacher_salary_rates`
--
ALTER TABLE `teacher_salary_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD CONSTRAINT `attendance_logs_ibfk_1` FOREIGN KEY (`punch_in_location_id`) REFERENCES `office_locations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendance_logs_ibfk_2` FOREIGN KEY (`punch_out_location_id`) REFERENCES `office_locations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `attendance_summary`
--
ALTER TABLE `attendance_summary`
  ADD CONSTRAINT `attendance_summary_ibfk_1` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `daily_salary_calculations`
--
ALTER TABLE `daily_salary_calculations`
  ADD CONSTRAINT `daily_salary_calculations_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fees`
--
ALTER TABLE `fees`
  ADD CONSTRAINT `fees_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meeting_details`
--
ALTER TABLE `meeting_details`
  ADD CONSTRAINT `meeting_details_ibfk_1` FOREIGN KEY (`student_responsibility`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `meeting_details_ibfk_2` FOREIGN KEY (`namaz_responsibility`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `meeting_details_ibfk_3` FOREIGN KEY (`maktab_lock`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `meeting_details_ibfk_4` FOREIGN KEY (`cleanliness_ethics`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `meeting_details_ibfk_5` FOREIGN KEY (`food_responsibility`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `meeting_details_ibfk_6` FOREIGN KEY (`visit_fajar`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `meeting_details_ibfk_7` FOREIGN KEY (`visit_asar`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `meeting_details_ibfk_8` FOREIGN KEY (`visit_magrib`) REFERENCES `users` (`id`);

--
-- Constraints for table `meeting_fees_collection`
--
ALTER TABLE `meeting_fees_collection`
  ADD CONSTRAINT `meeting_fees_collection_ibfk_1` FOREIGN KEY (`meeting_id`) REFERENCES `meeting_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meeting_fees_collection_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `salary_calculation_logs`
--
ALTER TABLE `salary_calculation_logs`
  ADD CONSTRAINT `fk_salary_log_calc` FOREIGN KEY (`salary_id`) REFERENCES `teacher_salary_calculations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `salary_notifications`
--
ALTER TABLE `salary_notifications`
  ADD CONSTRAINT `fk_salary_notif_calc` FOREIGN KEY (`salary_id`) REFERENCES `teacher_salary_calculations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_salary_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sms_log`
--
ALTER TABLE `sms_log`
  ADD CONSTRAINT `sms_log_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`assigned_teacher`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `teacher_class_assignments`
--
ALTER TABLE `teacher_class_assignments`
  ADD CONSTRAINT `teacher_class_assignments_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_salary_calculations`
--
ALTER TABLE `teacher_salary_calculations`
  ADD CONSTRAINT `fk_salary_calc_period` FOREIGN KEY (`period_id`) REFERENCES `salary_periods` (`id`),
  ADD CONSTRAINT `fk_salary_calc_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `teacher_salary_rates`
--
ALTER TABLE `teacher_salary_rates`
  ADD CONSTRAINT `fk_salary_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
