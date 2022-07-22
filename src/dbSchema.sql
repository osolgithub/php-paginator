-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 22, 2022 at 12:15 PM
-- Server version: 5.7.36
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `osol_test_paginator`
--

-- --------------------------------------------------------

--
-- Table structure for table `otp_sample_list`
--

DROP TABLE IF EXISTS `otp_sample_list`;
CREATE TABLE IF NOT EXISTS `otp_sample_list` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sample_text` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `otp_sample_list`
--

INSERT INTO `otp_sample_list` (`id`, `sample_text`) VALUES
(1, 'This is row 1'),
(2, 'This is row 2'),
(3, 'This is row 3'),
(4, 'This is row 4'),
(5, 'This is row 5'),
(6, 'This is row 6'),
(7, 'This is row 7'),
(8, 'This is row 8'),
(9, 'This is row 9'),
(10, 'This is row 10'),
(11, 'This is row 11'),
(12, 'This is row 12'),
(13, 'This is row 13'),
(14, 'This is row 14'),
(15, 'This is row 15'),
(16, 'This is row 16'),
(17, 'This is row 17'),
(18, 'This is row 18'),
(19, 'This is row 19'),
(20, 'This is row 20'),
(21, 'This is row 21'),
(22, 'This is row 22'),
(23, 'This is row 23'),
(24, 'This is row 24'),
(25, 'This is row 25'),
(26, 'This is row 26'),
(27, 'This is row 27'),
(28, 'This is row 28'),
(29, 'This is row 29'),
(30, 'This is row 30'),
(31, 'This is row 31'),
(32, 'This is row 32');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
