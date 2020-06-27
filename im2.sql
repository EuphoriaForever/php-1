-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2020 at 08:50 AM
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
  `colNum` int(10) NOT NULL,
  `datatype` int(11) NOT NULL,
  `limitation` int(10) NOT NULL,
  `isPrimary` tinyint(1) NOT NULL,
  `isAutoInc` tinyint(1) NOT NULL,
  `isNull` tinyint(1) NOT NULL,
  `isFK` tinyint(1) NOT NULL,
  `tb_ID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`attr_ID`, `attr_Name`, `colNum`, `datatype`, `limitation`, `isPrimary`, `isAutoInc`, `isNull`, `isFK`, `tb_ID`) VALUES
(4, 'ID', 1, 1, 10, 1, 1, 0, 0, 17),
(5, 'ID', 1, 1, 10, 1, 1, 0, 0, 18),
(7, 'ID', 1, 1, 10, 1, 1, 0, 0, 19),
(10, 'Firstname', 4, 2, 255, 0, 0, 0, 0, 17),
(18, 'Character_Name', 3, 2, 255, 0, 0, 0, 1, 18),
(19, 'ID', 1, 1, 10, 1, 1, 0, 0, 28),
(20, 'lastName', 2, 2, 255, 0, 0, 0, 0, 17),
(21, 'middle_initial', 3, 2, 10, 0, 0, 0, 0, 17),
(22, 'bossName', 2, 2, 255, 0, 0, 0, 0, 19),
(23, 'stageNum', 2, 1, 10, 0, 0, 0, 0, 18);

-- --------------------------------------------------------

--
-- Table structure for table `datatypes`
--

CREATE TABLE `datatypes` (
  `data_ID` int(10) NOT NULL,
  `data_Name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `datatypes`
--

INSERT INTO `datatypes` (`data_ID`, `data_Name`) VALUES
(1, 'INT'),
(2, 'VARCHAR'),
(3, 'BOOLEAN'),
(4, 'ENUM');

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
(10, 'Tekken 7', 3),
(21, 'Send help', 9);

-- --------------------------------------------------------

--
-- Table structure for table `enum`
--

CREATE TABLE `enum` (
  `enum_ID` int(11) NOT NULL,
  `ind` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  `attr_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

--
-- Dumping data for table `permits`
--

INSERT INTO `permits` (`permit_ID`, `operation`, `user_ID`, `db`) VALUES
(45, 1, 9, 9),
(46, 2, 9, 9),
(47, 3, 9, 9),
(48, 4, 9, 9);

-- --------------------------------------------------------

--
-- Table structure for table `relationships`
--

CREATE TABLE `relationships` (
  `rel_ID` int(10) NOT NULL,
  `parent` int(10) NOT NULL,
  `child` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `relationships`
--

INSERT INTO `relationships` (`rel_ID`, `parent`, `child`) VALUES
(0, 4, 18);

-- --------------------------------------------------------

--
-- Table structure for table `rows`
--

CREATE TABLE `rows` (
  `row_ID` int(10) NOT NULL,
  `rowNum` int(10) NOT NULL,
  `attr_ID` int(10) NOT NULL,
  `value` longtext NOT NULL
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
(21, 'Characters', 10),
(28, 'helpishere', 21);

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
(3, 'DerekYu565', 'yeetyeetskrrt', 'user'),
(9, 'ellexide', 'Tagupa0690', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`attr_ID`),
  ADD KEY `attributes_ibfk_1` (`tb_ID`),
  ADD KEY `datatype` (`datatype`);

--
-- Indexes for table `datatypes`
--
ALTER TABLE `datatypes`
  ADD PRIMARY KEY (`data_ID`);

--
-- Indexes for table `db`
--
ALTER TABLE `db`
  ADD PRIMARY KEY (`db_ID`),
  ADD KEY `Author` (`Author`);

--
-- Indexes for table `enum`
--
ALTER TABLE `enum`
  ADD PRIMARY KEY (`enum_ID`),
  ADD KEY `attr_ID` (`attr_ID`);

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
-- Indexes for table `relationships`
--
ALTER TABLE `relationships`
  ADD PRIMARY KEY (`rel_ID`),
  ADD KEY `child` (`child`),
  ADD KEY `parent` (`parent`);

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
  MODIFY `attr_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `datatypes`
--
ALTER TABLE `datatypes`
  MODIFY `data_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `db`
--
ALTER TABLE `db`
  MODIFY `db_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `enum`
--
ALTER TABLE `enum`
  MODIFY `enum_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `operations`
--
ALTER TABLE `operations`
  MODIFY `op_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `permits`
--
ALTER TABLE `permits`
  MODIFY `permit_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `rows`
--
ALTER TABLE `rows`
  MODIFY `row_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb`
--
ALTER TABLE `tb`
  MODIFY `tb_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attributes`
--
ALTER TABLE `attributes`
  ADD CONSTRAINT `attributes_ibfk_1` FOREIGN KEY (`tb_ID`) REFERENCES `tb` (`tb_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `attributes_ibfk_2` FOREIGN KEY (`datatype`) REFERENCES `datatypes` (`data_ID`);

--
-- Constraints for table `db`
--
ALTER TABLE `db`
  ADD CONSTRAINT `db_ibfk_1` FOREIGN KEY (`Author`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `enum`
--
ALTER TABLE `enum`
  ADD CONSTRAINT `enum_ibfk_1` FOREIGN KEY (`attr_ID`) REFERENCES `attributes` (`attr_ID`);

--
-- Constraints for table `permits`
--
ALTER TABLE `permits`
  ADD CONSTRAINT `permits_ibfk_1` FOREIGN KEY (`db`) REFERENCES `db` (`db_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `permits_ibfk_2` FOREIGN KEY (`operation`) REFERENCES `operations` (`op_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `permits_ibfk_3` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `relationships`
--
ALTER TABLE `relationships`
  ADD CONSTRAINT `relationships_ibfk_1` FOREIGN KEY (`child`) REFERENCES `attributes` (`attr_ID`),
  ADD CONSTRAINT `relationships_ibfk_2` FOREIGN KEY (`parent`) REFERENCES `attributes` (`attr_ID`);

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
