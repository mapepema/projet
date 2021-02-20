-- phpMyAdmin SQL Dump
-- version 5.0.4deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 19, 2021 at 01:18 AM
-- Server version: 8.0.23
-- PHP Version: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vm`
--

-- --------------------------------------------------------

--
-- Table structure for table `inbound_rules`
--

CREATE TABLE `inbound_rules` (
  `id_inbound_rule` bigint NOT NULL,
  `inbound_rule_action` varchar(255) DEFAULT NULL,
  `port` int DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `inbound_rules`
--

INSERT INTO `inbound_rules` (`id_inbound_rule`, `inbound_rule_action`, `port`, `ip`) VALUES
(1, 'accept', 22, NULL),
(2, 'accept', 80, NULL),
(3, 'accept', 443, NULL),
(18, 'accept', 22, 'localhost');

-- --------------------------------------------------------

--
-- Table structure for table `instance_security_group`
--

CREATE TABLE `instance_security_group` (
  `id_instance_security_group` bigint NOT NULL,
  `inbound_default_policy` varchar(255) DEFAULT NULL,
  `outbound_default_policy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `instance_security_group`
--

INSERT INTO `instance_security_group` (`id_instance_security_group`, `inbound_default_policy`, `outbound_default_policy`) VALUES
(1, 'drop', 'accept');

-- --------------------------------------------------------

--
-- Table structure for table `instance_server`
--

CREATE TABLE `instance_server` (
  `id_instance_server` bigint NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `tags` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `instance_server`
--

INSERT INTO `instance_server` (`id_instance_server`, `type`, `image`, `tags`) VALUES
(1, 'DEV1-S', 'ubuntu-focal', '{\"tags\": [\"FocalFossa\", \"MyUbuntuInstance\"]}'),
(9, 'DEV1-L', 'ubuntu-focal', '{\"tags\": [\"FocalFossa\", \"MyUbuntuInstance\"]}');

-- --------------------------------------------------------

--
-- Table structure for table `instance_volume`
--

CREATE TABLE `instance_volume` (
  `id_instance_volume` bigint NOT NULL,
  `size_in_gb` int DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `instance_volume`
--

INSERT INTO `instance_volume` (`id_instance_volume`, `size_in_gb`, `type`) VALUES
(1, 20, 'l_ssd');

-- --------------------------------------------------------

--
-- Table structure for table `outbound_rules`
--

CREATE TABLE `outbound_rules` (
  `id_outbound_rule` bigint NOT NULL,
  `outbound_rule_action` varchar(255) DEFAULT NULL,
  `port` int DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` bigint NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `permission` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `firstname`, `surname`, `email`, `password`, `permission`) VALUES
(1, 'maxime', 'petitjean', 'maxime.petitjean41@orange.fr', '$2y$10$Q7pIEgokidKo/EYq/jb5h.ltLqwsBCNzh8XkdQjaoZF3NAj6qgHvi', 0),
(13, 'surname1', 'familyname1', 'surname1@familyname1.com', '$2y$10$8EeHZI.NGRgFL.EXyYTZvuOKZGyXpkydbC8UcBlM33H6FJdYNVKbG', 1),
(14, 'prenom', 'famille', 'monemail@com', '$2y$10$MhgZDAFpo8RJjp9m9MdCFuyRgTI8Y1DDgBoEt4HY042PqVAO91aOm', 1),
(15, 'monprenom', 'test', 'emailtest4@com', '$2y$10$rvUH12CWD3DBDMVC5a4pYOO7gIjztoFRLA469PdT7HlgG7mZ5rTwe', 1);

-- --------------------------------------------------------

--
-- Table structure for table `virtuals_machines`
--

CREATE TABLE `virtuals_machines` (
  `id_virtual_machine` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `instance_volume_id` bigint DEFAULT NULL,
  `instance_security_group_id` bigint DEFAULT NULL,
  `inbound_rule_ids` varchar(255) DEFAULT NULL,
  `outbound_rule_ids` varchar(255) DEFAULT NULL,
  `instance_server_id` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `virtuals_machines`
--

INSERT INTO `virtuals_machines` (`id_virtual_machine`, `user_id`, `instance_volume_id`, `instance_security_group_id`, `inbound_rule_ids`, `outbound_rule_ids`, `instance_server_id`) VALUES
(1, 1, 1, 1, '1-2-3', '', 1),
(14, 1, 1, 1, '18-2-3', '', 9);

-- --------------------------------------------------------

--
-- Table structure for table `virtuals_machines_states`
--

CREATE TABLE `virtuals_machines_states` (
  `id_virtual_machine` bigint NOT NULL,
  `state` int DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `virtuals_machines_states`
--

INSERT INTO `virtuals_machines_states` (`id_virtual_machine`, `state`, `message`) VALUES
(1, 1099, 'test1'),
(14, 1000, 'New machine');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inbound_rules`
--
ALTER TABLE `inbound_rules`
  ADD PRIMARY KEY (`id_inbound_rule`);

--
-- Indexes for table `instance_security_group`
--
ALTER TABLE `instance_security_group`
  ADD PRIMARY KEY (`id_instance_security_group`);

--
-- Indexes for table `instance_server`
--
ALTER TABLE `instance_server`
  ADD PRIMARY KEY (`id_instance_server`);

--
-- Indexes for table `instance_volume`
--
ALTER TABLE `instance_volume`
  ADD PRIMARY KEY (`id_instance_volume`);

--
-- Indexes for table `outbound_rules`
--
ALTER TABLE `outbound_rules`
  ADD PRIMARY KEY (`id_outbound_rule`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `virtuals_machines`
--
ALTER TABLE `virtuals_machines`
  ADD PRIMARY KEY (`id_virtual_machine`),
  ADD KEY `instance_volume_id` (`instance_volume_id`),
  ADD KEY `instance_security_group_id` (`instance_security_group_id`),
  ADD KEY `instance_server_id` (`instance_server_id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `virtuals_machines_states`
--
ALTER TABLE `virtuals_machines_states`
  ADD PRIMARY KEY (`id_virtual_machine`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inbound_rules`
--
ALTER TABLE `inbound_rules`
  MODIFY `id_inbound_rule` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `instance_security_group`
--
ALTER TABLE `instance_security_group`
  MODIFY `id_instance_security_group` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `instance_server`
--
ALTER TABLE `instance_server`
  MODIFY `id_instance_server` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `instance_volume`
--
ALTER TABLE `instance_volume`
  MODIFY `id_instance_volume` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `outbound_rules`
--
ALTER TABLE `outbound_rules`
  MODIFY `id_outbound_rule` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `virtuals_machines`
--
ALTER TABLE `virtuals_machines`
  MODIFY `id_virtual_machine` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `virtuals_machines`
--
ALTER TABLE `virtuals_machines`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `virtuals_machines_ibfk_1` FOREIGN KEY (`instance_volume_id`) REFERENCES `instance_volume` (`id_instance_volume`) ON DELETE CASCADE,
  ADD CONSTRAINT `virtuals_machines_ibfk_2` FOREIGN KEY (`instance_security_group_id`) REFERENCES `instance_security_group` (`id_instance_security_group`) ON DELETE CASCADE,
  ADD CONSTRAINT `virtuals_machines_ibfk_3` FOREIGN KEY (`instance_server_id`) REFERENCES `instance_server` (`id_instance_server`) ON DELETE CASCADE;

--
-- Constraints for table `virtuals_machines_states`
--
ALTER TABLE `virtuals_machines_states`
  ADD CONSTRAINT `virtuals_machines_states_ibfk_1` FOREIGN KEY (`id_virtual_machine`) REFERENCES `virtuals_machines` (`id_virtual_machine`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
