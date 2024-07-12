-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: ecom
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(50) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'mohamed94','moha','ben');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commande`
--

DROP TABLE IF EXISTS `commande`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commande` (
  `id_commande` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `date` date NOT NULL,
  `adresse` varchar(150) NOT NULL,
  `code_postal` varchar(20) NOT NULL,
  `ville` varchar(80) NOT NULL,
  `complement_adresse` varchar(150) NOT NULL,
  `valide` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_commande`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commande`
--

LOCK TABLES `commande` WRITE;
/*!40000 ALTER TABLE `commande` DISABLE KEYS */;
/*!40000 ALTER TABLE `commande` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail-commande`
--

DROP TABLE IF EXISTS `detail-commande`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detail-commande` (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `id_commande` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_modele` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix` float NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id_detail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail-commande`
--

LOCK TABLES `detail-commande` WRITE;
/*!40000 ALTER TABLE `detail-commande` DISABLE KEYS */;
/*!40000 ALTER TABLE `detail-commande` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modele`
--

DROP TABLE IF EXISTS `modele`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modele` (
  `id_modele` int(11) NOT NULL AUTO_INCREMENT,
  `Produits` varchar(80) NOT NULL,
  `Nom` varchar(100) NOT NULL,
  `Fabricant` varchar(100) NOT NULL,
  `Description` text NOT NULL,
  `Prix` float NOT NULL,
  `Image` varchar(100) NOT NULL,
  PRIMARY KEY (`id_modele`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modele`
--

LOCK TABLES `modele` WRITE;
/*!40000 ALTER TABLE `modele` DISABLE KEYS */;
INSERT INTO `modele` VALUES (1,'Péripheriques','G502 HERO','Logitech','Souris Gamer optique - Résolution ajustable 100 à 16 000 dpi - 11 boutons programmables',49.99,'p1.jfif'),(2,'Ordinateur','Swift X 14','Acer','14.5\" QHD+ OLED - Intel Core i7-13700H - GeForce RTX 4050 - 32 Go DDR5 - SSD 1 To - Windows 11 Pro',1299.99,'o1.jfif'),(3,'Composant','Core i7-14700KF','Intel','Processeur Socket 1700 - 20 coeurs - Cache 33 Mo - Raptor Lake refresh - Ventirad non inclus',499.99,'c1.jfif'),(4,'Péripheriques','BlackShark V2 X USB','Razer','Casque-micro gamer - Son surround 7.1 - USB-A - Micro avec annulation passive du bruit avancée',74.99,'p2.jfif');
/*!40000 ALTER TABLE `modele` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_modele` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `note` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_modele` (`id_modele`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`id_modele`) REFERENCES `modele` (`id_modele`),
  CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notes`
--

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `panier`
--

DROP TABLE IF EXISTS `panier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `panier` (
  `id_panier` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `id_modele` int(11) NOT NULL,
  PRIMARY KEY (`id_panier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `panier`
--

LOCK TABLES `panier` WRITE;
/*!40000 ALTER TABLE `panier` DISABLE KEYS */;
/*!40000 ALTER TABLE `panier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateur` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateur`
--

LOCK TABLES `utilisateur` WRITE;
/*!40000 ALTER TABLE `utilisateur` DISABLE KEYS */;
INSERT INTO `utilisateur` VALUES (1,'user1','user1','user1@gmail.com','$2y$10$/ek3/fUOAkhTqlNdo18PmOjbMcGVQAsxkcHClcpG81z4ixkfpaMfy'),(2,'user2','user2','user2@gmail.com','$2y$10$01XmAgJ/rmKwM6CTpq6ik.a6hRgss2VoVC2tOvRLSr0qQLJMFZ5Yq'),(3,'ouf','azert','oufaz@gmail.com','$2y$10$7lsbIM90uyIfwhSLDsLVeup38NHiwz6UTHd0SI0UCeuZ27mAYm08C'),(4,'azerty','uiop','qsd@gmail.com','$2y$10$U7zulYpKN5OzCq2A/SxN0.chYcOtFqSYqwcVbuEypJx54C1rqlKsq'),(5,'az','er','ty@gmail.com','$2y$10$HhhgDINOyfyURkF78X08curtAvzUNoxEB1Oichbt5JgTNnsts79EK'),(6,'user2','user2','user2@gmail.com','$2y$10$PGGw3bPNOkdLmklVF1iyruN//iIu.k9x2DWe.7EpduKzLKj9w7Qme'),(7,'user2','user2','user2@gmail.com','$2y$10$XBcTdqHvlCphfP7sLvj0q.RSB6bqHC3s/tAPCsaLeRn0mrMQqvpRu'),(8,'user3','user3','user3@gmail.com','$2y$10$rFJQM2le.6wnWsyND6bPp.S2HZ6UzyGtr8tc2xP7qM0xnZ.S4obIu'),(9,'user2','user2','user2@gmail.com','$2y$10$fBpdgIPYfgSjs2cvn4tqdeiDQ8fNhGPLT0vRLSnNmV8LDFkzECZEm'),(10,'user3','user3','user3@gmail.com','$2y$10$oFDr18IOJHpnOzDv1bC4Oud8PLXteg01gd2Hgvv3kU3ciy.LVpgvO'),(11,'','','a@b.fr','$2y$10$pg3cZeTQeDGHV2a5pMIViOJJB4pUlxl1/Aej0yd/FzMYKovItVCjW'),(12,'','','a@b.fr','$2y$10$I8SgXhnCsg6X70lgiaq6cuDXixDG.b9BwH3DVZ/JagMdpPlIOf5aW'),(13,'','','a@b.fr','$2y$10$MH/.tpyTNVG/uWMF/c9ZQuN8ODdjPsq4bwYWFYS/sByepAK5s56Nm'),(14,'','','a@b.fr','$2y$10$GleNBGKtsxMPV7VrrzxoVOBAEUssvqVua/5frp1XzuWmVIQViRiu2'),(15,'','','a@b.fr','$2y$10$13iSdnFSwLFVqYjmcoREa.OwXDwXusJL1ftr1Szoed5Zjl02oxdou'),(16,'','','a@b.fr','$2y$10$s3uexJMYlGoJ27UVanTmyOOoxJre82KaxN2MOdM8vRv6I8N3yahc6'),(17,'','','a@b.fr','$2y$10$0BRl4uVW6sTsxC5Q936OR.pkJNQ8gn8xrbB/pRqT1C3Vodmf9paT.'),(18,'','','a@b.fr','$2y$10$u0ZwpmBEbzDCie8V7OKwrenZHp8/CNe313iAGIogwWBGTEYDP2oe.'),(19,'','','a@b.fr','$2y$10$fN3tlVA5YwPj903jxWtsA.H4zhCHeA3lEhPW/pONxG1LyBhP/RA0q'),(20,'','','a@b.fr','$2y$10$mRFUxu0FoHErpiDZbCGx/.xzNXC.vAyGuoiSAvrT5sGMJu5FbIZbS'),(21,'a','','a@b.fr','$2y$10$eKGfeKLdJ/wqKBd5gg0lV.Ol8dfY80Eki8PCe1y65EMBizO3D044W'),(22,'a','a','a@b.fr','$2y$10$4j110Qx5lOHgMAoXyEA.hOk9sL/fCdsD2K0nTexCYn.dAt2K5MVMW'),(23,'a','a','a@b.fr','$2y$10$qJGXLAeFVd75twymSu80G.KvzgLAaG4hf8L.9oXJ9k1DxKRyCcLsi'),(24,'a','aa','a@c.fr','$2y$10$pxO7l/psv0sT.4Yg7twVg.G148xtBbIhYqKfGcAIBXv5eVH7psSWa'),(25,'a','a','a@g.com','$2y$10$tg2OHHONJcohKEBqekepAe3WcfDu/31tRRleLb/HrzWrlCRtfB6xO'),(26,'user6','user7','user6@gmail.com','$2y$10$TsxUQCdeE04Um5JzoAQqWOC488DG0sShR/8KO8x0EKktWisZsIDfi'),(27,'user6','user7','user6@gmail.com','$2y$10$SwWqeEMBCEnXQG1lpeQSqOWqMkljP3MQCwWk.vQlovABAjL0qQZc2');
/*!40000 ALTER TABLE `utilisateur` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-07-12 12:03:10
