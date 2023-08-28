-- create databases
CREATE DATABASE IF NOT EXISTS `cinema-local`;

-- create users and grant rights
-- CREATE USER 'vu'@'localhost' IDENTIFIED BY 'vu';
CREATE USER 'vu'@'localhost' IDENTIFIED BY 'vu';
GRANT ALL PRIVILEGES ON *.* TO 'vu'@'%' WITH GRANT OPTION;

FLUSH PRIVILEGES;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
USE `cinema-local`;

--
-- Database: `cinema-local`
--

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `firstName` varchar(256) NOT NULL,
  `lastName` varchar(256) NOT NULL,
  `fullName` varchar(256) GENERATED ALWAYS AS (concat(`firstName`,' ',`lastName`)) VIRTUAL NOT NULL,
  `address` varchar(256) DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `job` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Movies`
--

CREATE TABLE `Movies` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(256) NOT NULL,
  `actors` json NOT NULL,
  `yearOfRelease` year(4) DEFAULT NULL,
  `thumbnail` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

COMMIT;
