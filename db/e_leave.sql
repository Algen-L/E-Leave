-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 17, 2026 at 06:57 AM
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
-- Database: `e_leave`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(1, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-10 11:13:54'),
(2, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-10 14:12:12'),
(3, 4, 'Deleted User', 'User ID: 1 removed.', '127.0.0.1', '2026-02-10 14:31:08'),
(4, 4, 'Created User', 'User ID: 9 - tried', '127.0.0.1', '2026-02-10 14:34:23'),
(5, 4, 'Logout', 'User logged out', '127.0.0.1', '2026-02-10 14:34:37'),
(6, 9, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-10 14:34:53'),
(7, 10, 'Profile Created', 'New account registered and verified via Gmail: loveresalgen@gmail.com', '127.0.0.1', '2026-02-10 23:32:37'),
(8, 10, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-10 23:32:52'),
(9, 10, 'Logout', 'User logged out', '127.0.0.1', '2026-02-10 23:33:00'),
(10, 10, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-10 23:37:57'),
(11, 10, 'Logout', 'User logged out', '127.0.0.1', '2026-02-10 23:38:03'),
(12, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-10 23:40:20'),
(13, 4, 'Updated User Record', 'Updated user: Head HR Officer (ID: 6)', '127.0.0.1', '2026-02-10 23:46:58'),
(14, 4, 'Logout', 'User logged out', '127.0.0.1', '2026-02-10 23:48:49'),
(15, 6, 'Password Reset', 'Password was reset via email verification', '127.0.0.1', '2026-02-10 23:50:02'),
(16, 6, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-10 23:50:17'),
(17, 6, 'Logout', 'User logged out', '127.0.0.1', '2026-02-10 23:51:34'),
(18, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 00:08:30'),
(19, 4, 'Logout', 'User logged out', '127.0.0.1', '2026-02-11 00:27:09'),
(20, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 00:27:42'),
(21, 4, 'Logout', 'User logged out', '127.0.0.1', '2026-02-11 00:28:02'),
(22, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 00:42:43'),
(23, 4, 'Logout', 'User logged out', '127.0.0.1', '2026-02-11 00:48:33'),
(24, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 00:49:28'),
(25, 4, 'Logout', 'User logged out', '127.0.0.1', '2026-02-11 00:59:13'),
(26, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 01:00:11'),
(27, 4, 'Logout', 'User logged out', '127.0.0.1', '2026-02-11 01:06:40'),
(28, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 01:07:17'),
(29, 4, 'Logout', 'User logged out', '127.0.0.1', '2026-02-11 01:08:51'),
(30, 9, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 01:09:02'),
(31, 9, 'Logout', 'User logged out', '127.0.0.1', '2026-02-11 01:17:25'),
(32, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 01:32:35'),
(33, 4, 'Logout', 'User logged out', '127.0.0.1', '2026-02-11 01:32:46'),
(34, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 01:33:31'),
(35, 4, 'Reset Auth Counters', 'Email: xerdapparel@gmail.com', '127.0.0.1', '2026-02-11 01:33:40'),
(36, 4, 'Logout', 'User logged out', '127.0.0.1', '2026-02-11 01:33:57'),
(37, 6, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 01:34:06'),
(38, 6, 'Logout', 'User logged out', '127.0.0.1', '2026-02-11 01:34:26'),
(39, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 01:34:38'),
(40, 4, 'Deleted User', 'User ID: 2 removed.', '127.0.0.1', '2026-02-11 01:48:38'),
(41, 4, 'Unblocked Auth Reset', 'Email: xerdapparel@gmail.com', '127.0.0.1', '2026-02-11 01:49:35'),
(42, 4, 'Logout', 'User logged out', '127.0.0.1', '2026-02-11 01:55:39'),
(43, 9, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 01:55:55'),
(44, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 02:04:38'),
(45, 9, 'Profile Updated', 'User profile updated', '127.0.0.1', '2026-02-11 02:33:56'),
(46, 4, 'Logout', 'User logged out', '127.0.0.1', '2026-02-11 04:24:20'),
(47, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 04:25:38'),
(48, 4, 'Logout', 'User logged out', '127.0.0.1', '2026-02-11 04:41:28'),
(49, 9, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 04:41:37'),
(50, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 04:49:12'),
(51, 9, 'Password Changed', 'User changed password via profile', '127.0.0.1', '2026-02-11 05:40:25'),
(52, 9, 'Logout', 'User logged out', '127.0.0.1', '2026-02-11 05:40:45'),
(53, 9, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 05:41:06'),
(54, 9, 'Logout', 'User logged out', '127.0.0.1', '2026-02-11 07:00:32'),
(55, 4, 'Updated User Record', 'Updated user: Cedrick Velarde Bacaresas (ID: 10)', '127.0.0.1', '2026-02-11 07:01:32'),
(56, 4, 'Updated User Record', 'Updated user: tried triednt trying (ID: 9)', '127.0.0.1', '2026-02-11 07:01:51'),
(57, 9, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 07:02:38'),
(58, 9, 'Profile Updated', 'User profile updated', '127.0.0.1', '2026-02-11 07:32:56'),
(59, 4, 'Updated Signatories', 'Administrative signatories updated', '127.0.0.1', '2026-02-11 07:35:36'),
(60, 9, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 23:07:18'),
(61, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-11 23:57:42'),
(62, 4, 'Updated Signatories', 'Administrative signatories updated', '127.0.0.1', '2026-02-12 00:21:29'),
(63, 4, 'Updated Signatories', 'Administrative signatories updated', '127.0.0.1', '2026-02-12 00:23:59'),
(64, 4, 'Updated Signatories', 'Administrative signatories updated', '127.0.0.1', '2026-02-12 00:26:21'),
(65, 9, 'Logout', 'User logged out', '127.0.0.1', '2026-02-12 00:37:34'),
(66, 6, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-12 00:37:52'),
(67, 4, 'Logout', 'User logged out', '127.0.0.1', '2026-02-12 00:40:20'),
(68, 9, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-12 00:40:32'),
(69, 6, 'Updated User Record', 'Updated user: HR HR HR (ID: 7)', '127.0.0.1', '2026-02-12 01:01:37'),
(70, 9, 'Logout', 'User logged out', '127.0.0.1', '2026-02-12 01:01:56'),
(73, 9, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-12 04:49:59'),
(74, 6, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-12 04:50:17'),
(75, 6, 'Logout', 'User logged out', '127.0.0.1', '2026-02-12 04:54:03'),
(77, 6, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-12 07:05:50'),
(78, 9, 'Logout', 'User logged out', '127.0.0.1', '2026-02-12 07:58:29'),
(79, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-12 07:58:39'),
(80, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-12 23:08:51'),
(81, 4, 'Logout', 'User logged out', '127.0.0.1', '2026-02-12 23:09:03'),
(82, 6, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-12 23:09:14'),
(83, 9, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-12 23:10:49'),
(84, 9, 'Logout', 'User logged out', '127.0.0.1', '2026-02-12 23:11:00'),
(85, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-12 23:11:41'),
(86, 4, 'Logout', 'User logged out', '127.0.0.1', '2026-02-12 23:11:58'),
(89, 6, 'update_credits', 'Updated Vacation Leave credits for tried triednt trying from 0 to 10. (Head HR Override)', '127.0.0.1', '2026-02-13 01:32:22'),
(90, 6, 'update_credits', 'Updated Sick Leave credits for tried triednt trying from 0 to 15. (Head HR Override)', '127.0.0.1', '2026-02-13 01:32:22'),
(91, 6, 'update_credits', 'Updated Maternity Leave credits for tried triednt trying from 0 to 0. (Head HR Override)', '127.0.0.1', '2026-02-13 01:32:22'),
(92, 6, 'update_credits', 'Updated Paternity Leave credits for tried triednt trying from 0 to 0. (Head HR Override)', '127.0.0.1', '2026-02-13 01:32:22'),
(93, 6, 'update_credits', 'Updated Special Privilege Leave credits for tried triednt trying from 0 to 0. (Head HR Override)', '127.0.0.1', '2026-02-13 01:32:22'),
(94, 6, 'update_credits', 'Updated Mandatory/Forced Leave credits for tried triednt trying from 0 to 0. (Head HR Override)', '127.0.0.1', '2026-02-13 01:32:22'),
(95, 6, 'update_credits', 'Updated Solo Parent Leave credits for tried triednt trying from 0 to 0. (Head HR Override)', '127.0.0.1', '2026-02-13 01:32:22'),
(96, 6, 'update_credits', 'Updated 10-Day VAWC Leave credits for tried triednt trying from 0 to 0. (Head HR Override)', '127.0.0.1', '2026-02-13 01:32:22'),
(97, 6, 'update_credits', 'Updated Rehabilitation Privilege credits for tried triednt trying from 0 to 0. (Head HR Override)', '127.0.0.1', '2026-02-13 01:32:22'),
(98, 6, 'update_credits', 'Updated Special Leave Benefits for Women credits for tried triednt trying from 0 to 0. (Head HR Override)', '127.0.0.1', '2026-02-13 01:32:22'),
(99, 6, 'update_credits', 'Updated Special Emergency (Calamity) Leave credits for tried triednt trying from 0 to 0. (Head HR Override)', '127.0.0.1', '2026-02-13 01:32:22'),
(100, 6, 'update_credits', 'Updated Adoption Leave credits for tried triednt trying from 0 to 0. (Head HR Override)', '127.0.0.1', '2026-02-13 01:32:22'),
(101, 6, 'update_credits', 'Updated Study Leave credits for tried triednt trying from 0 to 0. (Head HR Override)', '127.0.0.1', '2026-02-13 01:32:22'),
(102, 6, 'update_credits', 'Updated Others credits for tried triednt trying from 0 to 0. (Head HR Override)', '127.0.0.1', '2026-02-13 01:32:22'),
(103, 6, 'update_credits', 'Updated VAWC Leave (RA 9262) credits for tried triednt trying from 0 to 0. (Head HR Override)', '127.0.0.1', '2026-02-13 01:32:22'),
(104, 6, 'update_credits', 'Updated Rehabilitation Leave credits for tried triednt trying from 0 to 0. (Head HR Override)', '127.0.0.1', '2026-02-13 01:32:22'),
(106, 9, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-13 01:46:34'),
(107, 6, 'Logout', 'User logged out', '127.0.0.1', '2026-02-13 01:47:13'),
(108, 6, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-13 01:47:20'),
(109, 6, 'Logout', 'User logged out', '127.0.0.1', '2026-02-13 03:13:01'),
(110, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-13 03:14:11'),
(111, 9, 'Logout', 'User logged out', '127.0.0.1', '2026-02-13 04:48:04'),
(112, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-13 04:49:02'),
(113, 4, 'Logout', 'User logged out', '127.0.0.1', '2026-02-13 04:49:12'),
(114, 9, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-13 04:49:20'),
(115, 9, 'Logout', 'User logged out', '127.0.0.1', '2026-02-13 05:41:13'),
(116, 4, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-13 05:41:33'),
(117, 4, 'Created User', 'User ID: 11 - JOE-BREN L. CONSUELO', '127.0.0.1', '2026-02-13 05:45:11'),
(118, 4, 'Created User', 'User ID: 12 - PHILLIP B. GALLENDEZ', '127.0.0.1', '2026-02-13 05:46:46'),
(119, 4, 'Created User', 'User ID: 13 - PAUL JEREMY I. AGUJA', '127.0.0.1', '2026-02-13 05:48:20'),
(120, 4, 'Created User', 'User ID: 14 - FREDERICK G. BYRD JR.', '127.0.0.1', '2026-02-13 05:50:24'),
(121, 4, 'Created User', 'User ID: 15 - ERMA S. VALENZUELA', '127.0.0.1', '2026-02-13 05:52:36'),
(122, 9, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-13 05:53:37'),
(123, 9, 'Profile Updated', 'User profile updated', '127.0.0.1', '2026-02-13 05:54:49'),
(124, 4, 'Updated User Record', 'Updated user: JOE-BREN L. CONSUELO (ID: 11)', '127.0.0.1', '2026-02-13 06:30:43'),
(125, 4, 'Updated User Record', 'Updated user: ERMA S. VALENZUELA (ID: 15)', '127.0.0.1', '2026-02-13 06:31:08'),
(126, 4, 'Updated User Record', 'Updated user: FREDERICK G. BYRD JR. (ID: 14)', '127.0.0.1', '2026-02-13 06:31:24'),
(127, 4, 'Updated User Record', 'Updated user: PAUL JEREMY I. AGUJA (ID: 13)', '127.0.0.1', '2026-02-13 06:31:46'),
(128, 4, 'Updated User Record', 'Updated user: PHILLIP B. GALLENDEZ (ID: 12)', '127.0.0.1', '2026-02-13 06:32:00'),
(129, 9, 'Logout', 'User logged out', '127.0.0.1', '2026-02-13 06:32:09'),
(130, 13, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-13 06:32:21'),
(131, 13, 'Logout', 'User logged out', '127.0.0.1', '2026-02-13 06:32:38'),
(132, 6, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-13 06:32:49'),
(133, 6, 'Logout', 'User logged out', '127.0.0.1', '2026-02-13 06:33:04'),
(135, 4, 'Logout', 'User logged out', '127.0.0.1', '2026-02-13 06:57:50'),
(136, 9, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-13 06:58:05'),
(138, 13, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-13 06:59:45'),
(139, 13, 'Logout', 'User logged out', '127.0.0.1', '2026-02-13 07:03:03'),
(140, 12, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-13 07:03:18'),
(141, 12, 'Logout', 'User logged out', '127.0.0.1', '2026-02-13 07:05:39'),
(142, 13, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-13 07:05:52'),
(143, 13, 'Logout', 'User logged out', '127.0.0.1', '2026-02-13 07:06:01'),
(148, 9, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-14 03:38:45'),
(151, 13, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-14 03:53:44'),
(152, 13, 'Profile Updated', 'User profile updated', '127.0.0.1', '2026-02-14 04:15:47'),
(153, 13, 'Profile Updated', 'User profile updated', '127.0.0.1', '2026-02-14 04:20:04'),
(155, 9, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-15 10:46:50'),
(157, 9, 'Logout', 'User logged out', '127.0.0.1', '2026-02-15 11:29:10'),
(158, 8, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-15 11:29:18'),
(159, 8, 'Profile Updated', 'User profile updated', '127.0.0.1', '2026-02-15 11:30:27'),
(160, 8, 'Logout', 'User logged out', '127.0.0.1', '2026-02-15 11:32:09'),
(161, 14, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-15 11:32:40'),
(162, 14, 'Logout', 'User logged out', '127.0.0.1', '2026-02-15 11:33:00'),
(163, 8, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-15 11:33:15'),
(164, 8, 'Logout', 'User logged out', '127.0.0.1', '2026-02-15 11:33:55'),
(165, 14, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-15 11:34:09'),
(166, 14, 'Profile Updated', 'User profile updated', '127.0.0.1', '2026-02-15 11:34:30'),
(167, 14, 'Logout', 'User logged out', '127.0.0.1', '2026-02-15 11:34:36'),
(168, 15, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-15 11:34:51'),
(169, 15, 'Profile Updated', 'User profile updated', '127.0.0.1', '2026-02-15 11:35:04'),
(170, 15, 'Logout', 'User logged out', '127.0.0.1', '2026-02-15 11:35:10'),
(171, 11, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-15 11:35:26'),
(172, 11, 'Profile Updated', 'User profile updated', '127.0.0.1', '2026-02-15 11:35:39'),
(173, 11, 'Logout', 'User logged out', '127.0.0.1', '2026-02-15 11:35:44'),
(174, 12, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-15 11:36:02'),
(175, 12, 'Profile Updated', 'User profile updated', '127.0.0.1', '2026-02-15 11:36:21'),
(176, 12, 'Logout', 'User logged out', '127.0.0.1', '2026-02-15 11:36:24'),
(177, 8, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-15 11:36:31'),
(179, 6, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-15 11:47:20'),
(180, 6, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-15 23:51:09'),
(181, 9, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-15 23:51:11'),
(182, 9, 'Logout', 'User logged out', '127.0.0.1', '2026-02-16 02:20:47'),
(183, 8, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-16 02:20:55'),
(184, 6, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-16 02:26:13'),
(186, 6, 'Logout', 'User logged out', '127.0.0.1', '2026-02-16 04:11:57'),
(189, 14, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-16 04:36:30'),
(190, 14, 'Logout', 'User logged out', '127.0.0.1', '2026-02-16 04:36:37'),
(193, 14, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-16 04:37:08'),
(194, 14, 'Logout', 'User logged out', '127.0.0.1', '2026-02-16 05:24:10'),
(195, 6, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-16 05:24:18'),
(196, 6, 'Logout', 'User logged out', '127.0.0.1', '2026-02-16 05:32:53'),
(197, 14, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-16 05:33:01'),
(198, 14, 'Logout', 'User logged out', '127.0.0.1', '2026-02-16 05:33:25'),
(199, 12, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-16 05:33:32'),
(200, 12, 'Logout', 'User logged out', '127.0.0.1', '2026-02-16 05:33:39'),
(201, 11, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-16 05:33:46'),
(202, 11, 'Logout', 'User logged out', '127.0.0.1', '2026-02-16 06:15:27'),
(203, 6, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-16 06:15:33'),
(204, 6, 'update_credits', 'Updated Vacation Leave credits for Leona 1 Test from 0.00 to 15. (Head HR Override)', '127.0.0.1', '2026-02-16 06:15:53'),
(205, 6, 'update_credits', 'Updated Sick Leave credits for Leona 1 Test from 0.00 to 15. (Head HR Override)', '127.0.0.1', '2026-02-16 06:15:53'),
(206, 6, 'update_credits', 'Updated Maternity Leave credits for Leona 1 Test from 0.00 to 0. (Head HR Override)', '127.0.0.1', '2026-02-16 06:15:53'),
(207, 6, 'update_credits', 'Updated Paternity Leave credits for Leona 1 Test from 0.00 to 0. (Head HR Override)', '127.0.0.1', '2026-02-16 06:15:53'),
(208, 6, 'update_credits', 'Updated Special Privilege Leave credits for Leona 1 Test from 0.00 to 0. (Head HR Override)', '127.0.0.1', '2026-02-16 06:15:53'),
(209, 6, 'update_credits', 'Updated Mandatory/Forced Leave credits for Leona 1 Test from 0.00 to 0. (Head HR Override)', '127.0.0.1', '2026-02-16 06:15:53'),
(210, 6, 'update_credits', 'Updated Solo Parent Leave credits for Leona 1 Test from 0.00 to 0. (Head HR Override)', '127.0.0.1', '2026-02-16 06:15:53'),
(211, 6, 'update_credits', 'Updated 10-Day VAWC Leave credits for Leona 1 Test from 0.00 to 0. (Head HR Override)', '127.0.0.1', '2026-02-16 06:15:53'),
(212, 6, 'update_credits', 'Updated Rehabilitation Privilege credits for Leona 1 Test from 0.00 to 0. (Head HR Override)', '127.0.0.1', '2026-02-16 06:15:53'),
(213, 6, 'update_credits', 'Updated Special Leave Benefits for Women credits for Leona 1 Test from 0.00 to 0. (Head HR Override)', '127.0.0.1', '2026-02-16 06:15:53'),
(214, 6, 'update_credits', 'Updated Special Emergency (Calamity) Leave credits for Leona 1 Test from 0.00 to 0. (Head HR Override)', '127.0.0.1', '2026-02-16 06:15:53'),
(215, 6, 'update_credits', 'Updated Adoption Leave credits for Leona 1 Test from 0.00 to 0. (Head HR Override)', '127.0.0.1', '2026-02-16 06:15:53'),
(216, 6, 'update_credits', 'Updated Study Leave credits for Leona 1 Test from 0.00 to 0. (Head HR Override)', '127.0.0.1', '2026-02-16 06:15:53'),
(217, 6, 'update_credits', 'Updated Others credits for Leona 1 Test from 0.00 to 0. (Head HR Override)', '127.0.0.1', '2026-02-16 06:15:53'),
(218, 6, 'update_credits', 'Updated VAWC Leave (RA 9262) credits for Leona 1 Test from 0 to 0. (Head HR Override)', '127.0.0.1', '2026-02-16 06:15:53'),
(219, 6, 'update_credits', 'Updated Rehabilitation Leave credits for Leona 1 Test from 0 to 0. (Head HR Override)', '127.0.0.1', '2026-02-16 06:15:53'),
(220, 6, 'Logout', 'User logged out', '127.0.0.1', '2026-02-16 06:27:07'),
(222, 8, 'Profile Updated', 'User profile updated', '127.0.0.1', '2026-02-16 06:33:12'),
(223, 8, 'Profile Updated', 'User profile updated', '127.0.0.1', '2026-02-16 07:07:43'),
(225, 11, 'Login', 'User logged in successfully', '127.0.0.1', '2026-02-16 07:26:48'),
(227, 4, 'Login', 'User logged in successfully', '::1', '2026-02-17 05:26:11'),
(228, 4, 'Logout', 'User logged out', '::1', '2026-02-17 05:26:47'),
(229, 8, 'Login', 'User logged in successfully', '::1', '2026-02-17 05:26:58'),
(230, 4, 'Login', 'User logged in successfully', '::1', '2026-02-17 23:08:44'),
(231, 4, 'Logout', 'User logged out', '::1', '2026-02-17 23:13:08'),
(232, 8, 'Login', 'User logged in successfully', '::1', '2026-02-17 23:13:23'),
(233, 8, 'Logout', 'User logged out', '::1', '2026-02-17 23:13:43'),
(234, 6, 'Login', 'User logged in successfully', '::1', '2026-02-17 23:13:55'),
(235, 6, 'Logout', 'User logged out', '::1', '2026-02-17 23:14:00'),
(236, 6, 'Login', 'User logged in successfully', '::1', '2026-02-17 23:14:09'),
(237, 6, 'Logout', 'User logged out', '::1', '2026-02-17 23:14:42'),
(238, 8, 'Login', 'User logged in successfully', '::1', '2026-02-17 23:14:54'),
(239, 8, 'Logout', 'User logged out', '::1', '2026-02-17 23:15:01'),
(242, 9, 'Login', 'User logged in successfully', '::1', '2026-02-17 23:15:35'),
(243, 9, 'Logout', 'User logged out', '::1', '2026-02-17 23:34:47'),
(246, 6, 'Login', 'User logged in successfully', '::1', '2026-02-17 23:49:40'),
(247, 8, 'Login', 'User logged in successfully', '::1', '2026-02-18 01:21:32'),
(248, 8, 'Logout', 'User logged out', '::1', '2026-02-18 02:50:58'),
(249, 6, 'Logout', 'User logged out', '::1', '2026-02-18 03:54:02'),
(250, 4, 'Login', 'User logged in successfully', '::1', '2026-02-18 03:54:08'),
(251, 4, 'Reset Auth Counters', 'Email: loveresalgen@gmail.com', '::1', '2026-02-18 03:54:23'),
(252, 4, 'Logout', 'User logged out', '::1', '2026-02-18 03:54:27'),
(253, 4, 'Login', 'User logged in successfully', '::1', '2026-02-18 03:54:44'),
(254, 4, 'Unblocked Auth Reset', 'Email: loveresalgen@gmail.com', '::1', '2026-02-18 03:54:51'),
(255, 4, 'Logout', 'User logged out', '::1', '2026-02-18 03:54:54'),
(257, 10, 'Password Reset', 'Password was reset via email verification', '::1', '2026-02-18 03:56:18'),
(258, 10, 'Login', 'User logged in successfully', '::1', '2026-02-18 03:56:29'),
(259, 10, 'Logout', 'User logged out', '::1', '2026-02-18 04:00:34'),
(261, 4, 'Login', 'User logged in successfully', '::1', '2026-02-18 04:01:50'),
(262, 4, 'Unblocked Auth Reset', 'Email: xerdapparel@gmail.com', '::1', '2026-02-18 04:01:58'),
(263, 4, 'Reset Auth Counters', 'Email: xerdapparel@gmail.com', '::1', '2026-02-18 04:02:01'),
(264, 4, 'Unblocked Auth Reset', 'Email: xerdapparel@gmail.com', '::1', '2026-02-18 04:12:51'),
(265, 4, 'Reset Auth Counters', 'Email: xerdapparel@gmail.com', '::1', '2026-02-18 04:12:55'),
(266, 4, 'Logout', 'User logged out', '::1', '2026-02-18 04:16:20'),
(269, 9, 'Login', 'User logged in successfully', '::1', '2026-02-18 04:17:06'),
(270, 6, 'Login', 'User logged in successfully', '::1', '2026-02-18 04:17:36'),
(271, 9, 'Logout', 'User logged out', '::1', '2026-02-18 04:59:25'),
(272, 8, 'Login', 'User logged in successfully', '::1', '2026-02-18 05:33:23'),
(273, 6, 'Logout', 'User logged out', '::1', '2026-02-18 05:34:39'),
(274, 4, 'Login', 'User logged in successfully', '::1', '2026-02-18 05:34:46'),
(275, 4, 'Logout', 'User logged out', '::1', '2026-02-18 05:58:35'),
(276, 6, 'Login', 'User logged in successfully', '::1', '2026-02-18 05:58:41'),
(277, 8, 'Login', 'User logged in successfully', '::1', '2026-02-18 11:04:52'),
(280, 6, 'Login', 'User logged in successfully', '::1', '2026-02-18 11:05:48'),
(281, 6, 'Logout', 'User logged out', '::1', '2026-02-18 11:06:12'),
(282, 14, 'Login', 'User logged in successfully', '::1', '2026-02-18 11:06:22'),
(283, 14, 'Logout', 'User logged out', '::1', '2026-02-18 11:06:39'),
(284, 11, 'Login', 'User logged in successfully', '::1', '2026-02-18 11:06:47'),
(285, 11, 'Logout', 'User logged out', '::1', '2026-02-18 11:11:31'),
(286, 6, 'Login', 'User logged in successfully', '::1', '2026-02-18 11:11:38'),
(287, 8, 'Logout', 'User logged out', '::1', '2026-02-18 11:17:19'),
(288, 11, 'Login', 'User logged in successfully', '::1', '2026-02-18 11:17:38'),
(289, 6, 'Profile Updated', 'Admin profile updated', '::1', '2026-02-18 11:31:07'),
(290, 6, 'Logout', 'User logged out', '::1', '2026-02-18 13:17:08'),
(291, 4, 'Login', 'User logged in successfully', '::1', '2026-02-18 13:17:21'),
(292, 4, 'Logout', 'User logged out', '::1', '2026-02-18 14:22:34'),
(293, 6, 'Login', 'User logged in successfully', '::1', '2026-02-18 14:22:42'),
(294, 11, 'Logout', 'User logged out', '::1', '2026-02-18 14:23:36'),
(295, 8, 'Login', 'User logged in successfully', '::1', '2026-02-18 14:24:02'),
(296, 4, 'Login', 'User logged in successfully', '::1', '2026-02-18 23:12:51'),
(297, 4, 'Profile Updated', 'Admin profile updated', '::1', '2026-02-18 23:25:13'),
(298, 4, 'Updated User Record', 'Updated user: Lorina B. Jurada (ID: 6)', '::1', '2026-02-18 23:28:09'),
(301, 4, 'Deleted User', 'User ID: 7 removed.', '::1', '2026-02-18 23:33:27'),
(302, 4, 'Deleted User', 'User ID: 5 removed.', '::1', '2026-02-18 23:35:18'),
(303, 4, 'Updated User Record', 'Updated user: ZED 2 TEST (ID: 9)', '::1', '2026-02-18 23:37:03'),
(304, 9, 'Login', 'User logged in successfully', '::1', '2026-02-18 23:37:58'),
(305, 9, 'Profile Updated', 'User profile updated', '::1', '2026-02-18 23:38:17'),
(306, 9, 'Profile Updated', 'User profile updated', '::1', '2026-02-18 23:39:18'),
(307, 4, 'Profile Updated', 'Admin profile updated', '::1', '2026-02-18 23:40:24'),
(308, 4, 'Updated User Record', 'Updated user: ZED 2 Test (ID: 9)', '::1', '2026-02-18 23:45:57'),
(309, 4, 'Updated Signatories', 'Administrative signatories updated', '::1', '2026-02-18 23:48:30'),
(310, 9, 'Logout', 'User logged out', '::1', '2026-02-19 00:04:26'),
(311, 8, 'Login', 'User logged in successfully', '::1', '2026-02-19 01:32:08'),
(312, 8, 'Logout', 'User logged out', '::1', '2026-02-19 02:15:58'),
(313, 12, 'Login', 'User logged in successfully', '::1', '2026-02-19 02:16:26'),
(314, 4, 'Logout', 'User logged out', '::1', '2026-02-19 02:18:53'),
(315, 8, 'Login', 'User logged in successfully', '::1', '2026-02-19 02:19:10'),
(316, 12, 'Logout', 'User logged out', '::1', '2026-02-19 02:19:39'),
(317, 6, 'Login', 'User logged in successfully', '::1', '2026-02-19 02:19:47'),
(318, 6, 'Logout', 'User logged out', '::1', '2026-02-19 02:36:49'),
(319, 4, 'Login', 'User logged in successfully', '::1', '2026-02-19 02:36:59'),
(320, 4, 'Logout', 'User logged out', '::1', '2026-02-19 04:36:46'),
(321, 6, 'Login', 'User logged in successfully', '::1', '2026-02-19 04:36:53'),
(322, 8, 'Logout', 'User logged out', '::1', '2026-02-19 05:23:05'),
(323, 9, 'Login', 'User logged in successfully', '::1', '2026-02-19 05:23:16'),
(324, 9, 'Profile Updated', 'User profile updated', '::1', '2026-02-19 05:27:25'),
(325, 6, 'Logout', 'User logged out', '::1', '2026-02-19 05:29:36'),
(326, 6, 'Login', 'User logged in successfully', '::1', '2026-02-19 05:37:49'),
(327, 6, 'Logout', 'User logged out', '::1', '2026-02-19 06:11:23'),
(328, 16, 'Profile Created', 'New account registered and verified via Gmail: lykajane.leosala@deped.gov.ph', '::1', '2026-02-19 06:20:52'),
(329, 16, 'Login', 'User logged in successfully', '::1', '2026-02-19 06:21:14'),
(330, 16, 'Profile Updated', 'User profile updated', '::1', '2026-02-19 06:22:44'),
(331, 9, 'Logout', 'User logged out', '::1', '2026-02-19 06:24:59'),
(332, 6, 'Login', 'User logged in successfully', '::1', '2026-02-19 06:25:08'),
(333, 6, 'update_credits', 'Updated Vacation Leave credits for LJ A. LEOSALA from 0 to 5. (Head HR Override)', '::1', '2026-02-19 06:35:02'),
(334, 6, 'update_credits', 'Updated Sick Leave credits for LJ A. LEOSALA from 0 to 5. (Head HR Override)', '::1', '2026-02-19 06:35:02'),
(335, 6, 'update_credits', 'Updated Maternity Leave credits for LJ A. LEOSALA from 0 to 0. (Head HR Override)', '::1', '2026-02-19 06:35:02'),
(336, 6, 'update_credits', 'Updated Paternity Leave credits for LJ A. LEOSALA from 0 to 0. (Head HR Override)', '::1', '2026-02-19 06:35:02'),
(337, 6, 'update_credits', 'Updated Special Privilege Leave credits for LJ A. LEOSALA from 0 to 0. (Head HR Override)', '::1', '2026-02-19 06:35:02'),
(338, 6, 'update_credits', 'Updated Mandatory/Forced Leave credits for LJ A. LEOSALA from 0 to 0. (Head HR Override)', '::1', '2026-02-19 06:35:02'),
(339, 6, 'update_credits', 'Updated Solo Parent Leave credits for LJ A. LEOSALA from 0 to 0. (Head HR Override)', '::1', '2026-02-19 06:35:02'),
(340, 6, 'update_credits', 'Updated 10-Day VAWC Leave credits for LJ A. LEOSALA from 0 to 0. (Head HR Override)', '::1', '2026-02-19 06:35:02'),
(341, 6, 'update_credits', 'Updated Rehabilitation Privilege credits for LJ A. LEOSALA from 0 to 0. (Head HR Override)', '::1', '2026-02-19 06:35:02'),
(342, 6, 'update_credits', 'Updated Special Leave Benefits for Women credits for LJ A. LEOSALA from 0 to 0. (Head HR Override)', '::1', '2026-02-19 06:35:02'),
(343, 6, 'update_credits', 'Updated Special Emergency (Calamity) Leave credits for LJ A. LEOSALA from 0 to 0. (Head HR Override)', '::1', '2026-02-19 06:35:02'),
(344, 6, 'update_credits', 'Updated Adoption Leave credits for LJ A. LEOSALA from 0 to 0. (Head HR Override)', '::1', '2026-02-19 06:35:02'),
(345, 6, 'update_credits', 'Updated Study Leave credits for LJ A. LEOSALA from 0 to 0. (Head HR Override)', '::1', '2026-02-19 06:35:02'),
(346, 6, 'update_credits', 'Updated Others credits for LJ A. LEOSALA from 0 to 0. (Head HR Override)', '::1', '2026-02-19 06:35:02'),
(347, 6, 'update_credits', 'Updated VAWC Leave (RA 9262) credits for LJ A. LEOSALA from 0 to 0. (Head HR Override)', '::1', '2026-02-19 06:35:02'),
(348, 6, 'update_credits', 'Updated Rehabilitation Leave credits for LJ A. LEOSALA from 0 to 0. (Head HR Override)', '::1', '2026-02-19 06:35:02'),
(349, 6, 'update_credits', 'Updated Wellness Leave credits for LJ A. LEOSALA from 0 to 0. (Head HR Override)', '::1', '2026-02-19 06:35:02'),
(350, 6, 'update_credits', 'Updated absent ka credits for LJ A. LEOSALA from 0 to 0. (Head HR Override)', '::1', '2026-02-19 06:35:02'),
(351, 16, 'Logout', 'User logged out', '::1', '2026-02-19 06:40:01'),
(352, 11, 'Login', 'User logged in successfully', '::1', '2026-02-19 06:40:10'),
(353, 11, 'Logout', 'User logged out', '::1', '2026-02-19 06:41:30'),
(354, 12, 'Login', 'User logged in successfully', '::1', '2026-02-19 06:41:39'),
(355, 12, 'Logout', 'User logged out', '::1', '2026-02-19 06:42:27'),
(356, 16, 'Login', 'User logged in successfully', '::1', '2026-02-19 06:42:34'),
(357, 8, 'Login', 'User logged in successfully', '::1', '2026-02-19 23:16:12'),
(358, 11, 'Login', 'User logged in successfully', '::1', '2026-02-20 01:06:52'),
(359, 11, 'Logout', 'User logged out', '::1', '2026-02-20 01:12:04'),
(360, 6, 'Login', 'User logged in successfully', '::1', '2026-02-20 01:12:11'),
(361, 6, 'Logout', 'User logged out', '::1', '2026-02-20 01:18:05'),
(362, 13, 'Login', 'User logged in successfully', '::1', '2026-02-20 01:18:12'),
(363, 13, 'Logout', 'User logged out', '::1', '2026-02-20 01:18:17'),
(364, 14, 'Login', 'User logged in successfully', '::1', '2026-02-20 01:18:23'),
(365, 14, 'Logout', 'User logged out', '::1', '2026-02-20 01:18:48'),
(366, 11, 'Login', 'User logged in successfully', '::1', '2026-02-20 01:18:54'),
(367, 11, 'Logout', 'User logged out', '::1', '2026-02-20 01:21:20'),
(368, 6, 'Login', 'User logged in successfully', '::1', '2026-02-20 01:21:25'),
(369, 8, 'Logout', 'User logged out', '::1', '2026-02-20 06:06:50'),
(370, 16, 'Login', 'User logged in successfully', '::1', '2026-02-20 06:06:59'),
(371, 6, 'Logout', 'User logged out', '::1', '2026-02-20 06:29:27'),
(372, 4, 'Login', 'User logged in successfully', '::1', '2026-02-20 06:29:34'),
(373, 4, 'Login', 'User logged in successfully', '::1', '2026-02-23 01:36:41'),
(374, 10, 'Login', 'User logged in successfully', '::1', '2026-02-23 01:42:30'),
(375, 10, 'Logout', 'User logged out', '::1', '2026-02-23 01:44:41'),
(376, 9, 'Login', 'User logged in successfully', '::1', '2026-02-23 01:44:54'),
(377, 9, 'Logout', 'User logged out', '::1', '2026-02-23 02:28:16'),
(378, 9, 'Login', 'User logged in successfully', '::1', '2026-02-23 02:44:29'),
(379, 9, 'Profile Updated', 'User profile updated', '::1', '2026-02-23 03:03:16'),
(380, 4, 'Logout', 'User logged out', '::1', '2026-02-23 03:09:06'),
(381, 6, 'Login', 'User logged in successfully', '::1', '2026-02-23 03:09:12'),
(382, 10, 'Login', 'User logged in successfully', '::1', '2026-02-23 05:43:35'),
(383, 6, 'Logout', 'User logged out', '::1', '2026-02-23 05:53:39'),
(384, 4, 'Login', 'User logged in successfully', '::1', '2026-02-23 05:53:49'),
(385, 4, 'Logout', 'User logged out', '::1', '2026-02-23 05:59:06'),
(386, 4, 'Login', 'User logged in successfully', '::1', '2026-02-23 05:59:12'),
(387, 4, 'Logout', 'User logged out', '::1', '2026-02-23 06:11:55'),
(388, 6, 'Login', 'User logged in successfully', '::1', '2026-02-23 06:12:01'),
(389, 6, 'update_credits', 'Updated Vacation Leave credits for Cedrick Velarde Bacaresas from 0 to 12. (Head HR Override)', '::1', '2026-02-23 06:12:57'),
(390, 6, 'update_credits', 'Updated Sick Leave credits for Cedrick Velarde Bacaresas from 0 to 12. (Head HR Override)', '::1', '2026-02-23 06:12:57'),
(391, 6, 'update_credits', 'Updated Maternity Leave credits for Cedrick Velarde Bacaresas from 0 to 0. (Head HR Override)', '::1', '2026-02-23 06:12:57'),
(392, 6, 'update_credits', 'Updated Paternity Leave credits for Cedrick Velarde Bacaresas from 0 to 0. (Head HR Override)', '::1', '2026-02-23 06:12:57'),
(393, 6, 'update_credits', 'Updated Special Privilege Leave credits for Cedrick Velarde Bacaresas from 0 to 0. (Head HR Override)', '::1', '2026-02-23 06:12:57'),
(394, 6, 'update_credits', 'Updated Mandatory/Forced Leave credits for Cedrick Velarde Bacaresas from 0 to 0. (Head HR Override)', '::1', '2026-02-23 06:12:57'),
(395, 6, 'update_credits', 'Updated Solo Parent Leave credits for Cedrick Velarde Bacaresas from 0 to 0. (Head HR Override)', '::1', '2026-02-23 06:12:57'),
(396, 6, 'update_credits', 'Updated 10-Day VAWC Leave credits for Cedrick Velarde Bacaresas from 0 to 0. (Head HR Override)', '::1', '2026-02-23 06:12:57'),
(397, 6, 'update_credits', 'Updated Rehabilitation Privilege credits for Cedrick Velarde Bacaresas from 0 to 0. (Head HR Override)', '::1', '2026-02-23 06:12:57'),
(398, 6, 'update_credits', 'Updated Special Leave Benefits for Women credits for Cedrick Velarde Bacaresas from 0 to 0. (Head HR Override)', '::1', '2026-02-23 06:12:57'),
(399, 6, 'update_credits', 'Updated Special Emergency (Calamity) Leave credits for Cedrick Velarde Bacaresas from 0 to 0. (Head HR Override)', '::1', '2026-02-23 06:12:57'),
(400, 6, 'update_credits', 'Updated Adoption Leave credits for Cedrick Velarde Bacaresas from 0 to 0. (Head HR Override)', '::1', '2026-02-23 06:12:57'),
(401, 6, 'update_credits', 'Updated Study Leave credits for Cedrick Velarde Bacaresas from 0 to 0. (Head HR Override)', '::1', '2026-02-23 06:12:57'),
(402, 6, 'update_credits', 'Updated Others credits for Cedrick Velarde Bacaresas from 0 to 0. (Head HR Override)', '::1', '2026-02-23 06:12:57'),
(403, 6, 'update_credits', 'Updated VAWC Leave (RA 9262) credits for Cedrick Velarde Bacaresas from 0 to 0. (Head HR Override)', '::1', '2026-02-23 06:12:57'),
(404, 6, 'update_credits', 'Updated Rehabilitation Leave credits for Cedrick Velarde Bacaresas from 0 to 0. (Head HR Override)', '::1', '2026-02-23 06:12:57'),
(405, 6, 'update_credits', 'Updated Wellness Leave credits for Cedrick Velarde Bacaresas from 0 to 0. (Head HR Override)', '::1', '2026-02-23 06:12:57'),
(406, 6, 'update_credits', 'Updated Monetization of Leave Credits credits for Cedrick Velarde Bacaresas from 0 to 0. (Head HR Override)', '::1', '2026-02-23 06:12:57'),
(407, 6, 'update_credits', 'Updated Terminal Leave credits for Cedrick Velarde Bacaresas from 0 to 0. (Head HR Override)', '::1', '2026-02-23 06:12:57'),
(408, 10, 'Profile Updated', 'User profile updated', '::1', '2026-02-23 06:13:59'),
(409, 10, 'Logout', 'User logged out', '::1', '2026-02-23 06:15:26'),
(410, 11, 'Login', 'User logged in successfully', '::1', '2026-02-23 06:15:32'),
(411, 11, 'Logout', 'User logged out', '::1', '2026-02-23 06:15:46'),
(412, 12, 'Login', 'User logged in successfully', '::1', '2026-02-23 06:15:54'),
(413, 12, 'Logout', 'User logged out', '::1', '2026-02-23 06:16:04'),
(414, 10, 'Login', 'User logged in successfully', '::1', '2026-02-23 06:16:36'),
(415, 6, 'Logout', 'User logged out', '::1', '2026-02-23 06:17:04'),
(416, 4, 'Login', 'User logged in successfully', '::1', '2026-02-23 06:17:11'),
(417, 10, 'Logout', 'User logged out', '::1', '2026-02-23 07:19:16'),
(418, 9, 'Login', 'User logged in successfully', '::1', '2026-02-23 07:19:35'),
(419, 9, 'Profile Updated', 'User profile updated', '::1', '2026-02-23 07:20:52'),
(420, 4, 'Login', 'User logged in successfully', '::1', '2026-02-23 23:26:29'),
(421, 4, 'Logout', 'User logged out', '::1', '2026-02-23 23:56:58'),
(422, 6, 'Login', 'User logged in successfully', '::1', '2026-02-23 23:57:06'),
(423, 6, 'Logout', 'User logged out', '::1', '2026-02-24 00:07:10'),
(424, 9, 'Login', 'User logged in successfully', '::1', '2026-02-24 00:07:37'),
(425, 9, 'Logout', 'User logged out', '::1', '2026-02-24 00:08:10'),
(426, 8, 'Login', 'User logged in successfully', '::1', '2026-02-24 00:20:55'),
(427, 8, 'Logout', 'User logged out', '::1', '2026-02-24 00:21:37'),
(428, 6, 'Login', 'User logged in successfully', '::1', '2026-02-24 00:21:49'),
(429, 6, 'Logout', 'User logged out', '::1', '2026-02-24 01:39:33'),
(430, 8, 'Login', 'User logged in successfully', '::1', '2026-02-24 01:39:39'),
(431, 8, 'Logout', 'User logged out', '::1', '2026-02-24 02:32:30'),
(432, 6, 'Login', 'User logged in successfully', '::1', '2026-02-24 02:32:36'),
(433, 6, 'Login', 'User logged in successfully', '::1', '2026-02-24 04:50:52'),
(434, 6, 'Logout', 'User logged out', '::1', '2026-02-24 04:52:20'),
(435, 16, 'Login', 'User logged in successfully', '::1', '2026-02-24 04:52:50'),
(436, 16, 'Profile Updated', 'User profile updated', '::1', '2026-02-24 05:03:04'),
(437, 16, 'Logout', 'User logged out', '::1', '2026-02-24 05:03:56'),
(438, 6, 'Login', 'User logged in successfully', '::1', '2026-02-24 05:04:01'),
(439, 6, 'Logout', 'User logged out', '::1', '2026-02-24 05:18:04'),
(440, 4, 'Login', 'User logged in successfully', '::1', '2026-02-24 05:18:18'),
(441, 4, 'Logout', 'User logged out', '::1', '2026-02-24 05:56:50'),
(442, 4, 'Login', 'User logged in successfully', '::1', '2026-02-25 05:25:37'),
(443, 4, 'Logout', 'User logged out', '::1', '2026-02-25 06:53:14'),
(444, 8, 'Login', 'User logged in successfully', '::1', '2026-02-25 06:53:27'),
(445, 8, 'Profile Updated', 'User profile updated', '::1', '2026-02-25 06:54:05'),
(446, 6, 'Login', 'User logged in successfully', '::1', '2026-02-25 06:54:56'),
(447, 6, 'Logout', 'User logged out', '::1', '2026-02-25 06:55:30'),
(448, 11, 'Login', 'User logged in successfully', '::1', '2026-02-25 06:55:39'),
(449, 11, 'Logout', 'User logged out', '::1', '2026-02-25 06:55:55'),
(450, 13, 'Login', 'User logged in successfully', '::1', '2026-02-25 06:56:01'),
(451, 13, 'Logout', 'User logged out', '::1', '2026-02-25 06:56:15'),
(452, 14, 'Login', 'User logged in successfully', '::1', '2026-02-25 06:56:23'),
(453, 14, 'Logout', 'User logged out', '::1', '2026-02-25 06:56:36'),
(454, 11, 'Login', 'User logged in successfully', '::1', '2026-02-25 06:56:44'),
(455, 11, 'Logout', 'User logged out', '::1', '2026-02-25 07:12:20'),
(456, 9, 'Login', 'User logged in successfully', '::1', '2026-02-25 07:15:11'),
(457, 9, 'Logout', 'User logged out', '::1', '2026-02-25 07:15:23'),
(458, 8, 'Login', 'User logged in successfully', '::1', '2026-02-25 07:15:29'),
(459, 8, 'Logout', 'User logged out', '::1', '2026-02-25 07:16:07'),
(460, 16, 'Login', 'User logged in successfully', '::1', '2026-02-25 07:16:13'),
(461, 16, 'Logout', 'User logged out', '::1', '2026-02-25 07:16:30'),
(462, 10, 'Login', 'User logged in successfully', '::1', '2026-02-25 07:16:36'),
(463, 10, 'Logout', 'User logged out', '::1', '2026-02-25 07:17:01'),
(464, 8, 'Logout', 'User logged out', '::1', '2026-02-25 07:17:16'),
(465, 4, 'Login', 'User logged in successfully', '::1', '2026-02-26 00:01:46'),
(466, 4, 'Logout', 'User logged out', '::1', '2026-02-26 05:34:00'),
(467, 8, 'Login', 'User logged in successfully', '::1', '2026-02-26 06:31:55'),
(468, 4, 'Login', 'User logged in successfully', '::1', '2026-02-27 01:22:32'),
(469, 4, 'Login', 'User logged in successfully', '::1', '2026-02-27 01:26:00'),
(470, 4, 'Logout', 'User logged out', '::1', '2026-02-27 01:28:11'),
(471, 8, 'Login', 'User logged in successfully', '::1', '2026-03-02 00:16:38'),
(472, 8, 'Login', 'User logged in successfully', '::1', '2026-03-02 04:49:25'),
(473, 8, 'Logout', 'User logged out', '::1', '2026-03-02 05:55:21'),
(474, 4, 'Login', 'User logged in successfully', '::1', '2026-03-02 06:05:22'),
(475, 4, 'Logout', 'User logged out', '::1', '2026-03-02 06:05:52'),
(476, 8, 'Login', 'User logged in successfully', '::1', '2026-03-02 06:06:00'),
(477, 8, 'Logout', 'User logged out', '::1', '2026-03-02 06:16:58'),
(478, 4, 'Login', 'User logged in successfully', '::1', '2026-03-02 06:17:09'),
(479, 8, 'Login', 'User logged in successfully', '::1', '2026-03-03 23:19:56'),
(480, 8, 'Logout', 'User logged out', '::1', '2026-03-04 00:42:21'),
(481, 10, 'Login', 'User logged in successfully', '::1', '2026-03-04 00:42:45'),
(482, 10, 'Logout', 'User logged out', '::1', '2026-03-04 01:30:12'),
(483, 8, 'Login', 'User logged in successfully', '::1', '2026-03-04 01:30:23'),
(484, 8, 'Login', 'User logged in successfully', '::1', '2026-03-04 07:42:45'),
(485, 4, 'Login', 'User logged in successfully', '::1', '2026-03-05 02:11:13'),
(486, 4, 'Login', 'User logged in successfully', '::1', '2026-03-06 01:08:36'),
(487, 4, 'Login', 'User logged in successfully', '::1', '2026-03-06 02:39:48'),
(488, 4, 'Login', 'User logged in successfully', '::1', '2026-03-06 05:56:06'),
(489, 4, 'Login', 'User logged in successfully', '::1', '2026-03-09 05:19:18'),
(490, 4, 'Logout', 'User logged out', '::1', '2026-03-09 07:00:14'),
(491, 4, 'Login', 'User logged in successfully', '::1', '2026-03-09 07:00:29'),
(492, 4, 'Logout', 'User logged out', '::1', '2026-03-09 07:00:46'),
(493, 10, 'Login', 'User logged in successfully', '::1', '2026-03-09 07:01:02'),
(494, 10, 'Logout', 'User logged out', '::1', '2026-03-09 07:01:26'),
(495, 4, 'Login', 'User logged in successfully', '::1', '2026-03-09 07:01:39'),
(496, 4, 'Login', 'User logged in successfully', '::1', '2026-03-10 00:29:12'),
(497, 4, 'Deactivated User', 'User ID: 10', '::1', '2026-03-10 01:14:51'),
(498, 4, 'Created User', 'User ID: 17 - wewea fdafad adfaf3', '::1', '2026-03-10 01:16:38'),
(499, 4, 'Deactivated User', 'User ID: 17', '::1', '2026-03-10 01:16:55'),
(500, 4, 'Updated User Record', 'Updated user: ERMA S. VALENZUELA (ID: 15)', '::1', '2026-03-10 01:18:21'),
(501, 4, 'Updated User Record', 'Updated user: ERMA S VALENZUELA (ID: 15)', '::1', '2026-03-10 01:20:56'),
(502, 4, 'Updated User Record', 'Updated user: ERMA S VALENZUELA (ID: 15)', '::1', '2026-03-10 01:22:41'),
(503, 4, 'Updated User Record', 'Updated user: ERMA S VALENZUELA (ID: 15)', '::1', '2026-03-10 01:24:26'),
(504, 4, 'Updated User Record', 'Updated user: ERMA S VALENZUELA (ID: 15)', '::1', '2026-03-10 01:27:05'),
(505, 4, 'Updated User Record', 'Updated user: ERMA S VALENZUELA (ID: 15)', '::1', '2026-03-10 01:28:29'),
(506, 4, 'Login', 'User logged in successfully', '::1', '2026-03-10 05:44:09'),
(507, 4, 'Login', 'User logged in successfully', '::1', '2026-03-11 00:46:54'),
(508, 4, 'Login', 'User logged in successfully', '::1', '2026-03-11 01:22:33'),
(509, 4, 'Login', 'User logged in successfully', '::1', '2026-03-11 02:55:14'),
(510, 4, 'Login', 'User logged in successfully', '::1', '2026-03-11 05:18:04'),
(511, 4, 'Profile Updated', 'Admin profile updated', '::1', '2026-03-11 06:10:00'),
(512, 4, 'Updated User Record', 'Updated user: Cedrick V. Bacaresas (ID: 9)', '::1', '2026-03-11 07:31:31'),
(513, 4, 'Updated User Record', 'Updated user: Cedrick V. Bacaresas (ID: 9)', '::1', '2026-03-11 07:32:12'),
(514, 4, 'Logout', 'User logged out', '::1', '2026-03-11 07:32:15'),
(515, 9, 'Login', 'User logged in successfully', '::1', '2026-03-11 07:32:23'),
(516, 4, 'Logout', 'User logged out', '::1', '2026-03-11 08:07:58'),
(517, 9, 'Login', 'User logged in successfully', '::1', '2026-03-11 08:08:16'),
(518, 4, 'Login', 'User logged in successfully', '::1', '2026-03-12 00:40:09'),
(519, 4, 'Logout', 'User logged out', '::1', '2026-03-12 00:41:10'),
(520, 4, 'Login', 'User logged in successfully', '::1', '2026-03-12 00:42:28'),
(521, 4, 'Updated User Record', 'Updated user: Cedrick V. Bacaresas (ID: 9)', '::1', '2026-03-12 00:42:55'),
(522, 4, 'Logout', 'User logged out', '::1', '2026-03-12 00:43:00'),
(523, 4, 'Login', 'User logged in successfully', '::1', '2026-03-12 00:43:23'),
(524, 4, 'Logout', 'User logged out', '::1', '2026-03-12 00:43:30'),
(525, 9, 'Login', 'User logged in successfully', '::1', '2026-03-12 00:44:06'),
(526, 9, 'Login', 'User logged in successfully', '::1', '2026-03-12 00:44:57'),
(527, 9, 'Login', 'User logged in successfully', '::1', '2026-03-12 05:40:26'),
(528, 9, 'Profile Updated', 'User profile updated', '::1', '2026-03-12 07:52:29'),
(529, 4, 'Login', 'User logged in successfully', '::1', '2026-03-16 01:47:48'),
(530, 4, 'Login', 'User logged in successfully', '::1', '2026-03-16 01:48:54'),
(531, 4, 'Logout', 'User logged out', '::1', '2026-03-16 01:49:00'),
(532, 9, 'Login', 'User logged in successfully', '::1', '2026-03-16 01:49:22'),
(533, 9, 'Logout', 'User logged out', '::1', '2026-03-16 06:23:46'),
(534, 4, 'Login', 'User logged in successfully', '::1', '2026-03-16 06:23:58'),
(535, 4, 'Logout', 'User logged out', '::1', '2026-03-16 06:24:45'),
(536, 15, 'Login', 'User logged in successfully', '::1', '2026-03-16 06:25:01'),
(537, 15, 'Logout', 'User logged out', '::1', '2026-03-16 06:37:25'),
(538, 14, 'Login', 'User logged in successfully', '::1', '2026-03-16 06:37:43'),
(539, 14, 'Logout', 'User logged out', '::1', '2026-03-16 07:10:36'),
(540, 4, 'Login', 'User logged in successfully', '::1', '2026-03-16 07:10:44'),
(541, 4, 'Profile Updated', 'Admin profile updated', '::1', '2026-03-16 07:14:09'),
(542, 4, 'Profile Updated', 'Admin profile updated', '::1', '2026-03-16 07:14:10'),
(543, 4, 'Login', 'User logged in successfully', '::1', '2026-03-17 01:01:09'),
(544, 4, 'Logout', 'User logged out', '::1', '2026-03-17 01:48:02'),
(545, 9, 'Login', 'User logged in successfully', '::1', '2026-03-17 01:48:10'),
(546, 9, 'Logout', 'User logged out', '::1', '2026-03-17 05:23:01'),
(547, 9, 'Login', 'User logged in successfully', '::1', '2026-03-17 05:51:09'),
(548, 9, 'Logout', 'User logged out', '::1', '2026-03-17 05:55:03'),
(549, 4, 'Login', 'User logged in successfully', '::1', '2026-03-17 05:55:16'),
(550, 4, 'Logout', 'User logged out', '::1', '2026-03-17 05:55:50');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `compensatory_leave_credits`
--

CREATE TABLE `compensatory_leave_credits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type_id` bigint(20) UNSIGNED NOT NULL,
  `credits` decimal(8,2) NOT NULL,
  `remaining_credits` decimal(8,2) NOT NULL,
  `expiration_date` date NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` enum('Active','Consumed','Expired') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `compensatory_leave_credits`
--

INSERT INTO `compensatory_leave_credits` (`id`, `user_id`, `leave_type_id`, `credits`, `remaining_credits`, `expiration_date`, `remarks`, `status`, `created_at`, `updated_at`) VALUES
(1, 8, 19, 15.00, 15.00, '2027-02-18', NULL, 'Active', '2026-02-18 05:29:35', '2026-02-18 05:29:35'),
(2, 16, 19, 3.00, 3.00, '2027-02-20', NULL, 'Active', '2026-02-19 20:02:25', '2026-02-19 20:02:25'),
(3, 13, 19, 12.00, 12.00, '2027-02-23', NULL, 'Active', '2026-02-22 19:16:45', '2026-02-22 19:16:45'),
(4, 10, 19, 12.00, 11.00, '2027-02-23', NULL, 'Active', '2026-02-22 21:12:40', '2026-02-22 21:16:01');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `category` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `sku`, `description`, `quantity`, `price`, `category`, `created_at`, `updated_at`) VALUES
(1, 'gen', 'sssss', 'wwqeqwewqe', 2, 0.04, 'wewqe', '2026-02-01 15:06:58', '2026-02-01 15:06:58');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_applications`
--

CREATE TABLE `leave_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type_id` bigint(20) UNSIGNED NOT NULL,
  `date_filing` date NOT NULL DEFAULT '2026-02-11',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `dates` text DEFAULT NULL,
  `days_applied` int(11) NOT NULL,
  `commutation` varchar(255) NOT NULL DEFAULT 'Not Requested',
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `recommending_officer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `approving_officer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hr_verified_at` timestamp NULL DEFAULT NULL,
  `hr_verifier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `recommended_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejection_remarks` text DEFAULT NULL,
  `days_with_pay` decimal(8,2) DEFAULT NULL,
  `days_without_pay` decimal(8,2) DEFAULT NULL,
  `others_remarks` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_applications`
--

INSERT INTO `leave_applications` (`id`, `user_id`, `leave_type_id`, `date_filing`, `start_date`, `end_date`, `dates`, `days_applied`, `commutation`, `status`, `created_at`, `updated_at`, `recommending_officer_id`, `approving_officer_id`, `hr_verified_at`, `hr_verifier_id`, `recommended_at`, `approved_at`, `rejected_at`, `rejection_remarks`, `days_with_pay`, `days_without_pay`, `others_remarks`) VALUES
(1, 9, 1, '2026-02-11', '2026-02-19', '2026-02-19', NULL, 1, 'Requested', 'Pending', '2026-02-10 21:35:04', '2026-02-10 21:35:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 9, 2, '2026-02-11', '2026-02-19', '2026-02-27', NULL, 9, 'Requested', 'Pending', '2026-02-10 22:03:28', '2026-02-10 22:03:28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 9, 6, '2026-02-11', '2026-02-19', '2026-02-26', NULL, 8, 'Requested', 'Pending', '2026-02-10 22:36:18', '2026-02-10 22:36:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 9, 1, '2026-02-13', '2026-02-23', '2026-02-27', '[\"2026-02-23\",\"2026-02-24\",\"2026-02-25\",\"2026-02-26\",\"2026-02-27\"]', 5, 'Requested', 'Disapproved', '2026-02-12 20:55:13', '2026-02-15 21:02:50', 13, 11, '2026-02-13 18:53:26', NULL, '2026-02-13 19:21:26', NULL, '2026-02-15 21:02:50', 'yepp', NULL, NULL, NULL),
(5, 9, 2, '2026-02-13', '2026-02-16', '2026-02-17', '[\"2026-02-16\",\"2026-02-17\"]', 2, 'Requested', 'Pending Recommending', '2026-02-12 21:58:48', '2026-02-15 02:31:48', 13, 11, '2026-02-15 02:31:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 8, 2, '2026-02-15', '2026-02-18', '2026-02-18', '[\"2026-02-18\"]', 1, 'Requested', 'Approved', '2026-02-15 02:30:52', '2026-02-15 22:36:51', 14, 11, '2026-02-15 02:31:59', NULL, '2026-02-15 02:32:48', '2026-02-15 22:36:51', NULL, NULL, NULL, 1.00, NULL),
(7, 8, 2, '2026-02-16', '2026-02-25', '2026-02-25', '[\"2026-02-25\"]', 1, 'Not Requested', 'Disapproved', '2026-02-15 19:23:42', '2026-02-15 19:54:30', 14, 11, '2026-02-15 19:36:56', NULL, NULL, NULL, '2026-02-15 19:54:30', 'wala basta bawal mag leave', NULL, NULL, NULL),
(8, 8, 10, '2026-02-16', '2026-02-20', '2026-02-20', '[\"2026-02-20\"]', 1, 'Not Requested', 'Approved', '2026-02-15 20:24:00', '2026-02-15 20:34:38', 14, 11, '2026-02-15 20:32:26', 6, '2026-02-15 20:33:20', '2026-02-15 20:34:38', NULL, NULL, NULL, NULL, NULL),
(9, 8, 1, '2026-02-16', '2026-02-19', '2026-02-20', '[\"2026-02-19\",\"2026-02-20\"]', 2, 'Not Requested', 'Pending Approval', '2026-02-15 21:20:51', '2026-02-18 02:06:34', 14, 11, '2026-02-18 02:06:06', 6, '2026-02-18 02:06:34', NULL, NULL, NULL, NULL, NULL, NULL),
(10, 8, 17, '2026-02-18', '2026-02-25', '2026-02-27', '[\"2026-02-25\",\"2026-02-26\",\"2026-02-27\"]', 3, 'Requested', 'Pending Approval', '2026-02-17 17:20:43', '2026-02-19 16:18:43', 14, 11, '2026-02-19 16:17:57', 6, '2026-02-19 16:18:43', NULL, NULL, NULL, 2.00, 1.00, NULL),
(11, 8, 17, '2026-02-18', '2026-02-25', '2026-02-27', '[\"2026-02-25\",\"2026-02-26\",\"2026-02-27\"]', 3, 'Requested', 'Pending HR', '2026-02-17 17:26:57', '2026-02-17 17:26:57', 14, 11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 16, 1, '2026-02-19', '2026-02-26', '2026-02-27', '[\"2026-02-26\",\"2026-02-27\"]', 2, 'Not Requested', 'Approved', '2026-02-18 21:24:08', '2026-02-18 21:42:17', 11, 12, '2026-02-18 21:39:24', 6, '2026-02-18 21:41:22', '2026-02-18 21:42:17', NULL, NULL, NULL, NULL, NULL),
(15, 8, 17, '2026-02-20', '2026-02-26', '2026-02-27', '[\"2026-02-26\",\"2026-02-27\"]', 2, 'Requested', 'Pending HR', '2026-02-19 16:21:15', '2026-02-19 16:21:15', 14, 11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 8, 20, '2026-02-20', '2026-02-26', '2026-02-27', '[\"2026-02-26\",\"2026-02-27\"]', 2, 'Not Requested', 'Pending HR', '2026-02-19 16:54:52', '2026-02-19 16:54:52', 14, 11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 10, 19, '2026-02-23', '2026-02-27', '2026-02-27', '[\"2026-02-27\"]', 1, 'Not Requested', 'Approved', '2026-02-22 21:14:11', '2026-02-22 21:16:01', 11, 12, '2026-02-22 21:14:41', 6, '2026-02-22 21:15:42', '2026-02-22 21:16:01', NULL, NULL, NULL, NULL, NULL),
(18, 10, 20, '2026-02-23', '2026-02-26', '2026-02-27', '[\"2026-02-26\",\"2026-02-27\"]', 2, 'Requested', 'Pending HR', '2026-02-22 21:43:16', '2026-02-22 21:43:16', 11, 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 10, 19, '2026-02-23', '2026-02-25', '2026-02-27', '[\"2026-02-25\",\"2026-02-26\",\"2026-02-27\"]', 3, 'Not Requested', 'Pending HR', '2026-02-22 21:44:07', '2026-02-22 21:44:07', 11, 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 8, 2, '2026-02-25', '2026-02-27', '2026-02-28', '[\"2026-02-27\",\"2026-02-28\"]', 2, 'Not Requested', 'Approved', '2026-02-24 21:54:36', '2026-02-24 22:01:58', 14, 11, '2026-02-24 21:55:19', 6, '2026-02-24 21:56:30', '2026-02-24 22:01:58', NULL, NULL, 2.00, NULL, NULL),
(21, 9, 1, '2026-03-12', '2026-03-12', '2026-03-14', '[\"2026-03-12\",\"2026-03-13\",\"2026-03-14\"]', 3, 'Not Requested', 'Pending HR', '2026-03-11 19:01:06', '2026-03-11 19:01:06', 13, 11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `leave_credits`
--

CREATE TABLE `leave_credits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type_id` bigint(20) UNSIGNED NOT NULL,
  `credits` decimal(8,2) NOT NULL DEFAULT 0.00,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_credits`
--

INSERT INTO `leave_credits` (`id`, `user_id`, `leave_type_id`, `credits`, `is_locked`, `created_at`, `updated_at`) VALUES
(3, 8, 1, 15.00, 1, '2026-02-12 15:16:34', '2026-02-15 21:15:53'),
(4, 8, 2, 13.00, 1, '2026-02-12 15:16:34', '2026-02-24 22:01:58'),
(5, 8, 3, 0.00, 1, '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(6, 8, 4, 0.00, 1, '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(7, 8, 5, 0.00, 1, '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(8, 8, 6, 0.00, 1, '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(9, 8, 7, 0.00, 1, '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(10, 8, 8, 0.00, 1, '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(12, 8, 10, 0.00, 1, '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(13, 8, 11, 0.00, 1, '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(14, 8, 12, 0.00, 1, '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(15, 8, 13, 0.00, 1, '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(17, 9, 1, 0.00, 1, '2026-02-12 16:32:22', '2026-02-15 18:06:58'),
(18, 9, 2, 15.00, 1, '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(19, 9, 3, 0.00, 1, '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(20, 9, 4, 0.00, 1, '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(21, 9, 5, 0.00, 1, '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(22, 9, 6, 0.00, 1, '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(23, 9, 7, 0.00, 1, '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(24, 9, 8, 0.00, 1, '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(26, 9, 10, 0.00, 1, '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(27, 9, 11, 0.00, 1, '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(28, 9, 12, 0.00, 1, '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(29, 9, 13, 0.00, 1, '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(31, 9, 15, 0.00, 1, '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(32, 9, 16, 0.00, 1, '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(33, 8, 15, 0.00, 1, '2026-02-15 21:15:53', '2026-02-15 21:15:53'),
(34, 8, 16, 0.00, 1, '2026-02-15 21:15:53', '2026-02-15 21:15:53'),
(35, 8, 19, 15.00, 0, '2026-02-18 05:29:35', '2026-02-18 05:29:35'),
(36, 16, 1, 3.00, 1, '2026-02-18 21:35:02', '2026-02-18 21:42:17'),
(37, 16, 2, 5.00, 1, '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(38, 16, 3, 0.00, 1, '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(39, 16, 4, 0.00, 1, '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(40, 16, 5, 0.00, 1, '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(41, 16, 6, 0.00, 1, '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(42, 16, 7, 0.00, 1, '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(43, 16, 8, 0.00, 1, '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(45, 16, 10, 0.00, 1, '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(46, 16, 11, 0.00, 1, '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(47, 16, 12, 0.00, 1, '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(48, 16, 13, 0.00, 1, '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(50, 16, 15, 0.00, 1, '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(51, 16, 16, 0.00, 1, '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(52, 16, 17, 0.00, 1, '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(54, 16, 19, 3.00, 0, '2026-02-19 20:02:25', '2026-02-19 20:02:25'),
(55, 13, 19, 12.00, 0, '2026-02-22 19:16:45', '2026-02-22 19:16:45'),
(56, 10, 19, 11.00, 0, '2026-02-22 21:12:40', '2026-02-22 21:16:01'),
(57, 10, 1, 12.00, 1, '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(58, 10, 2, 12.00, 1, '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(59, 10, 3, 0.00, 1, '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(60, 10, 4, 0.00, 1, '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(61, 10, 5, 0.00, 1, '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(62, 10, 6, 0.00, 1, '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(63, 10, 7, 0.00, 1, '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(64, 10, 8, 0.00, 1, '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(66, 10, 10, 0.00, 1, '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(67, 10, 11, 0.00, 1, '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(68, 10, 12, 0.00, 1, '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(69, 10, 13, 0.00, 1, '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(71, 10, 15, 0.00, 1, '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(72, 10, 16, 0.00, 1, '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(73, 10, 17, 0.00, 1, '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(74, 10, 20, 0.00, 1, '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(75, 10, 21, 0.00, 1, '2026-02-22 21:12:57', '2026-02-22 21:12:57');

-- --------------------------------------------------------

--
-- Table structure for table `leave_credit_audit_logs`
--

CREATE TABLE `leave_credit_audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `actor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `leave_type_name` varchar(255) NOT NULL,
  `previous_value` decimal(8,2) DEFAULT NULL,
  `new_value` decimal(8,2) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_credit_audit_logs`
--

INSERT INTO `leave_credit_audit_logs` (`id`, `actor_id`, `target_user_id`, `action`, `leave_type_name`, `previous_value`, `new_value`, `reason`, `created_at`, `updated_at`) VALUES
(3, NULL, 8, 'allocate', 'Vacation Leave', 0.00, 10.00, 'Initial credit allocation by HR', '2026-02-12 15:16:34', '2026-02-12 15:16:34'),
(4, NULL, 8, 'allocate', 'Sick Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 15:16:34', '2026-02-12 15:16:34'),
(5, NULL, 8, 'allocate', 'Maternity Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(6, NULL, 8, 'allocate', 'Paternity Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(7, NULL, 8, 'allocate', 'Special Privilege Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(8, NULL, 8, 'allocate', 'Mandatory/Forced Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(9, NULL, 8, 'allocate', 'Solo Parent Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(10, NULL, 8, 'allocate', '10-Day VAWC Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(11, NULL, 8, 'allocate', 'Rehabilitation Privilege', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(12, NULL, 8, 'allocate', 'Special Leave Benefits for Women', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(13, NULL, 8, 'allocate', 'Special Emergency (Calamity) Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(14, NULL, 8, 'allocate', 'Adoption Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(15, NULL, 8, 'allocate', 'Study Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(16, NULL, 8, 'allocate', 'Others', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 15:16:35', '2026-02-12 15:16:35'),
(17, NULL, 8, 'update', 'Vacation Leave', 10.00, 15.00, 'Initial credit allocation by HR', '2026-02-12 15:27:24', '2026-02-12 15:27:24'),
(18, 6, 9, 'allocate', 'Vacation Leave', 0.00, 10.00, 'Initial credit allocation by HR', '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(19, 6, 9, 'allocate', 'Sick Leave', 0.00, 15.00, 'Initial credit allocation by HR', '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(20, 6, 9, 'allocate', 'Maternity Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(21, 6, 9, 'allocate', 'Paternity Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(22, 6, 9, 'allocate', 'Special Privilege Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(23, 6, 9, 'allocate', 'Mandatory/Forced Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(24, 6, 9, 'allocate', 'Solo Parent Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(25, 6, 9, 'allocate', '10-Day VAWC Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(26, 6, 9, 'allocate', 'Rehabilitation Privilege', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(27, 6, 9, 'allocate', 'Special Leave Benefits for Women', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(28, 6, 9, 'allocate', 'Special Emergency (Calamity) Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(29, 6, 9, 'allocate', 'Adoption Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(30, 6, 9, 'allocate', 'Study Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(31, 6, 9, 'allocate', 'Others', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(32, 6, 9, 'allocate', 'VAWC Leave (RA 9262)', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(33, 6, 9, 'allocate', 'Rehabilitation Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-12 16:32:22', '2026-02-12 16:32:22'),
(34, 6, 8, 'update', 'Vacation Leave', 0.00, 15.00, 'Initial credit allocation by HR', '2026-02-15 21:15:53', '2026-02-15 21:15:53'),
(35, 6, 8, 'update', 'Sick Leave', 0.00, 15.00, 'Initial credit allocation by HR', '2026-02-15 21:15:53', '2026-02-15 21:15:53'),
(36, 6, 8, 'update', 'Maternity Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-15 21:15:53', '2026-02-15 21:15:53'),
(37, 6, 8, 'update', 'Paternity Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-15 21:15:53', '2026-02-15 21:15:53'),
(38, 6, 8, 'update', 'Special Privilege Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-15 21:15:53', '2026-02-15 21:15:53'),
(39, 6, 8, 'update', 'Mandatory/Forced Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-15 21:15:53', '2026-02-15 21:15:53'),
(40, 6, 8, 'update', 'Solo Parent Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-15 21:15:53', '2026-02-15 21:15:53'),
(41, 6, 8, 'update', '10-Day VAWC Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-15 21:15:53', '2026-02-15 21:15:53'),
(42, 6, 8, 'update', 'Rehabilitation Privilege', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-15 21:15:53', '2026-02-15 21:15:53'),
(43, 6, 8, 'update', 'Special Leave Benefits for Women', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-15 21:15:53', '2026-02-15 21:15:53'),
(44, 6, 8, 'update', 'Special Emergency (Calamity) Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-15 21:15:53', '2026-02-15 21:15:53'),
(45, 6, 8, 'update', 'Adoption Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-15 21:15:53', '2026-02-15 21:15:53'),
(46, 6, 8, 'update', 'Study Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-15 21:15:53', '2026-02-15 21:15:53'),
(47, 6, 8, 'update', 'Others', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-15 21:15:53', '2026-02-15 21:15:53'),
(48, 6, 8, 'allocate', 'VAWC Leave (RA 9262)', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-15 21:15:53', '2026-02-15 21:15:53'),
(49, 6, 8, 'allocate', 'Rehabilitation Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-15 21:15:53', '2026-02-15 21:15:53'),
(50, 6, 8, 'add_cto', 'Compensatory Time Off', 0.00, 15.00, 'Added CTO batch: 15 expiring 2027-02-18', '2026-02-18 05:29:35', '2026-02-18 05:29:35'),
(51, 6, 16, 'allocate', 'Vacation Leave', 0.00, 5.00, 'Initial credit allocation by HR', '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(52, 6, 16, 'allocate', 'Sick Leave', 0.00, 5.00, 'Initial credit allocation by HR', '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(53, 6, 16, 'allocate', 'Maternity Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(54, 6, 16, 'allocate', 'Paternity Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(55, 6, 16, 'allocate', 'Special Privilege Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(56, 6, 16, 'allocate', 'Mandatory/Forced Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(57, 6, 16, 'allocate', 'Solo Parent Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(58, 6, 16, 'allocate', '10-Day VAWC Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(59, 6, 16, 'allocate', 'Rehabilitation Privilege', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(60, 6, 16, 'allocate', 'Special Leave Benefits for Women', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(61, 6, 16, 'allocate', 'Special Emergency (Calamity) Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(62, 6, 16, 'allocate', 'Adoption Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(63, 6, 16, 'allocate', 'Study Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(64, 6, 16, 'allocate', 'Others', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(65, 6, 16, 'allocate', 'VAWC Leave (RA 9262)', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(66, 6, 16, 'allocate', 'Rehabilitation Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(67, 6, 16, 'allocate', 'Wellness Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(68, 6, 16, 'allocate', 'absent ka', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-18 21:35:02', '2026-02-18 21:35:02'),
(69, 12, 16, 'deduction', 'Vacation Leave', 5.00, 3.00, 'Leave Approved: 14', '2026-02-18 21:42:17', '2026-02-18 21:42:17'),
(70, 6, 16, 'add_cto', 'Compensatory Time Off', 0.00, 3.00, 'Added CTO batch: 3 expiring 2027-02-20', '2026-02-19 20:02:25', '2026-02-19 20:02:25'),
(71, 6, 13, 'add_coc', 'COC Compensatory Overtime Credit', 0.00, 12.00, 'Added COC batch: 12 expiring 2027-02-23', '2026-02-22 19:16:45', '2026-02-22 19:16:45'),
(72, 6, 10, 'add_coc', 'COC Compensatory Overtime Credit', 0.00, 12.00, 'Added COC batch: 12 expiring 2027-02-23', '2026-02-22 21:12:41', '2026-02-22 21:12:41'),
(73, 6, 10, 'allocate', 'Vacation Leave', 0.00, 12.00, 'Initial credit allocation by HR', '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(74, 6, 10, 'allocate', 'Sick Leave', 0.00, 12.00, 'Initial credit allocation by HR', '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(75, 6, 10, 'allocate', 'Maternity Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(76, 6, 10, 'allocate', 'Paternity Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(77, 6, 10, 'allocate', 'Special Privilege Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(78, 6, 10, 'allocate', 'Mandatory/Forced Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(79, 6, 10, 'allocate', 'Solo Parent Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(80, 6, 10, 'allocate', '10-Day VAWC Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(81, 6, 10, 'allocate', 'Rehabilitation Privilege', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(82, 6, 10, 'allocate', 'Special Leave Benefits for Women', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(83, 6, 10, 'allocate', 'Special Emergency (Calamity) Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(84, 6, 10, 'allocate', 'Adoption Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(85, 6, 10, 'allocate', 'Study Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(86, 6, 10, 'allocate', 'Others', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(87, 6, 10, 'allocate', 'VAWC Leave (RA 9262)', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(88, 6, 10, 'allocate', 'Rehabilitation Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(89, 6, 10, 'allocate', 'Wellness Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(90, 6, 10, 'allocate', 'Monetization of Leave Credits', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(91, 6, 10, 'allocate', 'Terminal Leave', 0.00, 0.00, 'Initial credit allocation by HR', '2026-02-22 21:12:57', '2026-02-22 21:12:57'),
(92, 12, 10, 'deduction', 'COC Compensatory Overtime Credit', 12.00, 11.00, 'Leave Approved: 17', '2026-02-22 21:16:01', '2026-02-22 21:16:01'),
(95, 11, 8, 'deduction', 'Sick Leave', 15.00, 13.00, 'Leave Approved: 20', '2026-02-24 22:01:58', '2026-02-24 22:01:58');

-- --------------------------------------------------------

--
-- Table structure for table `leave_credit_policies`
--

CREATE TABLE `leave_credit_policies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `leave_type_id` bigint(20) UNSIGNED NOT NULL,
  `accrual_rate` decimal(8,2) NOT NULL DEFAULT 0.00,
  `accrual_period` varchar(255) NOT NULL DEFAULT 'Monthly',
  `expiration_rule` varchar(255) NOT NULL DEFAULT 'None',
  `expiration_date` date DEFAULT NULL,
  `max_credits` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_credit_policies`
--

INSERT INTO `leave_credit_policies` (`id`, `leave_type_id`, `accrual_rate`, `accrual_period`, `expiration_rule`, `expiration_date`, `max_credits`, `created_at`, `updated_at`) VALUES
(1, 1, 1.25, 'Monthly', 'None', NULL, NULL, '2026-02-12 15:57:30', '2026-02-19 16:02:32'),
(2, 2, 1.25, 'Monthly', 'None', NULL, NULL, '2026-02-12 15:57:30', '2026-02-12 15:57:30'),
(3, 3, 0.00, 'None', 'None', NULL, NULL, '2026-02-12 15:57:30', '2026-02-19 16:02:32'),
(4, 4, 0.00, 'None', 'None', NULL, 7.00, '2026-02-12 15:57:30', '2026-02-19 16:02:32'),
(5, 5, 3.00, 'Yearly', 'Yearly', NULL, 3.00, '2026-02-12 15:57:30', '2026-02-19 16:02:33'),
(6, 7, 7.00, 'Yearly', 'Yearly', NULL, 7.00, '2026-02-12 15:57:30', '2026-02-19 16:02:33'),
(7, 13, 0.00, 'None', 'None', NULL, NULL, '2026-02-12 15:57:30', '2026-02-19 16:02:33'),
(8, 15, 10.00, 'Yearly', 'Yearly', NULL, 10.00, '2026-02-12 15:57:30', '2026-02-19 16:02:33'),
(9, 16, 0.00, 'None', 'None', NULL, NULL, '2026-02-12 15:57:30', '2026-02-19 16:02:33'),
(10, 10, 0.00, 'None', 'None', NULL, 60.00, '2026-02-12 15:57:30', '2026-02-19 16:02:33'),
(11, 11, 5.00, 'Yearly', 'Yearly', NULL, 5.00, '2026-02-12 15:57:30', '2026-02-19 16:02:33'),
(12, 12, 0.00, 'None', 'None', NULL, NULL, '2026-02-12 15:57:30', '2026-02-19 16:02:33'),
(13, 6, 5.00, 'Yearly', 'Yearly', NULL, 5.00, '2026-02-12 16:38:28', '2026-02-17 15:36:34'),
(14, 8, 0.00, 'Monthly', 'None', NULL, NULL, '2026-02-17 15:37:05', '2026-02-17 15:37:05'),
(15, 17, 5.00, 'Yearly', 'Yearly', NULL, 5.00, '2026-02-17 17:09:20', '2026-02-17 17:09:20'),
(17, 20, 0.00, 'None', 'None', NULL, NULL, '2026-02-19 16:02:33', '2026-02-19 16:02:33'),
(18, 21, 0.00, 'None', 'None', NULL, NULL, '2026-02-19 16:02:33', '2026-02-19 16:02:33');

-- --------------------------------------------------------

--
-- Table structure for table `leave_details_form6`
--

CREATE TABLE `leave_details_form6` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `leave_application_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type_name` varchar(255) DEFAULT NULL,
  `vacation_loc_type` varchar(255) DEFAULT NULL,
  `vacation_loc_details` varchar(255) DEFAULT NULL,
  `sick_loc_type` varchar(255) DEFAULT NULL,
  `sick_illness` varchar(255) DEFAULT NULL,
  `women_illness` varchar(255) DEFAULT NULL,
  `study_type` varchar(255) DEFAULT NULL,
  `study_details` varchar(255) DEFAULT NULL,
  `other_purpose` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_details_form6`
--

INSERT INTO `leave_details_form6` (`id`, `leave_application_id`, `leave_type_name`, `vacation_loc_type`, `vacation_loc_details`, `sick_loc_type`, `sick_illness`, `women_illness`, `study_type`, `study_details`, `other_purpose`, `created_at`, `updated_at`) VALUES
(1, 1, 'Vacation Leave', 'Philippines', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-10 21:35:04', '2026-02-10 21:35:04'),
(2, 2, 'Sick Leave', NULL, NULL, 'Out Patient', 'AGUY', NULL, NULL, NULL, NULL, '2026-02-10 22:03:28', '2026-02-10 22:03:28'),
(3, 3, 'Mandatory/Forced Leave', 'Abroad', 'cavite', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-10 22:36:18', '2026-02-10 22:36:18'),
(4, 4, 'Vacation Leave', 'Abroad', 'binan', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-12 20:55:13', '2026-02-12 20:55:13'),
(5, 5, 'Sick Leave', NULL, NULL, 'Hospital', 'muwamuwa', NULL, NULL, NULL, NULL, '2026-02-12 21:58:48', '2026-02-12 21:58:48'),
(6, 6, 'Sick Leave', NULL, NULL, 'Hospital', 'AGUY', NULL, NULL, NULL, NULL, '2026-02-15 02:30:52', '2026-02-15 02:30:52'),
(7, 7, 'Sick Leave', NULL, NULL, 'Out Patient', 'muwamuwawuwauwa', NULL, NULL, NULL, NULL, '2026-02-15 19:23:42', '2026-02-15 19:23:42'),
(8, 8, 'Special Leave Benefits for Women', NULL, NULL, NULL, NULL, 'Masaket puson', NULL, NULL, NULL, '2026-02-15 20:24:00', '2026-02-15 20:24:00'),
(9, 9, 'Vacation Leave', 'Philippines', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-15 21:20:51', '2026-02-15 21:20:51'),
(10, 10, 'Wellness Leave', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-17 17:20:43', '2026-02-17 17:20:43'),
(11, 11, 'Wellness Leave', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-17 17:26:57', '2026-02-17 17:26:57'),
(12, 12, 'Others', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'COMPENSATORY TIME OFF', '2026-02-17 20:57:48', '2026-02-17 20:57:48'),
(13, 13, 'Others', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'COMPENSATORY TIME OFF', '2026-02-18 02:05:07', '2026-02-18 02:05:07'),
(14, 14, 'Vacation Leave', 'Philippines', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-18 21:24:08', '2026-02-18 21:24:08'),
(15, 15, 'Wellness Leave', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-19 16:21:15', '2026-02-19 16:21:15'),
(16, 16, 'Monetization of Leave Credits', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-19 16:54:53', '2026-02-19 16:54:53'),
(17, 17, 'COC Compensatory Overtime Credit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-22 21:14:11', '2026-02-22 21:14:11'),
(18, 18, 'Monetization of Leave Credits', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-22 21:43:16', '2026-02-22 21:43:16'),
(19, 19, 'COC Compensatory Overtime Credit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-22 21:44:07', '2026-02-22 21:44:07'),
(20, 20, 'Sick Leave', NULL, NULL, 'Hospital', NULL, NULL, NULL, NULL, NULL, '2026-02-24 21:54:36', '2026-02-24 21:54:36'),
(21, 21, 'Vacation Leave', 'Philippines', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-11 19:01:06', '2026-03-11 19:01:06');

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'Statutory'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`id`, `type_name`, `description`, `is_active`, `created_at`, `updated_at`, `category`) VALUES
(1, 'Vacation Leave', 'Leave for personal reasons, travel, or rest. Accrues 1.25/month. Requires 5 days advance filing.', 1, '2026-02-10 21:30:14', '2026-02-12 16:28:46', 'Credit'),
(2, 'Sick Leave', 'Leave for illness or medical check-ups. Accrues 1.25/month.', 1, '2026-02-10 21:30:14', '2026-02-12 16:28:46', 'Credit'),
(3, 'Maternity Leave', '105 days with full pay for female employees.', 1, '2026-02-10 21:30:14', '2026-02-12 15:57:30', 'Statutory'),
(4, 'Paternity Leave', '7 days for married male employees.', 1, '2026-02-10 21:30:14', '2026-02-12 15:57:30', 'Statutory'),
(5, 'Special Privilege Leave', '3 days non-cumulative leave for personal milestones.', 1, '2026-02-10 21:30:14', '2026-02-12 15:57:30', 'Statutory'),
(6, 'Mandatory/Forced Leave', 'Standard CS Form 6 Leave Type', 1, '2026-02-10 21:30:14', '2026-02-10 21:30:14', 'Statutory'),
(7, 'Solo Parent Leave', '7 days for solo parents (renewable annually).', 1, '2026-02-10 21:30:14', '2026-02-12 15:57:30', 'Statutory'),
(8, '10-Day VAWC Leave', 'Standard CS Form 6 Leave Type', 1, '2026-02-10 21:30:14', '2026-02-10 21:30:14', 'Statutory'),
(10, 'Special Leave Benefits for Women', 'Up to 2 months for gynecological surgeries (RA 9710).', 1, '2026-02-10 21:30:14', '2026-02-12 15:57:30', 'Statutory'),
(11, 'Special Emergency (Calamity) Leave', '5 days max for employees affected by natural disasters.', 1, '2026-02-10 21:30:14', '2026-02-12 15:57:30', 'Statutory'),
(12, 'Adoption Leave', 'Leave for adoptive parents.', 1, '2026-02-10 21:30:14', '2026-02-12 15:57:30', 'Statutory'),
(13, 'Study Leave', 'Up to 6 months to review for Bar/Board exams or complete degrees.', 1, '2026-02-10 21:30:14', '2026-02-12 15:57:30', 'Statutory'),
(15, 'VAWC Leave (RA 9262)', '10 days for victims of violence against women and children.', 1, '2026-02-12 15:57:30', '2026-02-12 15:57:30', 'Statutory'),
(16, 'Rehabilitation Leave', 'Up to 6 months for work-related injuries.', 1, '2026-02-12 15:57:30', '2026-02-12 15:57:30', 'Statutory'),
(17, 'Wellness Leave', 'Special leave for health and wellness. 5 days per year, max 3 consecutive days.', 1, '2026-02-17 17:09:20', '2026-02-17 17:09:20', 'Non-Cummulative'),
(19, 'COC Compensatory Overtime Credit', 'COC - Manual Entry', 1, '2026-02-18 05:28:27', '2026-02-22 19:13:10', 'Statutory'),
(20, 'Monetization of Leave Credits', 'Payment in cash of the money value of the unused vacation/sick leave credits of an employee.', 1, '2026-02-19 16:02:33', '2026-02-19 16:02:33', 'Statutory'),
(21, 'Terminal Leave', 'Leave credits of an employee who retires, resigns, or is separated from the service through no fault of his own.', 1, '2026-02-19 16:02:33', '2026-02-19 16:02:33', 'Statutory');

-- --------------------------------------------------------

--
-- Table structure for table `leave_update_requests`
--

CREATE TABLE `leave_update_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `requester_id` bigint(20) UNSIGNED DEFAULT NULL,
  `approver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target_user_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type_id` bigint(20) UNSIGNED NOT NULL,
  `reason` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_update_requests`
--

INSERT INTO `leave_update_requests` (`id`, `requester_id`, `approver_id`, `target_user_id`, `leave_type_id`, `reason`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 6, 8, 1, 'PLEASEEE', 'Approved', '2026-02-12 15:17:04', '2026-02-12 15:27:02');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_02_02_000001_create_items_table', 2),
(5, '2026_02_09_000001_create_leave_applications_table', 3),
(6, '2026_02_09_000002_add_department_position_to_users_table', 4),
(7, '2026_02_10_000001_extend_users_table', 5),
(8, '2026_02_10_000002_create_offices_table', 5),
(9, '2026_02_10_000003_create_activity_logs_table', 5),
(10, '2026_02_10_000004_create_notifications_table', 5),
(11, '2026_02_10_000005_create_password_resets_table', 5),
(12, '2026_02_10_000006_create_registration_request_logs_table', 5),
(13, '2026_02_10_000007_create_reset_request_logs_table', 5),
(14, '2026_02_10_000008_create_security_tracking_table', 5),
(15, '2026_02_10_000009_create_user_needs_table', 5),
(16, '2026_02_11_000001_create_leave_system_tables', 6),
(17, '2026_02_11_000002_add_name_fields_to_users_table', 7),
(18, '2026_02_11_000003_add_salary_and_signatories', 8),
(19, '2026_02_12_000001_add_title_to_signatories', 9),
(20, '2026_02_12_022901_add_dates_column_to_leave_applications_table', 10),
(21, '2026_02_12_073554_create_leave_credits_tables', 11),
(22, '2026_02_13_012537_add_category_to_leave_types_table', 12),
(23, '2026_02_13_032634_add_workflow_columns_to_leave_applications_table', 13),
(24, '2026_02_13_035243_add_approvers_to_users_table', 14),
(25, '2026_02_14_040317_add_esignature_to_users_table', 15),
(26, '2026_02_14_044741_add_hr_verifier_to_leave_applications_table', 16),
(27, '2026_02_16_073322_add_pay_details_to_leave_applications_table', 17),
(28, '2026_02_18_020400_add_wellness_leave_type', 18),
(29, '2026_02_18_030000_create_compensatory_leave_credits_table', 19),
(30, '2026_02_18_233213_fix_actor_id_foreign_key_on_audit_logs', 20);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `recipient_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `sender_id`, `recipient_id`, `message`, `is_read`, `created_at`) VALUES
(1, 10, 6, 'A new account has been created and verified: cedrick bacaresas (loveresalgen@gmail.com)', 0, '2026-02-10 23:32:37'),
(2, 16, 6, 'A new account has been created and verified: LJ A. LEOSALA (lykajane.leosala@deped.gov.ph)', 0, '2026-02-19 06:20:52');

-- --------------------------------------------------------

--
-- Table structure for table `offices`
--

CREATE TABLE `offices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `offices`
--

INSERT INTO `offices` (`id`, `category`, `name`, `created_at`) VALUES
(1, 'OSDS', 'ADMINISTRATIVE', '2026-02-10 11:09:28'),
(2, 'OSDS', 'ADMINISTRATIVE (PERSONEL)', '2026-02-10 11:09:28'),
(3, 'OSDS', 'ADMINISTRATIVE (PROPERTY AND SUPPLY)', '2026-02-10 11:09:28'),
(4, 'OSDS', 'ADMINISTRATIVE (RECORDS)', '2026-02-10 11:09:28'),
(5, 'OSDS', 'ADMINISTRATIVE (CASH)', '2026-02-10 11:09:28'),
(6, 'OSDS', 'ADMINISTRATIVE (PROCUREMENT)', '2026-02-10 11:09:28'),
(7, 'OSDS', 'ADMINISTRATIVE (GENERAL SERVICES)', '2026-02-10 11:09:28'),
(8, 'OSDS', 'FINANCE (ACCOUNTING)', '2026-02-10 11:09:28'),
(9, 'OSDS', 'FINANCE (BUDGET)', '2026-02-10 11:09:28'),
(10, 'OSDS', 'LEGAL', '2026-02-10 11:09:28'),
(11, 'OSDS', 'ICT', '2026-02-10 11:09:28'),
(12, 'SGOD', 'SGOD (SCHOOL MANAGEMENT MONITORING & EVALUATION)', '2026-02-10 11:09:28'),
(13, 'SGOD', 'SGOD (HUMAN RESOURCES DEVELOPMENT)', '2026-02-10 11:09:28'),
(14, 'SGOD', 'SGOD (SOCIAL MOBILIZATION AND NETWORKING)', '2026-02-10 11:09:28'),
(15, 'SGOD', 'SGOD (PLANNING AND RESEARCH)', '2026-02-10 11:09:28'),
(16, 'SGOD', 'SGOD (DISASTER RISK REDUCTION AND MANAGEMENT)', '2026-02-10 11:09:28'),
(17, 'SGOD', 'SGOD (EDUCATION FACILITIES)', '2026-02-10 11:09:28'),
(18, 'SGOD', 'SGOD (SCHOOL HEALTH AND NUTRITION)', '2026-02-10 11:09:28'),
(19, 'SGOD', 'SGOD (SCHOOL HEALTH AND NUTRITION) (DENTAL)', '2026-02-10 11:09:28'),
(20, 'SGOD', 'SGOD (SCHOOL HEALTH AND NUTRITION) (MEDICAL)', '2026-02-10 11:09:28'),
(21, 'CID', 'CID (INSTRUCTIONAL MANAGEMENT)', '2026-02-10 11:09:28'),
(22, 'CID', 'CID (LEARNING RESOURCES MANAGEMENT)', '2026-02-10 11:09:28'),
(23, 'CID', 'CID (ALTERNATIVE LEARNING SYSTEM)', '2026-02-10 11:09:28'),
(24, 'CID', 'CID (DISTRICT INSTRUCTIONAL SUPERVISION)', '2026-02-10 11:09:28');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `token` varchar(100) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `attempts` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `username`, `token`, `expires_at`, `created_at`, `attempts`) VALUES
(13, 'Leona', '483901', '2026-02-18 04:06:09', '2026-02-18 04:01:09', 0),
(15, 'tried', '550439', '2026-02-18 04:13:23', '2026-02-18 04:08:23', 0);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registration_request_logs`
--

CREATE TABLE `registration_request_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `registration_request_logs`
--

INSERT INTO `registration_request_logs` (`id`, `email`, `requested_at`) VALUES
(1, 'loveresalgen@gmail.com', '2026-02-10 23:17:20'),
(2, 'loveresalgen@gmail.com', '2026-02-10 23:18:15'),
(3, 'loveresalgen@gmail.com', '2026-02-10 23:29:22'),
(4, 'flickhistories@gmail.com', '2026-02-11 00:03:42'),
(5, 'flickhistories@gmail.com', '2026-02-11 00:05:21'),
(6, 'flickhistories@gmail.com', '2026-02-11 00:05:56'),
(7, 'loveresalgen@deped.gov.ph', '2026-02-19 01:16:55'),
(8, 'lykajane.leosala@deped.gov.ph', '2026-02-19 06:20:16');

-- --------------------------------------------------------

--
-- Table structure for table `reset_request_logs`
--

CREATE TABLE `reset_request_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `type` enum('request','resend') NOT NULL DEFAULT 'request',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reset_request_logs`
--

INSERT INTO `reset_request_logs` (`id`, `email`, `type`, `requested_at`) VALUES
(1, 'relational02@gmail.com', 'request', '2026-02-10 23:49:09'),
(2, 'xerdapparel@gmail.com', 'request', '2026-02-11 00:28:09'),
(3, 'xerdapparel@gmail.com', 'request', '2026-02-11 00:39:05'),
(4, 'xerdapparel@gmail.com', 'resend', '2026-02-11 00:39:41'),
(5, 'xerdapparel@gmail.com', 'request', '2026-02-11 01:06:47'),
(6, 'xerdapparel@gmail.com', 'request', '2026-02-11 01:32:52'),
(7, 'flickhistories@gmail.com', 'request', '2026-02-12 01:02:14'),
(8, 'xerdapparel@gmail.com', 'request', '2026-02-18 02:51:15'),
(9, 'xerdapparel@gmail.com', 'resend', '2026-02-18 02:53:40'),
(10, 'loveresalgen@gmail.com', 'request', '2026-02-18 03:32:23'),
(11, 'loveresalgen@gmail.com', 'request', '2026-02-18 03:49:19'),
(12, 'loveresalgen@gmail.com', 'request', '2026-02-18 03:55:00'),
(13, 'kokoee972@gmail.com', 'request', '2026-02-18 04:01:09'),
(14, 'xerdapparel@gmail.com', 'request', '2026-02-18 04:02:20'),
(15, 'xerdapparel@gmail.com', 'request', '2026-02-18 04:08:23'),
(16, 'xerdapparel@gmail.com', 'resend', '2026-02-18 04:13:03');

-- --------------------------------------------------------

--
-- Table structure for table `security_tracking`
--

CREATE TABLE `security_tracking` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `page_visits` int(11) NOT NULL DEFAULT 0,
  `otp_requests` int(11) NOT NULL DEFAULT 0,
  `otp_inputs` int(11) NOT NULL DEFAULT 0,
  `resends` int(11) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `is_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `security_tracking`
--

INSERT INTO `security_tracking` (`id`, `email`, `page_visits`, `otp_requests`, `otp_inputs`, `resends`, `status`, `is_blocked`, `last_activity`) VALUES
(1, 'xerdapparel@gmail.com', 1, 1, 0, 1, 'Active', 0, '2026-02-18 04:13:03'),
(2, 'flickhistories@gmail.com', 1, 1, 2, 0, 'Active', 0, '2026-02-12 01:02:53'),
(3, 'loveresalgen@gmail.com', 2, 1, 2, 0, 'Active', 0, '2026-02-18 03:56:16'),
(4, 'kokoee972@gmail.com', 1, 1, 0, 0, 'Active', 0, '2026-02-18 04:01:09');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('dyZpSXughi8TywH6Gso1P6JYVfvxYPMef1v1Dc61', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMDVKS0ozNHRGZndRa2diSVNBM2Fkd09jc0w0MkJDSXR0R05rYlM5MSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly9sb2NhbGhvc3QvZS1sZWF2ZS9pbmRleC5waHAvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO319', 1773726950);

-- --------------------------------------------------------

--
-- Table structure for table `signatories`
--

CREATE TABLE `signatories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `position` varchar(100) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `signatories`
--

INSERT INTO `signatories` (`id`, `position`, `title`, `name`, `created_at`, `updated_at`) VALUES
(1, 'CID CHIEF', 'CHIEF EDUCATION SUPERVISOR, CID', 'ERMA S. VALENZUELA', '2026-02-10 22:27:51', '2026-02-18 14:48:30'),
(2, 'SGOD CHIEF', 'CHIEF EDUCATION SUPERVISOR, SGOD', 'FREDERICK G. BYRD JR.', '2026-02-10 22:27:51', '2026-02-18 14:48:30'),
(3, 'AO', 'ADMINISTRATIVE OFFICER V', 'PAUL JEREMY I. AGUJA', '2026-02-10 22:27:51', '2026-02-11 15:21:29'),
(4, 'ASDS', 'ASST. SCHOOLS DIVISION SUPERINTENDENT', 'JOE-BREN L. CONSUELO', '2026-02-10 22:27:51', '2026-02-11 15:23:59'),
(5, 'SDS', 'SCHOOLS DIVISION SUPERINTENDENT', 'PHILIP B. GALLENDEZ', '2026-02-10 22:27:51', '2026-02-11 15:21:29'),
(6, 'Verifier of Leave Credits', 'ADMINISTRATIVE OFFICER IV', 'LORINA B. JURADA', '2026-02-11 15:11:50', '2026-02-11 15:21:29');

-- --------------------------------------------------------

--
-- Table structure for table `todtr`
--

CREATE TABLE `todtr` (
  `Name` varchar(255) DEFAULT NULL,
  `Employee_number` varchar(100) DEFAULT NULL,
  `DateOfLeave` varchar(255) DEFAULT NULL,
  `TypeOfLeave` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `todtr`
--

INSERT INTO `todtr` (`Name`, `Employee_number`, `DateOfLeave`, `TypeOfLeave`) VALUES
('Leona 1 Test', '', '2026-02-18', 'Sick Leave'),
('Leona 1 Test', '', '2026-02-20', 'Special Leave Benefits for Women'),
('LJ A. LEOSALA', '', '2026-02-26 to 2026-02-27', 'Vacation Leave'),
('Cedrick Velarde Bacaresas', '', '2026-02-27', 'COC Compensatory Overtime Credit'),
('Leona 1 Test', '1003232', '2026-02-27, 2026-02-28', 'Sick Leave');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `gmail` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `salary` varchar(50) DEFAULT NULL,
  `recommending_approver` varchar(100) DEFAULT NULL,
  `final_approver` varchar(100) DEFAULT NULL,
  `employee_number` varchar(100) DEFAULT NULL,
  `rating_period` varchar(100) DEFAULT NULL,
  `area_of_specialization` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `sex` varchar(20) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `profile_picture` varchar(255) DEFAULT NULL,
  `esignature` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `passkey` varchar(6) DEFAULT NULL,
  `passkey_expires_at` datetime DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `office_station` varchar(100) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `recommending_officer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `approving_officer_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `first_name`, `middle_name`, `last_name`, `name`, `gmail`, `department`, `position`, `salary`, `recommending_approver`, `final_approver`, `employee_number`, `rating_period`, `area_of_specialization`, `age`, `sex`, `role`, `profile_picture`, `esignature`, `is_active`, `created_by`, `passkey`, `passkey_expires_at`, `email_verified_at`, `password`, `full_name`, `office_station`, `remember_token`, `created_at`, `updated_at`, `recommending_officer_id`, `approving_officer_id`) VALUES
(4, 'super_admin', NULL, NULL, NULL, 'Super Administrator', 'eegeenn@gmail.com', NULL, 'System Administrator', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'super_admin', NULL, 'storage/esignatures/esignature_1773645250.png', 1, NULL, NULL, NULL, NULL, '$2y$12$.92yq4BFaNqRR35KrUQqxuKmHjgpwPSF5tK5hqST/dAvIQ734DoEK', 'IT OFFICER 1', 'ICT', NULL, '2026-02-10 02:11:49', '2026-03-15 23:14:10', NULL, NULL),
(6, 'HR PERSONNEL', 'Lorina', 'B.', 'Jurada', 'Lorina B. Jurada', 'relational02@gmail.com', NULL, 'ADMINISTRATIVE OFFICER IV', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'head_hr', NULL, 'storage/esignatures/esignature_1771414267.png', 1, NULL, NULL, NULL, NULL, '$2y$12$IWpEO0ijm9m63worg9ILAeEiXYY1lRkxWDVs.mZmK5c4q4BwW3bui', 'Lorina B. Jurada', 'ADMINISTRATIVE (PERSONEL)', NULL, '2026-02-10 02:11:50', '2026-02-18 14:28:09', NULL, NULL),
(8, 'Leona', 'Leona', '1', 'Test', 'Leona 1 Test', 'kokoee972@gmail.com', NULL, 'Staff', 'SG 10', NULL, NULL, '1003232', NULL, NULL, NULL, NULL, 'user', 'storage/profile_pics/avatar_8_1771155026.jpg', 'storage/esignatures/sign_8_1771225663.png', 1, NULL, NULL, NULL, NULL, '$2y$12$j9SSdN68/KFOlj9rjSWDy.0sKdfbFe/P3L5lXNdo2LiHFEvG/O8.m', 'Leona 1 Test', 'ICT', NULL, '2026-02-10 02:11:50', '2026-02-24 21:54:05', 14, 11),
(9, 'ced123', 'Cedrick', 'V.', 'Bacaresas', 'Cedrick V. Bacaresas', 'xerdapparel@gmail.com', NULL, 'Intern', 'SG 19-1', 'SGOD CHIEF', 'ASDS', '1000042', NULL, NULL, NULL, NULL, 'user', 'storage/profile_pics/avatar_9_1773301949.png', 'storage/esignatures/sign_9_1771831252.png', 1, 4, NULL, NULL, NULL, '$2y$12$54OltOaFYmuWcGKffZfqJeFj21XRjXBOfqtFhyD2BzAjDvNprI9Hy', 'Cedrick V. Bacaresas', 'LEGAL', NULL, '2026-02-10 05:34:23', '2026-03-11 23:52:29', 13, 11),
(10, 'Ced', 'Cedrick', 'Velarde', 'Bacaresas', 'Cedrick Velarde Bacaresas', 'loveresalgen@gmail.com', NULL, 'HR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'user', NULL, NULL, 0, NULL, NULL, NULL, NULL, '$2y$12$L1x54N05O30R8B5Q.tI8OeikCiVcGHMnNm4b3l8STS4g8PPGNDuky', 'Cedrick Velarde Bacaresas', 'LEGAL', NULL, '2026-02-10 14:32:37', '2026-03-09 17:14:51', 11, 12),
(11, 'ASST. SCHOOL DIVISION SUPERINTENDENT', 'JOE-BREN', 'L.', 'CONSUELO', 'JOE-BREN L. CONSUELO', 'TEMPASDS@GMAIL.COM', NULL, 'ASST. SCHOOL DIVISION SUPERINTENDENT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'asds', NULL, 'storage/esignatures/sign_11_1771155339.png', 1, 4, NULL, NULL, NULL, '$2y$12$BD/aMBl/LBzYmvfBPQff4uWlX8ltoElsY0kdBcREyIfsrBBL2S84C', 'JOE-BREN L. CONSUELO', 'ICT', NULL, '2026-02-12 20:45:11', '2026-02-15 02:35:39', NULL, NULL),
(12, 'SCHOOL DIVISION SUPERINTENDENT', 'PHILLIP', 'B.', 'GALLENDEZ', 'PHILLIP B. GALLENDEZ', 'TEMPSDS@GMAIL.COM', NULL, 'SCHOOL DIVISION SUPERINTENDENT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sds', NULL, 'storage/esignatures/sign_12_1771155381.png', 1, 4, NULL, NULL, NULL, '$2y$12$tcGutXW4SwR2NjKQCpfwRuqVDOifc35CphXx5gIhyTHO7RkhzNf1m', 'PHILLIP B. GALLENDEZ', 'ICT', NULL, '2026-02-12 20:46:46', '2026-02-15 02:36:21', NULL, NULL),
(13, 'ADMINISTRATIVE OFFICER V', 'PAUL JEREMY', 'I.', 'AGUJA', 'PAUL JEREMY I. AGUJA', 'TEMPAO@GMAIL.COM', NULL, 'ADMINISTRATIVE OFFICER V', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ao', NULL, 'storage/esignatures/sign_13_1771042804.png', 1, 4, NULL, NULL, NULL, '$2y$12$TIug3DYqHpsJXgMzMxdMNeiDC0kjLHS0Ks50hVqIKq5SOgHBqZg4a', 'PAUL JEREMY I. AGUJA', 'ADMINISTRATIVE', NULL, '2026-02-12 20:48:20', '2026-02-13 19:20:04', 11, 12),
(14, 'CHIEF EDUCATION SUPERVISOR, SGOD', 'FREDERICK', 'G.', 'BYRD JR.', 'FREDERICK G. BYRD JR.', 'TEMPSGOD@GMAIL.COM', NULL, 'CHIEF EDUCATION SUPERVISOR, SGOD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sgod_chief', NULL, 'storage/esignatures/sign_14_1771155270.png', 1, 4, NULL, NULL, NULL, '$2y$12$U/19fpst4aDD2Skd.8zFLu3/ZjSL9q2.bAVIeRKvG/PySz6AnSvHO', 'FREDERICK G. BYRD JR.', 'ICT', NULL, '2026-02-12 20:50:24', '2026-02-15 02:34:30', NULL, NULL),
(15, 'CHIEF EDUCATION SUPERVISOR, CID', 'ERMA', 'S', 'VALENZUELA', 'ERMA S VALENZUELA', 'TEMPCID@GMAIL.COM', NULL, 'CHIEF EDUCATION SUPERVISOR, CID', NULL, NULL, NULL, '3424234', NULL, NULL, NULL, NULL, 'cid_chief', NULL, 'storage/esignatures/sign_15_1771155304.png', 1, 4, NULL, NULL, NULL, '$2y$12$UphogvoOjSPwNnDEJDjEfOSOvk1CTrUIngF8pGwGfb6eJPei82RFW', 'ERMA S VALENZUELA', 'ICT', NULL, '2026-02-12 20:52:36', '2026-03-09 17:20:56', NULL, NULL),
(16, 'LJ', 'LJ', 'A.', 'LEOSALA', 'LJ A. LEOSALA', 'lykajane.leosala@deped.gov.ph', NULL, 'IT01', 'SG 19-1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'user', NULL, 'storage/esignatures/sign_16_1771909384.png', 1, NULL, NULL, NULL, NULL, '$2y$12$U2Rweu.KuIKjuERo7bemv.2FvuCeZ1YJu9utxhhFLl1vWRq5SlADK', 'LJ A. LEOSALA', 'ICT', NULL, '2026-02-18 21:20:52', '2026-02-23 20:03:04', 11, 12),
(17, 'dawdaw', 'wewea', 'fdafad', 'adfaf3', 'wewea fdafad adfaf3', 'ceededom@gmail.com', NULL, 'Administrative Aide VI', NULL, NULL, NULL, '4353453', NULL, NULL, NULL, NULL, 'user', NULL, NULL, 0, 4, NULL, NULL, NULL, '$2y$12$roPHL578uzgvRESXqBgF9.fuoYZHz0jnjF/5E0d3l7SUGlJm6fpae', 'wewea fdafad adfaf3', 'ADMINISTRATIVE (PROPERTY AND SUPPLY)', NULL, '2026-03-09 17:16:38', '2026-03-09 17:16:55', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_needs`
--

CREATE TABLE `user_needs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `need_text` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `compensatory_leave_credits`
--
ALTER TABLE `compensatory_leave_credits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `compensatory_leave_credits_user_id_foreign` (`user_id`),
  ADD KEY `compensatory_leave_credits_leave_type_id_foreign` (`leave_type_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `items_sku_unique` (`sku`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_applications_user_id_foreign` (`user_id`),
  ADD KEY `leave_applications_leave_type_id_foreign` (`leave_type_id`),
  ADD KEY `leave_applications_recommending_officer_id_foreign` (`recommending_officer_id`),
  ADD KEY `leave_applications_approving_officer_id_foreign` (`approving_officer_id`),
  ADD KEY `leave_applications_hr_verifier_id_foreign` (`hr_verifier_id`);

--
-- Indexes for table `leave_credits`
--
ALTER TABLE `leave_credits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `leave_credits_user_id_leave_type_id_unique` (`user_id`,`leave_type_id`),
  ADD KEY `leave_credits_leave_type_id_foreign` (`leave_type_id`);

--
-- Indexes for table `leave_credit_audit_logs`
--
ALTER TABLE `leave_credit_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_credit_audit_logs_actor_id_foreign` (`actor_id`),
  ADD KEY `leave_credit_audit_logs_target_user_id_foreign` (`target_user_id`);

--
-- Indexes for table `leave_credit_policies`
--
ALTER TABLE `leave_credit_policies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `leave_credit_policies_leave_type_id_unique` (`leave_type_id`);

--
-- Indexes for table `leave_details_form6`
--
ALTER TABLE `leave_details_form6`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_details_form6_leave_application_id_foreign` (`leave_application_id`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leave_update_requests`
--
ALTER TABLE `leave_update_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_update_requests_requester_id_foreign` (`requester_id`),
  ADD KEY `leave_update_requests_approver_id_foreign` (`approver_id`),
  ADD KEY `leave_update_requests_target_user_id_foreign` (`target_user_id`),
  ADD KEY `leave_update_requests_leave_type_id_foreign` (`leave_type_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_sender_id_foreign` (`sender_id`),
  ADD KEY `notifications_recipient_id_foreign` (`recipient_id`);

--
-- Indexes for table `offices`
--
ALTER TABLE `offices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `offices_name_unique` (`name`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `registration_request_logs`
--
ALTER TABLE `registration_request_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registration_request_logs_email_index` (`email`);

--
-- Indexes for table `reset_request_logs`
--
ALTER TABLE `reset_request_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reset_request_logs_email_requested_at_index` (`email`,`requested_at`);

--
-- Indexes for table `security_tracking`
--
ALTER TABLE `security_tracking`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `security_tracking_email_unique` (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `signatories`
--
ALTER TABLE `signatories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `signatories_position_unique` (`position`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`gmail`),
  ADD KEY `users_recommending_officer_id_foreign` (`recommending_officer_id`),
  ADD KEY `users_approving_officer_id_foreign` (`approving_officer_id`);

--
-- Indexes for table `user_needs`
--
ALTER TABLE `user_needs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_needs_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=551;

--
-- AUTO_INCREMENT for table `compensatory_leave_credits`
--
ALTER TABLE `compensatory_leave_credits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_applications`
--
ALTER TABLE `leave_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `leave_credits`
--
ALTER TABLE `leave_credits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `leave_credit_audit_logs`
--
ALTER TABLE `leave_credit_audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `leave_credit_policies`
--
ALTER TABLE `leave_credit_policies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `leave_details_form6`
--
ALTER TABLE `leave_details_form6`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `leave_update_requests`
--
ALTER TABLE `leave_update_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `offices`
--
ALTER TABLE `offices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `registration_request_logs`
--
ALTER TABLE `registration_request_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reset_request_logs`
--
ALTER TABLE `reset_request_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `security_tracking`
--
ALTER TABLE `security_tracking`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `signatories`
--
ALTER TABLE `signatories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_needs`
--
ALTER TABLE `user_needs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `compensatory_leave_credits`
--
ALTER TABLE `compensatory_leave_credits`
  ADD CONSTRAINT `compensatory_leave_credits_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `compensatory_leave_credits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD CONSTRAINT `leave_applications_approving_officer_id_foreign` FOREIGN KEY (`approving_officer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leave_applications_hr_verifier_id_foreign` FOREIGN KEY (`hr_verifier_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leave_applications_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`),
  ADD CONSTRAINT `leave_applications_recommending_officer_id_foreign` FOREIGN KEY (`recommending_officer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leave_applications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_credits`
--
ALTER TABLE `leave_credits`
  ADD CONSTRAINT `leave_credits_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_credits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_credit_audit_logs`
--
ALTER TABLE `leave_credit_audit_logs`
  ADD CONSTRAINT `leave_credit_audit_logs_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leave_credit_audit_logs_target_user_id_foreign` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `leave_credit_policies`
--
ALTER TABLE `leave_credit_policies`
  ADD CONSTRAINT `leave_credit_policies_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_update_requests`
--
ALTER TABLE `leave_update_requests`
  ADD CONSTRAINT `leave_update_requests_approver_id_foreign` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leave_update_requests_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`),
  ADD CONSTRAINT `leave_update_requests_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leave_update_requests_target_user_id_foreign` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
