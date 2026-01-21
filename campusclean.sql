-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 21, 2026 at 06:45 AM
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
-- Database: `campusclean`
--

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `photo_path` varchar(255) DEFAULT NULL,
  `proof_photo` varchar(255) DEFAULT NULL,
  `user_feedback` varchar(20) DEFAULT NULL,
  `feedback_comment` text DEFAULT NULL,
  `assigned_staff` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `user_id`, `title`, `description`, `location`, `status`, `created_at`, `photo_path`, `proof_photo`, `user_feedback`, `feedback_comment`, `assigned_staff`) VALUES
(60, 10, 'cleaning', 'classroom cleaning', 'new scad 1st floor 102', 'completed', '2025-12-31 08:56:24', 'uploads/complaint_1767171384_temp_upload.jpg', 'uploads/proofs/proof_60_1767171484.jpg', NULL, NULL, NULL),
(64, 10, 'cleaning', 'washroom cleaning', 'SCAD 1st Floor Male Washroom', 'completed', '2026-01-02 03:12:07', 'uploads/complaint_1767323527_temp_upload.jpg', 'uploads/proofs/proof_64_1767323566.jpg', NULL, NULL, NULL),
(65, 10, 'cleaning', 'floor cleaning', 'new scad 1st floor 102', 'completed', '2026-01-02 03:34:46', 'uploads/complaint_1767324886_temp_upload.jpg', 'uploads/proofs/proof_65_1767670556.jpg', NULL, NULL, NULL),
(67, 10, 'cleaning', 'classroom cleaning', 'new scad 1st floor 103', 'completed', '2026-01-05 02:42:53', 'uploads/complaint_1767580973_temp_upload.jpg', 'uploads/proofs/proof_67_1767714651.jpg', NULL, NULL, NULL),
(68, 9, 'cleaning', 'classroom cleaning', 'new scad 1st floor 104', 'completed', '2026-01-06 03:24:34', 'uploads/complaint_1767669874_temp_upload.jpg', 'uploads/proofs/proof_68_1767670670.jpg', 'not_ok', NULL, NULL),
(69, 9, 'cleaning', 'campus cleaning', 'new scad 1st floor', 'completed', '2026-01-06 03:36:38', NULL, 'uploads/proofs/proof_69_1767712286.jpg', 'not_ok', NULL, NULL),
(70, 9, 'cleaning', 'washroom cleaning', 'new scad 1st floor male washroom', 'completed', '2026-01-06 12:29:02', 'uploads/complaint_1767702542_temp_upload.jpg', 'uploads/proofs/proof_70_1767702672.jpg', 'ok', NULL, NULL),
(71, 9, 'cleaning', 'washroom cleaning', 'new scad 1st floor male washroom', 'completed', '2026-01-06 15:07:56', 'uploads/complaint_1767712076_temp_upload.jpg', 'uploads/proofs/proof_71_1767714733.jpg', 'ok', NULL, NULL),
(80, 9, 'cleaning', 'classroom cleaning', 'new scad 1st floor 103', 'pending', '2026-01-07 02:21:23', NULL, NULL, NULL, NULL, NULL),
(81, 9, 'cleaning', 'classroom cleaning', 'new scad 1st floor 103', 'pending', '2026-01-07 02:23:25', 'uploads/complaint_1767752605_temp_upload.jpg', NULL, NULL, NULL, NULL),
(82, 9, 'cleaning', 'classroom cleaning', 'new scad 1st floor 103', 'completed', '2026-01-07 02:23:28', 'uploads/complaint_1767752608_temp_upload.jpg', 'uploads/proofs/proof_82_1767752636.jpg', 'ok', NULL, NULL),
(84, 9, 'cleaning', 'classroom cleaning', 'new scad 3rd floor 103', 'completed', '2026-01-07 02:53:46', NULL, 'uploads/proofs/proof_84_1767754447.jpg', 'ok', NULL, NULL),
(98, 9, 'cleaning', 'classroom cleaning', 'new scad 3rd floor 303', 'pending', '2026-01-07 05:35:50', 'uploads/complaint_1767764150_temp_upload.jpg', NULL, NULL, NULL, NULL),
(99, 9, 'cleaning', 'classroom cleaning', 'new scad 3rd floor 303', 'completed', '2026-01-07 05:35:55', 'uploads/complaint_1767764155_temp_upload.jpg', 'uploads/proofs/proof_99_1767764188.jpg', 'not_ok', 'not good', 'lakshmi'),
(100, 9, 'cleaning', 'classroom cleaning', 'new scad ground floor 006', 'pending', '2026-01-07 05:39:29', 'uploads/complaint_1767764369_temp_upload.jpg', NULL, NULL, NULL, NULL),
(101, 9, 'cleaning', 'classroom cleaning', 'new scad ground floor 006', 'pending', '2026-01-07 05:39:33', 'uploads/complaint_1767764373_temp_upload.jpg', NULL, NULL, NULL, NULL),
(102, 9, 'cleaning', 'classroom cleaning', 'new scad ground floor 006', 'completed', '2026-01-07 05:39:39', 'uploads/complaint_1767764379_temp_upload.jpg', 'uploads/proofs/proof_102_1767764419.jpg', NULL, NULL, 'divya'),
(103, 9, 'cleaning', 'classroom cleaning', 'new scad 1st floor 103', 'completed', '2026-01-08 03:53:59', 'uploads/complaint_1767844439_temp_upload.jpg', 'uploads/proofs/proof_103_1767844490.jpg', 'ok', 'good', 'lakshmi'),
(104, 9, 'bhkk', 'good xvj', 'hsjksms', 'completed', '2026-01-08 07:56:14', NULL, 'uploads/proofs/proof_104_1768970560.jpg', NULL, NULL, NULL),
(105, 9, 'bhkk', 'good xvj', 'hsjksms', 'pending', '2026-01-08 07:56:14', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'student',
  `campus` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `fcm_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `campus`, `created_at`, `fcm_token`) VALUES
(1, 'Manager Test', 'manager@test.com', '$2y$10$V82MTmE6Etj.swBpwgu4ke62/lFKRQgK8NEP7IzkCbrxlbYp3UALm', 'manager', 'building A', '2025-12-29 08:10:26', NULL),
(2, 'koti', 'koti@gmail.com', '$2y$10$psUHsnpl/lIUXlAxyCzgdOHK0ycPE2LRVX8eWknUQqj.Daqga9g3K', 'student', 'south', '2025-12-29 09:04:19', 'drZ___DBS_KEV-F5VmWfPT:APA91bFN-AF3Y6_JzzTHr8WXbDftB6bxUwvQv_46awvkdcwsUsIYcWhIIfen71Ftbr5rY3D-zhN4-Ib4kyk1k2hhwnDhMuSwxeNrU6UfSa6LaC_pTNodwE8'),
(3, 'manager', 'manager3@gmail.com', '$2y$10$WWj0Krp4k4.wUIKBh07LpOP1Upl8ctyr4pxNjsZuxgTb.HICc6hXq', 'manager', 'north', '2025-12-29 09:06:21', 'exDtDVn9SsWaB3eb45J5sk:APA91bFwTgsI5bHTgP-SxGuRGXzeEgwwroXfioIfzXdIK20TEkMNzO3I0xBCkq5L6vjiz-WSz2fI4cY0xq9nqT61BqYVHkU1MJgcWqqrhD9zJwHz4FQ6E-Y'),
(5, 'Manager Test', 'manager2@test.com', '$2y$10$XYETxYvhSkJyKwLXKSvct.kOJa123B.QumZsWF/4b.hIytGzqxJjy', 'manager', 'building B', '2025-12-29 09:10:26', NULL),
(6, 'Student One', 'student1@gmail.com', '$2y$10$JJV4W22LPI7fcZsd2O4sTOlO2BhcyX7tCUHycz3c26dFFMypWz.Ca', 'student', 'Block A', '2025-12-29 09:59:46', NULL),
(8, 'Student One', 'student2@gmail.com', '$2y$10$56I4VpBgGJCkp4Dky8oHiOxXWJInNBBjZn1AKL4zunvpYo38IyKOC', 'student', 'Block A', '2025-12-29 10:00:11', NULL),
(9, 'koti', 'peramkotireddy12345@gmail.com', '$2y$10$2pvNIPUTePiqFdWe6CUkX.E0UimocHrEXL6eghdnF2oTn4mg0dzxG', 'user', 'north', '2025-12-29 14:50:24', 'f5jPIYJbTiSsozwZ1uqwxF:APA91bGWJ3TYO002ZbsgCnkdwEOvBp991Xhhjf5sV7a6MaYkp7Z9w2uYYHQjQbqBj0TRPCP3SPws9WSItPKxAlQFX58O5BDrFOepRzFjo_05cYNbeSjZoas'),
(10, 'sravani', 'sravanireddy1121@gmail.com', '$2y$10$sXOzdqdFc3ofnzjqUYIv2u1sle/mkaXSDkM//u/5himA.98hK/Up6', 'user', '', '2025-12-30 07:13:23', 'drZ___DBS_KEV-F5VmWfPT:APA91bFN-AF3Y6_JzzTHr8WXbDftB6bxUwvQv_46awvkdcwsUsIYcWhIIfen71Ftbr5rY3D-zhN4-Ib4kyk1k2hhwnDhMuSwxeNrU6UfSa6LaC_pTNodwE8'),
(11, 'keerthana', 'keerthu@gmail.com', '$2y$10$P/0uhKmiO/yfye2fAZvyMOlbnRmLsAeV4.x7SWVWKgtiy4Ys/HcmW', 'user', '', '2025-12-31 03:25:48', 'e3EHrbO-TTSVvVg274NdeO:APA91bHVK4L5halr-wueSELp-NUoogRtgmFoa7CCzVtOWCES6iF9SHMe6CgmGasq4IvUo1jXeZNNS8G5-0olZ_L_p_peUPdURwAR6J7EYI-wi2EawBRCvTM'),
(12, 'hh', 'hu', '$2y$10$A2f.i/Bsli7ZPLZTZDiYXe1j4XeAaw1QZh5R4d6t0yZk1zBLXiqB6', 'user', '', '2026-01-21 05:14:40', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
