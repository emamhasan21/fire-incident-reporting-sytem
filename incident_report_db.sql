-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2024 at 11:17 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `incident_report_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `fire_stations`
--

CREATE TABLE `fire_stations` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fire_stations`
--

INSERT INTO `fire_stations` (`id`, `name`, `location`) VALUES
(1, 'Fire Service & Civil Defence Station, Baridhara', 'plot 1 Madani Ave, Dhaka 1212'),
(2, 'Kurmitola Fire Station', 'RCG3+QWM, Dhaka 1206'),
(3, 'Bangladesh Fire Service and Civil Defense, Mirpur Road', 'Q95F+CFR, Mirpur Rd, Dhaka 1205');

-- --------------------------------------------------------

--
-- Table structure for table `fire_units`
--

CREATE TABLE `fire_units` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `fire_station_id` int(11) NOT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `current_task` int(11) DEFAULT NULL,
  `current_latitude` double DEFAULT NULL,
  `current_longitude` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fire_units`
--

INSERT INTO `fire_units` (`id`, `name`, `fire_station_id`, `latitude`, `longitude`, `username`, `password`, `current_task`, `current_latitude`, `current_longitude`) VALUES
(5, 'kurmitola 1', 2, 23.82711750905987, 90.40476558138424, 'fhunit_001', '$2y$10$R.9ETgJicvKDhX/Rl/3KfuTwBXU.JB/nseOOaK7gsbJ7Cbzh1f/HK', NULL, 23.8015431, 90.3630762),
(6, 'kurmitola 2', 2, 23.82711750905987, 90.40476558138424, 'fhunit_002', '$2y$10$tM6OYIn5Vs1t2tXtSWfOvu/TTOkxNwMfqe04aOCQihLGHft8dylRu', NULL, 23.8015431, 90.3630762);

-- --------------------------------------------------------

--
-- Table structure for table `news_feed`
--

CREATE TABLE `news_feed` (
  `id` int(11) NOT NULL,
  `heading` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news_feed`
--

INSERT INTO `news_feed` (`id`, `heading`, `author`, `body`, `image`, `created_at`) VALUES
(1, 'Fire service week begins today', 'Emam Hasan', 'Fire Service and Civil Defence week will begin across the country today, aimed at creating awareness to prevent fire incidents and others disasters.\r\n\r\nThe fire department has chalked out elaborate programmes to observe the week.\r\n\r\nHome Minister Asaduzzaman Khan will formally inaugurate the weeklong events at Fire Service and Civil Defence Multipurpose Complex at Purbachal in Narayanganj at 9am.\r\n\r\nA total of 411 fire stations across the country will simultaneously observe the week from November 6 to 12.\r\n\r\nPresident Abdul Hamid and Prime Minister Sheikh Hasina yesterday issued separate messages on the occasion.\r\n\r\nThe president in his message said the role of fire service is crucial for ensuring security and safety of people during any manmade disaster.', 'emergency-management-fire-professionals.jpg', '2024-05-22 21:59:14'),
(2, 'Fire service gets 15 female firefighters for first time', 'Emam Hasan', 'For the first time in the history of fire service in Bangladesh, 15 female firefighters have joined the force.\r\n\r\nAccording to the appointment letters, they officially joined the force yesterday, located at Multipurpose Training Complex in Purbachal of Narayanganj, said Shahjahan Sikder, deputy assistant director of Fire Service and Civil Defence Headquarters Media Cell.\r\n\r\nAfter joining, they were transferred to Mirpur Training Complex.\r\n\r\nFire Service DG Brigadier General Main Uddin welcomed them at Mirpur Training Complex today.\r\n\r\nThe fire official said the post of \"Fireman\" was recently changed to \"Firefighter\" as per the order of Prime Minister Sheikh Hasina in order to eliminate gender discrimination.\r\n\r\nOn June 20 this year, the recruitment circular for the post of fire fighters was published.', 'img-20231119-wa0024.jpg', '2024-05-22 22:00:39');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_video_path` varchar(255) DEFAULT NULL,
  `gps_location` varchar(255) DEFAULT NULL,
  `casualties` enum('none','minor','major') DEFAULT NULL,
  `status` enum('unassigned','assigned','done') DEFAULT 'unassigned',
  `main_report_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `image_video` varchar(255) NOT NULL,
  `assigned_fire_unit` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `user_id`, `description`, `image_video_path`, `gps_location`, `casualties`, `status`, `main_report_id`, `timestamp`, `latitude`, `longitude`, `image_video`, `assigned_fire_unit`) VALUES
(6, 7, 'Fire incident at 123 Maple Street. Electrical fault in kitchen. Significant damage, no injuries.', NULL, NULL, 'minor', 'done', NULL, '2024-05-22 11:48:31', 23.792643956394308, 90.37212833762172, '../assets/uploads/Kastros1.jpg', 6),
(7, 1, 'Fire incident at 123 Maple Street. Electrical fault in kitchen. Significant damage, no injuries.', NULL, NULL, 'major', 'done', NULL, '2024-05-22 11:54:37', 23.7985656, 90.3642529, '../assets/uploads/Kastros1.jpg', 6),
(9, 7, 'Potential fire hazard in Central Park near playground. Dry, overgrown bushes and leaf piles.', NULL, NULL, 'minor', 'done', NULL, '2024-05-22 13:50:43', 23.8233, 90.365, '../assets/uploads/Kastros1.jpg', 5),
(10, 7, 'ssssdsssssssssss ssssssss sssssssss', NULL, NULL, 'minor', 'done', NULL, '2024-05-22 15:50:57', 23.8233, 90.365, '../assets/uploads/Kastros1.jpg', 5),
(11, 1, 'fire near scholastica school, ', NULL, NULL, 'none', 'done', NULL, '2024-05-22 20:03:14', 23.8233, 90.365, '../assets/uploads/Kastros1.jpg', 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `nid_photo` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `reset_token` varchar(255) DEFAULT NULL,
  `status` enum('active','banned') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `surname`, `email`, `phone_number`, `nid_photo`, `password`, `role`, `reset_token`, `status`) VALUES
(1, 'Emam', 'Hasan', '2044851033@uits.edu.bd', '01787748377', 'assets/uploads/Screenshot 2024-05-12 002915.png', '$2y$10$FDazfaO62RyUFSzxJxqobeSHV.tXMgNQuH.LnPHmy9Vg9V4LZIdje', 'admin', 'c30c140ab8a04e0548c9f231df158c96', 'active'),
(4, 'Md.', 'Rafi', 'rafi@gmail.com', '01787748377', 'assets/uploads/nid_1604762478.jpg', '$2y$10$MEgaQawQHGTfYV.KDp5W1.7f13kI/pDS4qBd9y9CnqFJpdw7RrMku', 'user', NULL, 'active'),
(5, 'Riccardo', 'Prince', '2044851004@uits.edu.bd', '01976326438', 'assets/uploads/nid_1604762478.jpg', '$2y$10$Yxvadd7/7qGPD.qGSuRVOOizzfnd4URyq.9MDQJdWoJiEf6aler2S', 'user', NULL, 'active'),
(6, 'Md Shuhanur', 'Islam Sujan', '2044851024@uits.edu.bd', '01521394730', 'assets/uploads/nid_1604762478.jpg', '$2y$10$fOcLXApewmV2pmNVJ9dH3Ox62JYV4uKNi9MccXWPOZfINygHJJ4j.', 'admin', NULL, 'active'),
(7, 'Fahad', 'Mir', 'fahadmir@gmail.com', '01739582859', 'assets/uploads/nid_1604762478.jpg', '$2y$10$bf81Z2CQaKRoAE8y37qL6OyMvxKm45VzwZPcOeoXsCwFkG5Y0IXuK', 'user', NULL, 'active'),
(8, 'DM', 'Tanvir', 'dmtanvir@gmail.com', '01521394730', 'assets/uploads/nid_1604762478.jpg', '$2y$10$a1ecOJvLgkd6RtBXYvyKmuXwy6iBmR6yIosmidW9bKMbFIyKG19ba', 'user', NULL, 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fire_stations`
--
ALTER TABLE `fire_stations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fire_units`
--
ALTER TABLE `fire_units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fire_station_id` (`fire_station_id`),
  ADD KEY `fk_current_task` (`current_task`);

--
-- Indexes for table `news_feed`
--
ALTER TABLE `news_feed`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `fire_stations`
--
ALTER TABLE `fire_stations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `fire_units`
--
ALTER TABLE `fire_units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `news_feed`
--
ALTER TABLE `news_feed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fire_units`
--
ALTER TABLE `fire_units`
  ADD CONSTRAINT `fire_units_ibfk_1` FOREIGN KEY (`fire_station_id`) REFERENCES `fire_stations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_current_task` FOREIGN KEY (`current_task`) REFERENCES `reports` (`id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
