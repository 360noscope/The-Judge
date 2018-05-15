CREATE DATABASE  IF NOT EXISTS `the_judge` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `the_judge`;
-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: localhost    Database: the_judge
-- ------------------------------------------------------
-- Server version	5.7.19-log

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
-- Table structure for table `exercise`
--

DROP TABLE IF EXISTS `exercise`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exercise` (
  `exercise_id` int(6) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `lesson_id` int(6) unsigned zerofill NOT NULL,
  `exercise_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exercise_detail` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exec_time` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exec_memory` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hint` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `difficulty` int(11) NOT NULL DEFAULT '1',
  `exercise_status` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'HIDDEN',
  `completed_score` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`exercise_id`),
  KEY `lesson_idx` (`lesson_id`),
  CONSTRAINT `lesson` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`lesson_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exercise`
--

LOCK TABLES `exercise` WRITE;
/*!40000 ALTER TABLE `exercise` DISABLE KEYS */;
INSERT INTO `exercise` VALUES (000003,000004,'teset33','<p>ttt<br></p>','4','4','<p>ttt<br></p>',5,'ACTIVATED',0),(000004,000004,'test12','<p>test<br></p>','4','4','<p>test<br></p>',5,'HIDDEN',0),(000005,000004,'testja','<p>testja<br></p><p><br></p><p></p>','4','4','<p>testja<br></p><p><br></p><p></p>',5,'HIDDEN',0);
/*!40000 ALTER TABLE `exercise` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exercise_session`
--

DROP TABLE IF EXISTS `exercise_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exercise_session` (
  `session_id` int(7) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `user_id` int(13) unsigned zerofill NOT NULL,
  `exercise_id` int(6) unsigned zerofill NOT NULL,
  `passed_case` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `complete_date` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `try_date` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `total_score` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`),
  KEY `session_idx` (`exercise_id`),
  KEY `user_idx` (`user_id`),
  CONSTRAINT `session` FOREIGN KEY (`exercise_id`) REFERENCES `exercise` (`exercise_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exercise_session`
--

LOCK TABLES `exercise_session` WRITE;
/*!40000 ALTER TABLE `exercise_session` DISABLE KEYS */;
/*!40000 ALTER TABLE `exercise_session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exercise_testcase`
--

DROP TABLE IF EXISTS `exercise_testcase`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exercise_testcase` (
  `testcase_id` int(6) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `exercise_id` int(6) unsigned zerofill NOT NULL,
  `score` int(11) NOT NULL,
  `input` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `output` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`testcase_id`),
  KEY `testcase_idx` (`exercise_id`),
  CONSTRAINT `testcase` FOREIGN KEY (`exercise_id`) REFERENCES `exercise` (`exercise_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exercise_testcase`
--

LOCK TABLES `exercise_testcase` WRITE;
/*!40000 ALTER TABLE `exercise_testcase` DISABLE KEYS */;
INSERT INTO `exercise_testcase` VALUES (000015,000003,100,'test1','test1'),(000016,000003,100,'test2','test2'),(000024,000004,100,'test1','test1'),(000025,000004,100,'test2','test2'),(000032,000005,100,'test1','test1'),(000033,000005,100,'test2','test2');
/*!40000 ALTER TABLE `exercise_testcase` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lesson`
--

DROP TABLE IF EXISTS `lesson`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lesson` (
  `lesson_id` int(6) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `owner_id` int(13) unsigned zerofill NOT NULL,
  `lesson_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lesson_detail` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`lesson_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lesson`
--

LOCK TABLES `lesson` WRITE;
/*!40000 ALTER TABLE `lesson` DISABLE KEYS */;
INSERT INTO `lesson` VALUES (000004,0000000000001,'Problem Solving','GG'),(000006,0000000000001,'Python Da Fuq','Python FTW!');
/*!40000 ALTER TABLE `lesson` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pair_table`
--

DROP TABLE IF EXISTS `pair_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pair_table` (
  `pair_id` int(6) unsigned zerofill NOT NULL,
  `student_id1` int(13) unsigned zerofill DEFAULT NULL,
  `student_id2` int(13) unsigned zerofill DEFAULT NULL,
  `pair_score` int(11) DEFAULT '0',
  PRIMARY KEY (`pair_id`),
  KEY `student_pair_idx` (`student_id1`,`student_id2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pair_table`
--

LOCK TABLES `pair_table` WRITE;
/*!40000 ALTER TABLE `pair_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `pair_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_detail`
--

DROP TABLE IF EXISTS `user_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_detail` (
  `user_id` int(13) unsigned zerofill NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `detail` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_detail`
--

LOCK TABLES `user_detail` WRITE;
/*!40000 ALTER TABLE `user_detail` DISABLE KEYS */;
INSERT INTO `user_detail` VALUES (0000000000001,'Panupong'),(0000000000002,'Panupong Prueksa'),(0000000000006,'kakjung'),(0000000000007,'ppap');
/*!40000 ALTER TABLE `user_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_enrollment`
--

DROP TABLE IF EXISTS `user_enrollment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_enrollment` (
  `enrollment_id` int(7) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `user_id` int(13) unsigned zerofill NOT NULL,
  `lesson_id` int(6) unsigned zerofill NOT NULL,
  `enrollment_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `unenrollment_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`enrollment_id`),
  KEY `enroll_idx` (`user_id`),
  KEY `lesson_idx` (`lesson_id`),
  CONSTRAINT `enroll` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `lesson_user` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`lesson_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_enrollment`
--

LOCK TABLES `user_enrollment` WRITE;
/*!40000 ALTER TABLE `user_enrollment` DISABLE KEYS */;
INSERT INTO `user_enrollment` VALUES (0000006,0000000000002,000004,'2018-05-03 17:17:13','2018-05-03 17:17:13');
/*!40000 ALTER TABLE `user_enrollment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_group`
--

DROP TABLE IF EXISTS `user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_group` (
  `group_id` int(6) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `group_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  PRIMARY KEY (`group_id`),
  CONSTRAINT `user_group` FOREIGN KEY (`group_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_group`
--

LOCK TABLES `user_group` WRITE;
/*!40000 ALTER TABLE `user_group` DISABLE KEYS */;
INSERT INTO `user_group` VALUES (000001,'CLASS OF 2018','just normal student'),(000002,'TEACHER','-');
/*!40000 ALTER TABLE `user_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(13) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `username` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_id` int(6) unsigned zerofill NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (0000000000001,'admin','9801cdf8cf0444e39d6e38ef49d82e21',000002),(0000000000002,'phai','9801cdf8cf0444e39d6e38ef49d82e21',000001),(0000000000006,'kkkk','201030a37a20579bb31ccbbe2ddd1c25',000001),(0000000000007,'ppap','7cd233baa367724e7f4b66afbb083a70',000001);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-05-15 17:18:34
