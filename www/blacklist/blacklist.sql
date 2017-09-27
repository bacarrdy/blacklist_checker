-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u8
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Sep 27, 2017 at 05:50 PM
-- Server version: 5.7.18
-- PHP Version: 5.6.31-1~dotdeb+7.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `blacklist`
--

-- --------------------------------------------------------

--
-- Table structure for table `ips_blacked`
--

CREATE TABLE IF NOT EXISTS `ips_blacked` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `listName` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ips_blacked`
--

INSERT INTO `ips_blacked` (`id`, `ip`, `userID`, `url`, `listName`, `date`) VALUES
(1, '127.0.0.1', NULL, NULL, 'LOCAL TEST', '2017-09-27 12:41:28');

-- --------------------------------------------------------

--
-- Table structure for table `ips_history`
--

CREATE TABLE IF NOT EXISTS `ips_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `action` varchar(255) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ips_history`
--

INSERT INTO `ips_history` (`id`, `ip`, `date`, `action`, `userID`) VALUES
(1, '127.0.0.1', '2017-09-27 13:57:08', 'TRY ACTION', NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
