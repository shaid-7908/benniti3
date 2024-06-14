-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: bennit
-- ------------------------------------------------------
-- Server version	8.0.36

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tbl_bennit_subscriptions`
--

DROP TABLE IF EXISTS `tbl_bennit_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_bennit_subscriptions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `payment_id` text,
  `subscription_id` text,
  `plan_id` text,
  `customer_id` text,
  `customer_email` text,
  `status` varchar(50) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `current_period_start` datetime DEFAULT NULL,
  `current_period_end` datetime DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_bennit_subscriptions`
--

LOCK TABLES `tbl_bennit_subscriptions` WRITE;
/*!40000 ALTER TABLE `tbl_bennit_subscriptions` DISABLE KEYS */;
INSERT INTO `tbl_bennit_subscriptions` VALUES (2,'cs_test_a1SQOG87X42BfmRQhx2DNCTx2WJD4bvlyLWYJPAVJqhCtwgudmmsSxmu8i','sub_1PJB01Ha6p7KdLXR4Z05JafK','price_1OQZwTHa6p7KdLXRCZJ543RA','cus_Q9TxR2xaeO6TRk','rr7384029333@gmail.com','active','2024-05-22 08:58:17',NULL,'2024-05-22 08:58:17','2024-06-22 08:58:17',2.00,'usd');
/*!40000 ALTER TABLE `tbl_bennit_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_credentials`
--

DROP TABLE IF EXISTS `tbl_credentials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_credentials` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_trainingpartner_id` int DEFAULT NULL,
  `credential_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_credentials`
--

LOCK TABLES `tbl_credentials` WRITE;
/*!40000 ALTER TABLE `tbl_credentials` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_credentials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_industry`
--

DROP TABLE IF EXISTS `tbl_industry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_industry` (
  `id` int NOT NULL AUTO_INCREMENT,
  `industry_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_industry`
--

LOCK TABLES `tbl_industry` WRITE;
/*!40000 ALTER TABLE `tbl_industry` DISABLE KEYS */;
INSERT INTO `tbl_industry` VALUES (1,'test'),(2,'bbk'),(3,'kkkk'),(4,'test industry'),(5,'react'),(6,'js'),(7,'marketing and advertising'),(8,'information technology (it)'),(9,'it');
/*!40000 ALTER TABLE `tbl_industry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_matches`
--

DROP TABLE IF EXISTS `tbl_matches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_matches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `public_id` varchar(30) NOT NULL,
  `fk_opportunity_id` varchar(30) NOT NULL,
  `fk_solver_id` varchar(30) NOT NULL,
  `matched_by` varchar(30) NOT NULL,
  `seeker_viewed` timestamp NULL DEFAULT NULL,
  `solver_viewed` timestamp NULL DEFAULT NULL,
  `seeker_match` varchar(30) DEFAULT '0',
  `solver_match` varchar(30) DEFAULT '0',
  `matchmaker_approved` varchar(30) DEFAULT '0',
  `seeker_solver_connect` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=259 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_matches`
--

LOCK TABLES `tbl_matches` WRITE;
/*!40000 ALTER TABLE `tbl_matches` DISABLE KEYS */;
INSERT INTO `tbl_matches` VALUES (243,'626939717228691692','66','54','595811327738383799',NULL,NULL,'595811327738383799','0','594283096145137493',NULL,'2024-05-03 08:42:44','2024-05-03 08:42:44'),(245,'626939717279026495','64','54','595811327738383799',NULL,NULL,'595811327738383799','0','594283096145137493',NULL,'2024-05-03 08:42:44','2024-05-03 08:42:44'),(246,'626939717295804140','63','54','595811327738383799',NULL,NULL,'595811327738383799','0','0',NULL,'2024-05-03 08:42:44','2024-05-03 08:42:44'),(247,'626939717312578111','62','54','595811327738383799',NULL,NULL,'595811327738383799','0','0',NULL,'2024-05-03 08:42:44','2024-05-03 08:42:44'),(248,'626939717325160803','61','54','595811327738383799',NULL,NULL,'595811327738383799','0','0',NULL,'2024-05-03 08:42:44','2024-05-03 08:42:44'),(249,'626939717337744484','60','54','595811327738383799',NULL,NULL,'595811327738383799','0','0',NULL,'2024-05-03 08:42:44','2024-05-03 08:42:44'),(250,'626939717350327345','59','54','595811327738383799',NULL,NULL,'595811327738383799','0','0',NULL,'2024-05-03 08:42:44','2024-05-03 08:42:44'),(251,'626980770178665629','66','45','595811327738383799',NULL,NULL,'595811327738383799','0','594283096145137493',NULL,'2024-05-03 11:25:52','2024-05-03 11:25:52'),(252,'626980770396769773','65','45','595811327738383799',NULL,NULL,'595811327738383799','0','594283096145137493',NULL,'2024-05-03 11:25:52','2024-05-03 11:25:52'),(253,'626980770409353197','64','45','595811327738383799',NULL,NULL,'595811327738383799','0','0',NULL,'2024-05-03 11:25:52','2024-05-03 11:25:52'),(254,'626980770426126384','63','45','595811327738383799',NULL,NULL,'595811327738383799','0','594283096145137493',NULL,'2024-05-03 11:25:52','2024-05-03 11:25:52'),(255,'626980770438712681','62','45','595811327738383799',NULL,NULL,'595811327738383799','0','0',NULL,'2024-05-03 11:25:52','2024-05-03 11:25:52'),(256,'626980770451294435','61','45','595811327738383799',NULL,NULL,'595811327738383799','0','0',NULL,'2024-05-03 11:25:52','2024-05-03 11:25:52'),(257,'626980770459683579','60','45','595811327738383799',NULL,NULL,'595811327738383799','0','0',NULL,'2024-05-03 11:25:52','2024-05-03 11:25:52'),(258,'626980770476461811','59','45','595811327738383799',NULL,NULL,'595811327738383799','0','0',NULL,'2024-05-03 11:25:52','2024-05-03 11:25:52');
/*!40000 ALTER TABLE `tbl_matches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_message_queue`
--

DROP TABLE IF EXISTS `tbl_message_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_message_queue` (
  `message_id` int NOT NULL AUTO_INCREMENT,
  `message` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) DEFAULT '0',
  `opportunity_id` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `matched_solver_id` varchar(255) DEFAULT NULL,
  `matched_opportunity_id` varchar(255) DEFAULT NULL,
  `matched_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_message_queue`
--

LOCK TABLES `tbl_message_queue` WRITE;
/*!40000 ALTER TABLE `tbl_message_queue` DISABLE KEYS */;
INSERT INTO `tbl_message_queue` VALUES (28,'New match made by a seeker','2024-05-03 06:11:47',1,'na','match_madeby_seeker',NULL,NULL,'626901726015783956'),(29,'New match made by a seeker','2024-05-03 08:42:44',1,'na','match_madeby_seeker',NULL,NULL,'626939717228691692'),(30,'New match made by a seeker','2024-05-03 08:42:44',1,'na','match_madeby_seeker',NULL,NULL,'626939717262248305'),(31,'New match made by a seeker','2024-05-03 08:42:44',1,'na','match_madeby_seeker',NULL,NULL,'626939717279026495'),(32,'New match made by a seeker','2024-05-03 08:42:44',1,'na','match_madeby_seeker',NULL,NULL,'626939717295804140'),(33,'New match made by a seeker','2024-05-03 08:42:44',1,'na','match_madeby_seeker',NULL,NULL,'626939717312578111'),(34,'New match made by a seeker','2024-05-03 08:42:44',1,'na','match_madeby_seeker',NULL,NULL,'626939717325160803'),(35,'New match made by a seeker','2024-05-03 08:42:44',1,'na','match_madeby_seeker',NULL,NULL,'626939717337744484'),(36,'New match made by a seeker','2024-05-03 08:42:44',1,'na','match_madeby_seeker',NULL,NULL,'626939717350327345'),(37,'New match made by a seeker','2024-05-03 11:25:52',1,'na','match_madeby_seeker',NULL,NULL,'626980770178665629'),(38,'New match made by a seeker','2024-05-03 11:25:52',1,'na','match_madeby_seeker',NULL,NULL,'626980770396769773'),(39,'New match made by a seeker','2024-05-03 11:25:52',1,'na','match_madeby_seeker',NULL,NULL,'626980770409353197'),(40,'New match made by a seeker','2024-05-03 11:25:52',1,'na','match_madeby_seeker',NULL,NULL,'626980770426126384'),(41,'New match made by a seeker','2024-05-03 11:25:52',1,'na','match_madeby_seeker',NULL,NULL,'626980770438712681'),(42,'New match made by a seeker','2024-05-03 11:25:52',1,'na','match_madeby_seeker',NULL,NULL,'626980770451294435'),(43,'New match made by a seeker','2024-05-03 11:25:52',1,'na','match_madeby_seeker',NULL,NULL,'626980770459683579'),(44,'New match made by a seeker','2024-05-03 11:25:52',1,'na','match_madeby_seeker',NULL,NULL,'626980770476461811'),(45,'New Opportunity created','2024-05-03 11:36:18',1,'626983393652899988','opportunity_created',NULL,NULL,'na'),(46,'New Opportunity created','2024-05-23 13:18:33',1,'634256883946881954','opportunity_created',NULL,NULL,'na'),(47,'New Opportunity created','2024-05-31 16:11:14',0,'637199442188959881','opportunity_created',NULL,NULL,'na'),(48,'New Opportunity created','2024-06-03 12:32:50',0,'638231642313330275','opportunity_created',NULL,NULL,'na');
/*!40000 ALTER TABLE `tbl_message_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_opportunities`
--

DROP TABLE IF EXISTS `tbl_opportunities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_opportunities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_user_id` varchar(30) NOT NULL,
  `fk_org_id` varchar(30) NOT NULL,
  `public_id` varchar(30) NOT NULL,
  `headline` varchar(255) DEFAULT NULL,
  `requirements` longtext,
  `start_date` varchar(255) DEFAULT NULL,
  `complete_date` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `rate` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `address_line_1` varchar(255) DEFAULT NULL,
  `address_line_2` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `rate_type` varchar(50) DEFAULT NULL,
  `active_status` varchar(20) NOT NULL DEFAULT 'review',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_opportunities`
--

LOCK TABLES `tbl_opportunities` WRITE;
/*!40000 ALTER TABLE `tbl_opportunities` DISABLE KEYS */;
INSERT INTO `tbl_opportunities` VALUES (54,'11','50','621460363707155017','Web Developer in progess','<p>Web Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progessWeb Developer in progess</p>','TBD','TBD','remote','TBD','2024-04-18 05:49:44','2024-04-18 05:49:44','na','na','na','na','na','null','review'),(55,'11','50','621822939938950228','Need A python developer','<p><strong>Project Scope: Clearly define the project\'s objectives, functionalities, and deliverables. Identify the target audience and stakeholders.</strong></p><p><strong>Technology Stack</strong>: Choose the appropriate technologies and frameworks based on the project requirements. For Python development, consider frameworks like Django, Flask, or FastAPI for web development, and libraries like NumPy, pandas, or TensorFlow for data science and machine learning.</p><p><strong>Development Environment</strong>: Set up a development environment with the necessary tools and dependencies. Use virtual environments to manage project dependencies and isolate them from system-wide installations.</p><p><strong>Version Control</strong>: Use a version control system like Git to track changes to the codebase, collaborate with team members, and manage project versions effectively.</p><p><strong>Coding Standards</strong>: Define coding standards and conventions to ensure consistency and readability across the codebase. Consider using tools like PEP 8 for Python code style.</p><p><strong>Testing</strong>: Write unit tests, integration tests, and end-to-end tests to ensure the reliability and robustness of the code. Consider using testing frameworks like pytest or unittest.</p><p><strong>Documentation</strong>: Document the codebase, including inline comments, function/method docstrings, and high-level project documentation. Use tools like Sphinx for generating documentation from docstrings.</p><p><strong>Security</strong>: Implement security best practices to protect against common vulnerabilities like SQL injection, cross-site scripting (XSS), and cross-site request forgery (CSRF). Use secure authentication and authorization mechanisms.</p><p><strong>Performance Optimization</strong>: Profile the codebase to identify performance bottlenecks and optimize critical sections for better performance. Consider techniques like caching, lazy loading, and asynchronous programming where appropriate.</p><p><strong>Deployment</strong>: Plan the deployment strategy and infrastructure requirements. Choose a hosting provider or cloud platform based on scalability, reliability, and cost considerations. Automate deployment processes using tools like Docker, Kubernetes, or continuous integration/continuous deployment (CI/CD) pipelines.</p><p><strong>Monitoring and Logging</strong>: Implement monitoring and logging solutions to track application performance, detect errors, and troubleshoot issues in real-time. Use tools like Prometheus, Grafana, or ELK stack for monitoring and logging.</p><p><strong>Maintenance and Support</strong>: Define a maintenance plan for regular updates, bug fixes, and feature enhancements. Provide ongoing support to users and address any issues or feedback promptly.</p>','TBD','TBD','remote',NULL,'2024-04-19 05:50:29','2024-04-19 05:50:29','na','na','na','na','na','per_day','review'),(56,'11','50','621823628182294018','Need a good java developer','<p><strong>Project Scope: Clearly define the project\'s objectives, functionalities, and deliverables. Identify the target audience and stakeholders.</strong></p><p><strong>Technology Stack</strong>: Choose the appropriate technologies and frameworks based on the project requirements. For Java development, consider frameworks like Spring Boot, Jakarta EE (formerly Java EE), or Micronaut for web development, and libraries like Hibernate for database interaction.</p><p><strong>Development Environment</strong>: Set up a development environment with the necessary tools and dependencies. Use build tools like Maven or Gradle to manage project dependencies and automate the build process.</p><p><strong>Version Control</strong>: Use a version control system like Git to track changes to the codebase, collaborate with team members, and manage project versions effectively.</p><p><strong>Coding Standards</strong>: Define coding standards and conventions to ensure consistency and readability across the codebase. Consider following Java Code Conventions and Code Style Guidelines.</p><p><strong>Testing</strong>: Write unit tests, integration tests, and end-to-end tests to ensure the reliability and robustness of the code. Consider using testing frameworks like JUnit or TestNG.</p><p><strong>Documentation</strong>: Document the codebase, including inline comments, method/class documentation, and high-level project documentation. Use tools like Javadoc for generating API documentation.</p><p><strong>Security</strong>: Implement security best practices to protect against common vulnerabilities like SQL injection, cross-site scripting (XSS), and cross-site request forgery (CSRF). Use secure authentication and authorization mechanisms.</p><p><strong>Performance Optimization</strong>: Profile the codebase to identify performance bottlenecks and optimize critical sections for better performance. Consider techniques like caching, connection pooling, and asynchronous programming where appropriate.</p><p><strong>Deployment</strong>: Plan the deployment strategy and infrastructure requirements. Choose a hosting provider or cloud platform based on scalability, reliability, and cost considerations. Automate deployment processes using tools like Docker, Kubernetes, or continuous integration/continuous deployment (CI/CD) pipelines.</p><p><strong>Monitoring and Logging</strong>: Implement monitoring and logging solutions to track application performance, detect errors, and troubleshoot issues in real-time. Use tools like Prometheus, Grafana, or ELK stack for monitoring and logging.</p><p><strong>Maintenance and Support</strong>: Define a maintenance plan for regular updates, bug fixes, and feature enhancements. Provide ongoing support to users and address any issues or feedback promptly.</p>','2024-04-30','2025-04-17','remote',NULL,'2024-04-19 05:53:13','2024-04-19 05:53:13','na','na','na','na','na','per_day','review'),(57,'11','50','621926032827483714','Checking rate issue appeared just now','<p>Checking rate issue appeared just nowChecking rate issue appeared just nowChecking rate issue appeared just nowChecking rate issue appeared just nowChecking rate issue appeared just nowChecking rate issue appeared just nowChecking rate issue appeared just nowChecking rate issue appeared just nowChecking rate issue appeared just nowChecking rate issue appeared just nowChecking rate issue appeared just nowChecking rate issue appeared just now</p>','TBD','TBD','remote',NULL,'2024-04-19 12:40:09','2024-04-19 12:40:09','na','na','na','na','na','per_day','review'),(59,'13','1','625584933762502614','tst socket tst socket  tst socket  tst socket ','<p>tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket tst socket&nbsp;</p>','TBD','TBD','remote','TBD','2024-04-29 14:59:19','2024-04-29 14:59:19','na','na','na','na','na','null','review'),(60,'13','1','625586209854328674','test socket test socket test socket','<p>vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv</p>','TBD','TBD','remote','TBD','2024-04-29 15:04:23','2024-04-29 15:04:23','na','na','na','na','na','null','review'),(61,'13','1','625586666211380413','tst22 socket tst socket  tst socket  tst socket ','<p>kbajdsb,bdsnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm</p>','TBD','TBD','remote','TBD','2024-04-29 15:06:12','2024-04-29 15:06:12','na','na','na','na','na','null','review'),(62,'13','1','625587006767891800','OPPPPPPPPPPPPPPP tst socket OPPPPPPPPPPPPPPP tst socket','<p>OPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socketOPPPPPPPPPPPPPPP tst socket</p>','TBD','TBD','remote','TBD','2024-04-29 15:07:33','2024-04-29 15:07:33','na','na','na','na','na','null','review'),(63,'13','1','625589546234413847','Web Developer test socket Web Developer test socket','<p>Web Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socketWeb Developer test socket</p>','TBD','TBD','remote','TBD','2024-04-29 15:17:38','2024-04-29 15:17:38','na','na','na','na','na','null','review'),(64,'13','1','625592316840640850','Test for skills socket socket','<p>Test for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socketTest for skills socket socket</p>','TBD','TBD','remote','TBD','2024-04-29 15:28:39','2024-04-29 15:28:39','na','na','na','na','na','null','review'),(65,'13','1','626539996358117067','socket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testing','<p>socket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testingsocket 1234 testing</p>','TBD','TBD','remote','TBD','2024-05-02 06:14:23','2024-05-02 06:14:23','na','na','na','na','na','null','review'),(66,'13','1','626551054598671003','Anything is ok Anything is ok socket socket Anything is ok Anything is ok socket socket','<p>Anything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socketAnything is ok Anything is ok socket socket</p>','TBD','TBD','remote','TBD','2024-05-02 06:58:20','2024-05-02 06:58:20','na','na','na','na','na','null','delay'),(67,'13','1','626983393652899988','Web Developer tst socket for now','<p>Web Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for nowWeb Developer tst socket for now</p>','2024-05-03','2024-05-31','On premise','200','2024-05-03 11:36:17','2024-05-03 11:36:17','na','na','na','na','na','per_day','active'),(68,'25','48','634256883946881954','any head line for testing the data','<p>any head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the data</p>','TBD','TBD','remote','TBD','2024-05-23 13:18:33','2024-05-23 13:18:33','na','na','na','na','na','null','active'),(70,'26','51','638231642313330275','jkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkklllllllllllllllllllllllllllllllnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn','<p>jjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk</p>','TBD','TBD','remote','TBD','2024-06-03 12:32:49','2024-06-03 12:32:49','na','na','na','na','na','null','review');
/*!40000 ALTER TABLE `tbl_opportunities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_opportunity_credentials`
--

DROP TABLE IF EXISTS `tbl_opportunity_credentials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_opportunity_credentials` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_opportunity_id` int NOT NULL,
  `fk_credential_id` int NOT NULL,
  `completed` int DEFAULT NULL,
  `level` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_opportunity_credentials`
--

LOCK TABLES `tbl_opportunity_credentials` WRITE;
/*!40000 ALTER TABLE `tbl_opportunity_credentials` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_opportunity_credentials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_opportunity_skills`
--

DROP TABLE IF EXISTS `tbl_opportunity_skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_opportunity_skills` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_opportunity_id` int NOT NULL,
  `fk_skill_id` int DEFAULT NULL,
  `duration` int DEFAULT NULL,
  `level` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_opportunity_skills`
--

LOCK TABLES `tbl_opportunity_skills` WRITE;
/*!40000 ALTER TABLE `tbl_opportunity_skills` DISABLE KEYS */;
INSERT INTO `tbl_opportunity_skills` VALUES (87,54,35,NULL,NULL),(88,55,32,NULL,NULL),(89,55,45,NULL,NULL),(90,56,36,NULL,NULL),(91,56,46,NULL,NULL),(92,57,35,NULL,NULL),(94,59,35,NULL,NULL),(95,60,35,NULL,NULL),(96,61,35,NULL,NULL),(97,62,35,NULL,NULL),(98,63,35,NULL,NULL),(99,64,35,NULL,NULL),(100,65,35,NULL,NULL),(101,66,35,NULL,NULL),(102,67,35,NULL,NULL),(103,67,31,NULL,NULL),(104,67,38,NULL,NULL),(105,68,47,NULL,NULL),(106,69,47,NULL,NULL),(107,70,47,NULL,NULL);
/*!40000 ALTER TABLE `tbl_opportunity_skills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_opportunity_smprofiles`
--

DROP TABLE IF EXISTS `tbl_opportunity_smprofiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_opportunity_smprofiles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_opportunity_id` int NOT NULL,
  `fk_profile_id` int NOT NULL,
  `last_activity` int DEFAULT NULL,
  `level` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_opportunity_smprofiles`
--

LOCK TABLES `tbl_opportunity_smprofiles` WRITE;
/*!40000 ALTER TABLE `tbl_opportunity_smprofiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_opportunity_smprofiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_organization_users`
--

DROP TABLE IF EXISTS `tbl_organization_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_organization_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_org_id` int NOT NULL,
  `fk_user_id` int NOT NULL,
  `org_level` tinyint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_organization_users`
--

LOCK TABLES `tbl_organization_users` WRITE;
/*!40000 ALTER TABLE `tbl_organization_users` DISABLE KEYS */;
INSERT INTO `tbl_organization_users` VALUES (0,1,1,1),(61,48,25,1),(62,1,13,100),(63,49,32,1),(64,50,11,1),(65,51,26,1);
/*!40000 ALTER TABLE `tbl_organization_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_organizations`
--

DROP TABLE IF EXISTS `tbl_organizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_organizations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `orgname` varchar(255) DEFAULT NULL,
  `creator` varchar(30) NOT NULL,
  `public_id` varchar(30) NOT NULL,
  `orgtype` varchar(255) DEFAULT NULL,
  `description` longtext,
  `location` varchar(255) DEFAULT NULL,
  `precise_location` point DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `buisness_ein` varchar(20) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `social_media` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_organizations`
--

LOCK TABLES `tbl_organizations` WRITE;
/*!40000 ALTER TABLE `tbl_organizations` DISABLE KEYS */;
INSERT INTO `tbl_organizations` VALUES (1,'Bennit Inc','1','1','0','Default Bennit organization','Novelty, Ohio',NULL,'https://www.bennit.ai',NULL,NULL,NULL,NULL,NULL,NULL,'2023-08-16 16:23:01','2023-08-16 16:23:01',NULL),(48,'Axiom','25','615413940381288383','Commercial','What is Lorem Ipsum?\r\nLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.',NULL,NULL,'https://hjdhkdsk/kk','793200001','Columbus','location','Columbus','Ohio','43215','2024-04-01 13:23:25','2024-04-01 13:23:25','https://social'),(49,'test solver','32','616919289022121000','Commercial','Testing the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the dataTesting the data',NULL,NULL,'https://hjdhkdsk/kk','793200000','Columbus','','Columbus','Ohio','43215','2024-04-05 17:05:08','2024-04-05 17:05:08','https://social'),(50,'jrndlknglkdf','11','621460011888935643','Commercial','jrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdfjrndlknglkdf',NULL,NULL,'https://hjdhkdsk/kk','793200000','Columbus','location','sssssssssss','Kansas','43215','2024-04-18 05:48:21','2024-04-18 05:48:21','https://social'),(51,'anything test','26','638230323531878262','Commercial','anything test  Cannot modify header information - headers already sent byCannot modify header information - headers already sent byCannot modify header information - headers already sent byCannot modify header information - headers already sent byCannot modify header information - headers already sent byCannot modify header information - headers already sent byCannot modify header information - headers already sent byCannot modify header information - headers already sent byCannot modify header information - headers already sent by',NULL,NULL,'http://localhost:7000/organization.php?action=create_organization&dummy_step=seeker','jkkkkkk','vvvvvvv','nnnnnn','hhhhhh','Alabama','12345','2024-06-03 12:27:34','2024-06-03 12:27:34','http://localhost:7000/organization.php?action=create_organization&dummy_step=seeker');
/*!40000 ALTER TABLE `tbl_organizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_roles`
--

DROP TABLE IF EXISTS `tbl_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_roles` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'role_id',
  `role` varchar(255) DEFAULT NULL COMMENT 'role_text',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_roles`
--

LOCK TABLES `tbl_roles` WRITE;
/*!40000 ALTER TABLE `tbl_roles` DISABLE KEYS */;
INSERT INTO `tbl_roles` VALUES (1,'Admin'),(2,'Editor'),(3,'User');
/*!40000 ALTER TABLE `tbl_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_skills`
--

DROP TABLE IF EXISTS `tbl_skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_skills` (
  `id` int NOT NULL AUTO_INCREMENT,
  `skill_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_skills`
--

LOCK TABLES `tbl_skills` WRITE;
/*!40000 ALTER TABLE `tbl_skills` DISABLE KEYS */;
INSERT INTO `tbl_skills` VALUES (31,'php'),(32,'python'),(33,'node js'),(34,'react js'),(35,'react developer'),(36,'java'),(37,'phps'),(38,'js'),(39,'jjk'),(40,'new skill 90022'),(41,'new test 9002245490'),(42,'digital marketing'),(43,'web developer'),(44,'pro c'),(45,'fast api'),(46,'spring boot'),(47,'react');
/*!40000 ALTER TABLE `tbl_skills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_smprofiles`
--

DROP TABLE IF EXISTS `tbl_smprofiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_smprofiles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `profile_name` varchar(255) NOT NULL,
  `profile_namespace_uri` varchar(255) NOT NULL,
  `profile_marketplace_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_smprofiles`
--

LOCK TABLES `tbl_smprofiles` WRITE;
/*!40000 ALTER TABLE `tbl_smprofiles` DISABLE KEYS */;
INSERT INTO `tbl_smprofiles` VALUES (0,'NCD Wireless Sensors','https://axiomsystems.io/profiles/ncdwireless',2147483647);
/*!40000 ALTER TABLE `tbl_smprofiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_solver_credentials`
--

DROP TABLE IF EXISTS `tbl_solver_credentials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_solver_credentials` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_user_id` int NOT NULL,
  `fk_credential_id` int NOT NULL,
  `completed` int DEFAULT NULL,
  `level` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_solver_credentials`
--

LOCK TABLES `tbl_solver_credentials` WRITE;
/*!40000 ALTER TABLE `tbl_solver_credentials` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_solver_credentials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_solver_industry`
--

DROP TABLE IF EXISTS `tbl_solver_industry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_solver_industry` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_solver_id` int NOT NULL,
  `fk_industry_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_solver_industry_solver` (`fk_solver_id`),
  KEY `fk_solver_industry_industry` (`fk_industry_id`),
  CONSTRAINT `fk_solver_industry_industry` FOREIGN KEY (`fk_industry_id`) REFERENCES `tbl_industry` (`id`),
  CONSTRAINT `fk_solver_industry_solver` FOREIGN KEY (`fk_solver_id`) REFERENCES `tbl_solvers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_solver_industry`
--

LOCK TABLES `tbl_solver_industry` WRITE;
/*!40000 ALTER TABLE `tbl_solver_industry` DISABLE KEYS */;
INSERT INTO `tbl_solver_industry` VALUES (10,52,8),(13,55,9);
/*!40000 ALTER TABLE `tbl_solver_industry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_solver_locations`
--

DROP TABLE IF EXISTS `tbl_solver_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_solver_locations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_solver_id` int NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `zip` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_solver_id` (`fk_solver_id`),
  CONSTRAINT `tbl_solver_locations_ibfk_1` FOREIGN KEY (`fk_solver_id`) REFERENCES `tbl_solvers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_solver_locations`
--

LOCK TABLES `tbl_solver_locations` WRITE;
/*!40000 ALTER TABLE `tbl_solver_locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_solver_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_solver_skills`
--

DROP TABLE IF EXISTS `tbl_solver_skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_solver_skills` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_solver_id` int NOT NULL,
  `fk_skill_id` int NOT NULL,
  `duration` int DEFAULT NULL,
  `level` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_solver_skills`
--

LOCK TABLES `tbl_solver_skills` WRITE;
/*!40000 ALTER TABLE `tbl_solver_skills` DISABLE KEYS */;
INSERT INTO `tbl_solver_skills` VALUES (39,45,42,NULL,NULL),(40,46,35,NULL,NULL),(41,47,35,NULL,NULL),(42,48,33,NULL,NULL),(43,49,33,NULL,NULL),(44,50,33,NULL,NULL),(45,51,33,NULL,NULL),(46,52,33,NULL,NULL),(51,55,47,NULL,NULL);
/*!40000 ALTER TABLE `tbl_solver_skills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_solver_smprofiles`
--

DROP TABLE IF EXISTS `tbl_solver_smprofiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_solver_smprofiles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_solver_id` int NOT NULL,
  `fk_profile_id` int NOT NULL,
  `last_activity` int DEFAULT NULL,
  `level` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_solver_smprofiles`
--

LOCK TABLES `tbl_solver_smprofiles` WRITE;
/*!40000 ALTER TABLE `tbl_solver_smprofiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_solver_smprofiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_solver_speciality`
--

DROP TABLE IF EXISTS `tbl_solver_speciality`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_solver_speciality` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_solver_id` int NOT NULL,
  `fk_speciality_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_solver_speciality_solver` (`fk_solver_id`),
  KEY `fk_solver_speciality_speciality` (`fk_speciality_id`),
  CONSTRAINT `fk_solver_speciality_solver` FOREIGN KEY (`fk_solver_id`) REFERENCES `tbl_solvers` (`id`),
  CONSTRAINT `fk_solver_speciality_speciality` FOREIGN KEY (`fk_speciality_id`) REFERENCES `tbl_speciality` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_solver_speciality`
--

LOCK TABLES `tbl_solver_speciality` WRITE;
/*!40000 ALTER TABLE `tbl_solver_speciality` DISABLE KEYS */;
INSERT INTO `tbl_solver_speciality` VALUES (22,52,3),(23,52,4),(24,52,5),(29,55,6);
/*!40000 ALTER TABLE `tbl_solver_speciality` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_solver_technology`
--

DROP TABLE IF EXISTS `tbl_solver_technology`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_solver_technology` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_solver_id` int NOT NULL,
  `fk_technology_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_solver_technology_solver` (`fk_solver_id`),
  KEY `fk_solver_technology_technology` (`fk_technology_id`),
  CONSTRAINT `fk_solver_technology_solver` FOREIGN KEY (`fk_solver_id`) REFERENCES `tbl_solvers` (`id`),
  CONSTRAINT `fk_solver_technology_technology` FOREIGN KEY (`fk_technology_id`) REFERENCES `tbl_technology` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_solver_technology`
--

LOCK TABLES `tbl_solver_technology` WRITE;
/*!40000 ALTER TABLE `tbl_solver_technology` DISABLE KEYS */;
INSERT INTO `tbl_solver_technology` VALUES (23,55,7);
/*!40000 ALTER TABLE `tbl_solver_technology` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_solvers`
--

DROP TABLE IF EXISTS `tbl_solvers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_solvers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_user_id` varchar(30) NOT NULL,
  `fk_org_id` varchar(30) NOT NULL,
  `public_id` varchar(30) NOT NULL,
  `headline` varchar(255) DEFAULT NULL,
  `abstract` varchar(255) DEFAULT NULL,
  `experience` longtext,
  `portraitImage` longblob,
  `bannerImage` longblob,
  `availability` varchar(255) DEFAULT NULL,
  `rate` varchar(255) DEFAULT NULL,
  `locations` varchar(255) DEFAULT NULL,
  `is_coach` bit(1) DEFAULT b'0',
  `allow_external` bit(1) DEFAULT b'0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `certificates` text,
  `location_preference` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `rate_type` varchar(50) DEFAULT NULL,
  `industry` varchar(255) DEFAULT NULL,
  `technology` varchar(255) DEFAULT NULL,
  `speciality` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_solvers`
--

LOCK TABLES `tbl_solvers` WRITE;
/*!40000 ALTER TABLE `tbl_solvers` DISABLE KEYS */;
INSERT INTO `tbl_solvers` VALUES (52,'13','1','615425839760146995',NULL,NULL,'<p>As a seasoned web developer, I bring years of hands-on experience in creating dynamic and responsive websites that meet the unique needs of clients across various industries. Throughout my career, I have worked on numerous projects, ranging from simple landing pages to complex web applications, gaining valuable insights and honing my skills along the way.</p><p>My journey as a web developer began with a passion for technology and a desire to create intuitive and user-friendly digital experiences. Over the years, I\'ve had the opportunity to collaborate with clients and colleagues to bring their visions to life, leveraging my expertise in front-end and back-end development to deliver solutions that exceed expectations.</p>',NULL,NULL,'24/7','100',NULL,_binary '\0',_binary '\0','2024-04-01 14:10:42','2024-04-01 14:10:42','gh','On premise,hybrid,remote','Columbus','Ohio','43215','per_hour',NULL,NULL,NULL),(55,'25','48','634257276168834333',NULL,NULL,'<p>any head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the dataany head line for testing the data</p>',NULL,NULL,'24/7','120',NULL,_binary '\0',_binary '\0','2024-05-23 13:20:06','2024-05-23 13:20:06','','On premise,hybrid,remote','ohhhhh','Texas','7777','per_day',NULL,NULL,NULL);
/*!40000 ALTER TABLE `tbl_solvers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_speciality`
--

DROP TABLE IF EXISTS `tbl_speciality`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_speciality` (
  `id` int NOT NULL AUTO_INCREMENT,
  `speciality_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_speciality`
--

LOCK TABLES `tbl_speciality` WRITE;
/*!40000 ALTER TABLE `tbl_speciality` DISABLE KEYS */;
INSERT INTO `tbl_speciality` VALUES (1,'test speciality'),(2,'data-driven strategies'),(3,'socket'),(4,'c++'),(5,'javascript'),(6,'it');
/*!40000 ALTER TABLE `tbl_speciality` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_subscriptions`
--

DROP TABLE IF EXISTS `tbl_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_subscriptions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `public_id` varchar(30) NOT NULL,
  `fk_user_id` varchar(30) NOT NULL,
  `fk_org_id` varchar(30) DEFAULT NULL,
  `subscription_type` varchar(255) DEFAULT NULL,
  `purchase_token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `canceled_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_subscriptions`
--

LOCK TABLES `tbl_subscriptions` WRITE;
/*!40000 ALTER TABLE `tbl_subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_technology`
--

DROP TABLE IF EXISTS `tbl_technology`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_technology` (
  `id` int NOT NULL AUTO_INCREMENT,
  `technology_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_technology`
--

LOCK TABLES `tbl_technology` WRITE;
/*!40000 ALTER TABLE `tbl_technology` DISABLE KEYS */;
INSERT INTO `tbl_technology` VALUES (1,'recat'),(2,'js'),(3,'react'),(4,'content management system (cms)'),(5,'analytics and tracking tools'),(6,'node js'),(7,'it');
/*!40000 ALTER TABLE `tbl_technology` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_temp_stripe_data`
--

DROP TABLE IF EXISTS `tbl_temp_stripe_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_temp_stripe_data` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `payment_id` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_temp_stripe_data`
--

LOCK TABLES `tbl_temp_stripe_data` WRITE;
/*!40000 ALTER TABLE `tbl_temp_stripe_data` DISABLE KEYS */;
INSERT INTO `tbl_temp_stripe_data` VALUES (1,'rr7384029333@gmail.com','cs_test_a1Y0rz7peMECUTagA4Ycjz4aiWsNwGSLuv7ko5bEXIsgA20M4OqymQGnBE'),(2,'rr7384029333@gmail.com','cs_test_a17rTBiQoowRQFIOvBzhFoG1wt3nRFRNNgLupjUtV9RUS9TEC2sqGuvhEw'),(3,'rr7384029333@gmail.com','cs_test_a17rTBiQoowRQFIOvBzhFoG1wt3nRFRNNgLupjUtV9RUS9TEC2sqGuvhEw'),(4,'rr7384029333@gmail.com','cs_test_a17rTBiQoowRQFIOvBzhFoG1wt3nRFRNNgLupjUtV9RUS9TEC2sqGuvhEw');
/*!40000 ALTER TABLE `tbl_temp_stripe_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_users`
--

DROP TABLE IF EXISTS `tbl_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `public_id` varchar(30) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(25) DEFAULT NULL,
  `roleid` tinyint DEFAULT NULL,
  `is_disabled` bit(1) DEFAULT b'0',
  `is_firstrun` bit(1) DEFAULT b'1',
  `stripe_id` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reset_token` varchar(255) DEFAULT NULL,
  `payment_id` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_users`
--

LOCK TABLES `tbl_users` WRITE;
/*!40000 ALTER TABLE `tbl_users` DISABLE KEYS */;
INSERT INTO `tbl_users` VALUES (2,'','Jonathan Wise','jwise','jonathan@bennit.ai','8bc7fa56ac4a66d2cb0fc781d213c14f2d518e2be0e1fe65e83231514420f011','2167721051',1,_binary '\0',_binary '\0',NULL,'2023-08-16 16:23:01','2023-08-16 16:23:01',NULL,NULL),(3,'','Guest User','guest','guest@bennit.ai','8016040fc911a0900c62d0da720ff13114f845d6eb84a923bb86537ec5896081','5551234',3,_binary '',_binary '\0',NULL,'2023-08-06 19:32:27','2023-08-06 19:32:27',NULL,NULL),(10,'594283096145137493','Shahid Ali','shahidali','shahid451998@gmail.com','8a872225e42caecc7f73e826e16a7f5222749f59d67faaadd8078d5c9df91248','7908169084',1,_binary '\0',_binary '',NULL,'2024-02-03 05:56:59','2024-02-03 05:56:59','2a3749fcb649fe02d0a3f2a959678779',NULL),(11,'594284322131804173','Shahid Editor','shahideditor','editor@gmail.com','dad387e8454b9098b6c355ba51a8af419550d4f08ebb3984d907234e716b749b','1234567890',3,_binary '\0',_binary '',NULL,'2024-02-03 06:01:51','2024-02-03 06:01:51',NULL,NULL),(12,'595380632893262578','testingthis','testingthis','testtwo@gmail.com','dad387e8454b9098b6c355ba51a8af419550d4f08ebb3984d907234e716b749b','7908169084',3,_binary '\0',_binary '',NULL,'2024-02-06 06:38:12','2024-02-06 06:38:12',NULL,NULL),(13,'595811327738383799','Test Case','TestCase','testcase@gmail.com','dad387e8454b9098b6c355ba51a8af419550d4f08ebb3984d907234e716b749b','7908169084',3,_binary '\0',_binary '',NULL,'2024-02-07 11:09:38','2024-02-07 11:09:38',NULL,NULL),(25,'604480362281700582','seekeriseeker','seekeriseeker','seeker@gmail.com','dad387e8454b9098b6c355ba51a8af419550d4f08ebb3984d907234e716b749b','1234567890',3,_binary '\0',_binary '',NULL,'2024-03-02 09:17:17','2024-03-02 09:17:17',NULL,NULL),(26,'604524969161916542','solverissolver','solverissolver','solver@gmail.com','dad387e8454b9098b6c355ba51a8af419550d4f08ebb3984d907234e716b749b','1234567890',3,_binary '\0',_binary '',NULL,'2024-03-02 12:14:32','2024-03-02 12:14:32',NULL,NULL),(27,'606031467411148114','Kathy Cahalane','KathyCahalane','kathy@bennit.ai','cb27ee9e5e1248484c6935fa3546a235c230e5d30eef7f10a4ae2ef1b033d84e','1234567890',1,_binary '\0',_binary '',NULL,'2024-03-06 16:00:49','2024-03-06 16:00:49',NULL,NULL),(28,'608089440648170392','newtest','newtest','newday2@gmail.com','dad387e8454b9098b6c355ba51a8af419550d4f08ebb3984d907234e716b749b','7908169084',3,_binary '\0',_binary '',NULL,'2024-03-12 08:18:28','2024-03-12 08:18:28',NULL,NULL),(29,'608134530133397351','newday3','newdaythree','newday3@gmail.com','dad387e8454b9098b6c355ba51a8af419550d4f08ebb3984d907234e716b749b','1234567890',3,_binary '\0',_binary '',NULL,'2024-03-12 11:17:38','2024-03-12 11:17:38',NULL,NULL),(30,'613712629684243851','Shahid Ali','shahidaliyo','finalfix@gmail.com','dad387e8454b9098b6c355ba51a8af419550d4f08ebb3984d907234e716b749b','1234567890',3,_binary '\0',_binary '',NULL,'2024-03-27 20:43:01','2024-03-27 20:43:01',NULL,NULL),(31,'614661485167116572','finaltest2','finatestttt','finaltest2@gmail.com','dad387e8454b9098b6c355ba51a8af419550d4f08ebb3984d907234e716b749b','1234567890',3,_binary '\0',_binary '',NULL,'2024-03-30 11:33:25','2024-03-30 11:33:25',NULL,NULL),(32,'616919054640220511','testsolver','testsolver','testsolver@gmail.com','dad387e8454b9098b6c355ba51a8af419550d4f08ebb3984d907234e716b749b','1234567890',3,_binary '\0',_binary '',NULL,'2024-04-05 17:04:12','2024-04-05 17:04:12',NULL,NULL),(33,'629948791859970220','Shahid Ali','Shahidkfkldl','sk123345@gmail.com','dad387e8454b9098b6c355ba51a8af419550d4f08ebb3984d907234e716b749b','1234567890',3,_binary '\0',_binary '',NULL,'2024-05-11 15:59:43','2024-05-11 15:59:43',NULL,NULL),(34,'630210193686267516','SHAHID ALI','Newupdatetestingkk','sa8701847@gmail.com','dad387e8454b9098b6c355ba51a8af419550d4f08ebb3984d907234e716b749b','7908169084',3,_binary '\0',_binary '',NULL,'2024-05-12 09:18:26','2024-05-12 09:18:26',NULL,NULL);
/*!40000 ALTER TABLE `tbl_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-06-14 11:15:26
