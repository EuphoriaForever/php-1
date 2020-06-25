-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2020 at 05:56 PM
-- Server version: 10.4.8-MariaDB
-- PHP Version: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `im2`
--

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE `attributes` (
  `attr_ID` int(10) NOT NULL,
  `attr_Name` varchar(255) NOT NULL,
  `datatype` varchar(255) NOT NULL,
  `limitation` int(10) NOT NULL,
  `isPrimary` tinyint(1) NOT NULL,
  `isAutoInc` tinyint(1) NOT NULL,
  `isNull` tinyint(1) NOT NULL,
  `isParent` tinyint(1) NOT NULL,
  `ParentOf` int(10) DEFAULT NULL,
  `isFK` tinyint(1) NOT NULL,
  `FK_of` int(10) NOT NULL,
  `tb_ID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`attr_ID`, `attr_Name`, `datatype`, `limitation`, `isPrimary`, `isAutoInc`, `isNull`, `isParent`, `ParentOf`, `isFK`, `FK_of`, `tb_ID`) VALUES
(4, 'ID', 'INT', 10, 1, 1, 0, 0, 0, 0, 0, 17),
(5, 'ID', 'INT', 10, 1, 1, 0, 0, 0, 0, 0, 18),
(7, 'ID', 'INT', 10, 1, 1, 0, 0, 0, 0, 0, 19),
(9, 'Character_Owner', 'INT', 10, 0, 0, 0, 0, 0, 1, 17, 18),
(10, 'Firstname', 'varchar', 255, 0, 0, 0, 0, 0, 0, 0, 17);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`attr_ID`),
  ADD KEY `attributes_ibfk_1` (`tb_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attributes`
--
ALTER TABLE `attributes`
  MODIFY `attr_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attributes`
--
ALTER TABLE `attributes`
  ADD CONSTRAINT `attributes_ibfk_1` FOREIGN KEY (`tb_ID`) REFERENCES `tb` (`tb_ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
