-- phpMyAdmin SQL Dump
-- version 4.9.8
-- https://www.phpmyadmin.net/
--
-- Generation Time: Jan 31, 2023 at 04:56 PM
-- Server version: 10.5.13-MariaDB-log
-- PHP Version: 7.4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gamedb`
--

-- --------------------------------------------------------

--
-- Table structure for table `expansion_info`
--

CREATE TABLE `expansion_info` (
  `exp_id` smallint(6) NOT NULL COMMENT 'Internal ID',
  `exp_name` varchar(120) NOT NULL COMMENT 'Expansion Name',
  `bgg_id` mediumint(9) DEFAULT NULL COMMENT 'BGG thing ID',
  `min_over` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Override min player count',
  `max_over` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Override max player count',
  `parent_id` smallint(6) NOT NULL COMMENT 'Internal ID of base game',
  `lent_to` varchar(30) DEFAULT NULL COMMENT 'Lent to whom'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Expansions';

-- --------------------------------------------------------

--
-- Table structure for table `game_info`
--

CREATE TABLE `game_info` (
  `game_id` smallint(6) NOT NULL COMMENT 'Internal ID',
  `game_name` varchar(120) NOT NULL COMMENT 'Game Name',
  `bgg_id` mediumint(9) DEFAULT NULL COMMENT 'BGG thing ID',
  `min_players` tinyint(3) UNSIGNED NOT NULL COMMENT 'Min player count',
  `max_players` tinyint(3) UNSIGNED NOT NULL COMMENT 'Max player count',
  `lent_to` varchar(30) DEFAULT NULL COMMENT 'Lent to whom'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user_login`
--

CREATE TABLE `user_login` (
  `username` varchar(16) COLLATE latin1_general_ci NOT NULL COMMENT 'User Login',
  `password` varchar(32) COLLATE latin1_general_ci NOT NULL COMMENT 'Application Password',
  `add_game` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'N' COMMENT 'Can Add Games?',
  `edit_game` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'N' COMMENT 'Can Edit Games?',
  `edit_loan` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'N' COMMENT 'Can Edit Loan Info?',
  `add_user` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'N' COMMENT 'Can Add Users?',
  `edit_user` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'N' COMMENT 'Can Edit Users?',
  `admin` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'N' COMMENT 'System Admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `expansion_info`
--
ALTER TABLE `expansion_info`
  ADD PRIMARY KEY (`exp_id`),
  ADD UNIQUE KEY `bgg_id` (`bgg_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `min_over` (`min_over`),
  ADD KEY `max_over` (`max_over`);

--
-- Indexes for table `game_info`
--
ALTER TABLE `game_info`
  ADD PRIMARY KEY (`game_id`),
  ADD UNIQUE KEY `game_name` (`game_name`),
  ADD UNIQUE KEY `bgg_id` (`bgg_id`),
  ADD KEY `max_players` (`max_players`),
  ADD KEY `min_players` (`min_players`);

--
-- Indexes for table `user_login`
--
ALTER TABLE `user_login`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `expansion_info`
--
ALTER TABLE `expansion_info`
  MODIFY `exp_id` smallint(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `game_info`
--
ALTER TABLE `game_info`
  MODIFY `game_id` smallint(6) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
