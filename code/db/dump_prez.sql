-- MySQL dump 10.13  Distrib 5.5.24, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: radius
-- ------------------------------------------------------
-- Server version	5.5.24-0ubuntu0.12.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `backups`
--

DROP TABLE IF EXISTS `backups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `commit` varchar(64) DEFAULT NULL,
  `datetime` datetime NOT NULL,
  `nas` varchar(100) NOT NULL,
  `action` varchar(50) NOT NULL,
  `users` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backups`
--

LOCK TABLES `backups` WRITE;
/*!40000 ALTER TABLE `backups` DISABLE KEYS */;
INSERT INTO `backups` VALUES (1,'78fa77727b878a15d98cf74cf30fa0c474dea4b1','2013-02-18 14:43:27','192.168.1.1','logoff','Charles'),(2,'78fa77727b878a15d98cf74cf30fa0c474dea4b1','2013-02-18 14:50:09','192.168.1.252','wrmem','Charles'),(3,'78fa77727b878a15d98cf74cf30fa0c474dea4b1','2013-02-18 14:52:54','192.168.1.252','wrmem','Brigitte'),(4,'78fa77727b878a15d98cf74cf30fa0c474dea4b1','2013-02-18 14:58:27','192.168.1.254','login','Brigitte'),(5,'ffdd1658575d529eccf86803316185dc958986d4','2013-02-18 14:59:24','192.168.1.254','login','Charles,Brigitte'),(6,'ffdd1658575d529eccf86803316185dc958986d4','2013-02-18 14:59:27','192.168.1.254','wrmem','Charles'),(10,'ffdd1658575d529eccf86803316185dc958986d4','2013-02-18 14:59:29','192.168.1.254','login','Charles'),(11,'dedb7b4efcc8cf4d2e21185e504e1ac45df0e949','2013-03-15 10:44:51','commit','1','toto'),(12,'337d6482301b883e711cc5516980486db843d63f','2013-03-15 10:45:56','commit','2','toto'),(13,'dedb7b4efcc8cf4d2e21185e504e1ac45df0e949','2013-02-18 14:43:27','10.0.1.252','login','Brigitte'),(14,'337d6482301b883e711cc5516980486db843d63f','2013-02-18 14:50:27','10.0.1.252','wrmem','Brigitte'),(16,'c58df01d430dfd904a8bab60d67821a85c86ffc5','2013-02-18 15:50:27','10.0.1.252','logoff','Brigitte'),(17,'c58df01d430dfd904a8bab60d67821a85c86ffc5','2013-02-18 15:50:27','10.0.1.250','wrmem','Brigitte'),(18,'c58df01d430dfd904a8bab60d67821a85c86ffc5','2013-02-18 15:50:27','10.0.1.253','wrmem','Brigitte'),(19,'c58df01d430dfd904a8bab60d67821a85c86ffc5','2013-02-18 15:50:27','10.0.1.254','wrmem','Brigitte');
/*!40000 ALTER TABLE `backups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs` (
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
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
INSERT INTO `logs` VALUES (1,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','rlm_sql (sql): Driver rlm_sql_mysql (module rlm_sql_mysql) loaded and linked'),(2,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','rlm_sql (sql): Attempting to connect to radius@localhost:/radius'),(3,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #0'),(4,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #0'),(5,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','rlm_sql (sql): Connected new DB handle, #0'),(6,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #1'),(7,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #1'),(8,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','rlm_sql (sql): Connected new DB handle, #1'),(9,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #2'),(10,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #2'),(11,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','rlm_sql (sql): Connected new DB handle, #2'),(12,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #3'),(13,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #3'),(14,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','rlm_sql (sql): Connected new DB handle, #3'),(15,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #4'),(16,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #4'),(17,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','rlm_sql (sql): Connected new DB handle, #4'),(18,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','Loaded virtual server snack'),(19,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','Loaded virtual server inner-tunnel'),(20,'charles','local2','info','info','96','2013-03-11 18:15:18','freeradius','Loaded virtual server <default>'),(21,'charles','local2','info','info','96','2013-03-11 18:15:19','freeradius','Ready to process requests.'),(22,'charles','local2','info','info','96','2013-03-11 18:29:18','freeradius','Exiting normally.'),(23,'charles','local2','info','info','96','2013-03-11 18:29:18','freeradius','rlm_sql (sql): Closing sqlsocket 4'),(24,'charles','local2','info','info','96','2013-03-11 18:29:18','freeradius','rlm_sql (sql): Closing sqlsocket 3'),(25,'charles','local2','info','info','96','2013-03-11 18:29:18','freeradius','rlm_sql (sql): Closing sqlsocket 2'),(26,'charles','local2','info','info','96','2013-03-11 18:29:18','freeradius','rlm_sql (sql): Closing sqlsocket 1'),(27,'charles','local2','info','info','96','2013-03-11 18:29:18','freeradius','rlm_sql (sql): Closing sqlsocket 0'),(28,'bhconsulting','local2','info','info','96','2013-03-14 14:27:12','freeradius','Loaded virtual server inner-tunnel'),(29,'bhconsulting','local2','info','info','96','2013-03-14 14:27:12','freeradius','rlm_sql (sql): Driver rlm_sql_mysql (module rlm_sql_mysql) loaded and linked'),(30,'bhconsulting','local2','info','info','96','2013-03-14 14:27:12','freeradius','rlm_sql (sql): Attempting to connect to radius@localhost:/radius'),(31,'bhconsulting','local2','info','info','96','2013-03-14 14:27:12','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #0'),(32,'bhconsulting','local2','info','info','96','2013-03-14 14:27:12','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #0'),(33,'bhconsulting','local2','info','info','96','2013-03-14 14:27:12','freeradius','rlm_sql (sql): Connected new DB handle, #0'),(34,'bhconsulting','local2','info','info','96','2013-03-14 14:27:12','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #1'),(35,'bhconsulting','local2','info','info','96','2013-03-14 14:27:12','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #1'),(36,'bhconsulting','local2','info','info','96','2013-03-14 14:27:12','freeradius','rlm_sql (sql): Connected new DB handle, #1'),(37,'bhconsulting','local2','info','info','96','2013-03-14 14:27:12','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #2'),(38,'bhconsulting','local2','info','info','96','2013-03-14 14:27:12','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #2'),(39,'bhconsulting','local2','info','info','96','2013-03-14 14:27:12','freeradius','rlm_sql (sql): Connected new DB handle, #2'),(40,'bhconsulting','local2','info','info','96','2013-03-14 14:27:12','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #3'),(41,'bhconsulting','local2','info','warn','96','2013-03-14 14:27:12','freeradius','rlm_sql_mysql: Starting connect to MySQL server with warns for #3'),(42,'bhconsulting','local2','info','info','96','2013-03-14 14:27:12','freeradius','rlm_sql (sql): Connected new DB handle, #3'),(43,'bhconsulting','local2','info','info','96','2013-03-14 14:27:12','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #4'),(44,'bhconsulting','local2','info','info','96','2013-03-14 14:27:12','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #4'),(45,'bhconsulting','local2','info','err','96','2013-03-14 14:27:12','freeradius','rlm_sql (sql): Connected new DB handle with errors, #4'),(46,'bhconsulting','local2','info','info','96','2013-03-14 14:27:13','freeradius','Loaded virtual server bh.consulting.net'),(47,'bhconsulting','local2','info','info','96','2013-03-14 14:27:13','freeradius','Loaded virtual server <default>'),(48,'bhconsulting','local2','info','info','96','2013-03-14 14:27:13','freeradius','Ready to process requests.'),(49,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','Loaded virtual server inner-tunnel'),(50,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','rlm_sql (sql): Driver rlm_sql_mysql (module rlm_sql_mysql) loaded and linked'),(51,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','rlm_sql (sql): Attempting to connect to radius@localhost:/radius'),(52,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #0'),(53,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #0'),(54,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','rlm_sql (sql): Connected new DB handle, #0'),(55,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #1'),(56,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #1'),(57,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','rlm_sql (sql): Connected new DB handle, #1'),(58,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #2'),(59,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #2'),(60,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','rlm_sql (sql): Connected new DB handle, #2'),(61,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #3'),(62,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #3'),(63,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','rlm_sql (sql): Connected new DB handle, #3'),(64,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #4'),(65,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #4'),(66,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','rlm_sql (sql): Connected new DB handle, #4'),(67,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','Loaded virtual server bh.consulting.net'),(68,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','Loaded virtual server <default>'),(69,'bhconsulting','local2','info','info','96','2013-03-18 19:15:23','freeradius','Ready to process requests.'),(70,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','Loaded virtual server inner-tunnel'),(71,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','rlm_sql (sql): Driver rlm_sql_mysql (module rlm_sql_mysql) loaded and linked'),(72,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','rlm_sql (sql): Attempting to connect to radius@localhost:/radius'),(73,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #0'),(74,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #0'),(75,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','rlm_sql (sql): Connected new DB handle, #0'),(76,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #1'),(77,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #1'),(78,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','rlm_sql (sql): Connected new DB handle, #1'),(79,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #2'),(80,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #2'),(81,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','rlm_sql (sql): Connected new DB handle, #2'),(82,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #3'),(83,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #3'),(84,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','rlm_sql (sql): Connected new DB handle, #3'),(85,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #4'),(86,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #4'),(87,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','rlm_sql (sql): Connected new DB handle, #4'),(88,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','Loaded virtual server bh.consulting.net'),(89,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','Loaded virtual server <default>'),(90,'bhconsulting','local2','info','info','96','2013-03-18 19:20:19','freeradius','Ready to process requests.'),(91,'bhconsulting','local2','info','info','96','2013-03-18 19:31:11','freeradius','Loaded virtual server inner-tunnel'),(92,'bhconsulting','local2','info','info','96','2013-03-18 19:31:11','freeradius','rlm_sql (sql): Driver rlm_sql_mysql (module rlm_sql_mysql) loaded and linked'),(93,'bhconsulting','local2','info','info','96','2013-03-18 19:31:11','freeradius','rlm_sql (sql): Attempting to connect to radius@localhost:/radius'),(94,'bhconsulting','local2','info','info','96','2013-03-18 19:31:11','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #0'),(95,'bhconsulting','local2','info','info','96','2013-03-18 19:31:11','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #0'),(96,'bhconsulting','local2','err','err','93','2013-03-18 19:31:11','freeradius','rlm_sql_mysql: Couldn\'t connect socket to MySQL server radius@localhost:radius'),(97,'bhconsulting','local2','err','err','93','2013-03-18 19:31:11','freeradius','rlm_sql_mysql: Mysql error \'Can\'t connect to local MySQL server through socket \'/var/run/mysqld/mysqld.sock\' (2)\''),(98,'bhconsulting','local2','err','err','93','2013-03-18 19:31:11','freeradius','rlm_sql (sql): Failed to connect DB handle #0'),(99,'bhconsulting','local2','info','info','96','2013-03-18 19:31:11','freeradius','rlm_sql (sql): There are no DB handles to use! skipped 5, tried to connect 0'),(100,'bhconsulting','local2','err','err','93','2013-03-18 19:31:11','freeradius','Failed to load clients from SQL.'),(101,'bhconsulting','local2','info','info','96','2013-03-18 19:31:11','freeradius','rlm_sql (sql): Closing sqlsocket 4'),(102,'bhconsulting','local2','info','info','96','2013-03-18 19:31:11','freeradius','rlm_sql (sql): Closing sqlsocket 3'),(103,'bhconsulting','local2','info','info','96','2013-03-18 19:31:11','freeradius','rlm_sql (sql): Closing sqlsocket 2'),(104,'bhconsulting','local2','info','info','96','2013-03-18 19:31:11','freeradius','rlm_sql (sql): Closing sqlsocket 1'),(105,'bhconsulting','local2','info','info','96','2013-03-18 19:31:11','freeradius','rlm_sql (sql): Closing sqlsocket 0'),(106,'bhconsulting','local2','err','err','93','2013-03-18 19:31:11','freeradius','/etc/freeradius/sql.conf[22]: Instantiation failed for module \"sql\"'),(107,'bhconsulting','local2','err','err','93','2013-03-18 19:31:11','freeradius','/etc/freeradius/sites-enabled/bh.consulting.net[9]: Failed to load module \"sql\".'),(108,'bhconsulting','local2','err','err','93','2013-03-18 19:31:11','freeradius','/etc/freeradius/sites-enabled/bh.consulting.net[2]: Errors parsing authorize section. '),(109,'bhconsulting','local2','err','err','93','2013-03-18 19:31:11','freeradius','Failed to load virtual server bh.consulting.net'),(110,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','Loaded virtual server inner-tunnel'),(111,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','rlm_sql (sql): Driver rlm_sql_mysql (module rlm_sql_mysql) loaded and linked'),(112,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','rlm_sql (sql): Attempting to connect to radius@localhost:/radius'),(113,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #0'),(114,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #0'),(115,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','rlm_sql (sql): Connected new DB handle, #0'),(116,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #1'),(117,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #1'),(118,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','rlm_sql (sql): Connected new DB handle, #1'),(119,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #2'),(120,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #2'),(121,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','rlm_sql (sql): Connected new DB handle, #2'),(122,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #3'),(123,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #3'),(124,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','rlm_sql (sql): Connected new DB handle, #3'),(125,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','rlm_sql (sql): Attempting to connect rlm_sql_mysql #4'),(126,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','rlm_sql_mysql: Starting connect to MySQL server for #4'),(127,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','rlm_sql (sql): Connected new DB handle, #4'),(128,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','Loaded virtual server bh.consulting.net'),(129,'bhconsulting','local2','info','info','96','2013-03-20 14:17:34','freeradius','Loaded virtual server <default>'),(130,'bhconsulting','local2','info','info','96','2013-03-20 14:17:35','freeradius','Ready to process requests.');
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nas`
--

DROP TABLE IF EXISTS `nas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nas` (
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nas`
--

LOCK TABLES `nas` WRITE;
/*!40000 ALTER TABLE `nas` DISABLE KEYS */;
INSERT INTO `nas` VALUES (1,'10.0.1.250','switch1','other',NULL,'poil',NULL,NULL,'Switch compta.'),(2,'10.0.1.252','switch2','other',1812,'Switch adm.',NULL,NULL,'Switch adm.'),(4,'10.0.1.254','switch3','other',1812,'ssss',NULL,NULL,'Switch remise.');
/*!40000 ALTER TABLE `nas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `radacct`
--

DROP TABLE IF EXISTS `radacct`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `radacct` (
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `radacct`
--

LOCK TABLES `radacct` WRITE;
/*!40000 ALTER TABLE `radacct` DISABLE KEYS */;
INSERT INTO `radacct` VALUES (1,'00000002','8a30fb05cfc6471e','rene','','','10.0.1.252','0','Async','2013-03-11 18:34:51','2013-03-11 18:34:53',2,'RADIUS','','',0,0,'','','User-Request','NAS-Prompt-User','','',0,0,''),(3,'00000004','862e4789c87ee830','rene','','','10.0.1.252','0','Ethernet','2013-03-11 18:38:57',NULL,29,'RADIUS','','',0,0,'','00-21-70-d6-e7-c7','User-Request','NAS-Prompt-User','','',0,0,''),(4,'00000006','86bfb3bfd145352f','rene','','','10.0.1.252','0','Async','2013-03-11 19:02:46','2013-03-11 19:02:58',13,'RADIUS','','',0,0,'','','User-Request','NAS-Prompt-User','','',0,0,''),(6,'00000008','dc72f1fecab7c049','rene','','','10.0.1.252','0','Async','2013-03-11 20:21:40','2013-03-11 20:22:07',28,'RADIUS','','',0,0,'','','User-Request','NAS-Prompt-User','','',0,0,'');
/*!40000 ALTER TABLE `radacct` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `radcheck`
--

DROP TABLE IF EXISTS `radcheck`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `radcheck` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(64) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '==',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `username` (`username`(32))
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `radcheck`
--

LOCK TABLES `radcheck` WRITE;
/*!40000 ALTER TABLE `radcheck` DISABLE KEYS */;
INSERT INTO `radcheck` VALUES (13,'rene','Cleartext-Password',':=','rene'),(14,'002170d6e7c7','NAS-Port-Type','==','15'),(15,'002170d6e7c7','Cleartext-Password',':=','002170d6e7c7'),(16,'002170d6e7c7','EAP-Type',':=','MD5-CHALLENGE'),(17,'stagiaire1','Expiration',':=','15 Mar 2012 10:18:33'),(30,'stagiaire1','NAS-Port-Type',':=','Ethernet'),(31,'totottls','NAS-Port-Type','=~','Async|Virtual|Ethernet'),(32,'totottls','Cleartext-Password',':=','totottls'),(33,'totottls','EAP-Type',':=','EAP-TTLS'),(35,'ttls2','NAS-Port-Type','=~','Ethernet'),(36,'ttls2','Cleartext-Password',':=','ttls2'),(37,'ttls2','EAP-Type',':=','MD5-CHALLENGE'),(42,'PI','NAS-Port-Type','=~','Ethernet'),(43,'PI','Cleartext-Password',':=','pi'),(44,'PI','EAP-Type',':=','EAP-TTLS'),(45,'PI','Expiration','==','14 Mar 2013 11:27:28');
/*!40000 ALTER TABLE `radcheck` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `radgroup`
--

DROP TABLE IF EXISTS `radgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `radgroup` (
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `radgroup`
--

LOCK TABLES `radgroup` WRITE;
/*!40000 ALTER TABLE `radgroup` DISABLE KEYS */;
INSERT INTO `radgroup` VALUES (10,'Stagiaires 2013',NULL,'',0,0,0,0);
/*!40000 ALTER TABLE `radgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `radgroupcheck`
--

DROP TABLE IF EXISTS `radgroupcheck`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `radgroupcheck` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(64) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '==',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `groupname` (`groupname`(32))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `radgroupcheck`
--

LOCK TABLES `radgroupcheck` WRITE;
/*!40000 ALTER TABLE `radgroupcheck` DISABLE KEYS */;
INSERT INTO `radgroupcheck` VALUES (1,'stagiaires2012','Expiration',':=','2012-09-1 14:27:13'),(2,'stagiaires2012','Expiration',':=','2012-09-01 14:27:13');
/*!40000 ALTER TABLE `radgroupcheck` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `radgroupreply`
--

DROP TABLE IF EXISTS `radgroupreply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `radgroupreply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(64) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '=',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `groupname` (`groupname`(32))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `radgroupreply`
--

LOCK TABLES `radgroupreply` WRITE;
/*!40000 ALTER TABLE `radgroupreply` DISABLE KEYS */;
/*!40000 ALTER TABLE `radgroupreply` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `radpostauth`
--

DROP TABLE IF EXISTS `radpostauth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `radpostauth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `pass` varchar(64) NOT NULL DEFAULT '',
  `reply` varchar(32) NOT NULL DEFAULT '',
  `authdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `radpostauth`
--

LOCK TABLES `radpostauth` WRITE;
/*!40000 ALTER TABLE `radpostauth` DISABLE KEYS */;
INSERT INTO `radpostauth` VALUES (1,'rene','pwdrene','Access-Accept','2013-03-11 16:34:51'),(2,'cunegonde','pwd1','Access-Accept','2013-03-11 16:36:27'),(3,'rene','pwdrene','Access-Accept','2013-03-11 16:38:57'),(4,'rene','pwdrene','Access-Accept','2013-03-11 17:02:46'),(5,'rene','pwdrene','Access-Accept','2013-03-11 17:03:13'),(6,'rene','pwdrene','Access-Accept','2013-03-11 18:21:40');
/*!40000 ALTER TABLE `radpostauth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `radreply`
--

DROP TABLE IF EXISTS `radreply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `radreply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(64) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '=',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `username` (`username`(32))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `radreply`
--

LOCK TABLES `radreply` WRITE;
/*!40000 ALTER TABLE `radreply` DISABLE KEYS */;
/*!40000 ALTER TABLE `radreply` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `raduser`
--

DROP TABLE IF EXISTS `raduser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `raduser` (
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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `raduser`
--

LOCK TABLES `raduser` WRITE;
/*!40000 ALTER TABLE `raduser` DISABLE KEYS */;
INSERT INTO `raduser` VALUES (9,'rene','root',0,NULL,NULL,0,0,0,0),(10,'charles','admin',0,NULL,'',1,0,1,0),(11,'brigitte','tech',0,NULL,'',0,1,0,0),(12,'002170d6e7c7','user',0,NULL,'Imprimante 42',0,0,0,1),(13,'stagiaire1','user',0,NULL,'Stagiaire TELECOM Nancy.',0,1,0,0),(18,'totottls','user',0,NULL,'',1,1,0,0),(19,'ttls2','user',0,NULL,'',0,1,0,0),(21,'PI','admin',0,NULL,'',0,1,0,0);
/*!40000 ALTER TABLE `raduser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `radusergroup`
--

DROP TABLE IF EXISTS `radusergroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `radusergroup` (
  `username` varchar(64) NOT NULL DEFAULT '',
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `priority` int(11) NOT NULL DEFAULT '1',
  KEY `username` (`username`(32))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `radusergroup`
--

LOCK TABLES `radusergroup` WRITE;
/*!40000 ALTER TABLE `radusergroup` DISABLE KEYS */;
INSERT INTO `radusergroup` VALUES ('charles','Stagiaires 2013',1),('002170d6e7c7','Stagiaires 2013',1),('brigitte','Stagiaires 2013',1),('PI','Stagiaires 2013',1);
/*!40000 ALTER TABLE `radusergroup` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-03-20 14:22:32
