/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.14-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: meuapp
-- ------------------------------------------------------
-- Server version	10.11.14-MariaDB-0+deb12u2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tbl_meuapp_patients`
--

DROP TABLE IF EXISTS `tbl_meuapp_patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_meuapp_patients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` varchar(30) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(190) NOT NULL,
  `convenio` varchar(80) NOT NULL DEFAULT 'particular',
  `situacao_cadastro` enum('ativo','incompleto','suspenso','cancelado') NOT NULL DEFAULT 'ativo',
  `avatar` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `cpf` char(11) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `last_consulta_em` datetime DEFAULT NULL,
  `last_health_state` enum('leve','inspira_cuidados','grave','critico','terminal','recuperado','estavel') DEFAULT NULL,
  `last_cid` varchar(10) DEFAULT NULL,
  `ultima_clinica` varchar(150) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `staff_id` (`staff_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `uniq_staff_id` (`staff_id`),
  UNIQUE KEY `uniq_email` (`email`),
  KEY `idx_status` (`situacao_cadastro`),
  KEY `idx_role` (`convenio`),
  KEY `idx_birth_date` (`birth_date`),
  KEY `idx_cpf` (`cpf`),
  KEY `idx_tbl_meuapp_users_last_state` (`last_health_state`),
  KEY `idx_tbl_meuapp_patients_situacao` (`situacao_cadastro`),
  KEY `idx_tbl_meuapp_patients_convenio` (`convenio`),
  KEY `idx_tbl_meuapp_patients_last_state` (`last_health_state`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-06 18:49:28
