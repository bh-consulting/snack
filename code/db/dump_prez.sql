-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mer 20 Mars 2013 à 15:51
-- Version du serveur: 5.5.29
-- Version de PHP: 5.4.6-1ubuntu1.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `radius`
--

-- --------------------------------------------------------

--
-- Structure de la table `backups`
--

CREATE TABLE IF NOT EXISTS `backups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `commit` varchar(64) DEFAULT NULL,
  `datetime` datetime NOT NULL,
  `nas` varchar(100) NOT NULL,
  `action` varchar(50) NOT NULL,
  `users` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Contenu de la table `backups`
--

INSERT INTO `backups` (`id`, `commit`, `datetime`, `nas`, `action`, `users`) VALUES
(1, '78fa77727b878a15d98cf74cf30fa0c474dea4b1', '2013-02-18 14:43:27', '192.168.1.1', 'logoff', 'Charles'),
(2, '78fa77727b878a15d98cf74cf30fa0c474dea4b1', '2013-02-18 14:50:09', '192.168.1.252', 'wrmem', 'Charles'),
(3, '78fa77727b878a15d98cf74cf30fa0c474dea4b1', '2013-02-18 14:52:54', '192.168.1.252', 'wrmem', 'Brigitte'),
(4, '78fa77727b878a15d98cf74cf30fa0c474dea4b1', '2013-02-18 14:58:27', '192.168.1.254', 'login', 'Brigitte'),
(5, 'ffdd1658575d529eccf86803316185dc958986d4', '2013-02-18 14:59:24', '192.168.1.254', 'login', 'Charles,Brigitte'),
(6, 'ffdd1658575d529eccf86803316185dc958986d4', '2013-02-18 14:59:27', '192.168.1.254', 'wrmem', 'Charles'),
(10, 'ffdd1658575d529eccf86803316185dc958986d4', '2013-02-18 14:59:29', '192.168.1.254', 'login', 'Charles'),
(11, 'dedb7b4efcc8cf4d2e21185e504e1ac45df0e949', '2013-03-15 10:44:51', 'commit', '1', 'toto'),
(12, '337d6482301b883e711cc5516980486db843d63f', '2013-03-15 10:45:56', 'commit', '2', 'toto'),
(13, 'dedb7b4efcc8cf4d2e21185e504e1ac45df0e949', '2013-02-18 14:43:27', '10.0.1.252', 'login', 'Brigitte'),
(14, '337d6482301b883e711cc5516980486db843d63f', '2013-02-18 14:50:27', '10.0.1.252', 'wrmem', 'Brigitte'),
(16, 'c58df01d430dfd904a8bab60d67821a85c86ffc5', '2013-02-18 15:50:27', '10.0.1.252', 'logoff', 'Brigitte'),
(17, 'c58df01d430dfd904a8bab60d67821a85c86ffc5', '2013-02-18 15:50:27', '10.0.1.250', 'wrmem', 'Brigitte'),
(18, 'c58df01d430dfd904a8bab60d67821a85c86ffc5', '2013-02-18 15:50:27', '10.0.1.253', 'wrmem', 'Brigitte'),
(19, 'c58df01d430dfd904a8bab60d67821a85c86ffc5', '2013-02-18 15:50:27', '10.0.1.254', 'wrmem', 'Brigitte');

-- --------------------------------------------------------

--
-- Structure de la table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `host` varchar(128) DEFAULT NULL,
  `facility` varchar(10) DEFAULT NULL,
  `priority` varchar(10) DEFAULT NULL,
  `level` varchar(10) DEFAULT NULL,
  `tag` varchar(10) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `program` varchar(15) DEFAULT NULL,
  `msg` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=163 ;

--
-- Contenu de la table `logs`
--

INSERT INTO `logs` (`id`, `host`, `facility`, `priority`, `level`, `tag`, `datetime`, `program`, `msg`) VALUES
(1, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'rlm_sql (sql): Driver rlm_sql_mysql (module rlm_sql_mysql) loaded and linked'),
(2, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'rlm_sql (sql): Attempting to connect to radius@localhost:/radius'),
(3, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #0'),
(4, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #0'),
(5, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #0'),
(6, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #1'),
(7, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #1'),
(8, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #1'),
(9, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #2'),
(10, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #2'),
(11, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #2'),
(12, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #3'),
(13, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #3'),
(14, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #3'),
(15, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #4'),
(16, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #4'),
(17, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #4'),
(18, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'Loaded virtual server snack'),
(19, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'Loaded virtual server inner-tunnel'),
(20, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:18', 'freeradius', 'Loaded virtual server <default>'),
(21, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:15:19', 'freeradius', 'Ready to process requests.'),
(22, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:29:18', 'freeradius', 'Exiting normally.'),
(23, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:29:18', 'freeradius', 'rlm_sql (sql): Closing sqlsocket 4'),
(24, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:29:18', 'freeradius', 'rlm_sql (sql): Closing sqlsocket 3'),
(25, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:29:18', 'freeradius', 'rlm_sql (sql): Closing sqlsocket 2'),
(26, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:29:18', 'freeradius', 'rlm_sql (sql): Closing sqlsocket 1'),
(27, 'charles', 'local2', 'info', 'info', '96', '2013-03-11 18:29:18', 'freeradius', 'rlm_sql (sql): Closing sqlsocket 0'),
(28, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-14 14:27:12', 'freeradius', 'Loaded virtual server inner-tunnel'),
(29, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-14 14:27:12', 'freeradius', 'rlm_sql (sql): Driver rlm_sql_mysql (module rlm_sql_mysql) loaded and linked'),
(30, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-14 14:27:12', 'freeradius', 'rlm_sql (sql): Attempting to connect to radius@localhost:/radius'),
(31, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-14 14:27:12', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #0'),
(32, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-14 14:27:12', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #0'),
(33, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-14 14:27:12', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #0'),
(34, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-14 14:27:12', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #1'),
(35, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-14 14:27:12', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #1'),
(36, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-14 14:27:12', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #1'),
(37, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-14 14:27:12', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #2'),
(38, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-14 14:27:12', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #2'),
(39, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-14 14:27:12', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #2'),
(40, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-14 14:27:12', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #3'),
(41, 'bhconsulting', 'local2', 'info', 'warn', '96', '2013-03-14 14:27:12', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server with warns for #3'),
(42, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-14 14:27:12', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #3'),
(43, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-14 14:27:12', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #4'),
(44, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-14 14:27:12', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #4'),
(45, 'bhconsulting', 'local2', 'info', 'err', '96', '2013-03-14 14:27:12', 'freeradius', 'rlm_sql (sql): Connected new DB handle with errors, #4'),
(46, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-14 14:27:13', 'freeradius', 'Loaded virtual server bh.consulting.net'),
(47, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-14 14:27:13', 'freeradius', 'Loaded virtual server <default>'),
(48, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-14 14:27:13', 'freeradius', 'Ready to process requests.'),
(49, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'Loaded virtual server inner-tunnel'),
(50, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'rlm_sql (sql): Driver rlm_sql_mysql (module rlm_sql_mysql) loaded and linked'),
(51, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'rlm_sql (sql): Attempting to connect to radius@localhost:/radius'),
(52, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #0'),
(53, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #0'),
(54, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #0'),
(55, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #1'),
(56, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #1'),
(57, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #1'),
(58, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #2'),
(59, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #2'),
(60, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #2'),
(61, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #3'),
(62, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #3'),
(63, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #3'),
(64, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #4'),
(65, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #4'),
(66, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #4'),
(67, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'Loaded virtual server bh.consulting.net'),
(68, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'Loaded virtual server <default>'),
(69, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:15:23', 'freeradius', 'Ready to process requests.'),
(70, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'Loaded virtual server inner-tunnel'),
(71, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'rlm_sql (sql): Driver rlm_sql_mysql (module rlm_sql_mysql) loaded and linked'),
(72, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'rlm_sql (sql): Attempting to connect to radius@localhost:/radius'),
(73, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #0'),
(74, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #0'),
(75, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #0'),
(76, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #1'),
(77, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #1'),
(78, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #1'),
(79, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #2'),
(80, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #2'),
(81, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #2'),
(82, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #3'),
(83, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #3'),
(84, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #3'),
(85, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #4'),
(86, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #4'),
(87, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #4'),
(88, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'Loaded virtual server bh.consulting.net'),
(89, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'Loaded virtual server <default>'),
(90, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:20:19', 'freeradius', 'Ready to process requests.'),
(91, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:31:11', 'freeradius', 'Loaded virtual server inner-tunnel'),
(92, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:31:11', 'freeradius', 'rlm_sql (sql): Driver rlm_sql_mysql (module rlm_sql_mysql) loaded and linked'),
(93, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:31:11', 'freeradius', 'rlm_sql (sql): Attempting to connect to radius@localhost:/radius'),
(94, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:31:11', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #0'),
(95, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:31:11', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #0'),
(96, 'bhconsulting', 'local2', 'err', 'err', '93', '2013-03-18 19:31:11', 'freeradius', 'rlm_sql_mysql: Couldn''t connect socket to MySQL server radius@localhost:radius'),
(97, 'bhconsulting', 'local2', 'err', 'err', '93', '2013-03-18 19:31:11', 'freeradius', 'rlm_sql_mysql: Mysql error ''Can''t connect to local MySQL server through socket ''/var/run/mysqld/mysqld.sock'' (2)'''),
(98, 'bhconsulting', 'local2', 'err', 'err', '93', '2013-03-18 19:31:11', 'freeradius', 'rlm_sql (sql): Failed to connect DB handle #0'),
(99, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:31:11', 'freeradius', 'rlm_sql (sql): There are no DB handles to use! skipped 5, tried to connect 0'),
(100, 'bhconsulting', 'local2', 'err', 'err', '93', '2013-03-18 19:31:11', 'freeradius', 'Failed to load clients from SQL.'),
(101, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:31:11', 'freeradius', 'rlm_sql (sql): Closing sqlsocket 4'),
(102, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:31:11', 'freeradius', 'rlm_sql (sql): Closing sqlsocket 3'),
(103, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:31:11', 'freeradius', 'rlm_sql (sql): Closing sqlsocket 2'),
(104, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:31:11', 'freeradius', 'rlm_sql (sql): Closing sqlsocket 1'),
(105, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-18 19:31:11', 'freeradius', 'rlm_sql (sql): Closing sqlsocket 0'),
(106, 'bhconsulting', 'local2', 'err', 'err', '93', '2013-03-18 19:31:11', 'freeradius', '/etc/freeradius/sql.conf[22]: Instantiation failed for module "sql"'),
(107, 'bhconsulting', 'local2', 'err', 'err', '93', '2013-03-18 19:31:11', 'freeradius', '/etc/freeradius/sites-enabled/bh.consulting.net[9]: Failed to load module "sql".'),
(108, 'bhconsulting', 'local2', 'err', 'err', '93', '2013-03-18 19:31:11', 'freeradius', '/etc/freeradius/sites-enabled/bh.consulting.net[2]: Errors parsing authorize section. '),
(109, 'bhconsulting', 'local2', 'err', 'err', '93', '2013-03-18 19:31:11', 'freeradius', 'Failed to load virtual server bh.consulting.net'),
(110, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'Loaded virtual server inner-tunnel'),
(111, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'rlm_sql (sql): Driver rlm_sql_mysql (module rlm_sql_mysql) loaded and linked'),
(112, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'rlm_sql (sql): Attempting to connect to radius@localhost:/radius'),
(113, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #0'),
(114, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #0'),
(115, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #0'),
(116, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #1'),
(117, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #1'),
(118, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #1'),
(119, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #2'),
(120, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #2'),
(121, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #2'),
(122, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #3'),
(123, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #3'),
(124, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #3'),
(125, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'rlm_sql (sql): Attempting to connect rlm_sql_mysql #4'),
(126, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'rlm_sql_mysql: Starting connect to MySQL server for #4'),
(127, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'rlm_sql (sql): Connected new DB handle, #4'),
(128, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'Loaded virtual server bh.consulting.net'),
(129, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:34', 'freeradius', 'Loaded virtual server <default>'),
(130, 'bhconsulting', 'local2', 'info', 'info', '96', '2013-03-20 14:17:35', 'freeradius', 'Ready to process requests.'),
(132, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:05:22', 'snack', 'rene: logged in'),
(133, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:05:45', 'snack', 'rene: deleted user marie'),
(134, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:07:39', 'snack', 'rene: added group Administrateurs'),
(135, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:08:31', 'snack', 'rene: added group 12'),
(136, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:09:24', 'snack', 'rene: edited group Administrateurs'),
(137, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:10:02', 'snack', 'rene: added group 13'),
(138, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:11:01', 'snack', 'rene: added group 14'),
(139, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:18:44', 'snack', 'rene: created certificate for user -1'),
(140, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:18:44', 'snack', 'rene: added user 22'),
(141, '1215N', 'local4', 'err', 'err', 'a3', '2013-03-20 15:18:54', 'snack', 'rene: warning: while deleting user brigitte, certificate files not found!'),
(142, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:18:54', 'snack', 'rene: deleted user 22'),
(143, '1215N', 'local4', 'err', 'err', 'a3', '2013-03-20 15:19:41', 'snack', 'rene: Server freeradius cannot be restarted.'),
(144, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:20:12', 'snack', 'rene: Parameters have been updated.'),
(145, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:37:46', 'snack', 'rene: utilisateur 18 supprimé'),
(146, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:37:58', 'snack', 'rene: utilisateur 19 supprimé'),
(147, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:38:39', 'snack', 'rene: utilisateur 23 ajouté'),
(148, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:39:21', 'snack', 'rene: certificat créé pour l''utilisateur rene'),
(149, '1215N', 'local4', 'err', 'err', 'a3', '2013-03-20 15:39:21', 'snack', 'rene: erreur en ajoutant un utilisateur par certificat'),
(150, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:40:49', 'snack', 'rene: certificat créé pour l''utilisateur charles'),
(151, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:40:49', 'snack', 'rene: utilisateur charles ajouté'),
(152, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:42:02', 'snack', 'rene: certificat créé pour l''utilisateur francois'),
(153, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:42:02', 'snack', 'rene: utilisateur francois ajouté'),
(154, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:42:50', 'snack', 'rene: utilisateur andre édité'),
(155, '1215N', 'local4', 'err', 'err', 'a3', '2013-03-20 15:43:34', 'snack', 'rene: erreur en ajoutant un utilisateur SNACK'),
(156, '1215N', 'local4', 'err', 'err', 'a3', '2013-03-20 15:43:47', 'snack', 'rene: erreur en ajoutant un utilisateur SNACK'),
(157, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:45:09', 'snack', 'rene: certificat créé pour l''utilisateur jean'),
(158, '1215N', 'local4', 'err', 'err', 'a3', '2013-03-20 15:45:09', 'snack', 'rene: erreur en ajoutant un utilisateur par certificat'),
(159, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:45:21', 'snack', 'rene: certificat créé pour l''utilisateur brigitte'),
(160, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:45:22', 'snack', 'rene: utilisateur bruno ajouté'),
(161, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:48:00', 'snack', 'rene: groupe Direction édité'),
(162, '1215N', 'local4', 'info', 'info', 'a6', '2013-03-20 15:48:25', 'snack', 'rene: groupe Stagiaires édité');

-- --------------------------------------------------------

--
-- Structure de la table `nas`
--

CREATE TABLE IF NOT EXISTS `nas` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nasname` varchar(128) NOT NULL,
  `shortname` varchar(32) DEFAULT NULL,
  `type` varchar(30) DEFAULT 'other',
  `ports` int(5) DEFAULT NULL,
  `secret` varchar(60) NOT NULL DEFAULT 'secret',
  `server` varchar(64) DEFAULT NULL,
  `community` varchar(50) DEFAULT NULL,
  `description` varchar(200) DEFAULT 'RADIUS Client',
  PRIMARY KEY (`id`),
  KEY `nasname` (`nasname`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `nas`
--

INSERT INTO `nas` (`id`, `nasname`, `shortname`, `type`, `ports`, `secret`, `server`, `community`, `description`) VALUES
(1, '10.0.1.250', 'switch1', 'other', NULL, 'poil', NULL, NULL, 'Switch compta.'),
(2, '10.0.1.252', 'switch2', 'other', 1812, 'Switch adm.', NULL, NULL, 'Switch adm.'),
(4, '10.0.1.254', 'switch3', 'other', 1812, 'ssss', NULL, NULL, 'Switch remise.');

-- --------------------------------------------------------

--
-- Structure de la table `radacct`
--

CREATE TABLE IF NOT EXISTS `radacct` (
  `radacctid` bigint(21) NOT NULL AUTO_INCREMENT,
  `acctsessionid` varchar(64) NOT NULL DEFAULT '',
  `acctuniqueid` varchar(32) NOT NULL DEFAULT '',
  `username` varchar(64) NOT NULL DEFAULT '',
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `realm` varchar(64) DEFAULT '',
  `nasipaddress` varchar(15) NOT NULL DEFAULT '',
  `nasportid` varchar(15) DEFAULT NULL,
  `nasporttype` varchar(32) DEFAULT NULL,
  `acctstarttime` datetime DEFAULT NULL,
  `acctstoptime` datetime DEFAULT NULL,
  `acctsessiontime` int(12) DEFAULT NULL,
  `acctauthentic` varchar(32) DEFAULT NULL,
  `connectinfo_start` varchar(50) DEFAULT NULL,
  `connectinfo_stop` varchar(50) DEFAULT NULL,
  `acctinputoctets` bigint(20) DEFAULT NULL,
  `acctoutputoctets` bigint(20) DEFAULT NULL,
  `calledstationid` varchar(50) NOT NULL DEFAULT '',
  `callingstationid` varchar(50) NOT NULL DEFAULT '',
  `acctterminatecause` varchar(32) NOT NULL DEFAULT '',
  `servicetype` varchar(32) DEFAULT NULL,
  `framedprotocol` varchar(32) DEFAULT NULL,
  `framedipaddress` varchar(15) NOT NULL DEFAULT '',
  `acctstartdelay` int(12) DEFAULT NULL,
  `acctstopdelay` int(12) DEFAULT NULL,
  `xascendsessionsvrkey` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`radacctid`),
  KEY `username` (`username`),
  KEY `framedipaddress` (`framedipaddress`),
  KEY `acctsessionid` (`acctsessionid`),
  KEY `acctsessiontime` (`acctsessiontime`),
  KEY `acctuniqueid` (`acctuniqueid`),
  KEY `acctstarttime` (`acctstarttime`),
  KEY `acctstoptime` (`acctstoptime`),
  KEY `nasipaddress` (`nasipaddress`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Contenu de la table `radacct`
--

INSERT INTO `radacct` (`radacctid`, `acctsessionid`, `acctuniqueid`, `username`, `groupname`, `realm`, `nasipaddress`, `nasportid`, `nasporttype`, `acctstarttime`, `acctstoptime`, `acctsessiontime`, `acctauthentic`, `connectinfo_start`, `connectinfo_stop`, `acctinputoctets`, `acctoutputoctets`, `calledstationid`, `callingstationid`, `acctterminatecause`, `servicetype`, `framedprotocol`, `framedipaddress`, `acctstartdelay`, `acctstopdelay`, `xascendsessionsvrkey`) VALUES
(1, '00000002', '8a30fb05cfc6471e', 'rene', '', '', '10.0.1.252', '0', 'Async', '2013-03-11 16:34:51', '2013-03-11 17:34:53', 2, 'RADIUS', '', '', 0, 0, '', '', 'User-Request', 'NAS-Prompt-User', '', '', 0, 0, ''),
(3, '00000004', '862e4789c87ee830', 'rene', '', '', '10.0.1.254', '0', 'Ethernet', '2013-03-11 18:38:57', NULL, 29, 'RADIUS', '', '', 0, 0, '', '00-21-70-d6-e7-c7', 'User-Request', 'NAS-Prompt-User', '', '', 0, 0, ''),
(4, '00000006', '86bfb3bfd145352f', 'rene', '', '', '10.0.1.252', '0', 'Virtual', '2013-03-11 18:02:46', '2013-03-11 19:27:58', 13, 'RADIUS', '', '', 0, 0, '', '10.0.1.242', 'User-Request', 'NAS-Prompt-User', '', '', 0, 0, ''),
(6, '00000008', 'dc72f1fecab7c049', 'rene', '', '', '10.0.1.252', '0', 'Async', '2013-03-11 18:12:40', '2013-03-11 20:22:07', 28, 'RADIUS', '', '', 0, 0, '', '', 'User-Request', 'NAS-Prompt-User', '', '', 0, 0, ''),
(7, '00000004', '862e4789c87ee830', 'brigitte', '', '', '10.0.1.254', '0', 'Ethernet', '2013-03-19 09:38:53', NULL, 29, 'RADIUS', '', '', 0, 0, '', '00-21-70-d6-e7-42', 'User-Request', 'NAS-Prompt-User', '', '', 0, 0, ''),
(8, '00000004', '862e4789c87ee830', 'brigitte', '', '', '10.0.1.254', '0', 'Ethernet', '2013-01-01 09:15:57', '2013-03-09 19:32:00', 29, 'RADIUS', '', '', 0, 0, '', '00-21-70-d6-e7-42', 'User-Request', 'NAS-Prompt-User', '', '', 0, 0, '');

-- --------------------------------------------------------

--
-- Structure de la table `radcheck`
--

CREATE TABLE IF NOT EXISTS `radcheck` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(64) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '==',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `username` (`username`(32))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

--
-- Contenu de la table `radcheck`
--

INSERT INTO `radcheck` (`id`, `username`, `attribute`, `op`, `value`) VALUES
(13, 'rene', 'Cleartext-Password', ':=', 'rene'),
(14, '002170d6e7c7', 'NAS-Port-Type', '==', '15'),
(15, '002170d6e7c7', 'Cleartext-Password', ':=', '002170d6e7c7'),
(16, '002170d6e7c7', 'EAP-Type', ':=', 'MD5-CHALLENGE'),
(17, 'andre', 'Expiration', ':=', '15 Mar 2012 10:18:33'),
(30, 'andre', 'NAS-Port-Type', ':=', 'Ethernet'),
(48, 'jean', 'NAS-Port-Type', '=~', 'Ethernet'),
(49, 'jean', 'Cleartext-Password', ':=', 'jean'),
(50, 'jean', 'EAP-Type', ':=', 'EAP-TTLS'),
(51, 'jean', 'Simultaneous-Use', ':=', '5'),
(52, 'francois', 'NAS-Port-Type', '=~', 'Ethernet'),
(53, 'francois', 'EAP-Type', ':=', 'EAP-TLS'),
(54, 'bruno', 'NAS-Port-Type', '=~', 'Ethernet'),
(55, 'bruno', 'EAP-Type', ':=', 'EAP-TLS'),
(56, 'guillaume', 'NAS-Port-Type', '=~', 'Async|Ethernet'),
(57, 'guillaume', 'EAP-Type', ':=', 'EAP-TLS'),
(58, 'guillaume', 'Cleartext-Password', ':=', 'g');

-- --------------------------------------------------------

--
-- Structure de la table `radgroup`
--

CREATE TABLE IF NOT EXISTS `radgroup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `cert_path` varchar(255) DEFAULT NULL,
  `comment` text,
  `is_cisco` tinyint(1) DEFAULT '0',
  `is_loginpass` tinyint(1) DEFAULT '0',
  `is_cert` tinyint(1) DEFAULT '0',
  `is_mac` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `groupname` (`groupname`(32))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Contenu de la table `radgroup`
--

INSERT INTO `radgroup` (`id`, `groupname`, `cert_path`, `comment`, `is_cisco`, `is_loginpass`, `is_cert`, `is_mac`) VALUES
(10, 'Stagiaires 2013', NULL, '', 0, 0, 0, 0),
(11, 'Administrateurs', NULL, '', 0, 0, 0, 0),
(12, 'Formations avril 2013', NULL, 'Formation au logiciel SNACK', 0, 0, 0, 0),
(13, 'Direction', NULL, '', 0, 0, 0, 0),
(14, 'Employés', NULL, '', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `radgroupcheck`
--

CREATE TABLE IF NOT EXISTS `radgroupcheck` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(64) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '==',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `groupname` (`groupname`(32))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `radgroupcheck`
--

INSERT INTO `radgroupcheck` (`id`, `groupname`, `attribute`, `op`, `value`) VALUES
(1, 'stagiaires2012', 'Expiration', ':=', '2012-09-1 14:27:13'),
(2, 'stagiaires2012', 'Expiration', ':=', '2012-09-01 14:27:13'),
(3, 'Formations avril 2013', 'Expiration', '==', '30 Apr 2013 15:07:56');

-- --------------------------------------------------------

--
-- Structure de la table `radgroupreply`
--

CREATE TABLE IF NOT EXISTS `radgroupreply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(64) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '=',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `groupname` (`groupname`(32))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Contenu de la table `radgroupreply`
--

INSERT INTO `radgroupreply` (`id`, `groupname`, `attribute`, `op`, `value`) VALUES
(1, 'Administrateurs', 'Tunnel-Type', ':=', 'VLAN'),
(2, 'Administrateurs', 'Tunnel-Medium-Type', ':=', 'IEEE-802'),
(3, 'Administrateurs', 'Tunnel-Private-Group-Id', ':=', '3'),
(4, 'Formations avril 2013', 'Tunnel-Type', ':=', 'VLAN'),
(5, 'Formations avril 2013', 'Tunnel-Medium-Type', ':=', 'IEEE-802'),
(6, 'Formations avril 2013', 'Tunnel-Private-Group-Id', ':=', '4'),
(7, 'Direction', 'Tunnel-Type', ':=', 'VLAN'),
(8, 'Direction', 'Tunnel-Medium-Type', ':=', 'IEEE-802'),
(9, 'Direction', 'Tunnel-Private-Group-Id', ':=', '5'),
(10, 'Employés', 'Tunnel-Type', ':=', 'VLAN'),
(11, 'Employés', 'Tunnel-Medium-Type', ':=', 'IEEE-802'),
(12, 'Employés', 'Tunnel-Private-Group-Id', ':=', '6');

-- --------------------------------------------------------

--
-- Structure de la table `radpostauth`
--

CREATE TABLE IF NOT EXISTS `radpostauth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `pass` varchar(64) NOT NULL DEFAULT '',
  `reply` varchar(32) NOT NULL DEFAULT '',
  `authdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `radpostauth`
--

INSERT INTO `radpostauth` (`id`, `username`, `pass`, `reply`, `authdate`) VALUES
(1, 'rene', 'pwdrene', 'Access-Accept', '2013-03-11 16:34:51'),
(2, 'cunegonde', 'pwd1', 'Access-Accept', '2013-03-11 16:36:27'),
(3, 'rene', 'pwdrene', 'Access-Accept', '2013-03-11 16:38:57'),
(4, 'rene', 'pwdrene', 'Access-Accept', '2013-03-11 17:02:46'),
(5, 'rene', 'pwdrene', 'Access-Accept', '2013-03-11 17:03:13'),
(6, 'rene', 'pwdrene', 'Access-Accept', '2013-03-11 18:21:40');

-- --------------------------------------------------------

--
-- Structure de la table `radreply`
--

CREATE TABLE IF NOT EXISTS `radreply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(64) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '=',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `username` (`username`(32))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `raduser`
--

CREATE TABLE IF NOT EXISTS `raduser` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `role` varchar(200) DEFAULT NULL,
  `admin` tinyint(1) DEFAULT '0',
  `cert_path` varchar(255) DEFAULT NULL,
  `comment` text,
  `is_cisco` tinyint(1) DEFAULT '0',
  `is_loginpass` tinyint(1) DEFAULT '0',
  `is_cert` tinyint(1) DEFAULT '0',
  `is_mac` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `username` (`username`(32))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

--
-- Contenu de la table `raduser`
--

INSERT INTO `raduser` (`id`, `username`, `role`, `admin`, `cert_path`, `comment`, `is_cisco`, `is_loginpass`, `is_cert`, `is_mac`) VALUES
(9, 'rene', 'root', 0, NULL, NULL, 0, 0, 0, 0),
(10, 'charles', 'admin', 0, NULL, '', 1, 0, 1, 0),
(11, 'brigitte', 'tech', 0, NULL, '', 0, 1, 0, 0),
(12, '002170d6e7c7', 'user', 0, NULL, 'Imprimante 42', 0, 0, 0, 1),
(13, 'andre', 'user', 0, NULL, 'Stagiaire TELECOM Nancy.', 0, 1, 0, 0),
(23, 'jean', 'user', 0, NULL, '', 0, 1, 0, 0),
(24, 'francois', 'user', 0, NULL, '', 0, 0, 1, 0),
(25, 'bruno', 'user', 0, NULL, '', 0, 0, 1, 0),
(26, 'guillaume', 'root', 0, NULL, '', 1, 0, 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `radusergroup`
--

CREATE TABLE IF NOT EXISTS `radusergroup` (
  `username` varchar(64) NOT NULL DEFAULT '',
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `priority` int(11) NOT NULL DEFAULT '1',
  KEY `username` (`username`(32))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `radusergroup`
--

INSERT INTO `radusergroup` (`username`, `groupname`, `priority`) VALUES
('charles', 'Stagiaires 2013', 1),
('002170d6e7c7', 'Stagiaires 2013', 1),
('brigitte', 'Stagiaires 2013', 1),
('rene', 'Administrateurs', 1),
('charles', 'Direction', 2),
('jean', 'Employés', 1),
('bruno', 'Employés', 1),
('bruno', 'Formations avril 2013', 2),
('francois', 'Formations avril 2013', 1),
('jean', 'Formations avril 2013', 2),
('guillaume', 'Administrateurs', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
