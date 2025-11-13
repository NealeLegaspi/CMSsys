-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 13, 2025 at 06:37 PM
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
-- Database: `laravel`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `role`, `action`, `is_archived`, `description`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(163, 1, 'Admin', 'Create Announcement', 0, 'Added Student announcement: test', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-17 22:50:12', '2025-10-17 22:50:12'),
(164, 1, 'Admin', 'Create Announcement', 0, 'Added Teacher announcement: test', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-17 22:50:27', '2025-10-17 22:50:27'),
(165, 1, 'Admin', 'Delete Announcement', 0, 'Deleted announcement: test', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-17 23:01:13', '2025-10-17 23:01:13'),
(166, 1, 'Admin', 'Delete Announcement', 0, 'Deleted announcement: test', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-17 23:01:17', '2025-10-17 23:01:17'),
(167, 1, 'Admin', 'Create Announcement', 0, 'Added Student announcement: test', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-17 23:01:33', '2025-10-17 23:01:33'),
(168, 1, 'Admin', 'Create Announcement', 1, 'Added Teacher announcement: test', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-17 23:05:21', '2025-10-23 02:18:16'),
(169, 2, NULL, 'Add Section', 1, 'Added section Example', NULL, NULL, '2025-10-18 10:27:09', '2025-10-23 02:18:13'),
(170, 2, NULL, 'Delete Section', 1, 'Deleted section Example', NULL, NULL, '2025-10-18 10:27:21', '2025-10-23 02:18:10'),
(171, 1, 'Admin', 'Create User', 1, 'Created user: legaspi@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-18 18:40:00', '2025-10-23 02:18:06'),
(172, 2, NULL, 'Enroll Student', 1, 'Enrolled student Legaspi, Neale D. (2) in section Jupiter', NULL, NULL, '2025-10-18 18:40:40', '2025-10-23 02:18:05'),
(173, 2, NULL, 'Verify Enrollment', 1, 'Verified enrollment ID 3', NULL, NULL, '2025-10-18 18:40:56', '2025-10-23 02:18:04'),
(174, 3, NULL, 'Export Reports', 1, 'Exported reports for 1 students', NULL, NULL, '2025-10-18 19:08:30', '2025-10-23 02:18:01'),
(175, 3, NULL, 'Export Reports', 1, 'Exported reports for 1 students', NULL, NULL, '2025-10-18 19:24:11', '2025-10-18 20:15:12'),
(176, 2, NULL, 'Update Enrollment', 1, 'Updated enrollment ID 3', NULL, NULL, '2025-10-19 04:41:27', '2025-10-23 02:18:00'),
(177, 2, NULL, 'Verify Enrollment', 1, 'Verified enrollment ID 3', NULL, NULL, '2025-10-19 04:44:10', '2025-10-23 02:18:00'),
(178, 3, NULL, 'Export Reports', 1, 'Exported reports for 1 students', NULL, NULL, '2025-10-20 00:30:48', '2025-10-23 02:17:59'),
(179, 3, NULL, 'Save Grades', 1, 'Updated grades for 4 quarter entries in Health, well-Being, and Motor Development (Jupiter).', NULL, NULL, '2025-10-20 08:40:51', '2025-10-23 02:17:58'),
(180, 3, NULL, 'Save Grades', 1, 'Updated grades for 3 quarter entries in Health, well-Being, and Motor Development (Jupiter).', NULL, NULL, '2025-10-20 08:45:28', '2025-10-23 02:17:58'),
(181, 2, NULL, 'Verify Enrollment', 1, 'Verified enrollment ID 3', NULL, NULL, '2025-10-21 10:10:54', '2025-10-23 02:17:56'),
(182, 2, NULL, 'Return Grades', 1, 'Returned grades for 2 to teacher for revision.', NULL, NULL, '2025-10-21 21:50:23', '2025-10-23 02:17:55'),
(183, 2, NULL, 'Return Grades', 1, 'Returned grades for 2 to teacher for revision.', NULL, NULL, '2025-10-21 21:50:25', '2025-10-23 02:17:50'),
(184, 3, NULL, 'Submit Grades', 1, 'Submitted grades for assignment ID 2.', NULL, NULL, '2025-10-21 22:04:00', '2025-10-23 02:17:43'),
(185, 3, NULL, 'Submit Grades', 1, 'Submitted grades for assignment ID 2.', NULL, NULL, '2025-10-21 22:09:15', '2025-10-23 02:17:42'),
(186, 2, NULL, 'Return Grades', 1, 'Return Grades for assignment #2.', NULL, NULL, '2025-10-21 22:17:50', '2025-10-23 02:17:41'),
(187, 2, NULL, 'Return Grades', 1, 'Return Grades for assignment #2.', NULL, NULL, '2025-10-21 22:19:37', '2025-10-23 02:17:40'),
(188, 2, NULL, 'Return Grades', 1, 'Return Grades for assignment #2.', NULL, NULL, '2025-10-21 22:23:07', '2025-10-23 02:17:37'),
(189, 2, NULL, 'Approve Grades', 1, 'Approve Grades for assignment #2.', NULL, NULL, '2025-10-21 22:23:09', '2025-10-23 02:17:36'),
(190, 2, NULL, 'Return Grades', 1, 'Return Grades for assignment #2.', NULL, NULL, '2025-10-21 22:23:17', '2025-10-23 02:17:35'),
(191, 2, NULL, 'Return Grades', 1, 'Return Grades for assignment #2.', NULL, NULL, '2025-10-21 22:25:41', '2025-10-23 02:17:34'),
(192, 3, NULL, 'Save Grades', 1, 'Updated grades for 4 quarter entries in Health, well-Being, and Motor Development (Jupiter).', NULL, NULL, '2025-10-21 23:27:44', '2025-10-23 02:17:33'),
(193, 2, NULL, 'Approve Grades', 1, 'Approved grades for assignment #2.', NULL, NULL, '2025-10-21 23:28:42', '2025-10-23 02:17:33'),
(194, 2, NULL, 'Approve Grades', 1, 'Approved grades for assignment #2.', NULL, NULL, '2025-10-22 02:00:58', '2025-10-23 02:17:32'),
(195, 2, NULL, 'Approve Grades', 1, 'Approved grades for assignment #2.', NULL, NULL, '2025-10-22 02:04:40', '2025-10-23 02:17:31'),
(196, 3, NULL, 'Save Grades', 1, 'Updated grades for 4 quarter entries in Health, well-Being, and Motor Development (Jupiter).', NULL, NULL, '2025-10-22 04:38:25', '2025-10-23 02:17:30'),
(197, 3, NULL, 'Save Grades', 1, 'Updated grades for 4 quarter entries in Health, well-Being, and Motor Development (Jupiter).', NULL, NULL, '2025-10-22 04:41:05', '2025-10-23 02:17:30'),
(198, 3, NULL, 'Save Grades', 1, 'Updated grades for 4 quarter entries in Health, well-Being, and Motor Development (Jupiter).', NULL, NULL, '2025-10-22 04:41:30', '2025-10-23 02:17:29'),
(199, 1, 'Admin', 'Update User', 1, 'Updated user: teacher@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-22 04:43:52', '2025-10-23 02:17:28'),
(200, 1, 'Admin', 'Delete User', 1, 'Deleted user: student@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-22 04:44:12', '2025-10-23 02:17:27'),
(201, 1, 'Admin', 'Create User', 1, 'Created user: teacher1@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-22 04:45:07', '2025-10-23 02:17:27'),
(202, 1, 'Admin', 'Create User', 1, 'Created user: clara@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-22 04:46:31', '2025-10-23 02:17:26'),
(203, 2, NULL, 'Enroll Student', 1, 'Enrolled student Clara, Maria P. (3) in section Jupiter', NULL, NULL, '2025-10-22 04:47:36', '2025-10-23 02:17:25'),
(204, 2, NULL, 'Return Grades', 1, 'Returned grades for assignment #2 to teacher.', NULL, NULL, '2025-10-22 06:10:49', '2025-10-23 02:17:24'),
(205, 2, NULL, 'Delete Enrollment', 1, 'Deleted enrollment ID 4', NULL, NULL, '2025-10-22 06:17:35', '2025-10-23 02:17:23'),
(206, 2, NULL, 'Enroll Student', 1, 'Enrolled student Clara, Maria P. (3) in section Jupiter', NULL, NULL, '2025-10-22 06:17:43', '2025-10-23 02:16:54'),
(207, 3, NULL, 'Save Grades', 1, 'Updated grades for 8 quarter entries in Health, well-Being, and Motor Development (Jupiter).', NULL, NULL, '2025-10-22 06:33:35', '2025-10-23 02:16:53'),
(208, 1, 'Admin', 'Update Profile', 1, 'Updated profile: admin@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-22 22:28:50', '2025-10-23 02:16:52'),
(209, 1, 'Admin', 'Update Profile', 1, 'Updated profile: admin@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-22 22:29:34', '2025-10-23 02:16:52'),
(210, 1, 'Admin', 'Update User', 1, 'Updated user: registrar@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-22 22:31:27', '2025-10-23 02:16:51'),
(211, 3, NULL, 'Update Profile', 1, 'Updated profile for teacher@mindware.edu.ph', NULL, NULL, '2025-10-22 22:32:32', '2025-10-23 02:16:50'),
(212, NULL, NULL, 'Update Profile', 1, 'Updated profile for legaspi@mindware.edu.ph', NULL, NULL, '2025-10-22 22:34:35', '2025-10-23 02:16:49'),
(213, 1, 'Admin', 'Delete Announcement', 1, 'Deleted announcement: test', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-22 22:37:58', '2025-10-23 02:16:48'),
(214, 1, 'Admin', 'Create Announcement', 1, 'Added Teacher announcement: Testing', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-22 22:38:37', '2025-10-23 02:16:47'),
(215, 1, 'Admin', 'Create Announcement', 0, 'Added Student announcement: Testing 2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-22 22:38:59', '2025-10-23 02:18:20'),
(216, 3, NULL, 'Create Announcement', 0, 'Created announcement Testing', NULL, NULL, '2025-10-23 02:33:41', '2025-10-23 02:33:41'),
(217, 3, NULL, 'Delete Announcement', 0, 'Deleted announcement 18', NULL, NULL, '2025-10-23 02:44:01', '2025-10-23 02:44:01'),
(218, 3, NULL, 'Create Announcement', 0, 'Created announcement Testing', NULL, NULL, '2025-10-23 02:44:15', '2025-10-23 02:44:15'),
(219, 3, NULL, 'Submit Grades', 0, 'Submitted grades for 46 (22).', NULL, NULL, '2025-10-23 03:29:25', '2025-10-23 03:29:25'),
(220, 2, NULL, 'Approve Grades', 0, 'Approved grades for assignment #2.', NULL, NULL, '2025-10-23 03:36:05', '2025-10-23 03:36:05'),
(221, 2, NULL, 'Return Grades', 0, 'Returned grades for assignment #2 to teacher.', NULL, NULL, '2025-10-23 03:53:48', '2025-10-23 03:53:48'),
(222, 1, 'Admin', 'Create User', 0, 'Created user: tolentino@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-23 09:37:39', '2025-10-23 09:37:39'),
(223, 1, 'Admin', 'Create User', 0, 'Created user: carreon@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-23 09:39:53', '2025-10-23 09:39:53'),
(224, 1, 'Admin', 'Create User', 0, 'Created user: chingcuangco@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-23 09:41:09', '2025-10-23 09:41:09'),
(225, 2, NULL, 'Add Section', 0, 'Added section Section A', NULL, NULL, '2025-10-23 09:43:40', '2025-10-23 09:43:40'),
(226, 2, NULL, 'Update Section', 0, 'Updated section Earth', NULL, NULL, '2025-10-23 09:45:22', '2025-10-23 09:45:22'),
(227, 2, NULL, 'Add Section', 0, 'Added section Mars', NULL, NULL, '2025-10-23 09:46:40', '2025-10-23 09:46:40'),
(228, 2, NULL, 'Add Section', 0, 'Added section Moon', NULL, NULL, '2025-10-23 09:47:22', '2025-10-23 09:47:22'),
(229, 2, NULL, 'Add Section', 0, 'Added section Uranus', NULL, NULL, '2025-10-23 09:48:07', '2025-10-23 09:48:07'),
(230, 1, 'Admin', 'Create User', 0, 'Created user: ciao@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-23 09:50:40', '2025-10-23 09:50:40'),
(231, 1, 'Admin', 'Create User', 0, 'Created user: christian@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-23 09:52:18', '2025-10-23 09:52:18'),
(232, 2, NULL, 'Add Section', 0, 'Added section Saturn', NULL, NULL, '2025-10-23 09:53:14', '2025-10-23 09:53:14'),
(233, 2, NULL, 'Add Section', 0, 'Added section Venus', NULL, NULL, '2025-10-23 09:53:40', '2025-10-23 09:53:40'),
(234, 1, 'Admin', 'Create User', 0, 'Created user: madlangawa@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-23 09:56:00', '2025-10-23 09:56:00'),
(235, 1, 'Admin', 'Create User', 0, 'Created user: madlangawa1@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-23 09:57:33', '2025-10-23 09:57:33'),
(236, 1, 'Admin', 'Create User', 0, 'Created user: tolentino1@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-23 09:59:09', '2025-10-23 09:59:09'),
(237, 1, 'Admin', 'Create User', 0, 'Created user: wenceslao@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-23 10:01:18', '2025-10-23 10:01:18'),
(238, 1, 'Admin', 'Create User', 0, 'Created user: legaspi1@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-23 10:03:24', '2025-10-23 10:03:24'),
(239, 1, 'Admin', 'Create User', 0, 'Created user: legaspi2@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-23 10:04:17', '2025-10-23 10:04:17'),
(240, 2, NULL, 'Approve Grades', 0, 'Approved grades for assignment #2.', NULL, NULL, '2025-10-23 13:15:26', '2025-10-23 13:15:26'),
(241, 2, NULL, 'Delete Enrollment', 0, 'Deleted enrollment ID 5', NULL, NULL, '2025-10-24 02:45:29', '2025-10-24 02:45:29'),
(242, 2, NULL, 'Delete Enrollment', 0, 'Deleted enrollment ID 3', NULL, NULL, '2025-10-24 02:45:32', '2025-10-24 02:45:32'),
(243, 1, 'Admin', 'Delete User', 0, 'Deleted user: legaspi@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 02:47:02', '2025-10-24 02:47:02'),
(244, 1, 'Admin', 'Delete User', 0, 'Deleted user: clara@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 02:47:08', '2025-10-24 02:47:08'),
(245, 1, 'Admin', 'Delete User', 0, 'Deleted user: madlangawa@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 02:47:38', '2025-10-24 02:47:38'),
(246, 1, 'Admin', 'Delete User', 0, 'Deleted user: madlangawa1@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 02:47:42', '2025-10-24 02:47:42'),
(247, 1, 'Admin', 'Delete User', 0, 'Deleted user: tolentino1@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 02:47:46', '2025-10-24 02:47:46'),
(248, 1, 'Admin', 'Delete User', 0, 'Deleted user: wenceslao@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 02:47:50', '2025-10-24 02:47:50'),
(249, 1, 'Admin', 'Delete User', 0, 'Deleted user: legaspi1@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 02:47:53', '2025-10-24 02:47:53'),
(250, 1, 'Admin', 'Delete User', 0, 'Deleted user: legaspi2@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 02:47:57', '2025-10-24 02:47:57'),
(251, 3, NULL, 'Delete Announcement', 0, 'Deleted announcement 19', NULL, NULL, '2025-10-24 02:49:03', '2025-10-24 02:49:03'),
(252, 1, 'Admin', 'Approve User', 0, 'Approved user dionisio@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 03:41:54', '2025-10-24 03:41:54'),
(253, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 03:46:20', '2025-10-24 03:46:20'),
(254, 1, 'Admin', 'Approve User', 0, 'Approved user dionisio@mindware.edu.ph and verified enrollment(s).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 03:47:05', '2025-10-24 03:47:05'),
(255, 2, NULL, 'Delete Student', 0, 'Deleted student dionisio@mindware.edu.ph', NULL, NULL, '2025-10-24 03:52:19', '2025-10-24 03:52:19'),
(256, 2, NULL, 'Delete Enrollment', 0, 'Deleted enrollment ID 8', NULL, NULL, '2025-10-24 03:59:28', '2025-10-24 03:59:28'),
(257, 1, 'Admin', 'Approve User', 0, 'Approved user dionisio@mindware.edu.ph and verified enrollment(s).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 04:03:01', '2025-10-24 04:03:01'),
(258, 2, NULL, 'Update Enrollment', 0, 'Updated enrollment ID 9', NULL, NULL, '2025-10-24 04:20:23', '2025-10-24 04:20:23'),
(259, 2, NULL, 'Update Enrollment', 0, 'Updated enrollment ID 9', NULL, NULL, '2025-10-24 04:20:29', '2025-10-24 04:20:29'),
(260, 2, NULL, 'Update Enrollment', 0, 'Updated enrollment ID 9', NULL, NULL, '2025-10-24 04:20:44', '2025-10-24 04:20:44'),
(261, 2, NULL, 'Update Enrollment', 0, 'Updated enrollment ID 9', NULL, NULL, '2025-10-24 04:20:53', '2025-10-24 04:20:53'),
(262, 2, NULL, 'Update Enrollment', 0, 'Updated enrollment ID 9', NULL, NULL, '2025-10-24 04:20:58', '2025-10-24 04:20:58'),
(263, 1, 'Admin', 'Reject User', 0, 'Rejected user wenceslao@mindware.edu.ph. Reason: ', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 04:49:39', '2025-10-24 04:49:39'),
(264, 1, 'Admin', 'Delete User', 0, 'Deleted user: wenceslao@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 04:49:57', '2025-10-24 04:49:57'),
(265, 1, 'Admin', 'Reject User', 0, 'Rejected user wenceslao@mindware.edu.ph. Reason: ', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 04:56:09', '2025-10-24 04:56:09'),
(266, 1, 'Admin', 'Delete User', 0, 'Deleted user: wenceslao@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 04:57:46', '2025-10-24 04:57:46'),
(267, 1, 'Admin', 'Update User', 0, 'Updated user: registrar@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 05:03:04', '2025-10-24 05:03:04'),
(268, 2, NULL, 'Delete Enrollment', 0, 'Deleted enrollment ID 9', NULL, NULL, '2025-10-24 05:10:48', '2025-10-24 05:10:48'),
(269, 1, 'Admin', 'Approve User', 0, 'Approved user wenceslao@mindware.edu.ph and verified enrollment(s).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 05:11:41', '2025-10-24 05:11:41'),
(270, 1, 'Admin', 'Update User', 0, 'Updated user: wenceslao@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 05:11:47', '2025-10-24 05:11:47'),
(271, 1, 'Admin', 'Delete Announcement', 0, 'Deleted announcement: Testing 2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 05:12:12', '2025-10-24 05:12:12'),
(272, 1, 'Admin', 'Delete Announcement', 0, 'Deleted announcement: Testing', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 05:12:15', '2025-10-24 05:12:15'),
(273, 1, 'Admin', 'Create Announcement', 0, 'Added Global announcement: test', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 22:13:33', '2025-10-24 22:13:33'),
(274, 1, 'Admin', 'Approve User', 0, 'Approved user legaspi21@mindware.edu.ph and verified enrollment(s).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 22:26:54', '2025-10-24 22:26:54'),
(275, 3, NULL, 'Save Grades', 0, 'Updated grades for 4 quarter entries in Health, well-Being, and Motor Development (Jupiter).', NULL, NULL, '2025-10-24 22:35:25', '2025-10-24 22:35:25'),
(276, 2, NULL, 'Approve Grades', 0, 'Approved grades for assignment #4.', NULL, NULL, '2025-10-24 22:36:23', '2025-10-24 22:36:23'),
(277, 1, 'Admin', 'Update User', 0, 'Updated user: legaspi21@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 22:42:24', '2025-10-24 22:42:24'),
(278, 1, 'Admin', 'Reset Password', 0, 'Reset password for legaspi21@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 22:42:26', '2025-10-24 22:42:26'),
(279, 1, 'Admin', 'Reset Password', 0, 'Reset password for legaspi21@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 22:42:34', '2025-10-24 22:42:34'),
(280, 1, 'Admin', 'Reset Password', 0, 'Reset password for dionisio@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-24 22:44:36', '2025-10-24 22:44:36'),
(281, 2, NULL, 'Delete Student', 0, 'Deleted student legaspi21@mindware.edu.ph', NULL, NULL, '2025-10-25 00:31:14', '2025-10-25 00:31:14'),
(282, 2, NULL, 'Delete Student', 0, 'Deleted student wenceslao@mindware.edu.ph', NULL, NULL, '2025-10-25 00:31:17', '2025-10-25 00:31:17'),
(283, 1, 'Admin', 'Delete Announcement', 0, 'Deleted announcement: test', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 00:32:34', '2025-10-25 00:32:34'),
(284, 2, NULL, 'Return Grades', 0, 'Returned grades for assignment #4 to teacher.', NULL, NULL, '2025-10-25 00:33:07', '2025-10-25 00:33:07'),
(285, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 00:36:02', '2025-10-25 00:36:02'),
(286, 1, 'Admin', 'Delete User', 0, 'Deleted user: christian@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 00:36:07', '2025-10-25 00:36:07'),
(287, 1, 'Admin', 'Create User', 0, 'Created user: dionisio@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 01:16:50', '2025-10-25 01:16:50'),
(288, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 01:21:27', '2025-10-25 01:21:27'),
(289, 1, 'Admin', 'Create User', 0, 'Created user: dionisio@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 01:21:48', '2025-10-25 01:21:48'),
(290, 2, NULL, 'Delete Section', 0, 'Deleted section Venus', NULL, NULL, '2025-10-25 01:35:02', '2025-10-25 01:35:02'),
(291, 2, NULL, 'Delete Section', 0, 'Deleted section Jupiter', NULL, NULL, '2025-10-25 01:35:29', '2025-10-25 01:35:29'),
(292, 2, NULL, 'Delete Section', 0, 'Deleted section Earth', NULL, NULL, '2025-10-25 01:35:33', '2025-10-25 01:35:33'),
(293, 2, NULL, 'Delete Section', 0, 'Deleted section Mars', NULL, NULL, '2025-10-25 01:35:36', '2025-10-25 01:35:36'),
(294, 2, NULL, 'Delete Section', 0, 'Deleted section Moon', NULL, NULL, '2025-10-25 01:35:39', '2025-10-25 01:35:39'),
(295, 2, NULL, 'Delete Section', 0, 'Deleted section Uranus', NULL, NULL, '2025-10-25 01:35:42', '2025-10-25 01:35:42'),
(296, 2, NULL, 'Delete Section', 0, 'Deleted section Saturn', NULL, NULL, '2025-10-25 01:35:45', '2025-10-25 01:35:45'),
(297, 2, NULL, 'Add Section', 0, 'Added section Jupiter', NULL, NULL, '2025-10-25 02:46:24', '2025-10-25 02:46:24'),
(298, 1, 'Admin', 'Toggle User Status', 0, 'Changed status of dionisio.neale.@mindware.edu.ph to active', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 02:56:37', '2025-10-25 02:56:37'),
(299, 1, 'Admin', 'Toggle User Status', 0, 'Changed status of dionisio.neale.@mindware.edu.ph to inactive', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 02:56:41', '2025-10-25 02:56:41'),
(300, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio.neale.@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 02:56:56', '2025-10-25 02:56:56'),
(301, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio.neale@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 08:33:23', '2025-10-25 08:33:23'),
(302, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 08:35:14', '2025-10-25 08:35:14'),
(303, 2, NULL, 'Delete Enrollment', 0, 'Deleted enrollment ID 17', NULL, NULL, '2025-10-25 08:35:21', '2025-10-25 08:35:21'),
(304, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio.neale@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 08:35:45', '2025-10-25 08:35:45'),
(305, 2, NULL, 'Update Enrollment', 0, 'Updated enrollment ID 18', NULL, NULL, '2025-10-25 08:44:05', '2025-10-25 08:44:05'),
(306, 2, NULL, 'Delete Enrollment', 0, 'Deleted enrollment ID 19', NULL, NULL, '2025-10-25 09:05:04', '2025-10-25 09:05:04'),
(307, 2, NULL, 'Delete Enrollment', 0, 'Deleted enrollment ID 18', NULL, NULL, '2025-10-25 09:47:35', '2025-10-25 09:47:35'),
(308, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio.neale@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 09:52:32', '2025-10-25 09:52:32'),
(309, 3, NULL, 'Export Reports', 0, 'Exported reports for 1 students', NULL, NULL, '2025-10-25 09:54:04', '2025-10-25 09:54:04'),
(310, 2, NULL, 'Return Grades', 0, 'Returned grades for assignment #5 to teacher.', NULL, NULL, '2025-10-25 09:58:57', '2025-10-25 09:58:57'),
(311, 2, NULL, 'Delete Enrollment', 0, 'Deleted enrollment ID 20', NULL, NULL, '2025-10-25 10:08:20', '2025-10-25 10:08:20'),
(312, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio.neale@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 10:16:06', '2025-10-25 10:16:06'),
(313, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 10:30:44', '2025-10-25 10:30:44'),
(314, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 10:37:02', '2025-10-25 10:37:02'),
(315, 2, NULL, 'Delete Enrollment', 0, 'Deleted enrollment ID 23', NULL, NULL, '2025-10-25 10:43:08', '2025-10-25 10:43:08'),
(316, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 10:43:21', '2025-10-25 10:43:21'),
(317, 2, NULL, 'Delete Enrollment', 0, 'Deleted enrollment ID 24', NULL, NULL, '2025-10-25 10:48:38', '2025-10-25 10:48:38'),
(318, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 10:49:06', '2025-10-25 10:49:06'),
(319, 2, NULL, 'Delete Enrollment', 0, 'Deleted enrollment ID 25', NULL, NULL, '2025-10-25 10:55:06', '2025-10-25 10:55:06'),
(320, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 10:55:35', '2025-10-25 10:55:35'),
(321, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 11:02:54', '2025-10-25 11:02:54'),
(322, 2, NULL, 'Delete Enrollment', 0, 'Deleted enrollment ID 27', NULL, NULL, '2025-10-25 11:15:10', '2025-10-25 11:15:10'),
(323, 2, NULL, 'Delete Enrollment', 0, 'Deleted enrollment ID 28', NULL, NULL, '2025-10-25 12:11:12', '2025-10-25 12:11:12'),
(324, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio.neale@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 12:13:00', '2025-10-25 12:13:00'),
(325, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio.neale@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 12:15:49', '2025-10-25 12:15:49'),
(326, 2, NULL, 'Return Grades', 0, 'Returned grades for assignment #9 to teacher.', NULL, NULL, '2025-10-25 12:18:25', '2025-10-25 12:18:25'),
(327, 3, NULL, 'Save Grades', 0, 'Updated grades for 8 quarter entries in Health, well-Being, and Motor Development (Jupiter).', NULL, NULL, '2025-10-25 13:00:30', '2025-10-25 13:00:30'),
(328, 2, NULL, 'Approve Grades', 0, 'Approved grades for assignment #9.', NULL, NULL, '2025-10-25 13:00:53', '2025-10-25 13:00:53'),
(329, 2, NULL, 'Return Grades', 0, 'Returned grades for assignment #9 to teacher.', NULL, NULL, '2025-10-25 13:01:02', '2025-10-25 13:01:02'),
(330, 2, NULL, 'Approve Grades', 0, 'Approved grades for assignment #9.', NULL, NULL, '2025-10-25 13:01:14', '2025-10-25 13:01:14'),
(331, 2, NULL, 'Return Grades', 0, 'Returned grades for assignment #9 to teacher.', NULL, NULL, '2025-10-25 13:01:33', '2025-10-25 13:01:33'),
(332, 3, NULL, 'Save Grades', 0, 'Updated grades for 8 quarter entries in Health, well-Being, and Motor Development (Jupiter).', NULL, NULL, '2025-10-25 13:03:26', '2025-10-25 13:03:26'),
(333, 2, NULL, 'Approve Grades', 0, 'Approved grades for assignment #9.', NULL, NULL, '2025-10-25 13:03:44', '2025-10-25 13:03:44'),
(334, 2, NULL, 'Return Grades', 0, 'Returned grades for assignment #9 to teacher.', NULL, NULL, '2025-10-25 13:03:58', '2025-10-25 13:03:58'),
(335, 3, NULL, 'Save Grades', 0, 'Updated grades for 8 quarter entries in Health, well-Being, and Motor Development (Jupiter).', NULL, NULL, '2025-10-25 13:05:40', '2025-10-25 13:05:40'),
(336, 3, NULL, 'Save Grades', 0, 'Updated grades for 0 quarter entries in Health, well-Being, and Motor Development (Jupiter).', NULL, NULL, '2025-10-25 13:06:03', '2025-10-25 13:06:03'),
(337, 2, NULL, 'Delete Enrollment', 0, 'Deleted enrollment ID 31', NULL, NULL, '2025-10-25 13:06:51', '2025-10-25 13:06:51'),
(338, 2, NULL, 'Delete Enrollment', 0, 'Deleted enrollment ID 30', NULL, NULL, '2025-10-25 13:06:53', '2025-10-25 13:06:53'),
(339, 1, 'Admin', 'Toggle User Status', 0, 'Changed status of legaspi2@mindware.edu.ph to inactive', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 13:07:58', '2025-10-25 13:07:58'),
(340, 1, 'Admin', 'Toggle User Status', 0, 'Changed status of legaspi2@mindware.edu.ph to active', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 13:08:00', '2025-10-25 13:08:00'),
(341, 1, 'Admin', 'Delete User', 0, 'Deleted user: legaspi2@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 13:08:03', '2025-10-25 13:08:03'),
(342, 1, 'Admin', 'Delete User', 0, 'Deleted user: dela cruz.maria@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 13:08:21', '2025-10-25 13:08:21'),
(343, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio.neale@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 13:08:27', '2025-10-25 13:08:27'),
(344, 1, 'Admin', 'Delete User', 0, 'Deleted user: clara.maria@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 13:08:31', '2025-10-25 13:08:31'),
(345, 3, NULL, 'Save Grades', 0, 'Updated grades for 4 quarter entries in Health, well-Being, and Motor Development (Jupiter).', NULL, NULL, '2025-10-25 17:40:16', '2025-10-25 17:40:16'),
(346, 2, NULL, 'Approve Grades', 0, 'Approved grades for assignment #10.', NULL, NULL, '2025-10-25 17:41:18', '2025-10-25 17:41:18'),
(347, 2, NULL, 'Return Grades', 0, 'Returned grades for assignment #10 to teacher.', NULL, NULL, '2025-10-25 17:41:38', '2025-10-25 17:41:38'),
(348, 3, NULL, 'Save Grades', 0, 'Updated grades for 0 quarter entries in Health, well-Being, and Motor Development (Jupiter).', NULL, NULL, '2025-10-25 17:42:01', '2025-10-25 17:42:01'),
(349, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio.neale@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 17:56:32', '2025-10-25 17:56:32'),
(350, 1, 'Admin', 'Create Announcement', 0, 'Added Global announcement: test', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 20:49:18', '2025-10-25 20:49:18'),
(351, 3, NULL, 'Save Grades', 0, 'Updated grades for 8 quarter entries in Health, well-Being, and Motor Development (Jupiter).', NULL, NULL, '2025-10-25 21:02:57', '2025-10-25 21:02:57'),
(352, 3, NULL, 'Save Grades', 0, 'Updated grades for 2 quarter entries in Health, well-Being, and Motor Development (Jupiter).', NULL, NULL, '2025-10-25 21:03:17', '2025-10-25 21:03:17'),
(353, 2, NULL, 'Approve Grades', 0, 'Approved grades for assignment #11.', NULL, NULL, '2025-10-25 21:03:48', '2025-10-25 21:03:48'),
(354, 3, NULL, 'Create Announcement', 0, 'Created announcement no classes', NULL, NULL, '2025-10-25 21:09:27', '2025-10-25 21:09:27'),
(355, 1, 'Admin', 'Add School Year', 0, 'Added school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 21:12:09', '2025-10-25 21:12:09'),
(356, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 21:12:18', '2025-10-25 21:12:18'),
(357, 1, 'Admin', 'Delete School Year', 0, 'Deleted school year 2026-06-15 - 2027-03-19', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 21:12:33', '2025-10-25 21:12:33'),
(358, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2025-09-25 - 2026-06-25', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 21:13:06', '2025-10-25 21:13:06'),
(359, 3, NULL, 'Export Reports', 0, 'Exported reports for 1 students', NULL, NULL, '2025-10-25 21:24:43', '2025-10-25 21:24:43'),
(360, 3, NULL, 'Export Reports', 0, 'Exported reports for 1 students', NULL, NULL, '2025-10-25 21:26:42', '2025-10-25 21:26:42'),
(361, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 21:31:45', '2025-10-25 21:31:45'),
(362, 2, NULL, 'Add Section', 0, 'Added section Earth', NULL, NULL, '2025-10-25 21:34:16', '2025-10-25 21:34:16'),
(363, 2, NULL, 'Update Section', 0, 'Updated section Earth', NULL, NULL, '2025-10-25 21:34:50', '2025-10-25 21:34:50'),
(364, 1, 'Admin', 'Add Subject', 0, 'Added subject English 2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 21:37:35', '2025-10-25 21:37:35'),
(365, 1, 'Admin', 'Reset Password', 1, 'Reset password for tolentino@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-25 21:40:02', '2025-10-25 21:41:07'),
(366, 2, NULL, 'Return Grades', 0, 'Returned grades for assignment #11 to teacher.', NULL, NULL, '2025-10-31 09:05:39', '2025-10-31 09:05:39'),
(367, 3, NULL, 'Save Grades', 0, 'Updated grades for 0 quarter entries in Health, well-Being, and Motor Development (Jupiter).', NULL, NULL, '2025-10-31 09:22:58', '2025-10-31 09:22:58'),
(368, 3, NULL, 'Save Grades', 0, 'Updated grades for 1 entries in Health, well-Being, and Motor Development (Jupiter) for Q1.', NULL, NULL, '2025-10-31 09:39:48', '2025-10-31 09:39:48'),
(369, 3, NULL, 'Save Grades', 0, 'Updated grades for 0 entries in Health, well-Being, and Motor Development (Jupiter) for Q1.', NULL, NULL, '2025-10-31 09:40:36', '2025-10-31 09:40:36'),
(370, 1, 'Admin', 'Delete User', 0, 'Deleted user: student@example.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:50:44', '2025-10-31 09:50:44'),
(371, 1, 'Admin', 'Delete User', 0, 'Deleted user: teacher@example.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:50:47', '2025-10-31 09:50:47'),
(372, 1, 'Admin', 'Delete User', 0, 'Deleted user: registrar@example.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:50:49', '2025-10-31 09:50:49'),
(373, 1, 'Admin', 'Delete User', 0, 'Deleted user: admin@example.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-31 09:50:52', '2025-10-31 09:50:52'),
(374, 2, NULL, 'Delete Section', 0, 'Deleted section Section C - Kindergarten', NULL, NULL, '2025-10-31 09:52:25', '2025-10-31 09:52:25'),
(375, 2, NULL, 'Delete Section', 0, 'Deleted section Earth', NULL, NULL, '2025-10-31 09:52:51', '2025-10-31 09:52:51'),
(376, 2, NULL, 'Delete Enrollment', 0, 'Deleted enrollment ID 35', NULL, NULL, '2025-10-31 09:53:07', '2025-10-31 09:53:07'),
(377, 2, NULL, 'Delete Section', 0, 'Deleted section Jupiter', NULL, NULL, '2025-10-31 09:53:36', '2025-10-31 09:53:36'),
(378, 2, NULL, 'Update Section', 0, 'Updated section Section A - Kindergarten', NULL, NULL, '2025-10-31 09:53:47', '2025-10-31 09:53:47'),
(379, 3, NULL, 'Save Grades', 0, 'Updated grades for 1 entries in Health, well-Being, and Motor Development (Section A - Kindergarten) for Q1.', NULL, NULL, '2025-10-31 10:07:28', '2025-10-31 10:07:28'),
(380, 3, NULL, 'Save Grades', 0, 'Updated grades for 1 entries in Health, well-Being, and Motor Development (Section A - Kindergarten) for Q1.', NULL, NULL, '2025-10-31 10:19:51', '2025-10-31 10:19:51'),
(381, 3, NULL, 'Save Grades', 0, 'Updated grades for 2 entries in Health, well-Being, and Motor Development (Section A - Kindergarten) for Q2.', NULL, NULL, '2025-10-31 10:43:26', '2025-10-31 10:43:26'),
(382, 3, NULL, 'Save Grades', 0, 'Updated grades for 4 entries in Health, well-Being, and Motor Development (Section A - Kindergarten) for Q4.', NULL, NULL, '2025-10-31 10:44:05', '2025-10-31 10:44:05'),
(383, 3, NULL, 'Save Grades', 0, 'Updated 4 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q4.', NULL, NULL, '2025-10-31 11:21:40', '2025-10-31 11:21:40'),
(384, 3, NULL, 'Save Grades', 0, 'Updated 4 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q4.', NULL, NULL, '2025-11-02 10:55:26', '2025-11-02 10:55:26'),
(385, 3, NULL, 'Save Grades', 0, 'Updated 4 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q4.', NULL, NULL, '2025-11-02 11:27:19', '2025-11-02 11:27:19'),
(386, 3, NULL, 'Save Grades', 0, 'Updated 4 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q4.', NULL, NULL, '2025-11-02 11:28:56', '2025-11-02 11:28:56'),
(387, 3, NULL, 'Save Grades', 0, 'Updated 4 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q4.', NULL, NULL, '2025-11-02 11:29:53', '2025-11-02 11:29:53'),
(388, 3, NULL, 'Save Grades', 0, 'Updated 4 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q4.', NULL, NULL, '2025-11-02 12:24:01', '2025-11-02 12:24:01'),
(389, 2, NULL, 'Approve Grades', 0, 'Approved grades for assignment #12.', NULL, NULL, '2025-11-02 12:24:11', '2025-11-02 12:24:11'),
(390, 2, NULL, 'Return Grades', 0, 'Returned grades for assignment #12 to teacher.', NULL, NULL, '2025-11-02 12:32:03', '2025-11-02 12:32:03'),
(391, 2, NULL, 'Approve Grades', 0, 'Approved grades for assignment #12.', NULL, NULL, '2025-11-02 12:32:11', '2025-11-02 12:32:11'),
(392, 3, NULL, 'Save Grades', 0, 'Updated 0 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q4.', NULL, NULL, '2025-11-02 12:44:15', '2025-11-02 12:44:15'),
(393, 3, NULL, 'Save Grades', 0, 'Updated 1 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q1.', NULL, NULL, '2025-11-02 20:17:47', '2025-11-02 20:17:47'),
(394, 2, NULL, 'Approve Grades', 0, 'Approved grades for assignment #12.', NULL, NULL, '2025-11-02 20:18:01', '2025-11-02 20:18:01'),
(395, 3, NULL, 'Save Grades', 0, 'Updated 1 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q1.', NULL, NULL, '2025-11-02 20:22:56', '2025-11-02 20:22:56'),
(396, 2, NULL, 'Approve Grades', 0, 'Approved grades for assignment #12.', NULL, NULL, '2025-11-02 20:23:03', '2025-11-02 20:23:03'),
(397, 2, NULL, 'Return Grades', 0, 'Returned grades for assignment #12 to teacher.', NULL, NULL, '2025-11-02 20:23:32', '2025-11-02 20:23:32'),
(398, 2, NULL, 'Approve Grades', 0, 'Approved grades for assignment #12.', NULL, NULL, '2025-11-02 20:26:24', '2025-11-02 20:26:24'),
(399, 2, NULL, 'Return Grades', 0, 'Returned grades for assignment #12 to teacher.', NULL, NULL, '2025-11-02 20:27:24', '2025-11-02 20:27:24'),
(400, 3, NULL, 'Save Grades', 0, 'Updated 2 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q3.', NULL, NULL, '2025-11-02 20:27:35', '2025-11-02 20:27:35'),
(401, 3, NULL, 'Save Grades', 0, 'Updated 3 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q3.', NULL, NULL, '2025-11-02 20:27:41', '2025-11-02 20:27:41'),
(402, 2, NULL, 'Approve Grades', 0, 'Approved grades for assignment #12.', NULL, NULL, '2025-11-02 20:27:47', '2025-11-02 20:27:47'),
(403, 2, NULL, 'Return Grades', 0, 'Returned grades for assignment #12 to teacher.', NULL, NULL, '2025-11-02 20:28:17', '2025-11-02 20:28:17'),
(404, 3, NULL, 'Save Grades', 0, 'Updated 3 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q3.', NULL, NULL, '2025-11-02 20:28:26', '2025-11-02 20:28:26'),
(405, 3, NULL, 'Save Grades', 0, 'Updated 4 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q4.', NULL, NULL, '2025-11-02 20:28:56', '2025-11-02 20:28:56'),
(406, 2, NULL, 'Approve Grades', 0, 'Approved grades for assignment #12.', NULL, NULL, '2025-11-02 20:29:05', '2025-11-02 20:29:05'),
(407, 2, NULL, 'Update Status', 0, 'Set assignment #12 to status \'returned\'.', NULL, NULL, '2025-11-02 20:43:43', '2025-11-02 20:43:43'),
(408, 3, NULL, 'Save Grades', 0, 'Updated 1 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q1.', NULL, NULL, '2025-11-02 20:44:18', '2025-11-02 20:44:18'),
(409, 3, NULL, 'Save Grades', 0, 'Updated 1 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q1.', NULL, NULL, '2025-11-02 20:45:08', '2025-11-02 20:45:08'),
(410, 2, NULL, 'Update Status', 0, 'Set assignment #12 to status \'returned\'.', NULL, NULL, '2025-11-02 20:49:37', '2025-11-02 20:49:37'),
(411, 3, NULL, 'Save Grades', 0, 'Updated 1 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q1.', NULL, NULL, '2025-11-02 20:49:48', '2025-11-02 20:49:48'),
(412, 3, NULL, 'Save Grades', 0, 'Updated 0 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in 1 quarter.', NULL, NULL, '2025-11-02 20:52:03', '2025-11-02 20:52:03'),
(413, 3, NULL, 'Save Grades', 0, 'Updated 1 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q1.', NULL, NULL, '2025-11-02 20:54:06', '2025-11-02 20:54:06'),
(414, 2, NULL, 'Return Grades', 0, 'Returned grades for assignment #12 to teacher.', NULL, NULL, '2025-11-02 20:55:37', '2025-11-02 20:55:37'),
(415, 3, NULL, 'Save Grades', 0, 'Updated 1 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q1.', NULL, NULL, '2025-11-02 20:55:55', '2025-11-02 20:55:55'),
(416, 3, NULL, 'Save Grades', 0, 'Updated 1 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q1.', NULL, NULL, '2025-11-02 20:56:51', '2025-11-02 20:56:51'),
(417, 3, NULL, 'Save Grades', 0, 'Updated 0 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q1.', NULL, NULL, '2025-11-02 21:00:10', '2025-11-02 21:00:10'),
(418, 3, NULL, 'Save Grades', 0, 'Updated 0 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q4.', NULL, NULL, '2025-11-02 21:00:36', '2025-11-02 21:00:36'),
(419, 3, NULL, 'Save Grades', 0, 'Updated 1 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q1.', NULL, NULL, '2025-11-02 21:04:28', '2025-11-02 21:04:28'),
(420, 2, NULL, 'Approve Grades', 0, 'Approved Quarter 1 for assignment #12.', NULL, NULL, '2025-11-02 21:04:36', '2025-11-02 21:04:36'),
(421, 2, NULL, 'Update Status', 0, 'Set assignment #12 to status \'returned\'.', NULL, NULL, '2025-11-02 21:08:58', '2025-11-02 21:08:58');
INSERT INTO `activity_logs` (`id`, `user_id`, `role`, `action`, `is_archived`, `description`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(422, 3, NULL, 'Save Grades', 0, 'Updated 1 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q2.', NULL, NULL, '2025-11-02 21:09:42', '2025-11-02 21:09:42'),
(423, 2, NULL, 'Approve Grades', 0, 'Approved Quarter 2 for assignment #12.', NULL, NULL, '2025-11-02 21:09:56', '2025-11-02 21:09:56'),
(424, 2, NULL, 'Update Status', 0, 'Set assignment #12 to status \'returned\'.', NULL, NULL, '2025-11-02 21:10:45', '2025-11-02 21:10:45'),
(425, 2, NULL, 'Update Status', 0, 'Set assignment #12 to status \'returned\'.', NULL, NULL, '2025-11-02 21:12:35', '2025-11-02 21:12:35'),
(426, 2, NULL, 'Return Grades', 0, 'Returned grades for assignment #12 to teacher.', NULL, NULL, '2025-11-02 21:15:51', '2025-11-02 21:15:51'),
(427, 3, NULL, 'Save Grades', 0, 'Updated 0 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q2.', NULL, NULL, '2025-11-02 21:16:01', '2025-11-02 21:16:01'),
(428, 3, NULL, 'Save Grades', 0, 'Updated 1 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q1.', NULL, NULL, '2025-11-02 21:16:33', '2025-11-02 21:16:33'),
(429, 3, NULL, 'Save Grades', 0, 'Updated 0 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q1.', NULL, NULL, '2025-11-02 21:16:53', '2025-11-02 21:16:53'),
(430, 3, NULL, 'Save Grades', 0, 'Updated 1 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q1.', NULL, NULL, '2025-11-02 21:25:18', '2025-11-02 21:25:18'),
(431, 2, NULL, 'Approve Grades', 0, 'Approved grades for assignment #12.', NULL, NULL, '2025-11-02 21:25:23', '2025-11-02 21:25:23'),
(432, 2, NULL, 'Return Grades', 0, 'Returned grades for assignment #12 to teacher.', NULL, NULL, '2025-11-02 21:27:54', '2025-11-02 21:27:54'),
(433, 3, NULL, 'Save Grades', 0, 'Updated 4 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q4.', NULL, NULL, '2025-11-02 21:28:26', '2025-11-02 21:28:26'),
(434, 2, NULL, 'Approve Grades', 0, 'Approved grades for assignment #12.', NULL, NULL, '2025-11-02 21:28:30', '2025-11-02 21:28:30'),
(435, 2, NULL, 'Return Grades', 0, 'Returned grades for assignment #12 to teacher.', NULL, NULL, '2025-11-02 21:29:01', '2025-11-02 21:29:01'),
(436, 3, NULL, 'Save Grades', 0, 'Updated 0 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q4.', NULL, NULL, '2025-11-02 21:29:11', '2025-11-02 21:29:11'),
(437, 3, NULL, 'Create Announcement', 0, 'Created announcement No classes for tomorrow', NULL, NULL, '2025-11-02 21:58:14', '2025-11-02 21:58:14'),
(438, 3, NULL, 'Delete Announcement', 0, 'Deleted announcement 22', NULL, NULL, '2025-11-02 21:58:19', '2025-11-02 21:58:19'),
(439, 3, NULL, 'Export Reports', 0, 'Exported reports for 1 students', NULL, NULL, '2025-11-02 22:04:18', '2025-11-02 22:04:18'),
(440, 2, NULL, 'Archive Subject', 0, 'Archived subject English 2', NULL, NULL, '2025-11-02 22:28:14', '2025-11-02 22:28:14'),
(441, 2, NULL, 'Archive Subject', 0, 'Archived subject English 2', NULL, NULL, '2025-11-02 22:37:10', '2025-11-02 22:37:10'),
(442, 2, NULL, 'Archive Subject', 0, 'Archived subject English 2', NULL, NULL, '2025-11-02 22:39:48', '2025-11-02 22:39:48'),
(443, 2, NULL, 'Restore Subject', 0, 'Restored subject English 2', NULL, NULL, '2025-11-02 22:40:10', '2025-11-02 22:40:10'),
(444, 2, NULL, 'Archive Subject', 0, 'Archived subject English 2', NULL, NULL, '2025-11-02 22:40:16', '2025-11-02 22:40:16'),
(445, 2, NULL, 'Restore Subject', 0, 'Restored subject English 2', NULL, NULL, '2025-11-02 22:42:01', '2025-11-02 22:42:01'),
(446, 2, NULL, 'Archive Subject', 0, 'Archived subject English 2', NULL, NULL, '2025-11-02 22:42:07', '2025-11-02 22:42:07'),
(447, 2, NULL, 'Restore Subject', 0, 'Restored subject English 2', NULL, NULL, '2025-11-02 22:43:17', '2025-11-02 22:43:17'),
(448, 2, NULL, 'Update Section', 0, 'Updated section Section A - Kindergarten', NULL, NULL, '2025-11-02 23:12:14', '2025-11-02 23:12:14'),
(449, 2, NULL, 'Update Section', 0, 'Updated section Section A - Kindergarten', NULL, NULL, '2025-11-02 23:12:26', '2025-11-02 23:12:26'),
(450, 2, NULL, 'Archive Section', 0, 'Archived section Section C - Grade 6', NULL, NULL, '2025-11-02 23:20:41', '2025-11-02 23:20:41'),
(451, 2, NULL, 'Restore Section', 0, 'Restored section Section C - Grade 6', NULL, NULL, '2025-11-02 23:20:49', '2025-11-02 23:20:49'),
(452, 2, NULL, 'Archive Section', 0, 'Archived section Section C - Grade 6', NULL, NULL, '2025-11-02 23:22:19', '2025-11-02 23:22:19'),
(453, 2, NULL, 'Restore Section', 0, 'Restored section Section C - Grade 6', NULL, NULL, '2025-11-02 23:26:04', '2025-11-02 23:26:04'),
(454, 2, NULL, 'Archive Section', 0, 'Archived section Section C - Grade 6', NULL, NULL, '2025-11-02 23:26:12', '2025-11-02 23:26:12'),
(455, 2, NULL, 'Restore Section', 0, 'Restored section Section C - Grade 6', NULL, NULL, '2025-11-02 23:27:10', '2025-11-02 23:27:10'),
(456, 2, NULL, 'Archive Section', 0, 'Archived section Section C - Grade 6', NULL, NULL, '2025-11-02 23:28:53', '2025-11-02 23:28:53'),
(457, 2, NULL, 'Restore Section', 0, 'Restored section Section C - Grade 6', NULL, NULL, '2025-11-02 23:29:10', '2025-11-02 23:29:10'),
(458, 2, NULL, 'Update Section', 0, 'Updated section Section B - Kindergarten', NULL, NULL, '2025-11-02 23:30:02', '2025-11-02 23:30:02'),
(459, 2, NULL, 'Update Section', 0, 'Updated section Section A - Grade 1', NULL, NULL, '2025-11-02 23:30:10', '2025-11-02 23:30:10'),
(460, 2, NULL, 'Update Section', 0, 'Updated section Section B - Grade 1', NULL, NULL, '2025-11-02 23:30:16', '2025-11-02 23:30:16'),
(461, 2, NULL, 'Update Section', 0, 'Updated section Section C - Grade 1', NULL, NULL, '2025-11-02 23:30:21', '2025-11-02 23:30:21'),
(462, 2, NULL, 'Update Section', 0, 'Updated section Section A - Grade 2', NULL, NULL, '2025-11-02 23:30:25', '2025-11-02 23:30:25'),
(463, 2, NULL, 'Update Section', 0, 'Updated section Section B - Grade 2', NULL, NULL, '2025-11-02 23:30:30', '2025-11-02 23:30:30'),
(464, 2, NULL, 'Update Section', 0, 'Updated section Section C - Grade 2', NULL, NULL, '2025-11-02 23:30:34', '2025-11-02 23:30:34'),
(465, 2, NULL, 'Update Section', 0, 'Updated section Section A - Grade 3', NULL, NULL, '2025-11-02 23:30:38', '2025-11-02 23:30:38'),
(466, 2, NULL, 'Update Section', 0, 'Updated section Section B - Grade 3', NULL, NULL, '2025-11-02 23:30:43', '2025-11-02 23:30:43'),
(467, 2, NULL, 'Update Section', 0, 'Updated section Section C - Grade 3', NULL, NULL, '2025-11-02 23:30:50', '2025-11-02 23:30:50'),
(468, 2, NULL, 'Update Section', 0, 'Updated section Section A - Grade 4', NULL, NULL, '2025-11-02 23:30:54', '2025-11-02 23:30:54'),
(469, 2, NULL, 'Update Section', 0, 'Updated section Section B - Grade 4', NULL, NULL, '2025-11-02 23:30:58', '2025-11-02 23:30:58'),
(470, 2, NULL, 'Update Section', 0, 'Updated section Section C - Grade 4', NULL, NULL, '2025-11-02 23:31:02', '2025-11-02 23:31:02'),
(471, 2, NULL, 'Update Section', 0, 'Updated section Section A - Grade 5', NULL, NULL, '2025-11-02 23:31:06', '2025-11-02 23:31:06'),
(472, 2, NULL, 'Update Section', 0, 'Updated section Section B - Grade 5', NULL, NULL, '2025-11-02 23:31:10', '2025-11-02 23:31:10'),
(473, 2, NULL, 'Update Section', 0, 'Updated section Section C - Grade 5', NULL, NULL, '2025-11-02 23:31:14', '2025-11-02 23:31:14'),
(474, 2, NULL, 'Update Section', 0, 'Updated section Section A - Grade 6', NULL, NULL, '2025-11-02 23:31:17', '2025-11-02 23:31:17'),
(475, 2, NULL, 'Update Section', 0, 'Updated section Section B - Grade 6', NULL, NULL, '2025-11-02 23:31:23', '2025-11-02 23:31:23'),
(476, 2, NULL, 'Update Section', 0, 'Updated section Section C - Grade 6', NULL, NULL, '2025-11-02 23:31:27', '2025-11-02 23:31:27'),
(477, 2, NULL, 'Update Section', 0, 'Updated section Section B - Kindergarten', NULL, NULL, '2025-11-02 23:54:20', '2025-11-02 23:54:20'),
(478, 2, NULL, 'Update Section', 0, 'Updated section Section A - Grade 1', NULL, NULL, '2025-11-02 23:54:26', '2025-11-02 23:54:26'),
(479, 2, NULL, 'Update Section', 0, 'Updated section Section B - Grade 1', NULL, NULL, '2025-11-02 23:54:33', '2025-11-02 23:54:33'),
(480, 2, NULL, 'Update Section', 0, 'Updated section Section C - Grade 1', NULL, NULL, '2025-11-02 23:54:38', '2025-11-02 23:54:38'),
(481, 2, NULL, 'Update Section', 0, 'Updated section Section A - Grade 2', NULL, NULL, '2025-11-02 23:54:45', '2025-11-02 23:54:45'),
(482, 1, 'Admin', 'Reset Password', 0, 'Reset password for legaspi.elijah@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-05 19:13:34', '2025-11-05 19:13:34'),
(483, 1, 'Admin', 'Reset Password', 0, 'Reset password for legaspi.elijah@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-05 19:35:15', '2025-11-05 19:35:15'),
(484, 1, 'Admin', 'Reset Password', 0, 'Reset password for legaspi.elijah@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-05 19:35:35', '2025-11-05 19:35:35'),
(485, 1, 'Admin', 'Archive School Year', 0, 'Archived school year 2025-09-25 - 2026-06-25', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-05 19:57:30', '2025-11-05 19:57:30'),
(486, 1, 'Admin', 'Restore School Year', 0, 'Restored school year 2025-09-25 - 2026-06-25', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-05 19:57:34', '2025-11-05 19:57:34'),
(487, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2025-09-25 - 2026-06-25, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-05 20:05:31', '2025-11-05 20:05:31'),
(488, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2025-09-25 - 2026-06-25, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-05 20:05:42', '2025-11-05 20:05:42'),
(489, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-05 20:05:58', '2025-11-05 20:05:58'),
(490, 2, NULL, 'Return Grades', 0, 'Returned grades for assignment #12.', NULL, NULL, '2025-11-11 19:38:46', '2025-11-11 19:38:46'),
(491, 3, NULL, 'Save Grades', 0, 'Updated 1 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q1.', NULL, NULL, '2025-11-11 19:39:46', '2025-11-11 19:39:46'),
(492, 2, NULL, 'Approve Grades', 0, 'Approved Q1 grades for assignment #12.', NULL, NULL, '2025-11-11 19:39:56', '2025-11-11 19:39:56'),
(493, 2, NULL, 'Return Grades', 0, 'Returned grades for assignment #12.', NULL, NULL, '2025-11-11 19:40:31', '2025-11-11 19:40:31'),
(494, 3, NULL, 'Save Grades', 0, 'Updated 2 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q2.', NULL, NULL, '2025-11-11 19:40:58', '2025-11-11 19:40:58'),
(495, 3, NULL, 'Save Grades', 0, 'Updated 0 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q2.', NULL, NULL, '2025-11-11 19:41:12', '2025-11-11 19:41:12'),
(496, 3, NULL, 'Save Grades', 0, 'Updated 1 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q1.', NULL, NULL, '2025-11-11 19:54:41', '2025-11-11 19:54:41'),
(497, 2, NULL, 'Approve Grades', 0, 'Approved 1 quarter grades for assignment #12.', NULL, NULL, '2025-11-11 19:54:51', '2025-11-11 19:54:51'),
(498, 2, NULL, 'Return Grades', 0, 'Returned 1 quarter grades for assignment #12 to teacher.', NULL, NULL, '2025-11-11 19:59:00', '2025-11-11 19:59:00'),
(499, 3, NULL, 'Save Grades', 0, 'Updated 1 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q1.', NULL, NULL, '2025-11-11 19:59:08', '2025-11-11 19:59:08'),
(500, 2, NULL, 'Approve Grades', 0, 'Approved 1 quarter grades for assignment #12.', NULL, NULL, '2025-11-11 19:59:15', '2025-11-11 19:59:15'),
(501, 2, NULL, 'Return Grades', 0, 'Returned 1 quarter grades for assignment #12 to teacher.', NULL, NULL, '2025-11-11 20:11:23', '2025-11-11 20:11:23'),
(502, 2, NULL, 'Approve Grades', 0, 'Approved 1 quarter grades for assignment #12.', NULL, NULL, '2025-11-11 20:11:33', '2025-11-11 20:11:33'),
(503, 2, NULL, 'Return Grades', 0, 'Returned 2 quarter grades for assignment #12 to teacher.', NULL, NULL, '2025-11-11 20:12:10', '2025-11-11 20:12:10'),
(504, 3, NULL, 'Save Grades', 0, 'Updated 1 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q2.', NULL, NULL, '2025-11-11 20:12:30', '2025-11-11 20:12:30'),
(505, 2, NULL, 'Approve Grades', 0, 'Approved 2 quarter grades for assignment #12.', NULL, NULL, '2025-11-11 20:12:42', '2025-11-11 20:12:42'),
(506, 2, NULL, 'Return Grades', 0, 'Returned 2 quarter grades for assignment #12 to teacher.', NULL, NULL, '2025-11-11 20:12:56', '2025-11-11 20:12:56'),
(507, 3, NULL, 'Save Grades', 0, 'Updated 0 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q2.', NULL, NULL, '2025-11-11 20:13:11', '2025-11-11 20:13:11'),
(508, 2, NULL, 'Return Grades', 0, 'Returned 1 quarter grades for assignment #12 to teacher.', NULL, NULL, '2025-11-11 20:13:36', '2025-11-11 20:13:36'),
(509, 3, NULL, 'Save Grades', 0, 'Updated 0 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q1.', NULL, NULL, '2025-11-11 20:13:46', '2025-11-11 20:13:46'),
(510, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio.emil@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-11 20:16:58', '2025-11-11 20:16:58'),
(511, 1, 'Admin', 'Delete User', 0, 'Deleted user: dionisio.neale@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-11 20:17:36', '2025-11-11 20:17:36'),
(512, 1, 'Admin', 'Delete User', 0, 'Deleted user: legaspi.emil christian@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-11 20:17:42', '2025-11-11 20:17:42'),
(513, 1, 'Admin', 'Create Announcement', 0, 'Added Global announcement: For testing', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-11 20:28:21', '2025-11-11 20:28:21'),
(514, 3, NULL, 'Update Announcement', 0, 'Updated announcement 23', NULL, NULL, '2025-11-11 20:29:32', '2025-11-11 20:29:32'),
(515, 3, NULL, 'Create Announcement', 0, 'Created announcement Testing', NULL, NULL, '2025-11-11 20:38:12', '2025-11-11 20:38:12'),
(516, 3, NULL, 'Update Announcement', 0, 'Updated announcement 23', NULL, NULL, '2025-11-11 20:39:39', '2025-11-11 20:39:39'),
(517, 1, 'Admin', 'Reset Password', 0, 'Reset password for ciao@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-11 20:53:49', '2025-11-11 20:53:49'),
(518, 1, 'Admin', 'Reset Password', 0, 'Reset password for legaspi.elijah@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-11 20:54:21', '2025-11-11 20:54:21'),
(519, NULL, NULL, 'Update Profile', 0, 'Updated profile for legaspi.elijah@mindware.edu.ph', NULL, NULL, '2025-11-11 20:56:46', '2025-11-11 20:56:46'),
(520, NULL, NULL, 'Change Password', 0, 'Changed password for legaspi.elijah@mindware.edu.ph', NULL, NULL, '2025-11-11 21:05:52', '2025-11-11 21:05:52'),
(521, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2025-09-25 - 2026-06-25, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 21:27:36', '2025-11-11 21:27:36'),
(522, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 21:29:53', '2025-11-11 21:29:53'),
(523, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2025-09-25 - 2026-06-25, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 21:40:56', '2025-11-11 21:40:56'),
(524, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2025-09-25 - 2026-06-25', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 21:41:03', '2025-11-11 21:41:03'),
(525, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2025-09-25 - 2026-06-25, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 21:42:16', '2025-11-11 21:42:16'),
(526, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 21:42:39', '2025-11-11 21:42:39'),
(527, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2025-09-25 - 2026-06-25, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 22:01:08', '2025-11-11 22:01:08'),
(528, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2025-09-25 - 2026-06-25', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 22:01:47', '2025-11-11 22:01:47'),
(529, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 22:02:13', '2025-11-11 22:02:13'),
(530, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 22:03:47', '2025-11-11 22:03:47'),
(531, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2025-09-25 - 2026-06-25, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 22:13:08', '2025-11-11 22:13:08'),
(532, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 22:13:14', '2025-11-11 22:13:14'),
(533, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 22:13:22', '2025-11-11 22:13:22'),
(534, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 22:13:50', '2025-11-11 22:13:50'),
(535, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 22:21:40', '2025-11-11 22:21:40'),
(536, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 22:22:05', '2025-11-11 22:22:05'),
(537, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 22:26:17', '2025-11-11 22:26:17'),
(538, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 22:26:29', '2025-11-11 22:26:29'),
(539, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 22:29:46', '2025-11-11 22:29:46'),
(540, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 22:47:01', '2025-11-11 22:47:01'),
(541, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 22:59:34', '2025-11-11 22:59:34'),
(542, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
(543, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 23:04:24', '2025-11-11 23:04:24'),
(544, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 23:04:37', '2025-11-11 23:04:37'),
(545, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 23:05:02', '2025-11-11 23:05:02'),
(546, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 23:05:11', '2025-11-11 23:05:11'),
(547, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 23:05:32', '2025-11-11 23:05:32'),
(548, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-11 23:06:11', '2025-11-11 23:06:11'),
(549, 1, 'Admin', 'Archive School Year', 0, 'Archived school year 2025-09-25 - 2026-06-25', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 08:25:58', '2025-11-12 08:25:58'),
(550, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 08:34:03', '2025-11-12 08:34:03'),
(551, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 08:34:36', '2025-11-12 08:34:36'),
(552, 3, NULL, 'Create Announcement', 0, 'Created announcement hello', NULL, NULL, '2025-11-12 08:38:52', '2025-11-12 08:38:52'),
(553, 3, NULL, 'Delete Announcement', 0, 'Deleted announcement 26', NULL, NULL, '2025-11-12 08:38:56', '2025-11-12 08:38:56'),
(554, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 08:46:51', '2025-11-12 08:46:51'),
(555, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 08:48:20', '2025-11-12 08:48:20'),
(556, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 09:06:03', '2025-11-12 09:06:03'),
(557, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 09:06:49', '2025-11-12 09:06:49'),
(558, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 09:23:32', '2025-11-12 09:23:32'),
(559, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 09:24:38', '2025-11-12 09:24:38'),
(560, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 19:13:02', '2025-11-12 19:13:02'),
(561, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 19:26:05', '2025-11-12 19:26:05'),
(562, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 19:32:19', '2025-11-12 19:32:19'),
(563, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 19:38:17', '2025-11-12 19:38:17'),
(564, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 19:38:28', '2025-11-12 19:38:28'),
(565, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 19:47:57', '2025-11-12 19:47:57'),
(566, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 19:54:13', '2025-11-12 19:54:13'),
(567, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-12 20:02:36', '2025-11-12 20:02:36'),
(568, 1, 'Admin', 'Add School Year', 0, 'Added school year 2027-03-15 - 2028-06-16', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-13 01:02:31', '2025-11-13 01:02:31'),
(569, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2027-03-15 - 2028-06-16, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-13 01:02:37', '2025-11-13 01:02:37'),
(570, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2027-03-15 - 2028-06-16', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-13 01:04:14', '2025-11-13 01:04:14'),
(571, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-13 01:04:35', '2025-11-13 01:04:35'),
(572, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-13 01:08:27', '2025-11-13 01:08:27'),
(573, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-13 01:08:52', '2025-11-13 01:08:52'),
(574, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-13 01:30:38', '2025-11-13 01:30:38'),
(575, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-13 01:32:41', '2025-11-13 01:32:41'),
(576, 2, NULL, 'Archive Section', 0, 'Archived section Section C - Grade 6', NULL, NULL, '2025-11-13 02:27:38', '2025-11-13 02:27:38'),
(577, 2, NULL, 'Restore Section', 0, 'Restored section Section C - Grade 6', NULL, NULL, '2025-11-13 02:27:44', '2025-11-13 02:27:44'),
(578, 1, 'Admin', 'Delete User', 0, 'Deleted user: legaspi.elijah@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-13 02:35:40', '2025-11-13 02:35:40'),
(579, 92, NULL, 'Update Profile', 0, 'Updated profile for legaspi.elijah@mindware.edu.ph', NULL, NULL, '2025-11-13 02:36:43', '2025-11-13 02:36:43'),
(580, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-13 03:00:09', '2025-11-13 03:00:09'),
(581, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-13 03:05:43', '2025-11-13 03:05:43'),
(582, 1, 'Admin', 'Reset Password', 0, 'Reset password for tolentino@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-13 03:57:36', '2025-11-13 03:57:36'),
(583, 1, 'Admin', 'Reset Password', 0, 'Reset password for carreon@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-13 03:58:00', '2025-11-13 03:58:00'),
(584, 1, 'Admin', 'Reset Password', 0, 'Reset password for tolentino@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-13 03:59:26', '2025-11-13 03:59:26'),
(585, 3, NULL, 'Save Grades', 0, 'Updated 0 grade entries for Health, well-Being, and Motor Development (Section A - Kindergarten) in Q1.', NULL, NULL, '2025-11-13 04:31:23', '2025-11-13 04:31:23'),
(586, 34, NULL, 'Save Grades', 0, 'Updated 0 grade entries for Socio-Emotional Development (Section B - Kindergarten) in Q1.', NULL, NULL, '2025-11-13 04:33:05', '2025-11-13 04:33:05'),
(587, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2027-03-15 - 2028-06-16, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-13 09:25:44', '2025-11-13 09:25:44'),
(588, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-13 09:26:00', '2025-11-13 09:26:00'),
(589, 1, 'Admin', 'Close School Year', 0, 'Closed school year 2026-03-20 - 2027-12-21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-13 09:26:03', '2025-11-13 09:26:03'),
(590, 1, 'Admin', 'Activate School Year', 0, 'Activated school year 2026-03-20 - 2027-12-21, reactivated its enrollments, and inactivated previous years.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-13 09:26:18', '2025-11-13 09:26:18'),
(591, 1, 'Admin', 'Update User', 0, 'Updated user: carreon@mindware.edu.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-13 09:27:12', '2025-11-13 09:27:12');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `target_type` varchar(255) DEFAULT NULL,
  `target_id` bigint(20) UNSIGNED DEFAULT NULL,
  `section_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `expires_at`, `user_id`, `target_type`, `target_id`, `section_id`, `created_at`, `updated_at`) VALUES
(15, 'test', 'teachers', '2025-10-22 22:08:00', 1, 'Teacher', NULL, NULL, '2025-10-17 23:05:21', '2025-10-17 23:05:21'),
(21, 'test', 'test', '2025-10-28 06:49:00', 1, 'Global', NULL, NULL, '2025-10-25 20:49:18', '2025-10-25 20:49:18'),
(23, 'No classes', 'Due to typhoon. Keep safe!', NULL, 3, 'Student', NULL, 32, '2025-11-02 21:58:14', '2025-11-11 20:39:39'),
(24, 'For testing', 'For all audience', '2025-11-19 06:30:00', 1, 'Global', NULL, NULL, '2025-11-11 20:28:21', '2025-11-11 20:28:21'),
(25, 'Testing', 'Hello students!', NULL, 3, 'Student', NULL, 32, '2025-11-11 20:38:12', '2025-11-11 20:38:12');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('childrens-mindware-school-inc-cache-carla@mindware.edu.ph|127.0.0.1', 'i:3;', 1761142165),
('childrens-mindware-school-inc-cache-carla@mindware.edu.ph|127.0.0.1:timer', 'i:1761142165;', 1761142165),
('childrens-mindware-school-inc-cache-legaspi@mindware.edu.ph|127.0.0.1', 'i:1;', 1761373065),
('childrens-mindware-school-inc-cache-legaspi@mindware.edu.ph|127.0.0.1:timer', 'i:1761373065;', 1761373065),
('childrens-mindware-school-inc-cache-registrar@gmail.com|127.0.0.1', 'i:2;', 1763003745),
('childrens-mindware-school-inc-cache-registrar@gmail.com|127.0.0.1:timer', 'i:1763003745;', 1763003745),
('childrens-mindware-school-inc-cache-student@mindware.edu.ph|127.0.0.1', 'i:1;', 1763055389),
('childrens-mindware-school-inc-cache-student@mindware.edu.ph|127.0.0.1:timer', 'i:1763055389;', 1763055389),
('laravel-cache-student1@gmail.com|127.0.0.1', 'i:1;', 1759545299),
('laravel-cache-student1@gmail.com|127.0.0.1:timer', 'i:1759545299;', 1759545299),
('laravel-cache-teacher1@example.com|127.0.0.1', 'i:1;', 1759770339),
('laravel-cache-teacher1@example.com|127.0.0.1:timer', 'i:1759770339;', 1759770339);

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
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `school_year_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('For Verification','Enrolled','Dropped','Transferred','Inactive') NOT NULL DEFAULT 'For Verification',
  `archived` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `section_id`, `school_year_id`, `status`, `archived`, `created_at`, `updated_at`) VALUES
(37, 45, 32, 14, 'Enrolled', 0, '2025-11-13 01:07:49', '2025-11-13 09:26:18'),
(38, 46, 33, 14, 'Enrolled', 0, '2025-11-13 01:23:27', '2025-11-13 09:26:18'),
(39, 47, 33, 14, 'Enrolled', 0, '2025-11-13 01:29:03', '2025-11-13 09:26:18'),
(40, 48, 32, 14, 'Enrolled', 0, '2025-11-13 02:36:01', '2025-11-13 09:26:18'),
(41, 49, 35, 14, 'Enrolled', 0, '2025-11-13 02:46:20', '2025-11-13 09:26:18'),
(42, 50, 35, 14, 'Enrolled', 0, '2025-11-13 02:47:28', '2025-11-13 09:26:18');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `school_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quarter` enum('1st','2nd','3rd','4th') NOT NULL,
  `grade` int(11) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grade_levels`
--

CREATE TABLE `grade_levels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grade_levels`
--

INSERT INTO `grade_levels` (`id`, `name`, `created_at`, `updated_at`) VALUES
(19, 'Kindergarten', '2025-10-14 08:43:24', '2025-10-14 08:43:24'),
(20, 'Grade 1', '2025-10-14 08:43:24', '2025-10-14 08:43:24'),
(21, 'Grade 2', '2025-10-14 08:43:24', '2025-10-14 08:43:24'),
(22, 'Grade 3', '2025-10-14 08:43:24', '2025-10-14 08:43:24'),
(23, 'Grade 4', '2025-10-14 08:43:24', '2025-10-14 08:43:24'),
(24, 'Grade 5', '2025-10-14 08:43:24', '2025-10-14 08:43:24'),
(25, 'Grade 6', '2025-10-14 08:43:24', '2025-10-14 08:43:24');

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
(1, '2013_09_09_170435_create_roles_table', 1),
(2, '2014_10_12_000000_create_users_table', 1),
(3, '2025_09_08_180227_create_profiles_table', 1),
(4, '2025_09_08_180228_create_grade_levels_table', 1),
(5, '2025_09_08_180228_create_school_years_table', 1),
(6, '2025_09_08_180228_create_sections_table', 1),
(7, '2025_09_08_180228_create_students_table', 1),
(8, '2025_09_08_180228_create_teachers_table', 1),
(9, '2025_09_08_180229_create_subjects_table', 1),
(10, '2025_09_08_180230_create_assignments_table', 1),
(11, '2025_09_08_180230_create_enrollments_table', 1),
(12, '2025_09_08_180231_create_announcements_table', 1),
(13, '2025_09_08_180231_create_grades_table', 1),
(14, '2025_09_09_101200_create_sessions_table', 1),
(15, '2025_09_09_101201_create_cache_table', 1),
(16, '2025_09_09_191723_create_activity_logs_table', 1),
(17, '2025_09_11_022831_create_subject_teacher_table', 1),
(18, '2025_09_13_080132_create_password_reset_tokens_table', 1),
(19, '2025_09_15_063117_create_settings_table', 2),
(20, '2025_09_17_180115_add_last_login_at_to_users_table', 3),
(21, '2025_09_18_080052_add_expires_at_to_announcements_table', 4),
(22, '2025_09_20_042654_add_status_to_users_table', 5),
(23, '2025_09_20_075144_add_capacity_to_sections_table', 6),
(24, '2025_09_28_042850_add_is_archived_to_activity_logs_table', 7),
(25, '2025_10_06_164146_add_target_to_announcements_table', 8),
(26, '2025_10_09_203845_create_student_documents_table', 9),
(27, '2025_10_10_061233_create_student_certificates_table', 10),
(28, '2025_10_10_063022_add_fields_to_student_certificates_table', 10),
(29, '2025_10_19_043551_create_subject_assignments_table', 11),
(30, '2025_10_21_184440_create_grade_submissions_table', 12),
(31, '2025_10_22_051154_add_grade_status_to_subject_assignments_table', 13),
(32, '2025_10_23_210700_add_school_year_id_to_grades_table', 14),
(33, '2025_10_24_113537_update_enrollment_status_enum', 15),
(34, '2025_10_25_102630_add_guardian_name_to_profiles_table', 16),
(35, '2025_10_24_125237_update_status_enum_in_users_table', 17),
(36, '2025_10_31_181002_add_remarks_and_comment_to_grades_table', 17),
(37, '2025_11_03_043543_add_approved_quarters_to_subject_assignments_table', 18),
(38, '2025_11_03_062509_add_is_archived_to_subjects_table', 19),
(39, '2025_11_03_071910_add_soft_deletes_to_sections_table', 20),
(40, '2025_11_06_034931_add_deleted_at_to_school_years_table', 21),
(41, '2025_11_12_032408_add_quarter_lock_to_subject_assignments_table', 22),
(42, '2025_11_12_035048_add_locked_to_grades_table', 22),
(43, '2025_11_13_094513_add_archived_to_enrollments_table', 23);

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
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) NOT NULL DEFAULT 'images/default.png',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `user_id`, `first_name`, `middle_name`, `last_name`, `sex`, `birthdate`, `address`, `contact_number`, `guardian_name`, `profile_picture`, `created_at`, `updated_at`) VALUES
(3, 1, 'Neale', 'D', 'Legaspi', 'Male', '2003-03-01', '123 balagtas, bulacan', '09123123', NULL, 'profile_pictures/vwYLLnAHxaK5iPSkRF83VpEmarUrhiKm7FZdrysB.png', '2025-09-14 01:16:47', '2025-10-22 22:29:34'),
(4, 3, 'Bella', 'W', 'Ciao', 'Male', '2001-12-12', '123 balagtas, bulacan', '09123123', NULL, 'profile_pictures/5POCUEm2YRb8ZL4418BCZthYKPEpFV8NYjzR53Ov.png', '2025-09-14 01:36:04', '2025-10-22 22:32:32'),
(6, 2, 'Juan Miguel', 'C', 'Borja', 'Male', '2004-01-01', '123 balagtas, bulacan', '09123123', NULL, 'profiles/d64UR9aZhfE6QeM1XkYaRHKwhKc0NFPsRq4CfuGu.jpg', '2025-09-14 02:00:50', '2025-10-22 22:31:27'),
(58, 34, 'Jane', 'C', 'Doe', 'Female', '1980-01-04', '123 balagtas, bulacan', '09123123', NULL, 'images/default.png', '2025-10-22 04:45:07', '2025-10-22 04:45:07'),
(60, 36, 'John Wilson', 'M', 'Tolentino', 'Male', '2002-06-15', '123 bagumbayan, bulacan', '09123123123', NULL, 'images/default.png', '2025-10-23 09:37:39', '2025-10-23 09:37:39'),
(61, 37, 'Angelo', 'S', 'Carreon', 'Male', '2002-03-06', '123 bagumbayan, bulacan', '09123123123', NULL, 'images/default.png', '2025-10-23 09:39:53', '2025-10-23 09:39:53'),
(62, 38, 'John Leonard', 'R', 'Chingcuangco', 'Male', '2004-10-12', '123 balagtas, bulacan', '09123123', NULL, 'images/default.png', '2025-10-23 09:41:09', '2025-10-23 09:41:09'),
(63, 39, 'Trisha', NULL, 'Ciao', 'Female', '2000-12-21', '123 San Nicolas, Bulacan', '09182456331', NULL, 'images/default.png', '2025-10-23 09:50:40', '2025-10-23 09:50:40'),
(106, 89, 'Maria', 'P', 'Clara', 'Female', '2000-01-01', '0618, Sadsaran, Sta. Ana,', '09123123', 'Elenita Legaspi', 'images/default.png', '2025-11-13 01:07:49', '2025-11-13 01:07:49'),
(107, 90, 'Neale', 'D', 'Dionisio', 'Male', '2005-01-03', '0618, Sadsaran, Sta. Ana,', '09123123123', 'Elenita Legaspi', 'images/default.png', '2025-11-13 01:23:27', '2025-11-13 01:23:27'),
(108, 91, 'Bella', NULL, 'Wenceslao', 'Female', '2007-01-19', '123 San Nicolas, Bulacan', '09123123098', 'Elenita Legaspi', 'images/default.png', '2025-11-13 01:29:03', '2025-11-13 01:29:03'),
(109, 92, 'Elijah', 'B', 'Legaspi', 'Male', '2003-01-01', '123 San Nicolas, Bulacan', '09182456331', 'Elenita Legaspi', 'profile_pictures/thybqT4ibdVVjMhT0iQUW2y60C7MG7Yk0JzPpeny.png', '2025-11-13 02:36:01', '2025-11-13 02:36:43'),
(110, 93, 'Princess Ashley', NULL, 'Wenceslao', 'Female', '2010-08-28', '123 San Nicolas, Bulacan', '09456789123', 'Roselita Wenceslao', 'images/default.png', '2025-11-13 02:46:20', '2025-11-13 02:46:20'),
(111, 94, 'Zyrone Andrei', 'W', 'Tolentino', 'Male', '2014-04-15', '123 San Nicolas, Bulacan', '09456789123', 'Roselita Wenceslao', 'images/default.png', '2025-11-13 02:47:28', '2025-11-13 02:47:28');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', '2025-09-13 19:54:40', '2025-09-13 19:54:40'),
(2, 'Registrar', '2025-09-13 19:54:40', '2025-09-13 19:54:40'),
(3, 'Teacher', '2025-09-13 19:54:40', '2025-09-13 19:54:40'),
(4, 'Student', '2025-09-13 19:54:40', '2025-09-13 19:54:40');

-- --------------------------------------------------------

--
-- Table structure for table `school_years`
--

CREATE TABLE `school_years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','closed') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `school_years`
--

INSERT INTO `school_years` (`id`, `name`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(11, '2025-09-25 - 2026-06-25', '2025-09-25', '2026-06-25', 'closed', '2025-09-19 21:33:15', '2025-11-12 08:25:58', '2025-11-12 08:25:58'),
(14, '2026-03-20 - 2027-12-21', '2026-03-20', '2027-12-21', 'active', '2025-10-25 21:12:09', '2025-11-13 09:26:18', NULL),
(15, '2027-03-15 - 2028-06-16', '2027-03-15', '2028-06-16', 'closed', '2025-11-13 01:02:31', '2025-11-13 09:26:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `gradelevel_id` bigint(20) UNSIGNED NOT NULL,
  `adviser_id` bigint(20) UNSIGNED DEFAULT NULL,
  `capacity` int(11) NOT NULL DEFAULT 40,
  `school_year_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `name`, `gradelevel_id`, `adviser_id`, `capacity`, `school_year_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(32, 'Section A - Kindergarten', 19, 3, 30, 14, '2025-10-31 09:36:58', '2025-11-02 23:12:14', NULL),
(33, 'Section B - Kindergarten', 19, 34, 30, 14, '2025-10-31 09:36:58', '2025-11-02 23:54:20', NULL),
(35, 'Section A - Grade 1', 20, 36, 30, 14, '2025-10-31 09:36:58', '2025-11-02 23:54:26', NULL),
(36, 'Section B - Grade 1', 20, 37, 30, 14, '2025-10-31 09:36:58', '2025-11-02 23:54:33', NULL),
(37, 'Section C - Grade 1', 20, 38, 30, 14, '2025-10-31 09:36:58', '2025-11-02 23:54:38', NULL),
(38, 'Section A - Grade 2', 21, 39, 30, 14, '2025-10-31 09:36:58', '2025-11-02 23:54:45', NULL),
(39, 'Section B - Grade 2', 21, NULL, 30, 14, '2025-10-31 09:36:58', '2025-11-02 23:30:30', NULL),
(40, 'Section C - Grade 2', 21, NULL, 30, 14, '2025-10-31 09:36:58', '2025-11-02 23:30:34', NULL),
(41, 'Section A - Grade 3', 22, NULL, 30, 14, '2025-10-31 09:36:58', '2025-11-02 23:30:38', NULL),
(42, 'Section B - Grade 3', 22, NULL, 30, 14, '2025-10-31 09:36:58', '2025-11-02 23:30:43', NULL),
(43, 'Section C - Grade 3', 22, NULL, 30, 14, '2025-10-31 09:36:58', '2025-11-02 23:30:50', NULL),
(44, 'Section A - Grade 4', 23, NULL, 30, 14, '2025-10-31 09:36:58', '2025-11-02 23:30:54', NULL),
(45, 'Section B - Grade 4', 23, NULL, 30, 14, '2025-10-31 09:36:58', '2025-11-02 23:30:58', NULL),
(46, 'Section C - Grade 4', 23, NULL, 30, 14, '2025-10-31 09:36:58', '2025-11-02 23:31:02', NULL),
(47, 'Section A - Grade 5', 24, NULL, 30, 14, '2025-10-31 09:36:58', '2025-11-02 23:31:06', NULL),
(48, 'Section B - Grade 5', 24, NULL, 30, 14, '2025-10-31 09:36:58', '2025-11-02 23:31:10', NULL),
(49, 'Section C - Grade 5', 24, NULL, 30, 14, '2025-10-31 09:36:58', '2025-11-02 23:31:14', NULL),
(50, 'Section A - Grade 6', 25, NULL, 30, 14, '2025-10-31 09:36:58', '2025-11-02 23:31:17', NULL),
(51, 'Section B - Grade 6', 25, NULL, 30, 14, '2025-10-31 09:36:58', '2025-11-02 23:31:23', NULL),
(52, 'Section C - Grade 6', 25, NULL, 30, 14, '2025-10-31 09:36:58', '2025-11-13 02:27:44', NULL);

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
('6llgHnPx7XGORLNzS6uMGnLBOkWqoMM5WvFDOkEy', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieGdaWmJOM2Z2WExmbUdxc3drUWpwb2JJNWJ2STJLaXYxZ0plaWFBbiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fX0=', 1763055368);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'active_quarter', '1', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_number` varchar(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `section_id`, `student_number`, `created_at`, `updated_at`) VALUES
(45, 89, 32, '4006550005', '2025-11-13 01:07:49', '2025-11-13 01:07:49'),
(46, 90, 33, '4006550006', '2025-11-13 01:23:27', '2025-11-13 01:23:27'),
(47, 91, 33, '4006550007', '2025-11-13 01:29:03', '2025-11-13 01:29:03'),
(48, 92, 32, '4006550008', '2025-11-13 02:36:01', '2025-11-13 02:36:01'),
(49, 93, 35, '4006550009', '2025-11-13 02:46:20', '2025-11-13 02:46:20'),
(50, 94, 35, '4006550010', '2025-11-13 02:47:28', '2025-11-13 02:47:28');

-- --------------------------------------------------------

--
-- Table structure for table `student_certificates`
--

CREATE TABLE `student_certificates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `issued_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_certificates`
--

INSERT INTO `student_certificates` (`id`, `student_id`, `type`, `purpose`, `remarks`, `file_path`, `issued_by`, `created_at`, `updated_at`) VALUES
(20, 48, 'Enrollment', NULL, NULL, 'certificates/48/enrollment-20.pdf', 2, '2025-11-13 02:49:14', '2025-11-13 02:49:15'),
(21, 45, 'Good Moral', NULL, NULL, 'certificates/45/good-moral-21.pdf', 2, '2025-11-13 02:49:55', '2025-11-13 02:49:56'),
(22, 46, 'Completion', NULL, NULL, 'certificates/46/completion-22.pdf', 2, '2025-11-13 02:50:05', '2025-11-13 02:50:06'),
(23, 47, 'Enrollment', NULL, NULL, 'certificates/47/enrollment-23.pdf', 2, '2025-11-13 02:50:20', '2025-11-13 02:50:21'),
(24, 49, 'Good Moral', NULL, NULL, 'certificates/49/good-moral-24.pdf', 2, '2025-11-13 02:50:32', '2025-11-13 02:50:33');

-- --------------------------------------------------------

--
-- Table structure for table `student_documents`
--

CREATE TABLE `student_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Submitted','Verified') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_documents`
--

INSERT INTO `student_documents` (`id`, `student_id`, `type`, `file_path`, `status`, `created_at`, `updated_at`) VALUES
(6, 50, 'Birth Certificate', 'student_documents/uQozNmU5ot8vm7eb4Wku0W0nB3gmrJ8Pc7JsRwEV.png', 'Pending', '2025-11-13 02:52:11', '2025-11-13 02:52:11'),
(7, 49, 'Birth Certificate', 'student_documents/YOjP88uhE8c5UCh7al4qgr6O3KUYHx3SshtXv54i.png', 'Pending', '2025-11-13 02:53:10', '2025-11-13 02:53:10'),
(8, 48, 'Form 137', 'student_documents/SWK1YN2MVlCF98XkeIOWyfPxIrQF1QheEO76072l.png', 'Pending', '2025-11-13 02:54:40', '2025-11-13 02:54:40');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `grade_level_id` bigint(20) UNSIGNED NOT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `grade_level_id`, `is_archived`, `name`, `created_at`, `updated_at`) VALUES
(46, 19, 0, 'Health, well-Being, and Motor Development', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(47, 19, 0, 'Socio-Emotional Development', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(48, 19, 0, 'Mathematics', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(49, 19, 0, 'Language, Literacy, and Communication', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(50, 19, 0, 'Understanding the Physical and Natural Environment', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(51, 20, 0, 'Mother Tongue', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(52, 20, 0, 'Filipino', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(53, 20, 0, 'English', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(54, 20, 0, 'Mathematics', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(55, 20, 0, 'Araling Panlipunan', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(56, 20, 0, 'MAPEH', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(57, 20, 0, 'Edukasyon sa Pagpapakatao (EsP)', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(58, 21, 0, 'Mother Tongue', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(59, 21, 0, 'Filipino', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(60, 21, 0, 'English', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(61, 21, 0, 'Mathematics', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(62, 21, 0, 'Araling Panlipunan', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(63, 21, 0, 'MAPEH', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(64, 21, 0, 'Edukasyon sa Pagpapakatao (EsP)', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(65, 22, 0, 'Mother Tongue', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(66, 22, 0, 'Filipino', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(67, 22, 0, 'English', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(68, 22, 0, 'Mathematics', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(69, 22, 0, 'Science', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(70, 22, 0, 'Araling Panlipunan', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(71, 22, 0, 'MAPEH', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(72, 22, 0, 'Edukasyon sa Pagpapakatao (EsP)', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(73, 23, 0, 'Filipino', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(74, 23, 0, 'English', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(75, 23, 0, 'Mathematics', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(76, 23, 0, 'Science', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(77, 23, 0, 'Araling Panlipunan', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(78, 23, 0, 'Edukasyon sa Pagpapakatao (EsP)', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(79, 23, 0, 'Edukasyong Pantahanan at Pangkabuhayan (EPP)', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(80, 23, 0, 'MAPEH', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(81, 24, 0, 'Filipino', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(82, 24, 0, 'English', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(83, 24, 0, 'Mathematics', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(84, 24, 0, 'Science', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(85, 24, 0, 'Araling Panlipunan', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(86, 24, 0, 'Edukasyon sa Pagpapakatao (EsP)', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(87, 24, 0, 'Edukasyong Pantahanan at Pangkabuhayan (EPP)', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(88, 24, 0, 'MAPEH', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(89, 25, 0, 'Filipino', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(90, 25, 0, 'English', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(91, 25, 0, 'Mathematics', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(92, 25, 0, 'Science', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(93, 25, 0, 'Araling Panlipunan', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(94, 25, 0, 'Edukasyon sa Pagpapakatao (EsP)', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(95, 25, 0, 'Edukasyong Pantahanan at Pangkabuhayan (EPP)', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(96, 25, 0, 'MAPEH', '2025-10-14 09:09:16', '2025-10-14 09:09:16'),
(97, 21, 0, 'English 2', '2025-10-25 21:37:35', '2025-11-02 22:43:17');

-- --------------------------------------------------------

--
-- Table structure for table `subject_assignments`
--

CREATE TABLE `subject_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `grade_status` enum('draft','submitted','approved','returned') NOT NULL DEFAULT 'draft',
  `approved_quarters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`approved_quarters`)),
  `school_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subject_assignments`
--

INSERT INTO `subject_assignments` (`id`, `section_id`, `subject_id`, `teacher_id`, `grade_status`, `approved_quarters`, `school_year_id`, `created_at`, `updated_at`) VALUES
(12, 32, 46, 3, 'returned', NULL, 14, '2025-10-31 10:05:59', '2025-11-11 20:13:36'),
(13, 33, 47, 34, 'draft', NULL, 14, '2025-11-13 04:32:47', '2025-11-13 04:33:05'),
(14, 35, 52, 36, 'draft', NULL, 14, '2025-11-13 04:33:56', '2025-11-13 04:33:56'),
(15, 36, 53, 37, 'draft', NULL, 14, '2025-11-13 04:34:16', '2025-11-13 04:34:16'),
(16, 37, 54, 38, 'draft', NULL, 14, '2025-11-13 04:34:29', '2025-11-13 04:34:29'),
(17, 38, 64, 39, 'draft', NULL, 14, '2025-11-13 04:34:44', '2025-11-13 04:34:44');

-- --------------------------------------------------------

--
-- Table structure for table `subject_teacher`
--

CREATE TABLE `subject_teacher` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` enum('pending','active','inactive','rejected') NOT NULL DEFAULT 'pending',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `status`, `email_verified_at`, `password`, `role_id`, `remember_token`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'admin@mindware.edu.ph', 'active', NULL, '$2y$12$rPOWv9/GKddWpQW04cbaSejehOdqtwyWgJO.6RWTGoeyryaD03n4i', 1, NULL, '2025-11-13 09:24:36', '2025-09-17 09:45:54', '2025-11-13 09:24:36'),
(2, 'registrar@mindware.edu.ph', 'active', NULL, '$2y$12$f9ZEzzAJvl1uPHAItuDucuRgM1UEHCM7FUfZdX/F2SiKBlpQYvZXq', 2, NULL, '2025-11-13 09:28:23', '2025-09-17 09:45:54', '2025-11-13 09:28:23'),
(3, 'teacher@mindware.edu.ph', 'active', NULL, '$2y$12$xbnTUWLKymQi8lzc.LQHwOwwjlKU6MFu2IXparjPsM17Z8gGJbDbK', 3, NULL, '2025-11-13 09:30:44', '2025-09-17 09:45:54', '2025-11-13 09:30:44'),
(34, 'teacher1@mindware.edu.ph', 'active', NULL, '$2y$12$OeJdpIOZruQCzLYBowcJ7OQdhM1YCN14yNjXmMSeEmNbfXd1jG.NO', 3, NULL, '2025-11-13 04:32:13', '2025-10-22 04:45:07', '2025-11-13 04:32:13'),
(36, 'tolentino@mindware.edu.ph', 'active', NULL, '$2y$12$24dEEWHIaxtTnXz7PmUyNO1uTRgL.3BvO2dWY7bOZlD0keboofSb.', 3, NULL, '2025-11-13 04:00:06', '2025-10-23 09:37:39', '2025-11-13 04:00:06'),
(37, 'carreon@mindware.edu.ph', 'active', NULL, '$2y$12$gftKG.JZC8o0XUSuWJT3hu5k4Ko9AFPAEuOyS/JfZj7jOzb1ztluu', 3, NULL, NULL, '2025-10-23 09:39:53', '2025-11-13 03:58:00'),
(38, 'chingcuangco@mindware.edu.ph', 'active', NULL, '$2y$12$Catzdf9AeO17fzpCNSA29u33m0xjTr2sb8nrYKHTaIolYdCyPbzk6', 3, NULL, NULL, '2025-10-23 09:41:09', '2025-10-23 09:41:09'),
(39, 'ciao@mindware.edu.ph', 'active', NULL, '$2y$12$TnEyD.dtEH//t7bJQy.yIe8uM2i395dcHltwnWuWWXao/9dfPkDPW', 3, NULL, NULL, '2025-10-23 09:50:40', '2025-11-11 20:53:49'),
(89, 'clara.maria@mindware.edu.ph', 'active', NULL, '$2y$12$SMwVjrKPNX6VfEApy3RzWevgysc1nWka..baAMwFgyGTBCyh7kVMS', 4, NULL, NULL, '2025-11-13 01:07:49', '2025-11-13 01:07:49'),
(90, 'dionisio.neale@mindware.edu.ph', 'active', NULL, '$2y$12$dUPW2nqJUxeCV5/kEqP2IOHpkXomWZGd/B5MiAvVLmsq4EGP6i7fC', 4, NULL, '2025-11-13 01:27:09', '2025-11-13 01:23:27', '2025-11-13 01:27:09'),
(91, 'wenceslao.bella@mindware.edu.ph', 'active', NULL, '$2y$12$BWmyRusikx1z9miqjX1xEOaXckKM3NYGaa4H3/ZqP3v6p.Cu/pZui', 4, NULL, '2025-11-13 02:38:59', '2025-11-13 01:29:02', '2025-11-13 02:38:59'),
(92, 'legaspi.elijah@mindware.edu.ph', 'active', NULL, '$2y$12$bNj606Bad5N/84ungq3WG.uW6OOITb0d2WcjrfaTcd3rnGwhXHMDu', 4, NULL, '2025-11-13 09:35:48', '2025-11-13 02:36:01', '2025-11-13 09:35:48'),
(93, 'wenceslao.princess ashley@mindware.edu.ph', 'active', NULL, '$2y$12$LnJ0B1ncPnW3VoyfwYW9qOgZAUtOsS7Ys2AUIcUfet7WJHMrwuT.u', 4, NULL, NULL, '2025-11-13 02:46:20', '2025-11-13 02:46:20'),
(94, 'tolentino.zyrone andrei@mindware.edu.ph', 'active', NULL, '$2y$12$TULYEJAASVk.xu9.8J8wa.N/htk33v2T4Dn1v6X2mijz.TjGPcuVu', 4, NULL, NULL, '2025-11-13 02:47:28', '2025-11-13 02:47:28');

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
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcements_user_id_foreign` (`user_id`),
  ADD KEY `announcements_section_id_foreign` (`section_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enrollments_student_id_foreign` (`student_id`),
  ADD KEY `enrollments_section_id_foreign` (`section_id`),
  ADD KEY `enrollments_school_year_id_foreign` (`school_year_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grades_student_id_foreign` (`student_id`),
  ADD KEY `grades_subject_id_foreign` (`subject_id`),
  ADD KEY `grades_school_year_id_foreign` (`school_year_id`);

--
-- Indexes for table `grade_levels`
--
ALTER TABLE `grade_levels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD KEY `password_reset_tokens_email_index` (`email`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profiles_user_id_foreign` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `school_years`
--
ALTER TABLE `school_years`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `school_years_name_unique` (`name`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sections_gradelevel_id_foreign` (`gradelevel_id`),
  ADD KEY `sections_adviser_id_foreign` (`adviser_id`),
  ADD KEY `sections_school_year_id_foreign` (`school_year_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_student_number_unique` (`student_number`),
  ADD KEY `students_user_id_foreign` (`user_id`),
  ADD KEY `students_section_id_foreign` (`section_id`);

--
-- Indexes for table `student_certificates`
--
ALTER TABLE `student_certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_certificates_student_id_foreign` (`student_id`),
  ADD KEY `student_certificates_issued_by_foreign` (`issued_by`);

--
-- Indexes for table `student_documents`
--
ALTER TABLE `student_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_documents_student_id_foreign` (`student_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subjects_grade_level_id_foreign` (`grade_level_id`);

--
-- Indexes for table `subject_assignments`
--
ALTER TABLE `subject_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_section_subject` (`section_id`,`subject_id`),
  ADD KEY `subject_assignments_subject_id_foreign` (`subject_id`),
  ADD KEY `subject_assignments_teacher_id_foreign` (`teacher_id`),
  ADD KEY `subject_assignments_school_year_id_foreign` (`school_year_id`);

--
-- Indexes for table `subject_teacher`
--
ALTER TABLE `subject_teacher`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_teacher_teacher_id_foreign` (`teacher_id`),
  ADD KEY `subject_teacher_subject_id_foreign` (`subject_id`),
  ADD KEY `subject_teacher_section_id_foreign` (`section_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teachers_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=592;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `grade_levels`
--
ALTER TABLE `grade_levels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `school_years`
--
ALTER TABLE `school_years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `student_certificates`
--
ALTER TABLE `student_certificates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `student_documents`
--
ALTER TABLE `student_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `subject_assignments`
--
ALTER TABLE `subject_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `subject_teacher`
--
ALTER TABLE `subject_teacher`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `announcements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_school_year_id_foreign` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_school_year_id_foreign` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `grades_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_adviser_id_foreign` FOREIGN KEY (`adviser_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sections_gradelevel_id_foreign` FOREIGN KEY (`gradelevel_id`) REFERENCES `grade_levels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sections_school_year_id_foreign` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_certificates`
--
ALTER TABLE `student_certificates`
  ADD CONSTRAINT `student_certificates_issued_by_foreign` FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `student_certificates_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_documents`
--
ALTER TABLE `student_documents`
  ADD CONSTRAINT `student_documents_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_grade_level_id_foreign` FOREIGN KEY (`grade_level_id`) REFERENCES `grade_levels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subject_assignments`
--
ALTER TABLE `subject_assignments`
  ADD CONSTRAINT `subject_assignments_school_year_id_foreign` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `subject_assignments_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_assignments_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_assignments_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subject_teacher`
--
ALTER TABLE `subject_teacher`
  ADD CONSTRAINT `subject_teacher_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_teacher_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_teacher_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
