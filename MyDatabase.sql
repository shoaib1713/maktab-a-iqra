-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2025 at 12:45 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `maktab_a_ekra_new`
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
(1, 2, 'admin', '2025-04-06 00:09:01', '2025-04-06 00:11:38', 1, 1, '18.00000000', '76.07418880', '18.72363520', '76.07418880', '::1', '::1', '0.04', 'weekend', NULL, NULL, '', '2025-04-05 22:09:01', '2025-04-05 22:11:38'),
(2, 2, 'admin', '2025-04-06 00:33:01', '2025-04-06 07:39:31', 1, 1, '18.00000000', '76.07418880', '18.72363520', '76.07418880', '::1', '::1', '7.11', 'weekend', '07:15:00', '08:15:00', '', '2025-04-05 22:33:01', '2025-04-06 05:39:31'),
(3, 1, 'teacher', '2025-04-06 09:27:55', '2025-04-06 09:28:28', 1, 1, '18.00000000', '76.07418880', '18.72363520', '76.07418880', '::1', '::1', '0.01', 'weekend', '07:15:00', '08:15:00', '', '2025-04-06 07:27:55', '2025-04-06 07:28:28'),
(4, 3, 'teacher', '2025-04-06 20:38:46', NULL, 1, NULL, '18.00000000', '76.07418880', NULL, NULL, '::1', NULL, NULL, 'weekend', '19:15:00', '20:15:00', NULL, '2025-04-06 18:38:46', '2025-04-06 18:38:46'),
(5, 3, 'teacher', '2025-04-07 00:22:41', '2025-04-07 01:11:32', 1, 1, '18.00000000', '76.07418880', '18.72363520', '76.07418880', '::1', '::1', '0.81', 'early_exit', '07:15:00', '08:15:00', '', '2025-04-06 18:52:41', '2025-04-06 19:41:32'),
(6, 1, 'teacher', '2025-04-07 00:27:37', '2025-04-07 01:08:44', 1, 1, '18.00000000', '76.07418880', '18.72363520', '76.07418880', '::1', '::1', '0.69', 'early_exit', '07:15:00', '08:15:00', '', '2025-04-06 18:57:37', '2025-04-06 19:38:44'),
(7, 3, 'teacher', '2025-04-08 16:34:13', NULL, 1, NULL, '18.00000000', '76.07418880', NULL, NULL, '::1', NULL, NULL, 'present', '17:15:00', '18:15:00', NULL, '2025-04-08 11:04:13', '2025-04-08 11:04:13'),
(8, 2, 'admin', '2025-04-15 18:10:03', NULL, 1, NULL, '18.00000000', '76.07418880', NULL, NULL, '::1', NULL, NULL, 'late', '17:15:00', '18:15:00', NULL, '2025-04-15 12:40:03', '2025-04-15 12:40:03');

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
(1, 'work_start_time', '09:00', 'Default work start time', '2025-04-05 21:13:59', '2025-04-05 21:49:40'),
(2, 'work_end_time', '17:00', 'Default work end time', '2025-04-05 21:13:59', '2025-04-05 21:49:40'),
(3, 'late_threshold_minutes', '5', 'Minutes after work start time to mark as late', '2025-04-05 21:13:59', '2025-04-05 22:12:46'),
(4, 'early_exit_threshold_minutes', '5', 'Minutes before work end time to mark as early exit', '2025-04-05 21:13:59', '2025-04-05 22:12:46'),
(5, 'weekend_days', '0', 'Days of week that are weekends (0=Sunday, 6=Saturday)', '2025-04-05 21:13:59', '2025-04-05 21:49:40'),
(6, 'geofencing_enabled', '1', 'Whether location-based attendance is enforced', '2025-04-05 21:13:59', '2025-04-05 21:13:59'),
(7, 'auto_punch_out', '0', 'Automatically punch out users at work end time if not done manually', '2025-04-05 21:13:59', '2025-04-05 21:49:40'),
(8, 'work_shifts', '[{\"start\":\"07:15\",\"end\":\"08:15\",\"min_hours\":1},{\"start\":\"17:15\",\"end\":\"18:15\",\"min_hours\":1},{\"start\":\"19:15\",\"end\":\"20:15\",\"min_hours\":1}]', 'Multiple work shifts configuration', '2025-04-05 22:26:11', '2025-04-05 22:26:56'),
(9, 'multiple_shifts_enabled', '1', 'Enable multiple punch in/out per day', '2025-04-05 22:26:11', '2025-04-05 22:26:11'),
(10, 'warn_incomplete_hours', '1', 'Warn users when punching out with incomplete hours', '2025-04-05 22:26:11', '2025-04-05 22:26:11');

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
(1, 2, 'admin', '2025-04-07', 4, 2025, 'leave', '0.00', 0, 0, 3, '2025-04-05 21:47:57', '2025-04-05 21:47:57'),
(2, 2, 'admin', '2025-04-06', 4, 2025, 'weekend', '7.15', 0, 0, NULL, '2025-04-05 22:09:01', '2025-04-06 05:39:31'),
(5, 1, 'teacher', '2025-04-06', 4, 2025, NULL, '2.00', 0, 0, NULL, '2025-04-06 07:27:55', '2025-04-06 07:29:19'),
(6, 3, 'teacher', '2025-04-06', 4, 2025, '', '0.00', 0, 0, NULL, '2025-04-06 18:38:46', '2025-04-06 18:38:46'),
(7, 3, 'teacher', '2025-04-07', 4, 2025, 'early_exit', '0.81', 0, 1, 1, '2025-04-06 18:52:41', '2025-04-06 19:41:32'),
(8, 1, 'teacher', '2025-04-07', 4, 2025, 'early_exit', '0.69', 0, 1, NULL, '2025-04-06 18:57:37', '2025-04-06 19:38:44'),
(9, 3, 'teacher', '2025-04-08', 4, 2025, '', '0.00', 0, 0, NULL, '2025-04-08 11:04:13', '2025-04-08 11:04:13'),
(10, 2, 'admin', '2025-04-15', 4, 2025, 'leave', '0.00', 0, 0, 1, '2025-04-15 12:21:23', '2025-04-15 12:21:23'),
(11, 2, 'admin', '2025-04-16', 4, 2025, 'leave', '0.00', 0, 0, 1, '2025-04-15 12:21:23', '2025-04-15 12:21:23');

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

--
-- Dumping data for table `cheque_details`
--

INSERT INTO `cheque_details` (`id`, `cheque_given_date`, `cheque_year`, `cheque_month`, `cheque_number`, `cheque_amount`, `cheque_photo`, `cheque_handover_teacher`, `is_deleted`, `created_by`, `created_on`, `deleted_by`, `deleted_on`, `is_cleared`, `is_bounced`, `bank_name`) VALUES
(1, '2025-02-14', 2025, 2, 'Test1234', '50000.00', 'assets/images/1739551394_IMG_0759.jpeg', 7, 0, 2, '2025-02-14 19:43:14', NULL, NULL, 1, 0, NULL),
(2, '2025-02-13', 2025, 1, ' Heck2', '10000.00', 'assets/images/1739553929_image.jpg', 3, 0, 2, '2025-02-14 20:25:29', NULL, NULL, 0, 1, NULL),
(3, '2025-02-20', 2025, 2, 'Test321', '46900.00', 'assets/images/1740684368_550c084c-8e2e-4b70-915b-5f30092897c9.jpeg', 9, 0, 2, '2025-02-27 22:26:08', NULL, NULL, 1, 0, NULL);

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

--
-- Dumping data for table `daily_salary_calculations`
--

INSERT INTO `daily_salary_calculations` (`id`, `teacher_id`, `calculation_date`, `base_amount`, `deduction_amount`, `final_amount`, `working_hours`, `required_hours`, `minutes_short`, `notes`, `created_at`) VALUES
(1, 3, '2025-04-07', '324.00', '291.60', '32.40', '0.00', '240.00', 0, 'No attendance record found', '2025-04-06 19:53:36'),
(2, 1, '2025-04-07', '243.00', '218.70', '24.30', '0.00', '180.00', 0, 'No attendance record found', '2025-04-06 19:53:36'),
(3, 3, '2025-04-06', '324.00', '291.60', '32.40', '0.00', '240.00', 0, 'No attendance record found', '2025-04-06 20:19:31'),
(4, 1, '2025-04-06', '243.00', '218.70', '24.30', '0.00', '180.00', 0, 'No attendance record found', '2025-04-06 20:19:31'),
(5, 3, '2025-04-05', '324.00', '291.60', '32.40', '0.00', '240.00', 0, 'No attendance record found', '2025-04-06 20:19:53'),
(6, 1, '2025-04-05', '243.00', '218.70', '24.30', '0.00', '180.00', 0, 'No attendance record found', '2025-04-06 20:19:53'),
(7, 3, '2025-04-08', '324.00', '291.60', '32.40', '0.00', '240.00', 0, 'No attendance record found', '2025-04-08 10:24:58'),
(8, 1, '2025-04-08', '243.00', '218.70', '24.30', '0.00', '180.00', 0, 'No attendance record found', '2025-04-08 10:24:58');

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
(1, 1017, '14.00', 2, '2025-02-14 16:33:04', 2, '2025', 2, '2025-04-06 18:59:14.000000', 'paid', NULL, NULL, NULL),
(2, 1017, '300.00', 2, '2025-02-14 16:33:42', 2, '2025', 2, '2025-04-06 18:59:08.000000', 'paid', NULL, NULL, NULL),
(3, 1017, '200.00', 2, '2025-02-14 17:23:16', 2, '2025', 2, '2025-04-06 18:59:39.000000', 'paid', NULL, NULL, NULL),
(4, 1017, '100.00', 12, '2025-02-15 18:35:05', 2, '2024', 2, '2025-04-06 19:03:02.000000', 'paid', NULL, NULL, NULL),
(5, 1017, '300.00', 3, '2025-02-15 19:30:41', 2, '2025', 2, '2025-04-06 18:59:36.000000', 'paid', NULL, NULL, NULL),
(6, 1017, '200.00', 1, '2025-02-23 07:00:48', 2, '2025', 2, '2025-04-06 18:56:41.000000', 'paid', NULL, NULL, NULL),
(7, 1017, '100.00', 1, '2025-03-16 10:18:26', 2, '2025', 2, '2025-04-06 18:58:45.000000', 'paid', NULL, NULL, NULL),
(14, 1026, '1100.00', 4, '2025-04-12 14:41:50', 2, '2025', 2, '2025-04-12 14:44:47.000000', 'paid', NULL, NULL, ''),
(15, 1026, '100.00', 4, '2025-04-12 18:46:04', 2, '2025', 2, '2025-04-15 12:12:43.000000', 'paid', NULL, NULL, ''),
(16, 1026, '100.00', 4, '2025-04-15 12:11:19', 2, '2025', 2, '2025-04-15 12:12:14.000000', 'paid', NULL, NULL, 'Test'),
(17, 1022, '500.00', 4, '2025-04-15 12:13:19', 2, '2025', 2, '2025-04-15 12:13:48.000000', 'paid', NULL, NULL, 'Testing');

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

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `user_id`, `user_type`, `leave_type_id`, `start_date`, `end_date`, `reason`, `attachment`, `status`, `approved_by`, `rejection_reason`, `created_at`, `updated_at`) VALUES
(1, 2, 'admin', 3, '2025-04-06', '2025-04-07', 'Test', '', 'approved', 2, NULL, '2025-04-05 21:47:57', '2025-04-05 21:59:59'),
(2, 1, 'teacher', 2, '2025-04-07', '2025-04-08', 'Testing', '', 'approved', 2, NULL, '2025-04-05 22:35:19', '2025-04-05 22:36:28'),
(3, 3, 'teacher', 1, '2025-04-07', '2025-04-07', 'ddd', '', 'rejected', 2, 'Not allowed', '2025-04-06 18:55:12', '2025-04-06 18:55:48'),
(4, 2, 'admin', 1, '2025-04-15', '2025-04-16', 'fsdf', '', 'pending', NULL, NULL, '2025-04-15 12:21:23', '2025-04-15 12:21:23');

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
(2, 'Casual Leave', 'Leave taken for personal reasons', 1, '2025-04-05 21:13:59', '2025-04-05 21:13:59'),
(3, 'Vacation', 'Annual vacation leave', 1, '2025-04-05 21:13:59', '2025-04-05 21:13:59'),
(4, 'Unpaid Leave', 'Leave without pay', 0, '2025-04-05 21:13:59', '2025-04-05 21:13:59'),
(5, 'Maternity Leave', 'Leave for female employees before and after childbirth', 1, '2025-04-05 21:13:59', '2025-04-05 21:13:59'),
(6, 'Paternity Leave', 'Leave for male employees after childbirth', 1, '2025-04-05 21:13:59', '2025-04-05 21:13:59'),
(7, 'Emergency Leave', 'Leave for family emergencies', 1, '2025-04-05 21:13:59', '2025-04-05 21:13:59');

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

--
-- Dumping data for table `maintenance`
--

INSERT INTO `maintenance` (`id`, `created_by`, `month`, `year`, `category`, `amount`, `comment`, `created_on`, `is_deleted`, `deleted_by`, `deleted_on`) VALUES
(1, 2, 2, 2025, 'Maktab Rest', '1800.00', '', '2025-02-14 19:40:54', 0, NULL, NULL),
(2, 2, 1, 2020, 'Miscellaneous', '3000.00', 'Test', '2025-02-14 20:26:47', 1, 2, '2025-04-05 12:38:37');

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

--
-- Dumping data for table `meeting_details`
--

INSERT INTO `meeting_details` (`id`, `meeting_date`, `student_responsibility`, `namaz_responsibility`, `daily_visit`, `fees_collection`, `maktab_lock`, `cleanliness_ethics`, `food_responsibility`, `created_at`, `updated_at`, `visit_fajar`, `visit_asar`, `visit_magrib`, `created_by`, `updated_by`) VALUES
(10, '2025-02-16', 1, 3, NULL, NULL, 7, 8, 5, '2025-02-15 18:40:19', '2025-02-15 18:40:19', 6, 4, 12, NULL, NULL),
(11, '2025-02-28', 1, 3, NULL, NULL, 8, 9, 4, '2025-02-27 19:53:07', '2025-02-27 19:53:07', 4, 5, 11, NULL, NULL);

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

--
-- Dumping data for table `meeting_fees_collection`
--

INSERT INTO `meeting_fees_collection` (`id`, `meeting_id`, `admin_id`, `amount`) VALUES
(1, 10, 2, '500.00'),
(2, 10, 4, '300.00'),
(3, 10, 5, '300.00'),
(4, 10, 6, '300.00'),
(5, 10, 11, '300.00'),
(6, 11, 5, '300.00'),
(7, 11, 11, '300.00');

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

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `reference_id`, `title`, `message`, `content`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 2, 'leave', NULL, 'Leave Approved', 'Your leave request has been approved', 'Your leave request has been approved', 1, '0000-00-00 00:00:00', '2025-04-06 03:30:15'),
(2, 1, 'leave', NULL, 'Leave Approved', 'Your leave request has been approved', 'Your leave request has been approved', 1, '0000-00-00 00:00:00', '2025-04-06 04:07:05'),
(12, 1, 'salary_processed', NULL, 'Salary Processed', 'Your salary for the period April has been processed. Total hours: 0.00, Final salary: ₹0.00', 'Your salary for the period April has been processed. Total hours: 0.00, Final salary: ₹0.00', 1, '2025-04-06 12:32:07', '2025-04-06 12:32:39'),
(13, 1, 'salary_processed', NULL, 'Salary Processed', 'Your salary for the period April has been processed. Total hours: 2.00, Final salary: ₹140.00', 'Your salary for the period April has been processed. Total hours: 2.00, Final salary: ₹140.00', 1, '2025-04-06 12:59:41', '2025-04-07 00:28:04'),
(14, 1, 'salary_processed', NULL, 'Salary Processed', 'Your salary for the period April has been processed. Total hours: 2.00, Final salary: ₹140.00', 'Your salary for the period April has been processed. Total hours: 2.00, Final salary: ₹140.00', 1, '2025-04-06 13:34:35', '2025-04-07 00:28:00'),
(15, 1, 'salary_processed', NULL, 'Salary Processed', 'Your salary for the period April has been processed. Total hours: 2.00, Final salary: ₹190.00', 'Your salary for the period April has been processed. Total hours: 2.00, Final salary: ₹190.00', 1, '2025-04-06 13:37:46', '2025-04-07 00:27:57'),
(16, 1, 'salary_processed', NULL, 'Salary Processed', 'Your salary for the period April has been processed. Total hours: 2.00, Final salary: ₹190.00', 'Your salary for the period April has been processed. Total hours: 2.00, Final salary: ₹190.00', 1, '2025-04-06 13:37:54', '2025-04-07 00:27:47'),
(17, 3, 'leave', NULL, 'Leave Rejected', 'Your leave request has been rejected: Not allowed', NULL, 1, '0000-00-00 00:00:00', '2025-04-07 00:26:05'),
(18, 1, 'salary_processed', NULL, 'Salary Processed', 'Your salary for the period April has been processed. Total hours: 2.69, Final salary: ₹217.89', 'Your salary for the period April has been processed. Total hours: 2.69, Final salary: ₹217.89', 0, '2025-04-07 01:09:52', NULL),
(19, 3, 'salary_processed', NULL, 'Salary Processed', 'Your salary for the period April has been processed. Total hours: 0.00, Final salary: ₹0.00', 'Your salary for the period April has been processed. Total hours: 0.00, Final salary: ₹0.00', 1, '2025-04-07 01:09:53', '2025-04-07 01:36:42'),
(20, 1, 'salary_processed', NULL, 'Salary Processed', 'Your salary for the period April has been processed. Total hours: 2.69, Final salary: ₹217.89', 'Your salary for the period April has been processed. Total hours: 2.69, Final salary: ₹217.89', 0, '2025-04-07 01:11:55', NULL),
(21, 3, 'salary_processed', NULL, 'Salary Processed', 'Your salary for the period April has been processed. Total hours: 0.81, Final salary: ₹65.61', 'Your salary for the period April has been processed. Total hours: 0.81, Final salary: ₹65.61', 1, '2025-04-07 01:11:55', '2025-04-07 01:36:37');

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
(1, 'MAKTAB', 'Roza Mohalla Tq. Kaij Dist Beed.', '18.72363520', '76.07418880', 50, 1, '2025-04-05 22:05:35', '2025-04-06 18:52:32');

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

--
-- Dumping data for table `salary_deduction_rules`
--

INSERT INTO `salary_deduction_rules` (`id`, `rule_name`, `percentage`, `hours_threshold`, `deduction_type`, `fixed_amount`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Minor Incomplete Hours', '5.00', '1.00', 'percentage', '0.00', 0, '2025-04-05 23:09:02', '2025-04-06 18:31:37'),
(2, 'Moderate Incomplete Hours', '10.00', '2.00', 'percentage', '0.00', 0, '2025-04-05 23:09:02', '2025-04-06 18:31:44'),
(3, 'Major Incomplete Hours', '15.00', '4.00', 'percentage', '0.00', 0, '2025-04-05 23:09:02', '2025-04-06 18:31:49');

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

--
-- Dumping data for table `salary_notifications`
--

INSERT INTO `salary_notifications` (`id`, `user_id`, `salary_id`, `notification_title`, `notification_text`, `is_read`, `created_at`) VALUES
(9, 1, 18, 'Salary Processed', 'Your salary for the period April has been processed. Total hours: 2.69, Final salary: ₹217.89', 0, '2025-04-06 19:41:55'),
(10, 3, 19, 'Salary Processed', 'Your salary for the period April has been processed. Total hours: 0.81, Final salary: ₹65.61', 1, '2025-04-06 19:41:55');

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

--
-- Dumping data for table `salary_periods`
--

INSERT INTO `salary_periods` (`id`, `period_name`, `start_date`, `end_date`, `is_processed`, `is_locked`, `created_at`, `updated_at`) VALUES
(2, 'April', '2025-04-01', '2025-04-30', 1, 1, '2025-04-06 05:43:15', '2025-04-06 20:05:55'),
(3, 'Salary Period - May 2025', '2025-05-01', '2025-05-31', 0, 0, '2025-04-06 08:09:41', '2025-04-06 08:09:41');

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

--
-- Dumping data for table `salary_settings`
--

INSERT INTO `salary_settings` (`id`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES
(1, 'salary_calculation_day', '1', 'Day of the month when salary calculation is triggered', '2025-04-05 23:09:02', '2025-04-05 23:09:02'),
(2, 'minimum_working_hours_per_day', '3', 'Minimum working hours required per day', '2025-04-05 23:09:02', '2025-04-05 23:11:09'),
(3, 'working_days_per_week', '6', 'Number of working days per week (typically 5 for Monday-Friday)', '2025-04-05 23:09:02', '2025-04-05 23:11:09'),
(4, 'overtime_multiplier', '1.5', 'Multiplier for overtime hours', '2025-04-05 23:09:02', '2025-04-05 23:09:02'),
(5, 'enable_deductions', '1', 'Enable deductions for incomplete hours (1=Yes, 0=No)', '2025-04-05 23:09:02', '2025-04-05 23:09:02'),
(6, 'notification_enabled', '1', 'Enable salary notifications (1=Yes, 0=No)', '2025-04-05 23:09:02', '2025-04-05 23:09:02'),
(7, 'default_hourly_rate', '81', 'Default hourly rate for new teachers', '2025-04-05 23:09:02', '2025-04-06 20:02:26'),
(8, 'salary_period_type', 'monthly', 'Salary period type (monthly, bi-weekly, weekly)', '2025-04-05 23:09:02', '2025-04-05 23:09:02'),
(9, 'auto_process_salary', '1', 'Automatically process salary at period end (1=Yes, 0=No)', '2025-04-05 23:09:02', '2025-04-05 23:09:02'),
(10, 'default_monthly_days', '30', 'Default number of days in a month for salary calculation', '2025-04-06 19:21:28', '2025-04-06 19:21:28'),
(11, 'deduction_per_minute', '1', 'Deduction amount per minute of missed work (in INR)', '2025-04-06 19:21:28', '2025-04-06 20:02:26');

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
  `assigned_teacher` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `photo`, `class`, `annual_fees`, `phone`, `assigned_teacher`, `is_deleted`) VALUES
(1017, 'Teststudent', 'assets/images/1744407026_Window Universe [1920x1080].jpg', '1', 2000, '2323123453', 1, 0),
(1018, 'Teststudent 2', 'assets/images/1744405428_MyStartWallpapers_3.jpg', '3', 2000, '4521452154', 1, 1),
(1019, 'student3', 'assets/images/1744407014_luca-micheli-ruWkmt3nU58-unsplash.jpg', '2', 2000, '2145214512', 1, 0),
(1020, 'student4', 'assets/images/1744407048_pexels-pixabay-531880.jpg', '2', 2000, '2145214512', 1, 0),
(1021, 'student5', 'assets/images/1744407068_Avengers.jpg', '2', 2000, '2145214512', 1, 0),
(1022, 'Student 6', 'assets/images/1744407151_MyStartWallpapers_3.jpg', '4', 2000, '4521452145', 1, 0),
(1023, 'Student 7', 'assets/images/1744407170_wp2781488-wallpaper-laptop-cute (1).png', '1', 2000, '4521452145', 1, 0),
(1024, 'Student 7', 'assets/images/1744407193_luca-micheli-ruWkmt3nU58-unsplash.jpg', '2', 2000, '4521452145', 3, 0),
(1025, 'Student9', 'assets/images/1744407234_SamplePhoto_1.jpg', '1', 2000, '4521452145', 1, 0),
(1026, 'Student 10', 'assets/images/1744407270_SamplePhoto_2.jpg', '1', 2000, '3216547895', 1, 0),
(1027, 'Student 11', 'assets/images/1744407311_SamplePhoto_3.jpg', '1', 2000, '4521452145', 1, 0),
(1028, 'Student 12', 'assets/images/1744448838_SamplePhoto_8.jpg', '2', 2000, '5487458745', 1, 0);

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
(1, 1017, 2025, 2, 3, '2000.00', 'active', 1, '2025-02-14 16:31:29', '2025-02-14 16:31:43', 2, 2),
(2, 1017, 2025, 2, 3, '2000.00', 'inactive', 1, '2025-02-14 16:31:43', '2025-02-14 16:31:50', 2, 2),
(3, 1017, 2025, 2, 3, '2000.00', 'active', 1, '2025-02-14 16:31:50', '2025-02-14 16:37:45', 2, 2),
(4, 1017, 2025, 2, 1, '2000.00', 'active', 1, '2025-02-14 16:37:45', '2025-02-14 16:44:30', 2, 2),
(5, 1017, 2025, 2, 1, '2000.00', 'inactive', 1, '2025-02-14 16:44:30', '2025-02-14 16:44:57', 2, 2),
(6, 1017, 2025, 2, 1, '2000.00', 'active', 1, '2025-02-14 16:44:57', '2025-04-11 21:30:26', 2, 2),
(7, 1018, 2025, 4, 3, '2000.00', 'active', 1, '2025-04-11 21:03:48', '2025-04-11 21:12:19', 2, 2),
(8, 1018, 2025, 4, 1, '2000.00', 'active', 1, '2025-04-11 21:12:19', '2025-04-11 22:01:23', 2, 2),
(9, 1019, 2025, 4, 1, '2000.00', 'active', 0, '2025-04-11 21:30:14', '2025-04-11 21:30:14', 2, NULL),
(10, 1017, 2025, 4, 1, '2000.00', 'active', 0, '2025-04-11 21:30:26', '2025-04-11 21:30:26', 2, NULL),
(11, 1020, 2025, 4, 1, '2000.00', 'active', 0, '2025-04-11 21:30:48', '2025-04-11 21:30:48', 2, NULL),
(12, 1021, 2025, 4, 1, '2000.00', 'active', 0, '2025-04-11 21:31:08', '2025-04-11 21:31:08', 2, NULL),
(13, 1022, 2025, 4, 1, '2000.00', 'active', 0, '2025-04-11 21:32:31', '2025-04-11 21:32:31', 2, NULL),
(14, 1023, 2025, 4, 1, '2000.00', 'active', 0, '2025-04-11 21:32:50', '2025-04-11 21:32:50', 2, NULL),
(15, 1024, 2025, 4, 1, '2000.00', 'active', 1, '2025-04-11 21:33:13', '2025-04-15 12:45:16', 2, 2),
(16, 1025, 2025, 4, 1, '2000.00', 'active', 0, '2025-04-11 21:33:54', '2025-04-11 21:33:54', 2, NULL),
(17, 1026, 2025, 4, 1, '2000.00', 'active', 0, '2025-04-11 21:34:30', '2025-04-11 21:34:30', 2, NULL),
(18, 1027, 2025, 4, 1, '2000.00', 'active', 0, '2025-04-11 21:35:11', '2025-04-11 21:35:11', 2, NULL),
(19, 1018, 2025, 4, 1, '2000.00', 'inactive', 0, '2025-04-11 22:01:23', '2025-04-11 22:01:23', 2, NULL),
(20, 1028, 2025, 4, 1, '2000.00', 'active', 0, '2025-04-12 09:07:18', '2025-04-12 09:07:18', 2, NULL),
(21, 1024, 2025, 4, 3, '2000.00', 'active', 0, '2025-04-15 12:45:16', '2025-04-15 12:45:16', 2, NULL);

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

--
-- Dumping data for table `teacher_class_assignments`
--

INSERT INTO `teacher_class_assignments` (`id`, `teacher_id`, `class_name`, `subject`, `class_hours`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 3, '1,2,3,4', 'test', '4.00', 1, '2025-04-06 19:34:55', '2025-04-06 19:35:59'),
(2, 1, '1,2,3', 'Test-2', '3.00', 1, '2025-04-06 19:35:52', '2025-04-06 19:35:52');

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

--
-- Dumping data for table `teacher_salary_calculations`
--

INSERT INTO `teacher_salary_calculations` (`id`, `teacher_id`, `user_id`, `period_id`, `base_salary`, `deductions`, `bonuses`, `total_working_hours`, `expected_working_hours`, `deduction_amount`, `bonus_amount`, `final_salary`, `status`, `payment_date`, `payment_method`, `reference_number`, `payment_notes`, `created_at`, `updated_at`, `hourly_rate`, `total_hours`, `expected_hours`, `notes`) VALUES
(18, 1, 1, 2, '217.89', '0.00', '0.00', '2.69', '6.00', '0.00', '0.00', '217.89', 'processed', NULL, NULL, NULL, NULL, '2025-04-06 19:41:55', '2025-04-06 19:41:55', '81', '2.69', '6.00', 'Total worked hours: 2.69 out of expected 6.00 hours.\nIncomplete days: 2\n'),
(19, 3, 3, 2, '65.61', '0.00', '0.00', '0.81', '6.00', '0.00', '0.00', '65.61', 'processed', NULL, NULL, NULL, NULL, '2025-04-06 19:41:55', '2025-04-06 19:41:55', '81', '0.81', '6.00', 'Total worked hours: 0.81 out of expected 6.00 hours.\nIncomplete days: 2\n');

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

--
-- Dumping data for table `teacher_salary_rates`
--

INSERT INTO `teacher_salary_rates` (`id`, `user_id`, `hourly_rate`, `effective_date`, `effective_from`, `effective_to`, `is_active`, `created_by`, `created_at`, `updated_at`, `minimum_working_hours`) VALUES
(1, 1, '81.00', '2025-04-01', '2025-04-01', NULL, 1, 2, '2025-04-05 23:19:47', '2025-04-06 19:25:40', '3.00'),
(2, 3, '81.00', NULL, '2025-04-01', NULL, 1, 2, '2025-04-06 19:26:18', '2025-04-06 19:26:18', '4.00');

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
(1, '7215bca5f28c6cb7b4252ce16519ec736783ce01c5e130a03be6f0ec5979fcb6', '2025-04-13 11:06:33', NULL, 'Irfan Hafiz', '9561917112@gmail.com', '$2y$10$OJOF4wELjpkJmkrfHMDNoeI8S2Y.PU74mmQTYvPUFuN2Y5SVDK.4C', 'teacher', 0, '9561917112', 1, NULL, NULL, NULL, NULL),
(2, '5d9dd34a3d51410bf5fcf637d6884e4d5d79e0556a55271578c117c31cf483f5', '2025-04-16 16:57:50', NULL, 'Shoaib Farooqui', '9595778797@gmail.com', '$2y$10$BCtAQclYS2dVc2sBg8w7w.WpFWaDDsWwgtTVdLNpbwHZSnDUq9ebO', 'admin', 0, '9595778797', 1, NULL, NULL, NULL, NULL),
(3, '58b4c1afd28a88a96e2626d9b64f75c2be62dba7279a134e68ff0a370a1583d3', '2025-04-16 16:52:52', NULL, 'Hafiz Farook', 'farok@gmail.com', '$2y$10$b0qq5aNNzHHxjQRYDA/4vO9hjRpZag5e7SiWTcUMZvjro9o87vovK', 'teacher', 0, '9923242833', 1, NULL, NULL, NULL, NULL),
(4, '572da090251a7f5eeaebe391bf913e634b4d1e383801d685f804b7951173b39f', '2025-04-13 09:50:10', NULL, 'Rizwan Khureshi', '9960555762@gmail.com', '$2y$10$ye0qPFDU9.8./gLYHBuMT.RTvDo6ZoyK6vJ/dd0oTsrzPNtoNv1vK', 'admin', 0, '9960555762', 1, NULL, NULL, NULL, NULL),
(5, NULL, NULL, NULL, 'Zaheer Farooqui', '7218932313@gmail.com', '$2y$10$z9Fpf2zZoL3REwAljMXoFuFzlCVyVyNsPMgc0CDrxswAQKHtCddS2', 'admin', 0, '7218932313', 1, NULL, NULL, NULL, NULL),
(6, NULL, NULL, NULL, 'Khadir Khureshi', '8421032525@gmail.com', '$2y$10$ig7hcg7JO3vcJ4UQ8XMObeXA3tNVJNBEKpFLWPknKVZr0jLSNbwmS', 'admin', 0, '8421032525', 1, NULL, NULL, NULL, NULL),
(7, NULL, NULL, NULL, 'Moulana Arif', 'arif@gmail.com', '$2y$10$O7MoT.aY8kSnxbHvShQo3OCmu9UuT7XgAnvtEuY9b.tM2dpBRKuaS', 'teacher', 0, '9284404710', 1, NULL, NULL, NULL, NULL),
(8, NULL, NULL, NULL, 'Moulana eliyas', '9527713539@gmai.com', '$2y$10$wo4FK/jcZ4Dcj143OG4d6ub8jwwPmR/MiEZ38d/bzLcV5nR3nzTLC', 'teacher', 0, '9527713539', 1, NULL, NULL, NULL, NULL),
(9, NULL, NULL, NULL, 'Hafiz Ejaz', '7498062171@gmail.com', '$2y$10$j.7zU/kb9B9YXpV1PO6vd.DXawksO61EQ4fj7D62H337PqLLuep2W', 'teacher', 0, '7498062171', 1, NULL, NULL, NULL, NULL),
(10, NULL, NULL, NULL, 'Test Login', 'testlogin@gmail.com', '$2y$10$W5KcB9Uj605kcjkpSdUQSe.jz3tXqhaR50iKW7dR5PAhEXUbNb2g6', 'admin', 1, NULL, 1, NULL, NULL, NULL, NULL),
(11, NULL, NULL, NULL, 'Imad Farooqui', '9158668082@gmail.com', '$2y$10$Sm7vlUR3TeYnjP1ZEJIKTuVKumkmCzCg6OSt3SmzOlPjJQImRgCaG', 'admin', 0, '9158668082', 1, NULL, NULL, NULL, NULL),
(12, NULL, NULL, NULL, 'Abed Farooqui', '9850041083@gmail.com', '$2y$10$WBQGq9c1T78ARj8LsreANux3A1OsfNWTHKsV2cWExAsiyXMXXvVmK', 'admin', 0, '9850041083', 1, NULL, NULL, NULL, NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `attendance_settings`
--
ALTER TABLE `attendance_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `attendance_summary`
--
ALTER TABLE `attendance_summary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `cheque_details`
--
ALTER TABLE `cheque_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `daily_salary_calculations`
--
ALTER TABLE `daily_salary_calculations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `maintenance`
--
ALTER TABLE `maintenance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `meeting_details`
--
ALTER TABLE `meeting_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `meeting_fees_collection`
--
ALTER TABLE `meeting_fees_collection`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `salary_notifications`
--
ALTER TABLE `salary_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `salary_periods`
--
ALTER TABLE `salary_periods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `salary_settings`
--
ALTER TABLE `salary_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `sms_log`
--
ALTER TABLE `sms_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1029;

--
-- AUTO_INCREMENT for table `student_status_history`
--
ALTER TABLE `student_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `teacher_class_assignments`
--
ALTER TABLE `teacher_class_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teacher_salary_calculations`
--
ALTER TABLE `teacher_salary_calculations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `teacher_salary_rates`
--
ALTER TABLE `teacher_salary_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
