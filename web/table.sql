-- LP:  Required table
-- Database: `symfony`
--
-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 01, 2017 at 08:06 PM
-- Server version: 10.1.19-MariaDB
-- PHP Version: 5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `symfony`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog_post`
--

CREATE TABLE `blog_post` (
  `id` int(11) NOT NULL,
  `post` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `blogTags` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:json_array)',
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `blog_post`
--

INSERT INTO `blog_post` (`id`, `post`, `status`, `blogTags`, `dateCreated`, `dateUpdated`) VALUES
(1, 'test 123', 1, NULL, '2017-09-29 00:00:00', NULL),
(3, 'hello ', 0, NULL, '2017-09-30 00:00:00', NULL),
(4, 'hello ', 1, NULL, '2017-09-30 00:00:01', NULL),
(5, 'test 0123', 1, NULL, '2017-09-30 18:10:19', NULL),
(6, 'test 0123', 1, NULL, '2017-09-30 18:13:20', NULL),
(7, 'test 0123', 1, NULL, '2017-09-30 18:54:50', NULL),
(8, 'test 0123', 1, NULL, '2017-09-30 19:38:59', NULL),
(10, 'test 0123', 1, NULL, '2017-09-30 22:02:14', NULL),
(12, 'test 0123', 1, NULL, '2017-09-30 22:49:32', NULL),
(13, 'test 0123', 1, NULL, '2017-10-01 12:43:03', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog_post`
--
ALTER TABLE `blog_post`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog_post`
--
ALTER TABLE `blog_post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;