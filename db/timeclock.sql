-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 03, 2014 at 11:10 AM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `timeclock_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE IF NOT EXISTS `employee` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(50) DEFAULT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `department_name` enum('Testing','Development') DEFAULT NULL,
  `email_address` varchar(50) NOT NULL,
  `current_timelog_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`id`, `employee_id`, `full_name`, `department_name`, `email_address`, `current_timelog_id`) VALUES
(1, '0001', 'kath borja', 'Development', 'kathborja@website.com', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_timelogs`
--

CREATE TABLE IF NOT EXISTS `employee_timelogs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_employee_id` varchar(50) DEFAULT NULL,
  `status` enum('in','break','back','out') DEFAULT NULL,
  `location` enum('home','office') DEFAULT NULL,
  `time` time DEFAULT NULL,
  `date` date DEFAULT NULL,
  `notes` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `employee_timelogs`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_employee_id` varchar(50) DEFAULT NULL,
  `user_name` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fk_employee_id`, `user_name`, `password`) VALUES
(1, '0001', 'kath', 'realkeep3r');
