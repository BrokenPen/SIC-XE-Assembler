-- MySQL dump 10.13  Distrib 5.6.24, for Win64 (x86_64)
--
-- Host: localhost    Database: wordpress
-- ------------------------------------------------------
-- Server version	5.6.13-enterprise-commercial-advanced-log

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
-- Table structure for table `opcode`
--

DROP TABLE IF EXISTS `optab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `optab` (
  `OPCODE` varchar(45) NOT NULL,
  `FORMAT` varchar(45) DEFAULT NULL,
  `OPCODEVAL` varchar(45) DEFAULT NULL,
  `P` varchar(45) DEFAULT NULL,
  `X` varchar(45) DEFAULT NULL,
  `F` varchar(45) DEFAULT NULL,
  `C` varchar(45) DEFAULT NULL,
  `Effect` varchar(255) DEFAULT NULL,
  `Mnemonic` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`OPCODE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `opcode`
--

LOCK TABLES `opcode` WRITE;
/*!40000 ALTER TABLE `opcode` DISABLE KEYS */;
INSERT INTO `opcode` VALUES ('ADD','3/4','18','','','','',NULL,NULL),('ADDF','3/4','58','','1','1','',NULL,NULL),('ADDR','2','90','','1','','',NULL,NULL),('AND','3/4','40','','','','',NULL,NULL),('CLEAR','2','B4','','1','','',NULL,NULL),('COMP','3/4','28','','','','1',NULL,NULL),('COMPF','3/4','88','','1','1','1',NULL,NULL),('COMPR','2','A0','','1','','1',NULL,NULL),('DIV','3/4','24','','','','',NULL,NULL),('DIVF','3/4','64','','1','1','',NULL,NULL),('DIVR','2','9C','','1','','',NULL,NULL),('FIX','1','C4','','1','1','',NULL,NULL),('FLOAT','1','C0','','1','1','',NULL,NULL),('HIO','1','F4','1','1','','',NULL,NULL),('J','3/4','3C','','','','',NULL,NULL),('JEQ','3/4','30','','','','',NULL,NULL),('JGT','3/4','34','','','','',NULL,NULL),('JLT','3/4','38','','','','',NULL,NULL),('JSUB','3/4','48','','','','',NULL,NULL),('LDA','3/4','0','','','','',NULL,NULL),('LDB','3/4','68','','1','','',NULL,NULL),('LDCH','3/4','50','','','','',NULL,NULL),('LDF','3/4','70','','1','1','',NULL,NULL),('LDL','3/4','8','','','','',NULL,NULL),('LDS','3/4','6C','','1','','',NULL,NULL),('LDT','3/4','74','','1','','',NULL,NULL),('LDX','3/4','4','','','','',NULL,NULL),('LPS','3/4','D0','1','1','','',NULL,NULL),('MUL','3/4','20','','','','',NULL,NULL),('MULF','3/4','60','','1','1','',NULL,NULL),('MULR','2','98','','1','','',NULL,NULL),('NORM','1','C8','','1','1','',NULL,NULL),('OPCODE','FORMAT','OPCODEVAL','P','X','F','C',NULL,NULL),('OR','3/4','44','','','','',NULL,NULL),('RD','3/4','D8','1','','','',NULL,NULL),('RMO','2','AC','','1','','',NULL,NULL),('RSUB','3/4','4C','','','','',NULL,NULL),('SHIFTL','2','A4','','1','','',NULL,NULL),('SHIFTR','2','A8','','1','','',NULL,NULL),('SIO','1','F0','1','1','','',NULL,NULL),('SSK','3/4','EC','1','1','','',NULL,NULL),('STA','3/4','0C','','','','',NULL,NULL),('STB','3/4','78','','1','','',NULL,NULL),('STCH','3/4','54','','','','',NULL,NULL),('STF','3/4','80','','1','1','',NULL,NULL),('STI','3/4','D4','1','1','','',NULL,NULL),('STL','3/4','14','','','','',NULL,NULL),('STS','3/4','7C','','1','','',NULL,NULL),('STSW','3/4','E8','1','','','',NULL,NULL),('STT','3/4','84','','1','','',NULL,NULL),('STX','3/4','10','','','','',NULL,NULL),('SUB','3/4','1C','','','','',NULL,NULL),('SUBF','3/4','5C','','1','1','',NULL,NULL),('SUBR','2','94','','1','','',NULL,NULL),('SVC','2','B0','','1','','',NULL,NULL),('TD','3/4','E0','1','','','1',NULL,NULL),('TIO','1','F8','1','1','','1',NULL,NULL),('TIX','3/4','2C','','','','1',NULL,NULL),('TIXR','2','B8','','1','','1',NULL,NULL),('WD','3/4','DC','1','','','',NULL,NULL);
/*!40000 ALTER TABLE `opcode` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'wordpress'
--

--
-- Dumping routines for database 'wordpress'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-11-21 19:57:55
