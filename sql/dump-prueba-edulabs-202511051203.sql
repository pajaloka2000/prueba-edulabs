-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: localhost    Database: prueba-edulabs
-- ------------------------------------------------------
-- Server version	8.0.41

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
-- Table structure for table `almacenamiento`
--

DROP TABLE IF EXISTS `almacenamiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `almacenamiento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `archivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `peso` float NOT NULL DEFAULT '0',
  `usuario_id` int NOT NULL,
  `fecha_subida` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_created_at` (`fecha_subida`),
  CONSTRAINT `almacenamiento_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `almacenamiento`
--

LOCK TABLES `almacenamiento` WRITE;
/*!40000 ALTER TABLE `almacenamiento` DISABLE KEYS */;
INSERT INTO `almacenamiento` VALUES (1,' 1762209072_4022002096.mp4',0.42,2,'2025-11-05 15:25:21'),(2,' Victoria537.zip',1.5,2,'2025-11-05 15:47:28'),(3,' Victoria537.zip',1.5,2,'2025-11-05 15:58:08');
/*!40000 ALTER TABLE `almacenamiento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `extensiones_prohibidas`
--

DROP TABLE IF EXISTS `extensiones_prohibidas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `extensiones_prohibidas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `extension` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `extension` (`extension`),
  KEY `idx_extension` (`extension`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `extensiones_prohibidas`
--

LOCK TABLES `extensiones_prohibidas` WRITE;
/*!40000 ALTER TABLE `extensiones_prohibidas` DISABLE KEYS */;
INSERT INTO `extensiones_prohibidas` VALUES (1,'exe','Ejecutable de Windows','2025-11-05 15:41:49'),(2,'bat','Archivo por lotes de Windows','2025-11-05 15:41:49'),(3,'cmd','Script de comandos de Windows','2025-11-05 15:41:49'),(4,'com','Ejecutable de DOS','2025-11-05 15:41:49'),(5,'pif','Información de programa','2025-11-05 15:41:49'),(6,'scr','Protector de pantalla','2025-11-05 15:41:49'),(7,'vbs','Visual Basic Script','2025-11-05 15:41:49'),(8,'js','JavaScript','2025-11-05 15:41:49'),(9,'jse','JavaScript codificado','2025-11-05 15:41:49'),(10,'wsf','Windows Script File','2025-11-05 15:41:49'),(11,'wsh','Windows Script Host','2025-11-05 15:41:49'),(12,'msi','Instalador de Windows','2025-11-05 15:41:49'),(13,'msp','Parche de instalador de Windows','2025-11-05 15:41:49'),(14,'scf','Comando de Windows Explorer','2025-11-05 15:41:49'),(15,'lnk','Acceso directo de Windows','2025-11-05 15:41:49'),(16,'inf','Archivo de información de instalación','2025-11-05 15:41:49'),(17,'reg','Archivo de registro de Windows','2025-11-05 15:41:49'),(18,'php','PHP Script','2025-11-05 15:41:49'),(19,'php3','PHP Script','2025-11-05 15:41:49'),(20,'php4','PHP Script','2025-11-05 15:41:49'),(21,'php5','PHP Script','2025-11-05 15:41:49'),(22,'phtml','PHP HTML','2025-11-05 15:41:49'),(23,'sh','Shell Script','2025-11-05 15:41:49'),(24,'bash','Bash Script','2025-11-05 15:41:49'),(25,'pl','Perl Script','2025-11-05 15:41:49'),(26,'py','Python Script','2025-11-05 15:41:49'),(27,'rb','Ruby Script','2025-11-05 15:41:49'),(28,'jar','Java Archive','2025-11-05 15:41:49'),(29,'app','Aplicación de macOS','2025-11-05 15:41:49'),(30,'deb','Paquete Debian','2025-11-05 15:41:49'),(31,'rpm','Paquete RPM','2025-11-05 15:41:49');
/*!40000 ALTER TABLE `extensiones_prohibidas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grupos`
--

DROP TABLE IF EXISTS `grupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grupos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `admin_id` int NOT NULL,
  `peso` float NOT NULL DEFAULT '10',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `grupos_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grupos`
--

LOCK TABLES `grupos` WRITE;
/*!40000 ALTER TABLE `grupos` DISABLE KEYS */;
INSERT INTO `grupos` VALUES (1,' Dev Ops',1,6),(2,' Backend',1,10);
/*!40000 ALTER TABLE `grupos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `rol` enum('administrador','basico') DEFAULT 'basico',
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `grupo_id` int DEFAULT NULL,
  `peso_max` float DEFAULT '10',
  `usado` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_usuario_grupo` (`grupo_id`),
  CONSTRAINT `fk_usuario_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Administrador Principal','administrador','admin@empresa.com','$2y$10$nzmhvVq2Qw8ZMuwkbptsWeBRDP1hj8kbFRjiav3HemEnMLe7aopKu','activo',NULL,10,NULL),(2,'Juan camilo','basico','juan@empresa.com','$2y$10$z1ZL/s5UBJdRloETUxGCiuoIHlSxcZ8ecYCFqb6esSD9xUr/.KS2C','activo',2,10,3.42),(3,' jose jose','basico','jose@empresa.com','$2y$10$z1ZL/s5UBJdRloETUxGCiuoIHlSxcZ8ecYCFqb6esSD9xUr/.KS2C','activo',1,10,NULL);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'prueba-edulabs'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-05 12:03:51
