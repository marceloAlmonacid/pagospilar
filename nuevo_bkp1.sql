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
  PRIMARY KEY (`id_historial`)
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
  PRIMARY KEY (`id_imp`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imp`
--

LOCK TABLES `imp` WRITE;
/*!40000 ALTER TABLE `imp` DISABLE KEYS */;
INSERT INTO `imp` VALUES (24,'Luz - Electricidad','Cooperativa CEB','2024-04-26',71587.23,'../facturas/17684mar2024pdf.pdf','LUZ'),(25,'Internet','AVC','2024-04-10',16900.00,'../facturas/facturaavc-8pdf.pdf','INTERNET'),(27,'Agua Potable','Aguas RN','2024-04-10',13228.87,'../facturas/202404082134551280001pdf.pdf','AGUA');
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
  PRIMARY KEY (`id_usu_imp`),
  KEY `id_usu1` (`id_usu1`),
  KEY `id_imp1` (`id_imp1`),
  CONSTRAINT `usu_imp_ibfk_1` FOREIGN KEY (`id_usu1`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `usu_imp_ibfk_2` FOREIGN KEY (`id_imp1`) REFERENCES `imp` (`id_imp`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usu_imp`
--

LOCK TABLES `usu_imp` WRITE;
/*!40000 ALTER TABLE `usu_imp` DISABLE KEYS */;
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
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (3,'Adri'),(4,'Primo'),(5,'Ana');
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

-- Dump completed on 2024-04-09 20:53:49
