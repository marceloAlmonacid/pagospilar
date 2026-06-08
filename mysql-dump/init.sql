-- MariaDB dump 10.19  Distrib 10.4.21-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: impuestos
-- ------------------------------------------------------
-- Server version	10.4.21-MariaDB-1:10.4.21+maria~focal

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
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id_admin`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'admin','$2y$10$pmN2JL2nc50Pq/VAlQH9Cu4rRX.1gmfqjcJMPqkyXXKymFkUGFuOq');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial`
--

DROP TABLE IF EXISTS `historial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historial` (
  `id_historial` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_imp_hist` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL,
  `costo_imp_hist` decimal(10,0) DEFAULT NULL,
  `fecha_pago_hist` date DEFAULT NULL,
  `ruta_factura_hist` varchar(180) COLLATE utf8_spanish_ci DEFAULT NULL,
  `id_usu2` int(11) NOT NULL,
  `id_imp2` int(11) NOT NULL,
  PRIMARY KEY (`id_historial`),
  KEY `id_usu2` (`id_usu2`,`id_imp2`),
  KEY `id_imp2` (`id_imp2`),
  CONSTRAINT `historial_ibfk_1` FOREIGN KEY (`id_usu2`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial`
--

LOCK TABLES `historial` WRITE;
/*!40000 ALTER TABLE `historial` DISABLE KEYS */;
/*!40000 ALTER TABLE `historial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imp`
--

DROP TABLE IF EXISTS `imp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imp` (
  `id_imp` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_imp` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL,
  `proveedor_imp` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_vencimiento_imp` date DEFAULT NULL,
  `costo_imp` decimal(10,2) DEFAULT NULL,
  `ruta_comprobante_imp` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipo_imp` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL,
  `estado_imp` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`id_imp`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imp`
--

LOCK TABLES `imp` WRITE;
/*!40000 ALTER TABLE `imp` DISABLE KEYS */;
INSERT INTO `imp` VALUES (38,'Agua Potable','Aguas RN','2024-04-10',13228.87,'../facturas/202404082134551280001pdf.pdf','AGUA','PAGADO'),(39,'Luz - Electricidad','Cooperativa CEB','2024-04-26',71587.23,'../facturas/17684mar2024pdf.pdf','LUZ',NULL),(40,'GAS','Camuzzi','2024-04-10',4998.09,'../facturas/facturaperiodo0224pdf.pdf','GAS',NULL),(41,'Internet','AVC ','2024-04-10',16900.00,'../facturas/facturaavc-8pdf.pdf','INTERNET',NULL),(42,'Impuesto Inmobiliario','Agencia R. Tributaria','2024-04-15',24537.64,'../facturas/informedeudainmobiliariopdf.pdf','IMPUESTO INMOBILIARIO',NULL),(43,'Tasas Municipales','Municipio Bariloche','1111-11-11',0.00,'../facturas/2024-04-10-153644-pdf.pdf','TASAS MUNICIPALES',NULL),(44,'Tasas Municipales','Municipio Bariloche','2024-05-10',14512.45,'../facturas/boleta-638502469949323383pdf.pdf','TASAS MUNICIPALES',NULL),(51,'GAS','Camuzzi','2026-04-26',133711.11,'facturas/1778870585_2507.pdf','GAS',NULL),(52,'GAS','Camuzzi','2026-05-26',135389.19,'facturas/1779464069_9022.pdf','GAS',NULL),(54,'GAS','Camuzzi','2026-03-20',69852.94,'facturas/1779464207_2148.pdf','GAS',NULL),(55,'GAS','Camuzzi','2026-02-20',70949.37,'facturas/1779464252_2016.pdf','GAS',NULL),(56,'GAS','Camuzzi','2026-01-26',96981.60,'facturas/1779580656_2479.pdf','GAS',NULL);
/*!40000 ALTER TABLE `imp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usu_imp`
--

DROP TABLE IF EXISTS `usu_imp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usu_imp` (
  `id_usu_imp` int(11) NOT NULL AUTO_INCREMENT,
  `id_usu1` int(11) NOT NULL,
  `id_imp1` int(80) NOT NULL,
  `monto` decimal(10,0) NOT NULL,
  `estado_pago` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_pago` date DEFAULT NULL,
  PRIMARY KEY (`id_usu_imp`),
  KEY `id_usu1` (`id_usu1`),
  KEY `id_imp1` (`id_imp1`),
  CONSTRAINT `usu_imp_ibfk_1` FOREIGN KEY (`id_usu1`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `usu_imp_ibfk_2` FOREIGN KEY (`id_imp1`) REFERENCES `imp` (`id_imp`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usu_imp`
--

LOCK TABLES `usu_imp` WRITE;
/*!40000 ALTER TABLE `usu_imp` DISABLE KEYS */;
INSERT INTO `usu_imp` VALUES (12,3,38,2646,NULL,NULL),(13,4,38,2646,NULL,NULL),(14,5,38,2646,NULL,NULL),(15,6,38,2646,NULL,NULL),(16,7,38,2646,NULL,NULL),(17,3,39,14317,NULL,NULL),(18,4,39,14317,NULL,NULL),(19,5,39,14317,NULL,NULL),(20,6,39,14317,NULL,NULL),(21,7,39,14317,NULL,NULL),(22,4,40,1250,NULL,NULL),(23,5,40,1250,NULL,NULL),(24,6,40,1250,NULL,NULL),(25,7,40,1250,NULL,NULL),(26,3,41,4225,NULL,NULL),(27,5,41,4225,NULL,NULL),(28,6,41,4225,NULL,NULL),(29,7,41,4225,NULL,NULL),(30,3,42,4908,NULL,NULL),(31,4,42,4908,NULL,NULL),(32,5,42,4908,NULL,NULL),(33,6,42,4908,NULL,NULL),(34,7,42,4908,NULL,NULL),(35,3,43,0,NULL,NULL),(36,4,43,0,NULL,NULL),(37,5,43,0,NULL,NULL),(38,6,43,0,NULL,NULL),(39,7,43,0,NULL,NULL),(40,3,44,2902,NULL,NULL),(41,4,44,2902,NULL,NULL),(42,5,44,2902,NULL,NULL),(43,6,44,2902,NULL,NULL),(44,7,44,2902,NULL,NULL),(72,4,51,33428,NULL,NULL),(73,5,51,33428,NULL,NULL),(74,6,51,33428,NULL,NULL),(75,7,51,33428,NULL,NULL),(76,4,52,33847,NULL,NULL),(77,5,52,33847,NULL,NULL),(78,6,52,33847,NULL,NULL),(79,7,52,33847,NULL,NULL),(84,4,54,17463,NULL,NULL),(85,5,54,17463,NULL,NULL),(86,6,54,17463,NULL,NULL),(87,7,54,17463,NULL,NULL),(88,4,55,17737,NULL,NULL),(89,5,55,17737,NULL,NULL),(90,6,55,17737,NULL,NULL),(91,7,55,17737,NULL,NULL),(92,4,56,24245,NULL,NULL),(93,5,56,24245,NULL,NULL),(94,6,56,24245,NULL,NULL),(95,7,56,24245,NULL,NULL);
/*!40000 ALTER TABLE `usu_imp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(80) COLLATE utf8_spanish_ci NOT NULL,
  `pago` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `telegram_id` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (3,'Adri','NO',NULL,NULL),(4,'Primo','SI',NULL,NULL),(5,'Ana','SI',NULL,NULL),(6,'Carlos','SI',NULL,NULL),(7,'Cris y Dani',NULL,'2944301148','6589541088');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-25  1:44:24
