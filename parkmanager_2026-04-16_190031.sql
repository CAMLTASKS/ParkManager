-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: parkmanager
-- ------------------------------------------------------
-- Server version	8.0.45-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `auditable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auditable_id` bigint unsigned DEFAULT NULL,
  `detail` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta` json DEFAULT NULL,
  `logged_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `audit_logs_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,3,'Cobro','general',NULL,NULL,'Liquidacion Ticket #TKT-8902 ($12,500)',NULL,'2026-04-16 15:45:00','2026-04-16 18:27:14','2026-04-16 18:27:14'),(2,4,'Anulacion','general',NULL,NULL,'Anulacion Ticket #TKT-8900 (Error de placa)',NULL,'2026-04-16 15:00:00','2026-04-16 18:27:14','2026-04-16 18:27:14'),(3,2,'Login','general',NULL,NULL,'Inicio de sesion (Caja Salida Principal)',NULL,'2026-04-16 11:00:00','2026-04-16 18:27:14','2026-04-16 18:27:14'),(4,5,'Bloqueo','general',NULL,NULL,'Usuario bloqueado por 3 intentos fallidos',NULL,'2026-04-15 23:30:00','2026-04-16 18:27:14','2026-04-16 18:27:14'),(5,1,'Configuracion','general',NULL,NULL,'Parametros iniciales del sistema creados',NULL,'2026-04-16 10:50:00','2026-04-16 18:27:14','2026-04-16 18:27:14'),(6,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',2,'Ticket TKT-1002 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 2800, \"minutes\": 132, \"discount\": 0, \"strategy\": \"fraction\", \"subtotal\": 2800, \"surcharge\": 0, \"full_blocks\": 0, \"grace_minutes\": 20, \"pricing_label\": \"4 fraccion(es)\", \"fraction_units\": 4, \"billable_minutes\": 112, \"remainder_minutes\": 112, \"threshold_minutes\": 120}','2026-04-16 19:09:14','2026-04-16 19:09:14','2026-04-16 19:09:14'),(7,1,'entrada','ticket','App\\Models\\ParkingTicket',7,'Ticket PK-260416-0007 creado para YDY84F','{\"tarifa\": \"Motocicleta\", \"ubicacion\": \"15\"}','2026-04-16 19:10:07','2026-04-16 19:10:07','2026-04-16 19:10:07'),(8,1,'tarifa_actualizada','configuracion','App\\Models\\TariffProfile',2,'Tarifa Motocicleta actualizada','{\"name\": \"Motocicleta\", \"active\": \"1\", \"daily_cap\": \"12000\", \"unit_rate\": \"5000\", \"charge_unit\": \"minute\", \"vehicle_type\": \"moto\", \"tax_percentage\": \"0.00\", \"agreement_hours\": null, \"charge_interval\": \"15\", \"lost_ticket_fee\": \"5000\", \"grace_exit_minutes\": \"15\", \"grace_entry_minutes\": \"15\"}','2026-04-16 19:39:23','2026-04-16 19:39:23','2026-04-16 19:39:23'),(9,1,'entrada','ticket','App\\Models\\ParkingTicket',8,'Ticket PK-260416-0008 creado para FASFA','{\"tarifa\": \"Motocicleta\", \"ubicacion\": \"50\"}','2026-04-16 19:40:15','2026-04-16 19:40:15','2026-04-16 19:40:15'),(10,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',8,'Ticket PK-260416-0008 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 12000, \"minutes\": 122, \"discount\": 0, \"strategy\": \"fraction\", \"subtotal\": 12000, \"surcharge\": 0, \"full_blocks\": 0, \"grace_minutes\": 30, \"pricing_label\": \"7 bloque(s) de 15 min\", \"fraction_units\": 7, \"billable_minutes\": 92, \"remainder_minutes\": 0, \"threshold_minutes\": 15}','2026-04-16 19:42:47','2026-04-16 19:42:47','2026-04-16 19:42:47'),(11,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',7,'Ticket PK-260416-0007 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 12000, \"minutes\": 286, \"discount\": 0, \"strategy\": \"fraction\", \"subtotal\": 12000, \"surcharge\": 0, \"full_blocks\": 0, \"grace_minutes\": 30, \"pricing_label\": \"18 bloque(s) de 15 min\", \"fraction_units\": 18, \"billable_minutes\": 256, \"remainder_minutes\": 0, \"threshold_minutes\": 15}','2026-04-16 23:56:58','2026-04-16 23:56:58','2026-04-16 23:56:58');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2026_04_16_100000_create_sites_table',1),(6,'2026_04_16_100100_add_operational_fields_to_users_table',1),(7,'2026_04_16_100200_create_tariff_profiles_table',1),(8,'2026_04_16_100300_create_parking_tickets_table',1),(9,'2026_04_16_100400_create_payments_table',1),(10,'2026_04_16_100500_create_audit_logs_table',1),(11,'2026_04_16_200000_add_tariff_operation_fields_to_tariff_profiles_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

--
-- Table structure for table `parking_tickets`
--

DROP TABLE IF EXISTS `parking_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parking_tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `site_id` bigint unsigned NOT NULL,
  `tariff_profile_id` bigint unsigned NOT NULL,
  `ticket_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plate` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `location_number` int unsigned DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `closed_by` bigint unsigned DEFAULT NULL,
  `entry_time` timestamp NOT NULL,
  `exit_time` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_lost_ticket` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parking_tickets_ticket_code_unique` (`ticket_code`),
  UNIQUE KEY `parking_tickets_barcode_unique` (`barcode`),
  KEY `parking_tickets_site_id_foreign` (`site_id`),
  KEY `parking_tickets_tariff_profile_id_foreign` (`tariff_profile_id`),
  KEY `parking_tickets_created_by_foreign` (`created_by`),
  KEY `parking_tickets_closed_by_foreign` (`closed_by`),
  KEY `parking_tickets_plate_index` (`plate`),
  CONSTRAINT `parking_tickets_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `parking_tickets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `parking_tickets_site_id_foreign` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  CONSTRAINT `parking_tickets_tariff_profile_id_foreign` FOREIGN KEY (`tariff_profile_id`) REFERENCES `tariff_profiles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parking_tickets`
--

/*!40000 ALTER TABLE `parking_tickets` DISABLE KEYS */;
INSERT INTO `parking_tickets` VALUES (1,1,1,'TKT-1001','TKT-1001','ABC-123','auto','active',11,NULL,NULL,2,NULL,'2026-04-16 15:42:14',NULL,NULL,0,'2026-04-16 18:27:14','2026-04-16 18:27:14'),(2,1,2,'TKT-1002','TKT-1002','XYZ-987','moto','paid',5,NULL,NULL,2,1,'2026-04-16 16:57:14','2026-04-16 19:09:14','',0,'2026-04-16 18:27:14','2026-04-16 19:09:14'),(3,1,1,'TKT-1003','TKT-1003','DEF-456','auto','active',9,NULL,NULL,2,NULL,'2026-04-16 14:27:14',NULL,NULL,0,'2026-04-16 18:27:14','2026-04-16 18:27:14'),(4,1,1,'TKT-8902','TKT-8902','ABC-123','auto','paid',6,'Cliente ABC-123','3000000000',2,3,'2026-04-16 13:15:00','2026-04-16 15:45:00',NULL,0,'2026-04-16 18:27:14','2026-04-16 18:27:14'),(5,1,2,'TKT-8901','TKT-8901','XYZ-987','moto','paid',1,'Cliente XYZ-987','3000000000',2,3,'2026-04-16 14:00:00','2026-04-16 15:30:00',NULL,0,'2026-04-16 18:27:14','2026-04-16 18:27:14'),(6,1,1,'TKT-8900','TKT-8900','DEF-456','auto','voided',4,'Cliente DEF-456','3000000000',2,4,'2026-04-16 12:30:00','2026-04-16 15:00:00',NULL,0,'2026-04-16 18:27:14','2026-04-16 18:27:14'),(7,1,2,'PK-260416-0007','PK-260416-0007','YDY84F','moto','paid',15,'Alejo','3503752816',1,1,'2026-04-16 19:10:07','2026-04-16 23:56:57','nuevo |',0,'2026-04-16 19:10:07','2026-04-16 23:56:57'),(8,1,2,'PK-260416-0008','PK-260416-0008','FASFA','moto','paid',50,'alej','265',1,1,'2026-04-16 17:40:15','2026-04-16 19:42:47','',0,'2026-04-16 19:40:15','2026-04-16 19:42:47');
/*!40000 ALTER TABLE `parking_tickets` ENABLE KEYS */;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parking_ticket_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `method` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtotal` int unsigned NOT NULL DEFAULT '0',
  `discount` int NOT NULL DEFAULT '0',
  `surcharge` int unsigned NOT NULL DEFAULT '0',
  `tax` int unsigned NOT NULL DEFAULT '0',
  `total` int unsigned NOT NULL DEFAULT '0',
  `received_amount` int unsigned NOT NULL DEFAULT '0',
  `change_amount` int unsigned NOT NULL DEFAULT '0',
  `paid_at` timestamp NULL DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'paid',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_parking_ticket_id_foreign` (`parking_ticket_id`),
  KEY `payments_user_id_foreign` (`user_id`),
  CONSTRAINT `payments_parking_ticket_id_foreign` FOREIGN KEY (`parking_ticket_id`) REFERENCES `parking_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,4,3,'Efectivo',12150,1215,0,1565,12500,15000,2500,'2026-04-16 15:45:00','paid',NULL,'2026-04-16 18:27:14','2026-04-16 18:27:14'),(2,5,3,'Tarjeta',3000,0,0,0,3000,3000,0,'2026-04-16 15:30:00','paid',NULL,'2026-04-16 18:27:14','2026-04-16 18:27:14'),(3,6,4,'Anulada',8500,0,0,0,8500,8500,0,'2026-04-16 15:00:00','voided',NULL,'2026-04-16 18:27:14','2026-04-16 18:27:14'),(4,2,1,'efectivo',2800,0,0,0,2800,2800,0,'2026-04-16 19:09:14','paid',NULL,'2026-04-16 19:09:14','2026-04-16 19:09:14'),(5,8,1,'efectivo',12000,0,0,0,12000,12000,0,'2026-04-16 19:42:47','paid',NULL,'2026-04-16 19:42:47','2026-04-16 19:42:47'),(6,7,1,'efectivo',12000,0,0,0,12000,12000,0,'2026-04-16 23:56:58','paid',NULL,'2026-04-16 23:56:58','2026-04-16 23:56:58');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;

--
-- Table structure for table `sites`
--

DROP TABLE IF EXISTS `sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` int unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sites_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sites`
--

/*!40000 ALTER TABLE `sites` DISABLE KEYS */;
INSERT INTO `sites` VALUES (1,'Sede Principal - Centro','CENTRO',50,1,'2026-04-16 18:27:13','2026-04-16 18:27:13'),(2,'Sede Norte','NORTE',35,1,'2026-04-16 18:27:13','2026-04-16 18:27:13');
/*!40000 ALTER TABLE `sites` ENABLE KEYS */;

--
-- Table structure for table `tariff_profiles`
--

DROP TABLE IF EXISTS `tariff_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tariff_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pricing_strategy` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fraction',
  `billing_mode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `charge_unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'minute',
  `charge_interval` int unsigned NOT NULL DEFAULT '15',
  `unit_rate` int unsigned NOT NULL DEFAULT '0',
  `is_full_rate` tinyint(1) NOT NULL DEFAULT '0',
  `is_agreement` tinyint(1) NOT NULL DEFAULT '0',
  `agreement_hours` int unsigned DEFAULT NULL,
  `rate_per_hour` int unsigned NOT NULL DEFAULT '0',
  `fraction_minutes` int unsigned NOT NULL DEFAULT '15',
  `fraction_rate` int unsigned NOT NULL DEFAULT '0',
  `full_rate_threshold_minutes` int unsigned DEFAULT NULL,
  `flat_rate` int unsigned NOT NULL DEFAULT '0',
  `daily_cap` int unsigned NOT NULL DEFAULT '0',
  `grace_entry_minutes` int unsigned NOT NULL DEFAULT '0',
  `grace_exit_minutes` int unsigned NOT NULL DEFAULT '0',
  `lost_ticket_fee` int unsigned NOT NULL DEFAULT '0',
  `tax_percentage` decimal(5,2) unsigned NOT NULL DEFAULT '0.00',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tariff_profiles_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tariff_profiles`
--

/*!40000 ALTER TABLE `tariff_profiles` DISABLE KEYS */;
INSERT INTO `tariff_profiles` VALUES (1,'Automovil estandar','automovil-estandar','auto','fraction','Fraccion 15 min','minute',15,0,0,0,NULL,3500,15,900,60,3500,25000,15,10,5000,0.00,1,'2026-04-16 18:27:13','2026-04-16 18:27:13'),(2,'Motocicleta','motocicleta','moto','fraction','Tarifa variable','minute',15,5000,0,0,NULL,20000,15,5000,NULL,0,12000,15,15,5000,0.00,1,'2026-04-16 18:27:13','2026-04-16 19:39:23'),(3,'Convenio Gimnasio','gimnasio-2-horas','moto','fixed','Tarifa fija por bloque','minute',15,0,0,0,NULL,0,120,0,120,3000,0,0,10,3000,0.00,1,'2026-04-16 18:27:13','2026-04-16 18:27:13');
/*!40000 ALTER TABLE `tariff_profiles` ENABLE KEYS */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `site_id` bigint unsigned DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'operario',
  `shift_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_site_id_foreign` (`site_id`),
  CONSTRAINT `users_site_id_foreign` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'admin','Administrador General','admin@parkmanager.com','admin','Administrativo',1,NULL,'$2y$12$FERIMFO2xDkWEhb6pR2EYOX9wnGtghtHA3gsecpWQfxKsCmMMHQPu',NULL,'2026-04-16 18:27:13','2026-04-16 18:27:13'),(2,1,'operador','Carlos Op.','operador@sede.com','operario','Manana (06:00 - 14:00)',1,NULL,'$2y$12$EIp7Z4BR.IFWPrW/VJDmpO00r21qg0lk8a.zQB.29H5stqDs8JtzW',NULL,'2026-04-16 18:27:13','2026-04-16 18:27:13'),(3,1,'ana.caja','Ana Martinez','ana@parkmanager.com','operario','Manana (06:00 - 14:00)',1,NULL,'$2y$12$mhM9KIR32B3vSVdsXinOFeqvxAhde//9X3nCHp5Icm98rJlwGlLEi',NULL,'2026-04-16 18:27:14','2026-04-16 18:27:14'),(4,1,'roberto.admin','Roberto Gomez','roberto@parkmanager.com','admin','Tarde (14:00 - 22:00)',1,NULL,'$2y$12$g5cVpSfqVSRGCq5bwemoHuePTNOpyf3U3J.Y1o/E.dKwP9caldfcC',NULL,'2026-04-16 18:27:14','2026-04-16 18:27:14'),(5,1,'luis.op','Luis Fernando','luis@parkmanager.com','operario','Sin turno',0,NULL,'$2y$12$I7vlisFI6RuNDR/wnKg39ejkQ4g9imm5dJhQehpyjidnLSsyTwDni',NULL,'2026-04-16 18:27:14','2026-04-16 18:27:14');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-16 19:00:43
