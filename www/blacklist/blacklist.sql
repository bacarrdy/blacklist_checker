-- phpMyAdmin SQL Dump
-- version 4.4.15.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2017 m. Spa 10 d. 15:07
-- Server version: 5.5.50
-- PHP Version: 5.4.45

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blacklist`
--

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `ips_blacked`
--

CREATE TABLE IF NOT EXISTS `ips_blacked` (
  `id` int(11) NOT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `userID` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `listName` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `recheck` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `ips_categories`
--

CREATE TABLE IF NOT EXISTS `ips_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `ips_categories_ranges`
--

CREATE TABLE IF NOT EXISTS `ips_categories_ranges` (
  `id` int(11) NOT NULL,
  `categoryID` int(11) DEFAULT NULL,
  `rangeBegin` bigint(20) DEFAULT NULL,
  `rangeEnd` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `ips_history`
--

CREATE TABLE IF NOT EXISTS `ips_history` (
  `id` int(11) NOT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `action` varchar(255) NOT NULL,
  `userID` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ips_blacked`
--
ALTER TABLE `ips_blacked`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ips_categories`
--
ALTER TABLE `ips_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ips_categories_ranges`
--
ALTER TABLE `ips_categories_ranges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ips_history`
--
ALTER TABLE `ips_history`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ips_blacked`
--
ALTER TABLE `ips_blacked`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ips_categories`
--
ALTER TABLE `ips_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ips_categories_ranges`
--
ALTER TABLE `ips_categories_ranges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ips_history`
--
ALTER TABLE `ips_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
