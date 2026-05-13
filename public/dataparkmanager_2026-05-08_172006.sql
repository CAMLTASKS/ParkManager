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
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,3,'Cobro','general',NULL,NULL,'Liquidacion Ticket #TKT-8902 ($12,500)',NULL,'2026-04-16 15:45:00','2026-04-16 18:27:14','2026-04-16 18:27:14'),(2,4,'Anulacion','general',NULL,NULL,'Anulacion Ticket #TKT-8900 (Error de placa)',NULL,'2026-04-16 15:00:00','2026-04-16 18:27:14','2026-04-16 18:27:14'),(3,2,'Login','general',NULL,NULL,'Inicio de sesion (Caja Salida Principal)',NULL,'2026-04-16 11:00:00','2026-04-16 18:27:14','2026-04-16 18:27:14'),(4,5,'Bloqueo','general',NULL,NULL,'Usuario bloqueado por 3 intentos fallidos',NULL,'2026-04-15 23:30:00','2026-04-16 18:27:14','2026-04-16 18:27:14'),(5,1,'Configuracion','general',NULL,NULL,'Parametros iniciales del sistema creados',NULL,'2026-04-16 10:50:00','2026-04-16 18:27:14','2026-04-16 18:27:14'),(6,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',2,'Ticket TKT-1002 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 2800, \"minutes\": 132, \"discount\": 0, \"strategy\": \"fraction\", \"subtotal\": 2800, \"surcharge\": 0, \"full_blocks\": 0, \"grace_minutes\": 20, \"pricing_label\": \"4 fraccion(es)\", \"fraction_units\": 4, \"billable_minutes\": 112, \"remainder_minutes\": 112, \"threshold_minutes\": 120}','2026-04-16 19:09:14','2026-04-16 19:09:14','2026-04-16 19:09:14'),(7,1,'entrada','ticket','App\\Models\\ParkingTicket',7,'Ticket PK-260416-0007 creado para YDY84F','{\"tarifa\": \"Motocicleta\", \"ubicacion\": \"15\"}','2026-04-16 19:10:07','2026-04-16 19:10:07','2026-04-16 19:10:07'),(8,1,'tarifa_actualizada','configuracion','App\\Models\\TariffProfile',2,'Tarifa Motocicleta actualizada','{\"name\": \"Motocicleta\", \"active\": \"1\", \"daily_cap\": \"12000\", \"unit_rate\": \"5000\", \"charge_unit\": \"minute\", \"vehicle_type\": \"moto\", \"tax_percentage\": \"0.00\", \"agreement_hours\": null, \"charge_interval\": \"15\", \"lost_ticket_fee\": \"5000\", \"grace_exit_minutes\": \"15\", \"grace_entry_minutes\": \"15\"}','2026-04-16 19:39:23','2026-04-16 19:39:23','2026-04-16 19:39:23'),(9,1,'entrada','ticket','App\\Models\\ParkingTicket',8,'Ticket PK-260416-0008 creado para FASFA','{\"tarifa\": \"Motocicleta\", \"ubicacion\": \"50\"}','2026-04-16 19:40:15','2026-04-16 19:40:15','2026-04-16 19:40:15'),(10,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',8,'Ticket PK-260416-0008 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 12000, \"minutes\": 122, \"discount\": 0, \"strategy\": \"fraction\", \"subtotal\": 12000, \"surcharge\": 0, \"full_blocks\": 0, \"grace_minutes\": 30, \"pricing_label\": \"7 bloque(s) de 15 min\", \"fraction_units\": 7, \"billable_minutes\": 92, \"remainder_minutes\": 0, \"threshold_minutes\": 15}','2026-04-16 19:42:47','2026-04-16 19:42:47','2026-04-16 19:42:47'),(11,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',7,'Ticket PK-260416-0007 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 12000, \"minutes\": 286, \"discount\": 0, \"strategy\": \"fraction\", \"subtotal\": 12000, \"surcharge\": 0, \"full_blocks\": 0, \"grace_minutes\": 30, \"pricing_label\": \"18 bloque(s) de 15 min\", \"fraction_units\": 18, \"billable_minutes\": 256, \"remainder_minutes\": 0, \"threshold_minutes\": 15}','2026-04-16 23:56:58','2026-04-16 23:56:58','2026-04-16 23:56:58'),(12,1,'entrada','ticket','App\\Models\\ParkingTicket',9,'Ticket PK-260417-0001 creado para YDY84F','{\"tarifa\": \"Motocicleta\", \"ubicacion\": \"15\"}','2026-04-17 14:07:16','2026-04-17 14:07:16','2026-04-17 14:07:16'),(13,1,'tarifa_actualizada','configuracion','App\\Models\\TariffProfile',2,'Tarifa Motocicleta actualizada','{\"name\": \"Motocicleta\", \"active\": \"1\", \"daily_cap\": \"12000\", \"unit_rate\": \"750\", \"charge_unit\": \"minute\", \"vehicle_type\": \"moto\", \"tax_percentage\": \"0.00\", \"agreement_hours\": null, \"charge_interval\": \"1\", \"lost_ticket_fee\": \"5000\", \"grace_exit_minutes\": \"15\", \"grace_entry_minutes\": \"15\"}','2026-04-17 16:16:00','2026-04-17 16:16:00','2026-04-17 16:16:00'),(14,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',9,'Ticket PK-260417-0001 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 12000, \"minutes\": 129, \"discount\": 0, \"strategy\": \"fraction\", \"subtotal\": 12000, \"surcharge\": 0, \"full_blocks\": 0, \"grace_minutes\": 30, \"pricing_label\": \"99 bloque(s) de 1 min\", \"applied_tariff\": \"Motocicleta\", \"fraction_units\": 99, \"billable_minutes\": 99, \"remainder_minutes\": 0, \"threshold_minutes\": 1}','2026-04-17 16:16:59','2026-04-17 16:16:59','2026-04-17 16:16:59'),(15,1,'entrada','ticket','App\\Models\\ParkingTicket',10,'Ticket PK-260417-0002 creado para YDY84F','{\"tarifa\": \"Motocicleta\", \"ubicacion\": \"15\"}','2026-04-17 16:17:35','2026-04-17 16:17:35','2026-04-17 16:17:35'),(16,1,'pago_pendiente','pagos','App\\Models\\ParkingTicket',10,'Ticket PK-260417-0002 procesado con metodo PENDING','{\"tax\": 0, \"total\": 13250, \"minutes\": 41, \"discount\": 0, \"strategy\": \"fraction\", \"subtotal\": 8250, \"surcharge\": 5000, \"full_blocks\": 0, \"grace_minutes\": 30, \"pricing_label\": \"11 bloque(s) de 1 min\", \"applied_tariff\": \"Motocicleta\", \"fraction_units\": 11, \"billable_minutes\": 11, \"remainder_minutes\": 0, \"threshold_minutes\": 1}','2026-04-17 16:59:12','2026-04-17 16:59:12','2026-04-17 16:59:12'),(17,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',10,'Ticket PK-260417-0002 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 8250, \"minutes\": 41, \"discount\": 0, \"strategy\": \"fraction\", \"subtotal\": 8250, \"surcharge\": 0, \"full_blocks\": 0, \"grace_minutes\": 30, \"pricing_label\": \"11 bloque(s) de 1 min\", \"applied_tariff\": \"Motocicleta\", \"fraction_units\": 11, \"billable_minutes\": 11, \"remainder_minutes\": 0, \"threshold_minutes\": 1}','2026-04-17 16:59:23','2026-04-17 16:59:23','2026-04-17 16:59:23'),(18,1,'entrada','ticket','App\\Models\\ParkingTicket',11,'Ticket PK-260417-0003 creado para EDC15F','{\"tarifa\": \"Motocicleta\", \"ubicacion\": \"1\"}','2026-04-17 17:15:00','2026-04-17 17:15:00','2026-04-17 17:15:00'),(19,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',1,'Ticket TKT-1001 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 0, \"minutes\": 1533, \"discount\": 0, \"strategy\": \"fraction\", \"subtotal\": 0, \"surcharge\": 0, \"full_blocks\": 0, \"grace_minutes\": 25, \"pricing_label\": \"101 bloque(s) de 15 min\", \"applied_tariff\": \"Automovil estandar\", \"fraction_units\": 101, \"billable_minutes\": 1508, \"remainder_minutes\": 0, \"threshold_minutes\": 15}','2026-04-17 17:15:30','2026-04-17 17:15:30','2026-04-17 17:15:30'),(20,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',11,'Ticket PK-260417-0003 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 0, \"minutes\": 1, \"discount\": 0, \"strategy\": \"fraction\", \"subtotal\": 0, \"surcharge\": 0, \"full_blocks\": 0, \"grace_minutes\": 30, \"pricing_label\": \"Sin cobro\", \"applied_tariff\": \"Motocicleta\", \"fraction_units\": 0, \"billable_minutes\": 0, \"remainder_minutes\": 0, \"threshold_minutes\": 1}','2026-04-17 17:16:04','2026-04-17 17:16:04','2026-04-17 17:16:04'),(21,1,'entrada','ticket','App\\Models\\ParkingTicket',12,'Ticket PK-260417-0004 creado para GGDS','{\"tarifa\": \"Motocicleta\", \"ubicacion\": \"15\"}','2026-04-17 19:04:55','2026-04-17 19:04:55','2026-04-17 19:04:55'),(22,1,'pago_pendiente','pagos','App\\Models\\ParkingTicket',12,'Ticket PK-260417-0004 procesado con metodo PENDING','{\"tax\": 0, \"total\": 0, \"minutes\": 1, \"discount\": 0, \"strategy\": \"fraction\", \"subtotal\": 0, \"surcharge\": 0, \"full_blocks\": 0, \"grace_minutes\": 30, \"pricing_label\": \"Sin cobro\", \"applied_tariff\": \"Motocicleta\", \"fraction_units\": 0, \"billable_minutes\": 0, \"remainder_minutes\": 0, \"threshold_minutes\": 1}','2026-04-17 19:05:22','2026-04-17 19:05:22','2026-04-17 19:05:22'),(23,1,'entrada','ticket','App\\Models\\ParkingTicket',13,'Ticket PK-260418-0001 creado para YDY84F','{\"tarifa\": \"Motocicleta\", \"ubicacion\": \"54\"}','2026-04-19 02:01:55','2026-04-19 02:01:55','2026-04-19 02:01:55'),(24,1,'tarifa_creada','configuracion','App\\Models\\TariffProfile',4,'Tarifa plena creada','{\"name\": \"plena\", \"active\": \"1\", \"daily_cap\": \"0\", \"unit_rate\": \"12000\", \"charge_unit\": \"hour\", \"is_full_rate\": \"1\", \"vehicle_type\": \"moto\", \"tax_percentage\": \"0\", \"agreement_hours\": null, \"charge_interval\": \"4\", \"lost_ticket_fee\": \"0\", \"grace_exit_minutes\": \"0\", \"grace_entry_minutes\": \"0\"}','2026-04-19 02:03:20','2026-04-19 02:03:20','2026-04-19 02:03:20'),(25,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',13,'Ticket PK-260418-0001 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 0, \"minutes\": 3, \"discount\": 0, \"strategy\": \"fraction\", \"subtotal\": 0, \"surcharge\": 0, \"full_blocks\": 0, \"grace_minutes\": 30, \"pricing_label\": \"Sin cobro\", \"applied_tariff\": \"Motocicleta\", \"fraction_units\": 0, \"billable_minutes\": 0, \"remainder_minutes\": 0, \"threshold_minutes\": 1}','2026-04-19 02:05:39','2026-04-19 02:05:39','2026-04-19 02:05:39'),(26,1,'entrada','ticket','App\\Models\\ParkingTicket',14,'Ticket PK-260429-0001 creado para YDY84F','{\"tarifa\": \"Motocicleta\", \"ubicacion\": \"54\"}','2026-04-29 23:07:08','2026-04-29 23:07:08','2026-04-29 23:07:08'),(27,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',14,'Ticket PK-260429-0001 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 12000, \"minutes\": 1088, \"discount\": 0, \"strategy\": \"fraction\", \"subtotal\": 12000, \"surcharge\": 0, \"full_blocks\": 0, \"grace_minutes\": 30, \"pricing_label\": \"1058 bloque(s) de 1 min\", \"applied_tariff\": \"Motocicleta\", \"fraction_units\": 1058, \"billable_minutes\": 1058, \"remainder_minutes\": 0, \"threshold_minutes\": 1}','2026-04-30 17:15:57','2026-04-30 17:15:57','2026-04-30 17:15:57'),(28,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',12,'Ticket PK-260417-0004 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 12000, \"minutes\": 18722, \"discount\": 0, \"strategy\": \"fraction\", \"subtotal\": 12000, \"surcharge\": 0, \"full_blocks\": 77, \"grace_minutes\": 30, \"pricing_label\": \"77 tarifa(s) plena(s) + 212 bloque(s) restantes\", \"applied_tariff\": \"Motocicleta + plena\", \"fraction_units\": 212, \"billable_minutes\": 18692, \"remainder_minutes\": 212, \"threshold_minutes\": 1}','2026-04-30 19:07:33','2026-04-30 19:07:33','2026-04-30 19:07:33'),(29,1,'tarifa_creada','configuracion','App\\Models\\TariffProfile',5,'Tarifa minuto creada','{\"name\": \"minuto\", \"type\": \"normal\", \"active\": \"1\", \"daily_cap\": \"5000\", \"full_rate\": \"8000\", \"unit_rate\": \"700\", \"charge_unit\": \"minute\", \"max_minutes\": \"720\", \"vehicle_type\": \"moto\", \"tax_percentage\": \"0\", \"charge_interval\": \"1\", \"lost_ticket_fee\": \"0\", \"threshold_minutes\": \"5\", \"grace_exit_minutes\": \"0\", \"grace_entry_minutes\": \"0\"}','2026-05-07 22:42:15','2026-05-07 22:42:15','2026-05-07 22:42:15'),(30,1,'entrada','ticket','App\\Models\\ParkingTicket',15,'Ticket PK-260507-0001 creado para YDY84F','{\"tarifa\": \"minuto\", \"ubicacion\": \"58\"}','2026-05-07 22:42:52','2026-05-07 22:42:52','2026-05-07 22:42:52'),(31,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',15,'Ticket PK-260507-0001 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 4900, \"minutes\": 7, \"discount\": 0, \"subtotal\": 4900, \"surcharge\": 0, \"full_blocks\": 0, \"grace_minutes\": 0, \"pricing_label\": \"7 minuto(s) x $700\", \"applied_tariff\": \"minuto\", \"fraction_units\": 7, \"billable_minutes\": 7, \"interval_minutes\": 1, \"remainder_minutes\": 0}','2026-05-07 22:50:41','2026-05-07 22:50:41','2026-05-07 22:50:41'),(32,1,'tarifa_creada','configuracion','App\\Models\\TariffProfile',6,'Tarifa Plena creada','{\"name\": \"Plena\", \"type\": \"normal\", \"active\": \"1\", \"daily_cap\": \"0\", \"full_rate\": \"5000\", \"unit_rate\": \"0\", \"charge_unit\": \"minute\", \"max_minutes\": null, \"vehicle_type\": \"moto\", \"tax_percentage\": \"0\", \"charge_interval\": \"1\", \"lost_ticket_fee\": \"0\", \"threshold_minutes\": \"3\", \"grace_exit_minutes\": \"0\", \"grace_entry_minutes\": \"0\"}','2026-05-07 22:51:49','2026-05-07 22:51:49','2026-05-07 22:51:49'),(33,1,'entrada','ticket','App\\Models\\ParkingTicket',16,'Ticket PK-260507-0002 creado para HJC54F','{\"tarifa\": \"Plena\", \"ubicacion\": \"55\"}','2026-05-07 22:52:22','2026-05-07 22:52:22','2026-05-07 22:52:22'),(34,1,'tarifa_creada','configuracion','App\\Models\\TariffProfile',7,'Tarifa Minuto creada','{\"name\": \"Minuto\", \"type\": \"normal\", \"active\": \"1\", \"daily_cap\": \"0\", \"full_rate\": null, \"unit_rate\": \"500\", \"charge_unit\": \"minute\", \"max_minutes\": null, \"vehicle_type\": \"moto\", \"tax_percentage\": \"0\", \"charge_interval\": \"1\", \"lost_ticket_fee\": \"0\", \"threshold_minutes\": null, \"grace_exit_minutes\": \"0\", \"grace_entry_minutes\": \"0\"}','2026-05-07 23:15:09','2026-05-07 23:15:09','2026-05-07 23:15:09'),(35,1,'tarifa_creada','configuracion','App\\Models\\TariffProfile',8,'Tarifa Plena creada','{\"name\": \"Plena\", \"type\": \"plena\", \"active\": \"1\", \"daily_cap\": \"0\", \"full_rate\": \"8000\", \"unit_rate\": \"0\", \"charge_unit\": \"minute\", \"max_minutes\": null, \"vehicle_type\": \"moto\", \"tax_percentage\": \"0\", \"charge_interval\": \"1\", \"lost_ticket_fee\": \"0\", \"threshold_minutes\": \"5\", \"grace_exit_minutes\": \"0\", \"grace_entry_minutes\": \"0\"}','2026-05-07 23:15:25','2026-05-07 23:15:25','2026-05-07 23:15:25'),(36,1,'tarifa_creada','configuracion','App\\Models\\TariffProfile',9,'Tarifa Gimnasio creada','{\"name\": \"Gimnasio\", \"type\": \"normal\", \"active\": \"1\", \"daily_cap\": \"0\", \"full_rate\": null, \"unit_rate\": \"100\", \"charge_unit\": \"minute\", \"max_minutes\": null, \"vehicle_type\": \"moto\", \"tax_percentage\": \"0\", \"charge_interval\": \"1\", \"lost_ticket_fee\": \"0\", \"threshold_minutes\": null, \"grace_exit_minutes\": \"0\", \"grace_entry_minutes\": \"0\"}','2026-05-07 23:15:47','2026-05-07 23:15:47','2026-05-07 23:15:47'),(37,1,'entrada','ticket','App\\Models\\ParkingTicket',17,'Ticket PK-260507-0001 creado para GHD54D','{\"tarifa\": \"Minuto\", \"ubicacion\": \"12\"}','2026-05-07 23:16:20','2026-05-07 23:16:20','2026-05-07 23:16:20'),(38,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',17,'Ticket PK-260507-0001 procesado con metodo NEQUI','{\"tax\": 0, \"total\": 8000, \"minutes\": 7, \"discount\": 0, \"subtotal\": 8000, \"surcharge\": 0, \"full_blocks\": 1, \"grace_minutes\": 0, \"pricing_label\": \"Tarifa plena desde 5 min\", \"applied_tariff\": \"Plena\", \"fraction_units\": 0, \"billable_minutes\": 7, \"interval_minutes\": 1, \"remainder_minutes\": 0}','2026-05-07 23:24:05','2026-05-07 23:24:05','2026-05-07 23:24:05'),(39,1,'entrada','ticket','App\\Models\\ParkingTicket',18,'Ticket PK-260507-0002 creado para FASF','{\"tarifa\": \"Gimnasio\", \"ubicacion\": \"42\"}','2026-05-07 23:24:58','2026-05-07 23:24:58','2026-05-07 23:24:58'),(40,1,'entrada','ticket','App\\Models\\ParkingTicket',19,'Ticket PK-260507-0003 creado para HDG','{\"tarifa\": \"Minuto\", \"ubicacion\": \"43\"}','2026-05-07 23:26:20','2026-05-07 23:26:20','2026-05-07 23:26:20'),(41,1,'tarifa_creada','configuracion','App\\Models\\TariffProfile',10,'Tarifa Gimnasio creada','{\"name\": \"Gimnasio\", \"type\": \"convenio\", \"active\": \"1\", \"daily_cap\": \"0\", \"full_rate\": null, \"unit_rate\": \"5000\", \"charge_unit\": \"minute\", \"max_minutes\": \"4\", \"vehicle_type\": \"moto\", \"tax_percentage\": \"0\", \"charge_interval\": \"1\", \"lost_ticket_fee\": \"0\", \"threshold_minutes\": null, \"grace_exit_minutes\": \"0\", \"grace_entry_minutes\": \"0\"}','2026-05-07 23:31:55','2026-05-07 23:31:55','2026-05-07 23:31:55'),(42,1,'entrada','ticket','App\\Models\\ParkingTicket',22,'Ticket PK-260507-0004 creado para GSDGDS','{\"tarifa\": \"Gimnasio\", \"ubicacion\": \"5\"}','2026-05-07 23:33:55','2026-05-07 23:33:55','2026-05-07 23:33:55'),(43,1,'pago_pendiente','pagos','App\\Models\\ParkingTicket',22,'Ticket PK-260507-0004 procesado con metodo PENDING','{\"tax\": 0, \"total\": 10000, \"minutes\": 5, \"discount\": 0, \"subtotal\": 10000, \"surcharge\": 0, \"full_blocks\": 2, \"grace_minutes\": 0, \"pricing_label\": \"2 convenio(s), cada uno cubre 4 min\", \"applied_tariff\": \"Gimnasio\", \"fraction_units\": 0, \"billable_minutes\": 5, \"interval_minutes\": 1, \"remainder_minutes\": 0}','2026-05-07 23:39:20','2026-05-07 23:39:20','2026-05-07 23:39:20'),(44,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',22,'Ticket PK-260507-0004 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 10000, \"minutes\": 5, \"discount\": 0, \"subtotal\": 10000, \"surcharge\": 0, \"full_blocks\": 2, \"grace_minutes\": 0, \"pricing_label\": \"2 convenio(s), cada uno cubre 4 min\", \"applied_tariff\": \"Gimnasio\", \"fraction_units\": 0, \"billable_minutes\": 5, \"interval_minutes\": 1, \"remainder_minutes\": 0}','2026-05-07 23:39:34','2026-05-07 23:39:34','2026-05-07 23:39:34'),(45,1,'entrada','ticket','App\\Models\\ParkingTicket',23,'Ticket PK-260508-0001 creado para OTKT','{\"tarifa\": \"Minuto\", \"ubicacion\": \"54\"}','2026-05-08 15:41:15','2026-05-08 15:41:15','2026-05-08 15:41:15'),(46,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',23,'Ticket PK-260508-0001 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 500, \"minutes\": 1, \"discount\": 0, \"subtotal\": 500, \"surcharge\": 0, \"full_blocks\": 0, \"grace_minutes\": 0, \"pricing_label\": \"1 minuto(s) x $500\", \"applied_tariff\": \"Minuto\", \"fraction_units\": 1, \"billable_minutes\": 1, \"interval_minutes\": 1, \"remainder_minutes\": 0}','2026-05-08 15:41:30','2026-05-08 15:41:30','2026-05-08 15:41:30'),(47,1,'entrada','ticket','App\\Models\\ParkingTicket',24,'Ticket PK-260508-0002 creado para YDY84F','{\"tarifa\": \"Gimnasio\", \"ubicacion\": \"2\"}','2026-05-08 15:42:07','2026-05-08 15:42:07','2026-05-08 15:42:07'),(48,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',24,'Ticket PK-260508-0002 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 5000, \"minutes\": 1, \"discount\": 0, \"subtotal\": 5000, \"surcharge\": 0, \"full_blocks\": 1, \"grace_minutes\": 0, \"pricing_label\": \"1 convenio(s), cada uno cubre 4 min\", \"applied_tariff\": \"Gimnasio\", \"fraction_units\": 0, \"billable_minutes\": 1, \"interval_minutes\": 1, \"remainder_minutes\": 0}','2026-05-08 15:42:15','2026-05-08 15:42:15','2026-05-08 15:42:15'),(49,1,'entrada','ticket','App\\Models\\ParkingTicket',25,'Ticket PK-260508-0003 creado para YDY84F','{\"tarifa\": \"Gimnasio\", \"ubicacion\": \"2\"}','2026-05-08 15:42:26','2026-05-08 15:42:26','2026-05-08 15:42:26'),(50,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',25,'Ticket PK-260508-0003 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 35000, \"minutes\": 28, \"discount\": 0, \"subtotal\": 35000, \"surcharge\": 0, \"full_blocks\": 7, \"grace_minutes\": 0, \"pricing_label\": \"7 convenio(s), cada uno cubre 4 min\", \"applied_tariff\": \"Gimnasio\", \"fraction_units\": 0, \"billable_minutes\": 28, \"interval_minutes\": 1, \"remainder_minutes\": 0}','2026-05-08 16:10:53','2026-05-08 16:10:53','2026-05-08 16:10:53'),(51,1,'entrada','ticket','App\\Models\\ParkingTicket',26,'Ticket PK-260508-0004 creado para DADF','{\"tarifa\": \"Minuto\", \"ubicacion\": \"84\"}','2026-05-08 16:11:09','2026-05-08 16:11:09','2026-05-08 16:11:09'),(52,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',19,'Ticket PK-260507-0003 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 8000, \"minutes\": 1005, \"discount\": 0, \"subtotal\": 8000, \"surcharge\": 0, \"full_blocks\": 1, \"grace_minutes\": 0, \"pricing_label\": \"Tarifa plena desde 5 min\", \"applied_tariff\": \"Plena\", \"fraction_units\": 0, \"billable_minutes\": 1005, \"interval_minutes\": 1, \"remainder_minutes\": 0}','2026-05-08 16:11:46','2026-05-08 16:11:46','2026-05-08 16:11:46'),(53,1,'entrada','ticket','App\\Models\\ParkingTicket',27,'Ticket PK-260508-0005 creado para GBHDA54','{\"tarifa\": \"Gimnasio\", \"ubicacion\": \"43\"}','2026-05-08 16:12:51','2026-05-08 16:12:51','2026-05-08 16:12:51'),(54,1,'entrada','ticket','App\\Models\\ParkingTicket',28,'Ticket PK-260508-0006 creado para YDY84F','{\"tarifa\": \"Minuto\", \"ubicacion\": \"2\"}','2026-05-08 16:14:19','2026-05-08 16:14:19','2026-05-08 16:14:19'),(55,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',28,'Ticket PK-260508-0006 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 8000, \"minutes\": 9, \"discount\": 0, \"subtotal\": 8000, \"surcharge\": 0, \"full_blocks\": 1, \"grace_minutes\": 0, \"pricing_label\": \"Tarifa plena desde 5 min\", \"applied_tariff\": \"Plena\", \"fraction_units\": 0, \"billable_minutes\": 9, \"interval_minutes\": 1, \"remainder_minutes\": 0}','2026-05-08 16:23:21','2026-05-08 16:23:21','2026-05-08 16:23:21'),(56,1,'entrada','ticket','App\\Models\\ParkingTicket',29,'Ticket PK-260508-0007 creado para YDY84F','{\"tarifa\": \"Minuto\", \"ubicacion\": \"2\"}','2026-05-08 16:23:31','2026-05-08 16:23:31','2026-05-08 16:23:31'),(57,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',29,'Ticket PK-260508-0007 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 8000, \"minutes\": 15, \"discount\": 0, \"subtotal\": 8000, \"surcharge\": 0, \"full_blocks\": 1, \"grace_minutes\": 0, \"pricing_label\": \"Tarifa plena desde 5 min\", \"applied_tariff\": \"Plena\", \"fraction_units\": 0, \"billable_minutes\": 15, \"interval_minutes\": 1, \"remainder_minutes\": 0}','2026-05-08 16:38:54','2026-05-08 16:38:54','2026-05-08 16:38:54'),(58,1,'entrada','ticket','App\\Models\\ParkingTicket',30,'Ticket PK-260508-0008 creado para HUF','{\"tarifa\": \"Minuto\", \"ubicacion\": \"45\"}','2026-05-08 16:39:20','2026-05-08 16:39:20','2026-05-08 16:39:20'),(59,1,'entrada','ticket','App\\Models\\ParkingTicket',31,'Ticket PK-260508-0009 creado para GDFGDF','{\"tarifa\": \"Gimnasio\", \"ubicacion\": \"43\"}','2026-05-08 16:39:27','2026-05-08 16:39:27','2026-05-08 16:39:27'),(60,1,'entrada','ticket','App\\Models\\ParkingTicket',32,'Ticket PK-260508-0010 creado para FDASFAS','{\"tarifa\": \"Minuto\", \"ubicacion\": \"23\"}','2026-05-08 16:42:50','2026-05-08 16:42:50','2026-05-08 16:42:50'),(61,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',32,'Ticket PK-260508-0010 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 500, \"minutes\": 1, \"discount\": 0, \"subtotal\": 500, \"surcharge\": 0, \"full_blocks\": 0, \"grace_minutes\": 0, \"pricing_label\": \"1 minuto(s) x $500\", \"applied_tariff\": \"Minuto\", \"fraction_units\": 1, \"billable_minutes\": 1, \"interval_minutes\": 1, \"remainder_minutes\": 0}','2026-05-08 16:43:45','2026-05-08 16:43:45','2026-05-08 16:43:45'),(62,1,'entrada','ticket','App\\Models\\ParkingTicket',33,'Ticket PK-260508-0011 creado para FFS','{\"tarifa\": \"Minuto\", \"ubicacion\": \"3\"}','2026-05-08 16:46:48','2026-05-08 16:46:48','2026-05-08 16:46:48'),(63,1,'entrada','ticket','App\\Models\\ParkingTicket',34,'Ticket PK-260508-0012 creado para GDSGD','{\"tarifa\": \"Minuto\", \"ubicacion\": \"432\"}','2026-05-08 16:47:10','2026-05-08 16:47:10','2026-05-08 16:47:10'),(64,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',34,'Ticket PK-260508-0012 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 500, \"minutes\": 1, \"discount\": 0, \"subtotal\": 500, \"surcharge\": 0, \"full_blocks\": 0, \"grace_minutes\": 0, \"pricing_label\": \"1 minuto(s) x $500\", \"applied_tariff\": \"Minuto\", \"fraction_units\": 1, \"billable_minutes\": 1, \"interval_minutes\": 1, \"remainder_minutes\": 0}','2026-05-08 16:47:22','2026-05-08 16:47:22','2026-05-08 16:47:22'),(65,1,'entrada','ticket','App\\Models\\ParkingTicket',35,'Ticket PK-260508-0013 creado para FASFAS','{\"tarifa\": \"Gimnasio\", \"ubicacion\": \"3\"}','2026-05-08 16:53:55','2026-05-08 16:53:55','2026-05-08 16:53:55'),(66,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',35,'Ticket PK-260508-0013 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 195000, \"minutes\": 153, \"discount\": 0, \"subtotal\": 195000, \"surcharge\": 0, \"full_blocks\": 39, \"grace_minutes\": 0, \"pricing_label\": \"39 convenio(s), cada uno cubre 4 min\", \"applied_tariff\": \"Gimnasio\", \"fraction_units\": 0, \"billable_minutes\": 153, \"interval_minutes\": 1, \"remainder_minutes\": 0}','2026-05-08 19:26:57','2026-05-08 19:26:57','2026-05-08 19:26:57'),(67,1,'entrada','ticket','App\\Models\\ParkingTicket',36,'Ticket PK-260508-0014 creado para HDHD','{\"tarifa\": \"Minuto\", \"ubicacion\": \"54\"}','2026-05-08 19:46:20','2026-05-08 19:46:20','2026-05-08 19:46:20'),(68,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',27,'Ticket PK-260508-0005 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 270000, \"minutes\": 213, \"discount\": 0, \"subtotal\": 270000, \"surcharge\": 0, \"full_blocks\": 54, \"grace_minutes\": 0, \"pricing_label\": \"54 convenio(s), cada uno cubre 4 min\", \"applied_tariff\": \"Gimnasio\", \"fraction_units\": 0, \"billable_minutes\": 213, \"interval_minutes\": 1, \"remainder_minutes\": 0}','2026-05-08 19:46:48','2026-05-08 19:46:48','2026-05-08 19:46:48'),(69,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',26,'Ticket PK-260508-0004 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 8000, \"minutes\": 216, \"discount\": 0, \"subtotal\": 8000, \"surcharge\": 0, \"full_blocks\": 1, \"grace_minutes\": 0, \"pricing_label\": \"Tarifa plena desde 5 min\", \"applied_tariff\": \"Plena\", \"fraction_units\": 0, \"billable_minutes\": 216, \"interval_minutes\": 1, \"remainder_minutes\": 0}','2026-05-08 19:47:22','2026-05-08 19:47:22','2026-05-08 19:47:22'),(70,1,'entrada','ticket','App\\Models\\ParkingTicket',37,'Ticket PK-260508-0015 creado para YDY84F','{\"tarifa\": \"Gimnasio\", \"ubicacion\": \"4\"}','2026-05-08 20:05:48','2026-05-08 20:05:48','2026-05-08 20:05:48'),(71,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',37,'Ticket PK-260508-0015 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 5000, \"minutes\": 3, \"discount\": 0, \"subtotal\": 5000, \"surcharge\": 0, \"full_blocks\": 1, \"grace_minutes\": 0, \"pricing_label\": \"1 convenio(s), cada uno cubre 4 min\", \"applied_tariff\": \"Gimnasio\", \"fraction_units\": 0, \"billable_minutes\": 3, \"interval_minutes\": 1, \"remainder_minutes\": 0}','2026-05-08 20:09:28','2026-05-08 20:09:28','2026-05-08 20:09:28'),(72,1,'entrada','ticket','App\\Models\\ParkingTicket',38,'Ticket PK-260508-0016 creado para JFHJF','{\"tarifa\": \"Minuto\", \"ubicacion\": \"44\"}','2026-05-08 20:11:26','2026-05-08 20:11:26','2026-05-08 20:11:26'),(73,1,'entrada','ticket','App\\Models\\ParkingTicket',39,'Ticket PK-260508-0017 creado para HHR','{\"tarifa\": \"Minuto\", \"ubicacion\": \"54\"}','2026-05-08 20:11:34','2026-05-08 20:11:34','2026-05-08 20:11:34'),(74,1,'entrada','ticket','App\\Models\\ParkingTicket',40,'Ticket PK-260508-0018 creado para FSAF','{\"tarifa\": \"Minuto\", \"ubicacion\": \"4\"}','2026-05-08 20:31:30','2026-05-08 20:31:30','2026-05-08 20:31:30'),(75,1,'entrada','ticket','App\\Models\\ParkingTicket',41,'Ticket PK-260508-0019 creado para FSFD','{\"tarifa\": \"Minuto\", \"ubicacion\": \"54\"}','2026-05-08 20:31:50','2026-05-08 20:31:50','2026-05-08 20:31:50'),(76,1,'entrada','ticket','App\\Models\\ParkingTicket',42,'Ticket PK-260508-0020 creado para YDY84F','{\"tarifa\": \"Gimnasio\", \"ubicacion\": \"4\"}','2026-05-08 20:31:55','2026-05-08 20:31:55','2026-05-08 20:31:55'),(77,1,'entrada','ticket','App\\Models\\ParkingTicket',43,'Ticket PK-260508-0021 creado para DASDASF','{\"tarifa\": \"Gimnasio\", \"ubicacion\": \"4\"}','2026-05-08 20:37:40','2026-05-08 20:37:40','2026-05-08 20:37:40'),(78,1,'entrada','ticket','App\\Models\\ParkingTicket',44,'Ticket PK-260508-0022 creado para DASD','{\"tarifa\": \"Minuto\", \"ubicacion\": \"4\"}','2026-05-08 20:37:49','2026-05-08 20:37:49','2026-05-08 20:37:49'),(79,1,'entrada','ticket','App\\Models\\ParkingTicket',45,'Ticket PK-260508-0023 creado para JUHD54','{\"tarifa\": \"Minuto\", \"ubicacion\": \"54\"}','2026-05-08 21:03:33','2026-05-08 21:03:33','2026-05-08 21:03:33'),(80,1,'tarifa_creada','configuracion','App\\Models\\TariffProfile',11,'Tarifa bicicleta creada','{\"name\": \"bicicleta\", \"type\": \"normal\", \"active\": \"1\", \"daily_cap\": \"0\", \"full_rate\": null, \"unit_rate\": \"500\", \"charge_unit\": \"minute\", \"max_minutes\": null, \"vehicle_type\": \"moto\", \"tax_percentage\": \"0\", \"charge_interval\": \"1\", \"lost_ticket_fee\": \"0\", \"threshold_minutes\": null, \"grace_exit_minutes\": \"0\", \"grace_entry_minutes\": \"0\"}','2026-05-08 21:04:50','2026-05-08 21:04:50','2026-05-08 21:04:50'),(81,1,'tarifa_creada','configuracion','App\\Models\\TariffProfile',13,'Tarifa Minuto bici creada','{\"name\": \"Minuto bici\", \"type\": \"normal\", \"active\": \"1\", \"daily_cap\": \"0\", \"full_rate\": null, \"unit_rate\": \"500\", \"charge_unit\": \"minute\", \"max_minutes\": null, \"vehicle_type\": \"moto\", \"tax_percentage\": \"0\", \"charge_interval\": \"1\", \"lost_ticket_fee\": \"0\", \"threshold_minutes\": null, \"grace_exit_minutes\": \"0\", \"grace_entry_minutes\": \"0\"}','2026-05-08 21:05:39','2026-05-08 21:05:39','2026-05-08 21:05:39'),(82,1,'tarifa_creada','configuracion','App\\Models\\TariffProfile',14,'Tarifa min bici creada','{\"name\": \"min bici\", \"type\": \"normal\", \"active\": \"1\", \"daily_cap\": \"0\", \"full_rate\": null, \"unit_rate\": \"500\", \"charge_unit\": \"minute\", \"max_minutes\": null, \"vehicle_type\": \"bicicleta\", \"tax_percentage\": \"0\", \"charge_interval\": \"1\", \"lost_ticket_fee\": \"0\", \"threshold_minutes\": null, \"grace_exit_minutes\": \"0\", \"grace_entry_minutes\": \"0\"}','2026-05-08 21:05:56','2026-05-08 21:05:56','2026-05-08 21:05:56'),(83,1,'usuario_actualizado','usuarios','App\\Models\\User',3,'Usuario ana.caja actualizado','{\"name\": \"Ana Martinez\", \"role\": \"operario\", \"email\": \"ana@parkmanager.com\", \"password\": \"$2y$12$XYNxeS0ZsCNjIdnaaFrS1.WsoojxVqNHEW3EZSGwaaTkrsG.VNUmi\", \"username\": \"ana.caja\", \"is_active\": true, \"shift_name\": \"Manana (06:00 - 14:00)\"}','2026-05-08 21:07:04','2026-05-08 21:07:04','2026-05-08 21:07:04'),(84,3,'salida_confirmada','pagos','App\\Models\\ParkingTicket',42,'Ticket PK-260508-0020 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 45000, \"minutes\": 35, \"discount\": 0, \"subtotal\": 45000, \"surcharge\": 0, \"full_blocks\": 9, \"grace_minutes\": 0, \"pricing_label\": \"9 convenio(s), cada uno cubre 4 min\", \"applied_tariff\": \"Gimnasio\", \"fraction_units\": 0, \"billable_minutes\": 35, \"interval_minutes\": 1, \"remainder_minutes\": 0}','2026-05-08 21:07:36','2026-05-08 21:07:36','2026-05-08 21:07:36'),(85,3,'entrada','ticket','App\\Models\\ParkingTicket',46,'Ticket PK-260508-0024 creado para YDY84F','{\"tarifa\": \"min bici\", \"ubicacion\": \"5\"}','2026-05-08 21:07:45','2026-05-08 21:07:45','2026-05-08 21:07:45'),(86,1,'locker_actualizado','configuracion','App\\Models\\Site',1,'Tarifa fija de locker actualizada a $2.000','{\"locker_fee\": 2000}','2026-05-08 21:31:32','2026-05-08 21:31:32','2026-05-08 21:31:32'),(87,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',46,'Ticket PK-260508-0024 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 12000, \"minutes\": 24, \"discount\": 0, \"subtotal\": 12000, \"surcharge\": 0, \"locker_fee\": 0, \"full_blocks\": 0, \"uses_locker\": false, \"grace_minutes\": 0, \"locker_number\": null, \"pricing_label\": \"24 minuto(s) x $500\", \"applied_tariff\": \"min bici\", \"fraction_units\": 24, \"billable_minutes\": 24, \"interval_minutes\": 1, \"parking_subtotal\": 12000, \"remainder_minutes\": 0}','2026-05-08 21:31:46','2026-05-08 21:31:46','2026-05-08 21:31:46'),(88,1,'entrada','ticket','App\\Models\\ParkingTicket',47,'Ticket PK-260508-0025 creado para DADSDA','{\"locker\": \"no\", \"tarifa\": \"Minuto\", \"ubicacion\": \"54\"}','2026-05-08 21:34:11','2026-05-08 21:34:11','2026-05-08 21:34:11'),(89,1,'entrada','ticket','App\\Models\\ParkingTicket',48,'Ticket PK-260508-0026 creado para YDY','{\"locker\": \"12\", \"tarifa\": \"Gimnasio\", \"ubicacion\": \"54\"}','2026-05-08 21:34:59','2026-05-08 21:34:59','2026-05-08 21:34:59'),(90,1,'entrada','ticket','App\\Models\\ParkingTicket',49,'Ticket PK-260508-0027 creado para YDY84F','{\"locker\": \"12\", \"tarifa\": \"Minuto\", \"ubicacion\": \"54\"}','2026-05-08 21:46:27','2026-05-08 21:46:27','2026-05-08 21:46:27'),(91,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',49,'Ticket PK-260508-0027 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 2500, \"minutes\": 1, \"discount\": 0, \"subtotal\": 2500, \"surcharge\": 0, \"locker_fee\": 2000, \"full_blocks\": 0, \"uses_locker\": true, \"grace_minutes\": 0, \"locker_number\": \"12\", \"pricing_label\": \"1 minuto(s) x $500\", \"applied_tariff\": \"Minuto\", \"fraction_units\": 1, \"billable_minutes\": 1, \"interval_minutes\": 1, \"parking_subtotal\": 500, \"remainder_minutes\": 0}','2026-05-08 21:47:04','2026-05-08 21:47:04','2026-05-08 21:47:04'),(92,1,'pago_pendiente','pagos','App\\Models\\ParkingTicket',33,'Ticket PK-260508-0011 procesado con metodo PENDING','{\"tax\": 0, \"total\": 8000, \"minutes\": 301, \"discount\": 0, \"subtotal\": 8000, \"surcharge\": 0, \"locker_fee\": 0, \"full_blocks\": 1, \"uses_locker\": false, \"grace_minutes\": 0, \"locker_number\": null, \"pricing_label\": \"Tarifa plena desde 5 min\", \"applied_tariff\": \"Plena\", \"fraction_units\": 0, \"billable_minutes\": 301, \"interval_minutes\": 1, \"parking_subtotal\": 8000, \"remainder_minutes\": 0}','2026-05-08 21:47:49','2026-05-08 21:47:49','2026-05-08 21:47:49'),(93,1,'pago_regularizado','pagos','App\\Models\\ParkingTicket',33,'Pago pendiente regularizado para PK-260508-0011','{\"total\": 8000, \"metodo\": \"efectivo\"}','2026-05-08 21:48:15','2026-05-08 21:48:15','2026-05-08 21:48:15'),(94,1,'entrada','ticket','App\\Models\\ParkingTicket',50,'Ticket PK-260508-0028 creado para HFHF','{\"locker\": \"no\", \"tarifa\": \"Minuto\", \"ubicacion\": \"54\"}','2026-05-08 21:48:34','2026-05-08 21:48:34','2026-05-08 21:48:34'),(95,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',50,'Ticket PK-260508-0028 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 500, \"minutes\": 1, \"discount\": 0, \"subtotal\": 500, \"surcharge\": 0, \"locker_fee\": 0, \"full_blocks\": 0, \"uses_locker\": false, \"grace_minutes\": 0, \"locker_number\": null, \"pricing_label\": \"1 minuto(s) x $500\", \"applied_tariff\": \"Minuto\", \"fraction_units\": 1, \"billable_minutes\": 1, \"interval_minutes\": 1, \"parking_subtotal\": 500, \"remainder_minutes\": 0}','2026-05-08 21:48:56','2026-05-08 21:48:56','2026-05-08 21:48:56'),(96,1,'entrada','ticket','App\\Models\\ParkingTicket',51,'Ticket PK-260508-0029 creado para FAFFAS','{\"locker\": \"no\", \"tarifa\": \"Gimnasio\", \"ubicacion\": \"4\"}','2026-05-08 22:08:55','2026-05-08 22:08:55','2026-05-08 22:08:55'),(97,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',51,'Ticket PK-260508-0029 procesado con metodo EFECTIVO','{\"tax\": 0, \"total\": 5000, \"minutes\": 1, \"discount\": 0, \"subtotal\": 5000, \"surcharge\": 0, \"locker_fee\": 0, \"full_blocks\": 1, \"uses_locker\": false, \"grace_minutes\": 0, \"locker_number\": null, \"pricing_label\": \"1 convenio(s), cada uno cubre 4 min\", \"applied_tariff\": \"Gimnasio\", \"fraction_units\": 0, \"billable_minutes\": 1, \"interval_minutes\": 1, \"parking_subtotal\": 5000, \"remainder_minutes\": 0}','2026-05-08 22:09:24','2026-05-08 22:09:24','2026-05-08 22:09:24'),(98,1,'entrada','ticket','App\\Models\\ParkingTicket',52,'Ticket PK-260508-0030 creado para YDHAD','{\"locker\": \"no\", \"tarifa\": \"Gimnasio\", \"ubicacion\": \"5\"}','2026-05-08 22:10:42','2026-05-08 22:10:42','2026-05-08 22:10:42'),(99,1,'entrada','ticket','App\\Models\\ParkingTicket',53,'Ticket PK-260508-0031 creado para YDY84F','{\"locker\": \"12\", \"tarifa\": \"Gimnasio\", \"ubicacion\": \"54\"}','2026-05-08 22:16:40','2026-05-08 22:16:40','2026-05-08 22:16:40'),(100,1,'salida_confirmada','pagos','App\\Models\\ParkingTicket',53,'Ticket PK-260508-0031 procesado con metodo NEQUI','{\"tax\": 0, \"total\": 7000, \"minutes\": 1, \"discount\": 0, \"subtotal\": 7000, \"surcharge\": 0, \"locker_fee\": 2000, \"full_blocks\": 1, \"uses_locker\": true, \"grace_minutes\": 0, \"locker_number\": \"12\", \"pricing_label\": \"1 convenio(s), cada uno cubre 4 min\", \"applied_tariff\": \"Gimnasio\", \"fraction_units\": 0, \"billable_minutes\": 1, \"interval_minutes\": 1, \"parking_subtotal\": 5000, \"remainder_minutes\": 0}','2026-05-08 22:16:57','2026-05-08 22:16:57','2026-05-08 22:16:57');
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2026_04_16_100000_create_sites_table',1),(6,'2026_04_16_100100_add_operational_fields_to_users_table',1),(7,'2026_04_16_100200_create_tariff_profiles_table',1),(8,'2026_04_16_100300_create_parking_tickets_table',1),(9,'2026_04_16_100400_create_payments_table',1),(10,'2026_04_16_100500_create_audit_logs_table',1),(11,'2026_04_16_200000_add_tariff_operation_fields_to_tariff_profiles_table',2),(12,'2026_04_30_161352_update_tariff_profiles_structure',3),(13,'2026_05_07_000000_add_full_rate_to_tariff_profiles_table',4),(14,'2026_05_08_000000_create_portal_sync_jobs_table',5),(15,'2026_05_08_010000_add_locker_fields_to_parking_tickets_and_sites',6);
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
  `uses_locker` tinyint(1) NOT NULL DEFAULT '0',
  `locker_number` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locker_fee` int unsigned NOT NULL DEFAULT '0',
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
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parking_tickets`
--

/*!40000 ALTER TABLE `parking_tickets` DISABLE KEYS */;
INSERT INTO `parking_tickets` VALUES (17,1,7,'PK-260507-0001','PK-260507-0001','GHD54D','moto','paid',12,0,NULL,0,NULL,NULL,1,1,'2026-05-07 23:16:20','2026-05-07 23:24:05','',0,'2026-05-07 23:16:20','2026-05-07 23:24:05'),(19,1,7,'PK-260507-0003','PK-260507-0003','HDG','moto','paid',43,0,NULL,0,NULL,NULL,1,1,'2026-05-07 23:26:20','2026-05-08 16:11:46','',0,'2026-05-07 23:26:20','2026-05-08 16:11:46'),(22,1,10,'PK-260507-0004','PK-260507-0004','GSDGDS','moto','paid',5,0,NULL,0,NULL,NULL,1,1,'2026-05-07 23:33:55','2026-05-07 23:39:34','',0,'2026-05-07 23:33:55','2026-05-07 23:39:34'),(23,1,7,'PK-260508-0001','PK-260508-0001','OTKT','moto','paid',54,0,NULL,0,NULL,NULL,1,1,'2026-05-08 15:41:15','2026-05-08 15:41:30','',0,'2026-05-08 15:41:15','2026-05-08 15:41:30'),(24,1,10,'PK-260508-0002','PK-260508-0002','YDY84F','moto','paid',2,0,NULL,0,'alejandro','3503752816',1,1,'2026-05-08 15:42:07','2026-05-08 15:42:15','',0,'2026-05-08 15:42:07','2026-05-08 15:42:15'),(25,1,10,'PK-260508-0003','PK-260508-0003','YDY84F','moto','paid',2,0,NULL,0,'alejandro','3503752816',1,1,'2026-05-08 15:42:26','2026-05-08 16:10:53','',0,'2026-05-08 15:42:26','2026-05-08 16:10:53'),(26,1,7,'PK-260508-0004','PK-260508-0004','DADF','moto','paid',84,0,NULL,0,NULL,NULL,1,1,'2026-05-08 16:11:09','2026-05-08 19:47:22','',0,'2026-05-08 16:11:09','2026-05-08 19:47:22'),(27,1,10,'PK-260508-0005','PK-260508-0005','GBHDA54','moto','paid',43,0,NULL,0,'jhon',NULL,1,1,'2026-05-08 16:12:51','2026-05-08 19:46:48','',0,'2026-05-08 16:12:51','2026-05-08 19:46:48'),(28,1,7,'PK-260508-0006','PK-260508-0006','YDY84F','moto','paid',2,0,NULL,0,'alejandro','3503752816',1,1,'2026-05-08 16:14:19','2026-05-08 16:23:21','',0,'2026-05-08 16:14:19','2026-05-08 16:23:21'),(29,1,7,'PK-260508-0007','PK-260508-0007','YDY84F','moto','paid',2,0,NULL,0,'alejandro','3503752816',1,1,'2026-05-08 16:23:31','2026-05-08 16:38:54','',0,'2026-05-08 16:23:31','2026-05-08 16:38:54'),(30,1,7,'PK-260508-0008','PK-260508-0008','HUF','moto','active',45,0,NULL,0,NULL,NULL,1,NULL,'2026-05-08 16:39:20',NULL,NULL,0,'2026-05-08 16:39:20','2026-05-08 16:39:20'),(31,1,10,'PK-260508-0009','PK-260508-0009','GDFGDF','moto','active',43,0,NULL,0,NULL,NULL,1,NULL,'2026-05-08 16:39:27',NULL,NULL,0,'2026-05-08 16:39:27','2026-05-08 16:39:27'),(32,1,7,'PK-260508-0010','PK-260508-0010','FDASFAS','moto','paid',23,0,NULL,0,NULL,NULL,1,1,'2026-05-08 16:42:50','2026-05-08 16:43:45','',0,'2026-05-08 16:42:50','2026-05-08 16:43:45'),(33,1,7,'PK-260508-0011','PK-260508-0011','FFS','moto','paid',3,0,NULL,0,NULL,NULL,1,1,'2026-05-08 16:46:48','2026-05-08 21:47:49','',0,'2026-05-08 16:46:48','2026-05-08 21:48:15'),(34,1,7,'PK-260508-0012','PK-260508-0012','GDSGD','moto','paid',432,0,NULL,0,NULL,NULL,1,1,'2026-05-08 16:47:10','2026-05-08 16:47:22','',0,'2026-05-08 16:47:10','2026-05-08 16:47:22'),(35,1,10,'PK-260508-0013','PK-260508-0013','FASFAS','moto','paid',3,0,NULL,0,NULL,NULL,1,1,'2026-05-08 16:53:55','2026-05-08 19:26:57','',0,'2026-05-08 16:53:55','2026-05-08 19:26:57'),(36,1,7,'PK-260508-0014','PK-260508-0014','HDHD','moto','active',54,0,NULL,0,NULL,NULL,1,NULL,'2026-05-08 19:46:20',NULL,NULL,0,'2026-05-08 19:46:20','2026-05-08 19:46:20'),(37,1,10,'PK-260508-0015','PK-260508-0015','YDY84F','moto','paid',4,0,NULL,0,'alejandro','3503752816',1,1,'2026-05-08 20:05:48','2026-05-08 20:09:28','',0,'2026-05-08 20:05:48','2026-05-08 20:09:28'),(38,1,7,'PK-260508-0016','PK-260508-0016','JFHJF','moto','active',44,0,NULL,0,NULL,NULL,1,NULL,'2026-05-08 20:11:26',NULL,NULL,0,'2026-05-08 20:11:26','2026-05-08 20:11:26'),(39,1,7,'PK-260508-0017','PK-260508-0017','HHR','moto','active',54,0,NULL,0,NULL,NULL,1,NULL,'2026-05-08 20:11:34',NULL,NULL,0,'2026-05-08 20:11:34','2026-05-08 20:11:34'),(40,1,7,'PK-260508-0018','PK-260508-0018','FSAF','moto','active',4,0,NULL,0,NULL,NULL,1,NULL,'2026-05-08 20:31:30',NULL,NULL,0,'2026-05-08 20:31:30','2026-05-08 20:31:30'),(41,1,7,'PK-260508-0019','PK-260508-0019','FSFD','moto','active',54,0,NULL,0,NULL,NULL,1,NULL,'2026-05-08 20:31:50',NULL,NULL,0,'2026-05-08 20:31:50','2026-05-08 20:31:50'),(42,1,10,'PK-260508-0020','PK-260508-0020','YDY84F','moto','paid',4,0,NULL,0,'alejandro','3503752816',1,3,'2026-05-08 20:31:55','2026-05-08 21:07:36','',0,'2026-05-08 20:31:55','2026-05-08 21:07:36'),(43,1,10,'PK-260508-0021','PK-260508-0021','DASDASF','moto','active',4,0,NULL,0,NULL,NULL,1,NULL,'2026-05-08 20:37:40',NULL,NULL,0,'2026-05-08 20:37:40','2026-05-08 20:37:40'),(44,1,7,'PK-260508-0022','PK-260508-0022','DASD','moto','active',4,0,NULL,0,NULL,NULL,1,NULL,'2026-05-08 20:37:49',NULL,NULL,0,'2026-05-08 20:37:49','2026-05-08 20:37:49'),(45,1,7,'PK-260508-0023','PK-260508-0023','JUHD54','moto','active',54,0,NULL,0,NULL,NULL,1,NULL,'2026-05-08 21:03:33',NULL,NULL,0,'2026-05-08 21:03:33','2026-05-08 21:03:33'),(46,1,14,'PK-260508-0024','PK-260508-0024','YDY84F','bicicleta','paid',5,0,NULL,0,'alejandro','3503752816',3,1,'2026-05-08 21:07:45','2026-05-08 21:31:46','',0,'2026-05-08 21:07:45','2026-05-08 21:31:46'),(47,1,7,'PK-260508-0025','PK-260508-0025','DADSDA','moto','active',54,0,NULL,0,NULL,NULL,1,NULL,'2026-05-08 21:34:11',NULL,NULL,0,'2026-05-08 21:34:11','2026-05-08 21:34:11'),(48,1,10,'PK-260508-0026','PK-260508-0026','YDY','moto','active',54,1,'12',2000,NULL,NULL,1,NULL,'2026-05-08 21:34:59',NULL,NULL,0,'2026-05-08 21:34:59','2026-05-08 21:34:59'),(49,1,7,'PK-260508-0027','PK-260508-0027','YDY84F','moto','paid',54,1,'12',2000,'alejandro','3503752816',1,1,'2026-05-08 21:46:27','2026-05-08 21:47:04','',0,'2026-05-08 21:46:27','2026-05-08 21:47:04'),(50,1,7,'PK-260508-0028','PK-260508-0028','HFHF','moto','paid',54,0,NULL,0,NULL,NULL,1,1,'2026-05-08 21:48:34','2026-05-08 21:48:56','',0,'2026-05-08 21:48:34','2026-05-08 21:48:56'),(51,1,10,'PK-260508-0029','PK-260508-0029','FAFFAS','moto','paid',4,0,NULL,0,NULL,NULL,1,1,'2026-05-08 22:08:55','2026-05-08 22:09:24','',0,'2026-05-08 22:08:55','2026-05-08 22:09:24'),(52,1,10,'PK-260508-0030','PK-260508-0030','YDHAD','moto','active',5,0,NULL,0,'aLEJANDREO','3142843266',1,NULL,'2026-05-08 22:10:42',NULL,NULL,0,'2026-05-08 22:10:42','2026-05-08 22:10:42'),(53,1,10,'PK-260508-0031','PK-260508-0031','YDY84F','moto','paid',54,1,'12',2000,'alejandro','3503752816',1,1,'2026-05-08 22:16:40','2026-05-08 22:16:57','',0,'2026-05-08 22:16:40','2026-05-08 22:16:57');
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
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (15,17,1,'nequi',8000,0,0,0,8000,8000,0,'2026-05-07 23:24:05','paid',NULL,'2026-05-07 23:24:05','2026-05-07 23:24:05'),(16,22,1,'efectivo',10000,0,0,0,10000,10000,0,'2026-05-07 23:39:34','paid',NULL,'2026-05-07 23:39:20','2026-05-07 23:39:34'),(17,23,1,'efectivo',500,0,0,0,500,500,0,'2026-05-08 15:41:30','paid',NULL,'2026-05-08 15:41:30','2026-05-08 15:41:30'),(18,24,1,'efectivo',5000,0,0,0,5000,5000,0,'2026-05-08 15:42:15','paid',NULL,'2026-05-08 15:42:15','2026-05-08 15:42:15'),(19,25,1,'efectivo',35000,0,0,0,35000,35000,0,'2026-05-08 16:10:53','paid',NULL,'2026-05-08 16:10:53','2026-05-08 16:10:53'),(20,19,1,'efectivo',8000,0,0,0,8000,8000,0,'2026-05-08 16:11:46','paid',NULL,'2026-05-08 16:11:46','2026-05-08 16:11:46'),(21,28,1,'efectivo',8000,0,0,0,8000,8000,0,'2026-05-08 16:23:21','paid',NULL,'2026-05-08 16:23:21','2026-05-08 16:23:21'),(22,29,1,'efectivo',8000,0,0,0,8000,8000,0,'2026-05-08 16:38:54','paid',NULL,'2026-05-08 16:38:54','2026-05-08 16:38:54'),(23,32,1,'efectivo',500,0,0,0,500,500,0,'2026-05-08 16:43:45','paid',NULL,'2026-05-08 16:43:45','2026-05-08 16:43:45'),(24,34,1,'efectivo',500,0,0,0,500,500,0,'2026-05-08 16:47:22','paid',NULL,'2026-05-08 16:47:22','2026-05-08 16:47:22'),(25,35,1,'efectivo',195000,0,0,0,195000,195000,0,'2026-05-08 19:26:57','paid',NULL,'2026-05-08 19:26:57','2026-05-08 19:26:57'),(26,27,1,'efectivo',270000,0,0,0,270000,270000,0,'2026-05-08 19:46:48','paid',NULL,'2026-05-08 19:46:48','2026-05-08 19:46:48'),(27,26,1,'efectivo',8000,0,0,0,8000,8000,0,'2026-05-08 19:47:22','paid',NULL,'2026-05-08 19:47:22','2026-05-08 19:47:22'),(28,37,1,'efectivo',5000,0,0,0,5000,5000,0,'2026-05-08 20:09:28','paid',NULL,'2026-05-08 20:09:28','2026-05-08 20:09:28'),(29,42,3,'efectivo',45000,0,0,0,45000,45000,0,'2026-05-08 21:07:36','paid',NULL,'2026-05-08 21:07:36','2026-05-08 21:07:36'),(30,46,1,'efectivo',12000,0,0,0,12000,12000,0,'2026-05-08 21:31:46','paid',NULL,'2026-05-08 21:31:46','2026-05-08 21:31:46'),(31,49,1,'efectivo',2500,0,0,0,2500,2500,0,'2026-05-08 21:47:04','paid',NULL,'2026-05-08 21:47:04','2026-05-08 21:47:04'),(32,33,1,'efectivo',8000,0,0,0,8000,8500,500,'2026-05-08 21:48:15','paid',NULL,'2026-05-08 21:47:49','2026-05-08 21:48:15'),(33,50,1,'efectivo',500,0,0,0,500,500,0,'2026-05-08 21:48:56','paid',NULL,'2026-05-08 21:48:56','2026-05-08 21:48:56'),(34,51,1,'efectivo',5000,0,0,0,5000,5000,0,'2026-05-08 22:09:24','paid',NULL,'2026-05-08 22:09:24','2026-05-08 22:09:24'),(35,53,1,'nequi',7000,0,0,0,7000,7000,0,'2026-05-08 22:16:57','paid',NULL,'2026-05-08 22:16:57','2026-05-08 22:16:57');
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
-- Table structure for table `portal_sync_jobs`
--

DROP TABLE IF EXISTS `portal_sync_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `portal_sync_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parking_ticket_id` bigint unsigned DEFAULT NULL,
  `ticket_code` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_type` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sync',
  `payload` json NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `attempts` int unsigned NOT NULL DEFAULT '0',
  `last_error` text COLLATE utf8mb4_unicode_ci,
  `synced_at` timestamp NULL DEFAULT NULL,
  `available_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `portal_sync_jobs_ticket_code_unique` (`ticket_code`),
  KEY `portal_sync_jobs_parking_ticket_id_foreign` (`parking_ticket_id`),
  KEY `portal_sync_jobs_status_index` (`status`),
  KEY `portal_sync_jobs_available_at_index` (`available_at`),
  CONSTRAINT `portal_sync_jobs_parking_ticket_id_foreign` FOREIGN KEY (`parking_ticket_id`) REFERENCES `parking_tickets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portal_sync_jobs`
--

/*!40000 ALTER TABLE `portal_sync_jobs` DISABLE KEYS */;
INSERT INTO `portal_sync_jobs` VALUES (1,37,'PK-260508-0015','salida','{\"token\": \"cambia-este-token\", \"ticket\": {\"tax\": 0, \"plate\": \"YDY84F\", \"total\": 5000, \"status\": \"paid\", \"barcode\": \"PK-260508-0015\", \"paid_at\": \"2026-05-08 15:09:28\", \"discount\": 0, \"subtotal\": 5000, \"exit_time\": \"2026-05-08 15:09:28\", \"site_name\": \"Sede Principal - Centro\", \"surcharge\": 0, \"synced_at\": \"2026-05-08 15:09:28\", \"entry_time\": \"2026-05-08 15:05:48\", \"tariff_name\": \"Gimnasio\", \"tariff_type\": \"convenio\", \"ticket_code\": \"PK-260508-0015\", \"vehicle_type\": \"moto\", \"payment_method\": \"efectivo\", \"payment_status\": \"paid\", \"location_number\": 4, \"duration_minutes\": 3, \"source_ticket_id\": \"37\"}, \"event_time\": \"2026-05-08 15:09:28\", \"event_type\": \"salida\"}','synced',2,NULL,'2026-05-08 20:29:18','2026-05-08 20:28:41','2026-05-08 20:05:48','2026-05-08 20:29:18'),(2,38,'PK-260508-0016','entrada','{\"token\": \"cambia-este-token\", \"ticket\": {\"tax\": 0, \"plate\": \"JFHJF\", \"total\": 0, \"status\": \"active\", \"barcode\": \"PK-260508-0016\", \"paid_at\": null, \"discount\": 0, \"subtotal\": 0, \"exit_time\": null, \"site_name\": \"Sede Principal - Centro\", \"surcharge\": 0, \"synced_at\": \"2026-05-08 15:11:26\", \"entry_time\": \"2026-05-08 15:11:26\", \"tariff_name\": \"Minuto\", \"tariff_type\": \"normal\", \"ticket_code\": \"PK-260508-0016\", \"vehicle_type\": \"moto\", \"payment_method\": null, \"payment_status\": null, \"location_number\": 44, \"duration_minutes\": 0, \"source_ticket_id\": \"38\"}, \"event_time\": \"2026-05-08 15:11:26\", \"event_type\": \"entrada\"}','synced',2,NULL,'2026-05-08 20:29:18','2026-05-08 20:28:41','2026-05-08 20:11:26','2026-05-08 20:29:18'),(3,39,'PK-260508-0017','entrada','{\"token\": \"cambia-este-token\", \"ticket\": {\"tax\": 0, \"plate\": \"HHR\", \"total\": 0, \"status\": \"active\", \"barcode\": \"PK-260508-0017\", \"paid_at\": null, \"discount\": 0, \"subtotal\": 0, \"exit_time\": null, \"site_name\": \"Sede Principal - Centro\", \"surcharge\": 0, \"synced_at\": \"2026-05-08 15:11:34\", \"entry_time\": \"2026-05-08 15:11:34\", \"tariff_name\": \"Minuto\", \"tariff_type\": \"normal\", \"ticket_code\": \"PK-260508-0017\", \"vehicle_type\": \"moto\", \"payment_method\": null, \"payment_status\": null, \"location_number\": 54, \"duration_minutes\": 0, \"source_ticket_id\": \"39\"}, \"event_time\": \"2026-05-08 15:11:34\", \"event_type\": \"entrada\"}','synced',2,NULL,'2026-05-08 20:29:18','2026-05-08 20:28:41','2026-05-08 20:11:34','2026-05-08 20:29:18'),(4,40,'PK-260508-0018','entrada','{\"token\": \"cambia-este-token\", \"ticket\": {\"tax\": 0, \"plate\": \"FSAF\", \"total\": 0, \"status\": \"active\", \"barcode\": \"PK-260508-0018\", \"paid_at\": null, \"discount\": 0, \"subtotal\": 0, \"exit_time\": null, \"site_name\": \"Sede Principal - Centro\", \"surcharge\": 0, \"synced_at\": \"2026-05-08 15:31:30\", \"entry_time\": \"2026-05-08 15:31:30\", \"tariff_name\": \"Minuto\", \"tariff_type\": \"normal\", \"ticket_code\": \"PK-260508-0018\", \"vehicle_type\": \"moto\", \"payment_method\": null, \"payment_status\": null, \"location_number\": 4, \"duration_minutes\": 0, \"source_ticket_id\": \"40\"}, \"event_time\": \"2026-05-08 15:31:30\", \"event_type\": \"entrada\"}','synced',1,NULL,'2026-05-08 20:35:10','2026-05-08 20:31:30','2026-05-08 20:31:30','2026-05-08 20:35:10'),(5,41,'PK-260508-0019','entrada','{\"token\": \"cambia-este-token\", \"ticket\": {\"tax\": 0, \"plate\": \"FSFD\", \"total\": 0, \"status\": \"active\", \"barcode\": \"PK-260508-0019\", \"paid_at\": null, \"discount\": 0, \"subtotal\": 0, \"exit_time\": null, \"site_name\": \"Sede Principal - Centro\", \"surcharge\": 0, \"synced_at\": \"2026-05-08 15:31:50\", \"entry_time\": \"2026-05-08 15:31:50\", \"tariff_name\": \"Minuto\", \"tariff_type\": \"normal\", \"ticket_code\": \"PK-260508-0019\", \"vehicle_type\": \"moto\", \"payment_method\": null, \"payment_status\": null, \"location_number\": 54, \"duration_minutes\": 0, \"source_ticket_id\": \"41\"}, \"event_time\": \"2026-05-08 15:31:50\", \"event_type\": \"entrada\"}','synced',1,NULL,'2026-05-08 20:35:10','2026-05-08 20:31:50','2026-05-08 20:31:50','2026-05-08 20:35:10'),(6,42,'PK-260508-0020','salida','{\"token\": \"cambia-este-token\", \"ticket\": {\"tax\": 0, \"plate\": \"YDY84F\", \"total\": 45000, \"status\": \"paid\", \"barcode\": \"PK-260508-0020\", \"paid_at\": \"2026-05-08 16:07:36\", \"discount\": 0, \"subtotal\": 45000, \"exit_time\": \"2026-05-08 16:07:36\", \"site_name\": \"Sede Principal - Centro\", \"surcharge\": 0, \"synced_at\": \"2026-05-08 16:07:36\", \"entry_time\": \"2026-05-08 15:31:55\", \"tariff_name\": \"Gimnasio\", \"tariff_type\": \"convenio\", \"ticket_code\": \"PK-260508-0020\", \"vehicle_type\": \"moto\", \"payment_method\": \"efectivo\", \"payment_status\": \"paid\", \"location_number\": 4, \"duration_minutes\": 35, \"source_ticket_id\": \"42\"}, \"event_time\": \"2026-05-08 16:07:36\", \"event_type\": \"salida\"}','synced',2,NULL,'2026-05-08 21:08:06','2026-05-08 21:07:36','2026-05-08 20:31:55','2026-05-08 21:08:06'),(7,43,'PK-260508-0021','entrada','{\"token\": \"cambia-este-token\", \"ticket\": {\"tax\": 0, \"plate\": \"DASDASF\", \"total\": 0, \"status\": \"active\", \"barcode\": \"PK-260508-0021\", \"paid_at\": null, \"discount\": 0, \"subtotal\": 0, \"exit_time\": null, \"site_name\": \"Sede Principal - Centro\", \"surcharge\": 0, \"synced_at\": \"2026-05-08 15:37:40\", \"entry_time\": \"2026-05-08 15:37:40\", \"tariff_name\": \"Gimnasio\", \"tariff_type\": \"convenio\", \"ticket_code\": \"PK-260508-0021\", \"vehicle_type\": \"moto\", \"payment_method\": null, \"payment_status\": null, \"location_number\": 4, \"duration_minutes\": 0, \"source_ticket_id\": \"43\"}, \"event_time\": \"2026-05-08 15:37:40\", \"event_type\": \"entrada\"}','synced',1,NULL,'2026-05-08 20:39:00','2026-05-08 20:37:40','2026-05-08 20:37:40','2026-05-08 20:39:00'),(8,44,'PK-260508-0022','entrada','{\"token\": \"cambia-este-token\", \"ticket\": {\"tax\": 0, \"plate\": \"DASD\", \"total\": 0, \"status\": \"active\", \"barcode\": \"PK-260508-0022\", \"paid_at\": null, \"discount\": 0, \"subtotal\": 0, \"exit_time\": null, \"site_name\": \"Sede Principal - Centro\", \"surcharge\": 0, \"synced_at\": \"2026-05-08 15:37:49\", \"entry_time\": \"2026-05-08 15:37:49\", \"tariff_name\": \"Minuto\", \"tariff_type\": \"normal\", \"ticket_code\": \"PK-260508-0022\", \"vehicle_type\": \"moto\", \"payment_method\": null, \"payment_status\": null, \"location_number\": 4, \"duration_minutes\": 0, \"source_ticket_id\": \"44\"}, \"event_time\": \"2026-05-08 15:37:49\", \"event_type\": \"entrada\"}','synced',1,NULL,'2026-05-08 20:39:00','2026-05-08 20:37:49','2026-05-08 20:37:49','2026-05-08 20:39:00'),(9,45,'PK-260508-0023','entrada','{\"token\": \"cambia-este-token\", \"ticket\": {\"tax\": 0, \"plate\": \"JUHD54\", \"total\": 0, \"status\": \"active\", \"barcode\": \"PK-260508-0023\", \"paid_at\": null, \"discount\": 0, \"subtotal\": 0, \"exit_time\": null, \"site_name\": \"Sede Principal - Centro\", \"surcharge\": 0, \"synced_at\": \"2026-05-08 16:03:33\", \"entry_time\": \"2026-05-08 16:03:33\", \"tariff_name\": \"Minuto\", \"tariff_type\": \"normal\", \"ticket_code\": \"PK-260508-0023\", \"vehicle_type\": \"moto\", \"payment_method\": null, \"payment_status\": null, \"location_number\": 54, \"duration_minutes\": 0, \"source_ticket_id\": \"45\"}, \"event_time\": \"2026-05-08 16:03:33\", \"event_type\": \"entrada\"}','synced',1,NULL,'2026-05-08 21:04:17','2026-05-08 21:03:33','2026-05-08 21:03:33','2026-05-08 21:04:17'),(10,46,'PK-260508-0024','salida','{\"token\": \"cambia-este-token\", \"ticket\": {\"tax\": 0, \"plate\": \"YDY84F\", \"total\": 12000, \"status\": \"paid\", \"barcode\": \"PK-260508-0024\", \"paid_at\": \"2026-05-08 16:31:46\", \"discount\": 0, \"subtotal\": 12000, \"exit_time\": \"2026-05-08 16:31:46\", \"site_name\": \"Sede Principal - Centro\", \"surcharge\": 0, \"synced_at\": \"2026-05-08 16:31:46\", \"entry_time\": \"2026-05-08 16:07:45\", \"tariff_name\": \"min bici\", \"tariff_type\": \"normal\", \"ticket_code\": \"PK-260508-0024\", \"vehicle_type\": \"bicicleta\", \"payment_method\": \"efectivo\", \"payment_status\": \"paid\", \"location_number\": 5, \"duration_minutes\": 24, \"source_ticket_id\": \"46\"}, \"event_time\": \"2026-05-08 16:31:46\", \"event_type\": \"salida\"}','synced',2,NULL,'2026-05-08 21:40:03','2026-05-08 21:31:46','2026-05-08 21:07:45','2026-05-08 21:40:03'),(11,47,'PK-260508-0025','entrada','{\"token\": \"cambia-este-token\", \"ticket\": {\"tax\": 0, \"plate\": \"DADSDA\", \"total\": 0, \"status\": \"active\", \"barcode\": \"PK-260508-0025\", \"paid_at\": null, \"discount\": 0, \"subtotal\": 0, \"exit_time\": null, \"site_name\": \"Sede Principal - Centro\", \"surcharge\": 0, \"synced_at\": \"2026-05-08 16:34:11\", \"entry_time\": \"2026-05-08 16:34:11\", \"tariff_name\": \"Minuto\", \"tariff_type\": \"normal\", \"ticket_code\": \"PK-260508-0025\", \"vehicle_type\": \"moto\", \"payment_method\": null, \"payment_status\": null, \"location_number\": 54, \"duration_minutes\": 0, \"source_ticket_id\": \"47\"}, \"event_time\": \"2026-05-08 16:34:11\", \"event_type\": \"entrada\"}','synced',1,NULL,'2026-05-08 21:40:03','2026-05-08 21:34:11','2026-05-08 21:34:11','2026-05-08 21:40:03'),(12,48,'PK-260508-0026','entrada','{\"token\": \"cambia-este-token\", \"ticket\": {\"tax\": 0, \"plate\": \"YDY\", \"total\": 0, \"status\": \"active\", \"barcode\": \"PK-260508-0026\", \"paid_at\": null, \"discount\": 0, \"subtotal\": 0, \"exit_time\": null, \"site_name\": \"Sede Principal - Centro\", \"surcharge\": 0, \"synced_at\": \"2026-05-08 16:34:59\", \"entry_time\": \"2026-05-08 16:34:59\", \"tariff_name\": \"Gimnasio\", \"tariff_type\": \"convenio\", \"ticket_code\": \"PK-260508-0026\", \"vehicle_type\": \"moto\", \"payment_method\": null, \"payment_status\": null, \"location_number\": 54, \"duration_minutes\": 0, \"source_ticket_id\": \"48\"}, \"event_time\": \"2026-05-08 16:34:59\", \"event_type\": \"entrada\"}','synced',1,NULL,'2026-05-08 21:40:03','2026-05-08 21:34:59','2026-05-08 21:34:59','2026-05-08 21:40:03'),(13,49,'PK-260508-0027','salida','{\"token\": \"cambia-este-token\", \"ticket\": {\"tax\": 0, \"plate\": \"YDY84F\", \"total\": 2500, \"status\": \"paid\", \"barcode\": \"PK-260508-0027\", \"paid_at\": \"2026-05-08 16:47:04\", \"discount\": 0, \"subtotal\": 2500, \"exit_time\": \"2026-05-08 16:47:04\", \"site_name\": \"Sede Principal - Centro\", \"surcharge\": 0, \"synced_at\": \"2026-05-08 16:47:04\", \"entry_time\": \"2026-05-08 16:46:27\", \"tariff_name\": \"Minuto\", \"tariff_type\": \"normal\", \"ticket_code\": \"PK-260508-0027\", \"vehicle_type\": \"moto\", \"payment_method\": \"efectivo\", \"payment_status\": \"paid\", \"location_number\": 54, \"duration_minutes\": 1, \"source_ticket_id\": \"49\"}, \"event_time\": \"2026-05-08 16:47:04\", \"event_type\": \"salida\"}','synced',1,NULL,'2026-05-08 21:53:57','2026-05-08 21:47:04','2026-05-08 21:46:27','2026-05-08 21:53:57'),(14,33,'PK-260508-0011','pago_regularizado','{\"token\": \"cambia-este-token\", \"ticket\": {\"tax\": 0, \"plate\": \"FFS\", \"total\": 8000, \"status\": \"paid\", \"barcode\": \"PK-260508-0011\", \"paid_at\": \"2026-05-08 16:48:15\", \"discount\": 0, \"subtotal\": 8000, \"exit_time\": \"2026-05-08 16:47:49\", \"site_name\": \"Sede Principal - Centro\", \"surcharge\": 0, \"synced_at\": \"2026-05-08 16:48:15\", \"entry_time\": \"2026-05-08 11:46:48\", \"tariff_name\": \"Minuto\", \"tariff_type\": \"normal\", \"ticket_code\": \"PK-260508-0011\", \"vehicle_type\": \"moto\", \"payment_method\": \"efectivo\", \"payment_status\": \"paid\", \"location_number\": 3, \"duration_minutes\": 301, \"source_ticket_id\": \"33\"}, \"event_time\": \"2026-05-08 16:48:15\", \"event_type\": \"pago_regularizado\"}','synced',1,NULL,'2026-05-08 21:53:57','2026-05-08 21:48:15','2026-05-08 21:47:49','2026-05-08 21:53:57'),(15,50,'PK-260508-0028','salida','{\"token\": \"cambia-este-token\", \"ticket\": {\"tax\": 0, \"plate\": \"HFHF\", \"total\": 500, \"status\": \"paid\", \"barcode\": \"PK-260508-0028\", \"paid_at\": \"2026-05-08 16:48:56\", \"discount\": 0, \"subtotal\": 500, \"exit_time\": \"2026-05-08 16:48:56\", \"site_name\": \"Sede Principal - Centro\", \"surcharge\": 0, \"synced_at\": \"2026-05-08 16:48:56\", \"entry_time\": \"2026-05-08 16:48:34\", \"tariff_name\": \"Minuto\", \"tariff_type\": \"normal\", \"ticket_code\": \"PK-260508-0028\", \"vehicle_type\": \"moto\", \"payment_method\": \"efectivo\", \"payment_status\": \"paid\", \"location_number\": 54, \"duration_minutes\": 1, \"source_ticket_id\": \"50\"}, \"event_time\": \"2026-05-08 16:48:56\", \"event_type\": \"salida\"}','synced',1,NULL,'2026-05-08 21:53:57','2026-05-08 21:48:56','2026-05-08 21:48:34','2026-05-08 21:53:57'),(16,51,'PK-260508-0029','salida','{\"token\": \"cambia-este-token\", \"ticket\": {\"tax\": 0, \"plate\": \"FAFFAS\", \"total\": 5000, \"status\": \"paid\", \"barcode\": \"PK-260508-0029\", \"paid_at\": \"2026-05-08 17:09:24\", \"discount\": 0, \"subtotal\": 5000, \"exit_time\": \"2026-05-08 17:09:24\", \"site_name\": \"Sede Principal - Centro\", \"surcharge\": 0, \"synced_at\": \"2026-05-08 17:09:24\", \"entry_time\": \"2026-05-08 17:08:55\", \"tariff_name\": \"Gimnasio\", \"tariff_type\": \"convenio\", \"ticket_code\": \"PK-260508-0029\", \"vehicle_type\": \"moto\", \"payment_method\": \"efectivo\", \"payment_status\": \"paid\", \"location_number\": 4, \"duration_minutes\": 1, \"source_ticket_id\": \"51\"}, \"event_time\": \"2026-05-08 17:09:24\", \"event_type\": \"salida\"}','synced',2,NULL,'2026-05-08 22:16:23','2026-05-08 22:09:24','2026-05-08 22:08:55','2026-05-08 22:16:23'),(17,52,'PK-260508-0030','entrada','{\"token\": \"cambia-este-token\", \"ticket\": {\"tax\": 0, \"plate\": \"YDHAD\", \"total\": 0, \"status\": \"active\", \"barcode\": \"PK-260508-0030\", \"paid_at\": null, \"discount\": 0, \"subtotal\": 0, \"exit_time\": null, \"site_name\": \"Sede Principal - Centro\", \"surcharge\": 0, \"synced_at\": \"2026-05-08 17:10:42\", \"entry_time\": \"2026-05-08 17:10:42\", \"tariff_name\": \"Gimnasio\", \"tariff_type\": \"convenio\", \"ticket_code\": \"PK-260508-0030\", \"vehicle_type\": \"moto\", \"payment_method\": null, \"payment_status\": null, \"location_number\": 5, \"duration_minutes\": 0, \"source_ticket_id\": \"52\"}, \"event_time\": \"2026-05-08 17:10:42\", \"event_type\": \"entrada\"}','synced',1,NULL,'2026-05-08 22:16:23','2026-05-08 22:10:42','2026-05-08 22:10:42','2026-05-08 22:16:23'),(18,53,'PK-260508-0031','salida','{\"token\": \"cambia-este-token\", \"ticket\": {\"tax\": 0, \"plate\": \"YDY84F\", \"total\": 7000, \"status\": \"paid\", \"barcode\": \"PK-260508-0031\", \"paid_at\": \"2026-05-08 17:16:57\", \"discount\": 0, \"subtotal\": 7000, \"exit_time\": \"2026-05-08 17:16:57\", \"site_name\": \"Sede Principal - Centro\", \"surcharge\": 0, \"synced_at\": \"2026-05-08 17:16:57\", \"entry_time\": \"2026-05-08 17:16:40\", \"tariff_name\": \"Gimnasio\", \"tariff_type\": \"convenio\", \"ticket_code\": \"PK-260508-0031\", \"vehicle_type\": \"moto\", \"payment_method\": \"nequi\", \"payment_status\": \"paid\", \"location_number\": 54, \"duration_minutes\": 1, \"source_ticket_id\": \"53\"}, \"event_time\": \"2026-05-08 17:16:57\", \"event_type\": \"salida\"}','synced',1,NULL,'2026-05-08 22:17:11','2026-05-08 22:16:57','2026-05-08 22:16:40','2026-05-08 22:17:11');
/*!40000 ALTER TABLE `portal_sync_jobs` ENABLE KEYS */;

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
  `locker_fee` int unsigned NOT NULL DEFAULT '0',
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
INSERT INTO `sites` VALUES (1,'Sede Principal - Centro','CENTRO',50,2000,1,'2026-04-16 18:27:13','2026-05-08 21:31:32'),(2,'Sede Norte','NORTE',35,0,1,'2026-04-16 18:27:13','2026-04-16 18:27:13');
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
  `tariff_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `threshold_minutes` int unsigned DEFAULT NULL,
  `max_minutes` int unsigned DEFAULT NULL,
  `full_rate` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tariff_profiles_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tariff_profiles`
--

/*!40000 ALTER TABLE `tariff_profiles` DISABLE KEYS */;
INSERT INTO `tariff_profiles` VALUES (7,'Minuto','minuto','moto','minute','Cobro por minuto','minute',1,500,0,0,NULL,0,15,0,NULL,0,0,0,0,0,0.00,1,'2026-05-07 23:15:09','2026-05-07 23:15:09','normal',NULL,NULL,NULL),(8,'Plena','plena','moto','fixed','Tarifa plena','minute',1,0,1,0,NULL,0,15,0,NULL,0,0,0,0,0,0.00,1,'2026-05-07 23:15:25','2026-05-07 23:15:25','plena',5,NULL,8000),(10,'Gimnasio','gimnasio','moto','fixed','Convenio por tiempo','minute',1,5000,0,1,1,0,15,0,NULL,0,0,0,0,0,0.00,1,'2026-05-07 23:31:55','2026-05-07 23:31:55','convenio',NULL,4,NULL),(14,'min bici','min-bici','bicicleta','minute','Cobro por minuto','minute',1,500,0,0,NULL,0,15,0,NULL,0,0,0,0,0,0.00,1,'2026-05-08 21:05:56','2026-05-08 21:05:56','normal',NULL,NULL,NULL);
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
INSERT INTO `users` VALUES (1,1,'admin','Administrador General','admin@parkmanager.com','admin','Administrativo',1,NULL,'$2y$12$.z5/YCCGWRFKRMDysjTowu8qc3exoJh7dfwZyzMOwBsEXTWVDWTQK',NULL,'2026-04-16 18:27:13','2026-04-29 19:20:21'),(2,1,'operador','Carlos Op.','operador@sede.com','operario','Manana (06:00 - 14:00)',1,NULL,'$2y$12$EIp7Z4BR.IFWPrW/VJDmpO00r21qg0lk8a.zQB.29H5stqDs8JtzW',NULL,'2026-04-16 18:27:13','2026-04-16 18:27:13'),(3,1,'ana.caja','Ana Martinez','ana@parkmanager.com','operario','Manana (06:00 - 14:00)',1,NULL,'$2y$12$XYNxeS0ZsCNjIdnaaFrS1.WsoojxVqNHEW3EZSGwaaTkrsG.VNUmi',NULL,'2026-04-16 18:27:14','2026-05-08 21:07:04'),(4,1,'roberto.admin','Roberto Gomez','roberto@parkmanager.com','admin','Tarde (14:00 - 22:00)',1,NULL,'$2y$12$g5cVpSfqVSRGCq5bwemoHuePTNOpyf3U3J.Y1o/E.dKwP9caldfcC',NULL,'2026-04-16 18:27:14','2026-04-16 18:27:14'),(5,1,'luis.op','Luis Fernando','luis@parkmanager.com','operario','Sin turno',0,NULL,'$2y$12$I7vlisFI6RuNDR/wnKg39ejkQ4g9imm5dJhQehpyjidnLSsyTwDni',NULL,'2026-04-16 18:27:14','2026-04-16 18:27:14');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-08 17:20:23
