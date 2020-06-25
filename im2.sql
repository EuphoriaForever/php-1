-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 24, 2020 at 03:13 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.1

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

-- --------------------------------------------------------

--
-- Table structure for table `db`
--

CREATE TABLE `db` (
  `db_ID` int(10) NOT NULL,
  `db_Name` varchar(255) NOT NULL,
  `Author` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `db`
--

INSERT INTO `db` (`db_ID`, `db_Name`, `Author`) VALUES
(9, 'SFV', 3),
(10, 'Tekken 7', 3);

-- --------------------------------------------------------

--
-- Table structure for table `operations`
--

CREATE TABLE `operations` (
  `op_ID` int(10) NOT NULL,
  `operation` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `operations`
--

INSERT INTO `operations` (`op_ID`, `operation`) VALUES
(1, 'Create'),
(2, 'Read'),
(3, 'Update'),
(4, 'Delete');

-- --------------------------------------------------------

--
-- Table structure for table `permits`
--

CREATE TABLE `permits` (
  `permit_ID` int(10) NOT NULL,
  `operation` int(10) NOT NULL,
  `user_ID` int(10) NOT NULL,
  `db` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `rows`
--

CREATE TABLE `rows` (
  `row_ID` int(10) NOT NULL,
  `rowNum` int(10) NOT NULL,
  `attr_ID` int(10) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rows`
--

INSERT INTO `rows` (`row_ID`, `rowNum`, `attr_ID`, `value`) VALUES
(2, 1, 4, '1'),
(3, 1, 10, 'Josie');

-- --------------------------------------------------------

--
-- Table structure for table `tb`
--

CREATE TABLE `tb` (
  `tb_ID` int(10) NOT NULL,
  `tb_Name` varchar(255) NOT NULL,
  `db_ID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb`
--

INSERT INTO `tb` (`tb_ID`, `tb_Name`, `db_ID`) VALUES
(17, 'Characters', 9),
(18, 'Stages', 9),
(19, 'Boss', 9),
(20, 'Skills', 10),
(21, 'Characters', 10);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `type`) VALUES
(1, 'admin', 'adminadmin', 'administrator'),
(2, 'Kweenisma', 'Kweenisma18100432', 'user'),
(3, 'DerekYu565', 'yeetyeetskrrt', 'user');

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
-- Indexes for table `db`
--
ALTER TABLE `db`
  ADD PRIMARY KEY (`db_ID`),
  ADD KEY `Author` (`Author`);

--
-- Indexes for table `operations`
--
ALTER TABLE `operations`
  ADD PRIMARY KEY (`op_ID`);

--
-- Indexes for table `permits`
--
ALTER TABLE `permits`
  ADD PRIMARY KEY (`permit_ID`),
  ADD KEY `permits_ibfk_1` (`db`),
  ADD KEY `operation` (`operation`),
  ADD KEY `user_ID` (`user_ID`);

--
-- Indexes for table `rows`
--
ALTER TABLE `rows`
  ADD PRIMARY KEY (`row_ID`),
  ADD KEY `rows_ibfk_1` (`attr_ID`) USING BTREE;

--
-- Indexes for table `tb`
--
ALTER TABLE `tb`
  ADD PRIMARY KEY (`tb_ID`),
  ADD KEY `tb_ibfk_1` (`db_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attributes`
--
ALTER TABLE `attributes`
  MODIFY `attr_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `db`
--
ALTER TABLE `db`
  MODIFY `db_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `operations`
--
ALTER TABLE `operations`
  MODIFY `op_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `permits`
--
ALTER TABLE `permits`
  MODIFY `permit_ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rows`
--
ALTER TABLE `rows`
  MODIFY `row_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb`
--
ALTER TABLE `tb`
  MODIFY `tb_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attributes`
--
ALTER TABLE `attributes`
  ADD CONSTRAINT `attributes_ibfk_1` FOREIGN KEY (`tb_ID`) REFERENCES `tb` (`tb_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `db`
--
ALTER TABLE `db`
  ADD CONSTRAINT `db_ibfk_1` FOREIGN KEY (`Author`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `permits`
--
ALTER TABLE `permits`
  ADD CONSTRAINT `permits_ibfk_1` FOREIGN KEY (`db`) REFERENCES `db` (`db_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `permits_ibfk_2` FOREIGN KEY (`operation`) REFERENCES `operations` (`op_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `permits_ibfk_3` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `rows`
--
ALTER TABLE `rows`
  ADD CONSTRAINT `rows_ibfk_1` FOREIGN KEY (`attr_ID`) REFERENCES `attributes` (`attr_ID`) ON DELETE CASCADE;

--
-- Constraints for table `tb`
--
ALTER TABLE `tb`
  ADD CONSTRAINT `tb_ibfk_1` FOREIGN KEY (`db_ID`) REFERENCES `db` (`db_ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
