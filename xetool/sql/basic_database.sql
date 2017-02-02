-- MySQL dump 10.13  Distrib 5.6.24, for Win32 (x86)
--
-- Host: localhost    Database: xe_install_db
-- ------------------------------------------------------
-- Server version	5.6.24

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
-- Table structure for table `api_data`
--

DROP TABLE IF EXISTS `api_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `api_key` varchar(50) NOT NULL,
  `store_version_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_data`
--

LOCK TABLES `api_data` WRITE;
/*!40000 ALTER TABLE `api_data` DISABLE KEYS */;
INSERT INTO `api_data` VALUES (1,'r0xM9ataxb^gG8ER',0);
/*!40000 ALTER TABLE `api_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_currency`
--

DROP TABLE IF EXISTS `app_currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_currency` (
  `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL,
  `symbol` varchar(2) NOT NULL,
  `code` varchar(5) NOT NULL,
  `is_default` enum('1','0') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_currency`
--

LOCK TABLES `app_currency` WRITE;
/*!40000 ALTER TABLE `app_currency` DISABLE KEYS */;
INSERT INTO `app_currency` VALUES (1,'Dollar','$','USD','1'),(2,'Rupees','â‚','INR','0'),(3,'China Yuan','Â¥','CNY','0'),(4,'Australia ','$','AUD','0'),(5,'Euro','â‚','EURO','0');
/*!40000 ALTER TABLE `app_currency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_language`
--

DROP TABLE IF EXISTS `app_language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_language` (
  `id` int(3) NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` varchar(10) NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_language`
--

LOCK TABLES `app_language` WRITE;
/*!40000 ALTER TABLE `app_language` DISABLE KEYS */;
INSERT INTO `app_language` VALUES (1,'English','en','1'),(2,'German','de','0'),(3,'Hebrew','he','0'),(4,'Spanish','es','0'),(5,'Chinese','zh','0'),(6,'Dutch','nl','0'),(7,'French','fr','0');
/*!40000 ALTER TABLE `app_language` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_unit`
--

DROP TABLE IF EXISTS `app_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_unit` (
  `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL,
  `is_default` enum('1','0') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_unit`
--

LOCK TABLES `app_unit` WRITE;
/*!40000 ALTER TABLE `app_unit` DISABLE KEYS */;
INSERT INTO `app_unit` VALUES (1,'Inch','1'),(2,'Ft','0'),(3,'Cm','0'),(4,'mm','0');
/*!40000 ALTER TABLE `app_unit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `back_pattern_cate_printmethod_rel`
--

DROP TABLE IF EXISTS `back_pattern_cate_printmethod_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `back_pattern_cate_printmethod_rel` (
  `print_method_id` int(5) NOT NULL,
  `pattern_category_id` int(5) NOT NULL,
  `is_enable` enum('1','0') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `back_pattern_cate_printmethod_rel`
--

LOCK TABLES `back_pattern_cate_printmethod_rel` WRITE;
/*!40000 ALTER TABLE `back_pattern_cate_printmethod_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `back_pattern_cate_printmethod_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `background_pattern`
--

DROP TABLE IF EXISTS `background_pattern`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `background_pattern` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `background_pattern_name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `price` float(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `background_pattern`
--

LOCK TABLES `background_pattern` WRITE;
/*!40000 ALTER TABLE `background_pattern` DISABLE KEYS */;
/*!40000 ALTER TABLE `background_pattern` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `background_pattern_category`
--

DROP TABLE IF EXISTS `background_pattern_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `background_pattern_category` (
  `category_id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `background_pattern_category`
--

LOCK TABLES `background_pattern_category` WRITE;
/*!40000 ALTER TABLE `background_pattern_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `background_pattern_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `background_pattern_category_rel`
--

DROP TABLE IF EXISTS `background_pattern_category_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `background_pattern_category_rel` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `pattern_category_id` int(5) NOT NULL,
  `pattern_id` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `background_pattern_category_rel`
--

LOCK TABLES `background_pattern_category_rel` WRITE;
/*!40000 ALTER TABLE `background_pattern_category_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `background_pattern_category_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `background_pattern_tags`
--

DROP TABLE IF EXISTS `background_pattern_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `background_pattern_tags` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `background_pattern_tags`
--

LOCK TABLES `background_pattern_tags` WRITE;
/*!40000 ALTER TABLE `background_pattern_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `background_pattern_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `background_pattern_tags_rel`
--

DROP TABLE IF EXISTS `background_pattern_tags_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `background_pattern_tags_rel` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `pattern_id` int(5) NOT NULL,
  `tag_id` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `background_pattern_tags_rel`
--

LOCK TABLES `background_pattern_tags_rel` WRITE;
/*!40000 ALTER TABLE `background_pattern_tags_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `background_pattern_tags_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `capture_image`
--

DROP TABLE IF EXISTS `capture_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `capture_image` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `image` varchar(100) NOT NULL,
  `type` enum('cart','pre-deco','socialShare','userSlot','template') NOT NULL DEFAULT 'cart',
  `state_id` bigint(20) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`) COMMENT 'primary key'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `capture_image`
--

LOCK TABLES `capture_image` WRITE;
/*!40000 ALTER TABLE `capture_image` DISABLE KEYS */;
/*!40000 ALTER TABLE `capture_image` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `checka`
--

DROP TABLE IF EXISTS `checka`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checka` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `checka`
--

LOCK TABLES `checka` WRITE;
/*!40000 ALTER TABLE `checka` DISABLE KEYS */;
/*!40000 ALTER TABLE `checka` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `color_price_group`
--

DROP TABLE IF EXISTS `color_price_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `color_price_group` (
  `pk_id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `price` float(7,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`pk_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `color_price_group`
--

LOCK TABLES `color_price_group` WRITE;
/*!40000 ALTER TABLE `color_price_group` DISABLE KEYS */;
INSERT INTO `color_price_group` VALUES (1,'Primium',0.00),(2,'Basic',0.00);
/*!40000 ALTER TABLE `color_price_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `color_price_group_rel`
--

DROP TABLE IF EXISTS `color_price_group_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `color_price_group_rel` (
  `color_id` int(11) NOT NULL,
  `color_price_group_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `color_price_group_rel`
--

LOCK TABLES `color_price_group_rel` WRITE;
/*!40000 ALTER TABLE `color_price_group_rel` DISABLE KEYS */;
INSERT INTO `color_price_group_rel` VALUES (1,1),(5,1),(6,1),(7,1);
/*!40000 ALTER TABLE `color_price_group_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `content_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content`
--

LOCK TABLES `content` WRITE;
/*!40000 ALTER TABLE `content` DISABLE KEYS */;
/*!40000 ALTER TABLE `content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `custom_boundary_unit`
--

DROP TABLE IF EXISTS `custom_boundary_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custom_boundary_unit` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `price` float(5,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `custom_boundary_unit`
--

LOCK TABLES `custom_boundary_unit` WRITE;
/*!40000 ALTER TABLE `custom_boundary_unit` DISABLE KEYS */;
/*!40000 ALTER TABLE `custom_boundary_unit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `custom_maskdata`
--

DROP TABLE IF EXISTS `custom_maskdata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custom_maskdata` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `file_name` varchar(150) NOT NULL,
  `maskheight` float(10,2) NOT NULL,
  `maskwidth` float(10,2) NOT NULL,
  `price` float(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `custom_maskdata`
--

LOCK TABLES `custom_maskdata` WRITE;
/*!40000 ALTER TABLE `custom_maskdata` DISABLE KEYS */;
/*!40000 ALTER TABLE `custom_maskdata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_order_info`
--

DROP TABLE IF EXISTS `customer_order_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_order_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refid` int(10) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `product_info` text NOT NULL,
  `order_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_order_info`
--

LOCK TABLES `customer_order_info` WRITE;
/*!40000 ALTER TABLE `customer_order_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_order_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `decorated_product`
--

DROP TABLE IF EXISTS `decorated_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `decorated_product` (
  `pk_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `refid` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_price` float(10,2) NOT NULL,
  `custom_price` float(10,2) NOT NULL,
  `print_method_id` int(4) NOT NULL,
  `template_image_json` text NOT NULL,
  `mini_qty` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_madified` datetime DEFAULT NULL,
  PRIMARY KEY (`pk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `decorated_product`
--

LOCK TABLES `decorated_product` WRITE;
/*!40000 ALTER TABLE `decorated_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `decorated_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `des_cat`
--

DROP TABLE IF EXISTS `des_cat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `des_cat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(50) NOT NULL DEFAULT '0',
  `is_shape` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COMMENT='stores the category value for the designs';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `des_cat`
--

LOCK TABLES `des_cat` WRITE;
/*!40000 ALTER TABLE `des_cat` DISABLE KEYS */;
INSERT INTO `des_cat` VALUES (1,'Animal',0),(2,'Sports',0);
/*!40000 ALTER TABLE `des_cat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `des_cat_rel`
--

DROP TABLE IF EXISTS `des_cat_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `des_cat_rel` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `design_id` int(5) NOT NULL,
  `category_id` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `des_cat_rel`
--

LOCK TABLES `des_cat_rel` WRITE;
/*!40000 ALTER TABLE `des_cat_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `des_cat_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `des_cat_sub_cat_rel`
--

DROP TABLE IF EXISTS `des_cat_sub_cat_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `des_cat_sub_cat_rel` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `category_id` int(5) NOT NULL,
  `sub_category_id` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `des_cat_sub_cat_rel`
--

LOCK TABLES `des_cat_sub_cat_rel` WRITE;
/*!40000 ALTER TABLE `des_cat_sub_cat_rel` DISABLE KEYS */;
INSERT INTO `des_cat_sub_cat_rel` VALUES (1,1,1),(2,1,2),(3,2,3);
/*!40000 ALTER TABLE `des_cat_sub_cat_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `des_sub_cat`
--

DROP TABLE IF EXISTS `des_sub_cat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `des_sub_cat` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT 'na',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COMMENT='stores the sub category for categories';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `des_sub_cat`
--

LOCK TABLES `des_sub_cat` WRITE;
/*!40000 ALTER TABLE `des_sub_cat` DISABLE KEYS */;
INSERT INTO `des_sub_cat` VALUES (1,'Lion'),(2,'Dog'),(3,'Bike');
/*!40000 ALTER TABLE `des_sub_cat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `des_sub_cat_rel`
--

DROP TABLE IF EXISTS `des_sub_cat_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `des_sub_cat_rel` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `design_id` int(5) NOT NULL,
  `sub_category_id` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `des_sub_cat_rel`
--

LOCK TABLES `des_sub_cat_rel` WRITE;
/*!40000 ALTER TABLE `des_sub_cat_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `des_sub_cat_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `des_tag_rel`
--

DROP TABLE IF EXISTS `des_tag_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `des_tag_rel` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `design_id` int(5) NOT NULL,
  `tag_id` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `des_tag_rel`
--

LOCK TABLES `des_tag_rel` WRITE;
/*!40000 ALTER TABLE `des_tag_rel` DISABLE KEYS */;
INSERT INTO `des_tag_rel` VALUES (2,1,1),(3,2,1),(4,3,1),(5,4,1),(12,5,1),(9,6,2);
/*!40000 ALTER TABLE `des_tag_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `des_tags`
--

DROP TABLE IF EXISTS `des_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `des_tags` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT 'na',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COMMENT='stores the tags for designs';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `des_tags`
--

LOCK TABLES `des_tags` WRITE;
/*!40000 ALTER TABLE `des_tags` DISABLE KEYS */;
INSERT INTO `des_tags` VALUES (1,'animal'),(2,'sports');
/*!40000 ALTER TABLE `des_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `design_back_cat_rel`
--

DROP TABLE IF EXISTS `design_back_cat_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `design_back_cat_rel` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `background_category_id` int(5) NOT NULL,
  `background_id` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `design_back_cat_rel`
--

LOCK TABLES `design_back_cat_rel` WRITE;
/*!40000 ALTER TABLE `design_back_cat_rel` DISABLE KEYS */;
INSERT INTO `design_back_cat_rel` VALUES (1,1,1),(2,1,2),(3,1,3),(4,2,4);
/*!40000 ALTER TABLE `design_back_cat_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `design_back_cate_printmethod_rel`
--

DROP TABLE IF EXISTS `design_back_cate_printmethod_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `design_back_cate_printmethod_rel` (
  `print_method_id` int(5) NOT NULL,
  `background_category_id` int(5) NOT NULL,
  `is_enable` enum('1','0') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `design_back_cate_printmethod_rel`
--

LOCK TABLES `design_back_cate_printmethod_rel` WRITE;
/*!40000 ALTER TABLE `design_back_cate_printmethod_rel` DISABLE KEYS */;
INSERT INTO `design_back_cate_printmethod_rel` VALUES (1,1,'0'),(1,2,'0'),(3,1,'0'),(3,2,'0');
/*!40000 ALTER TABLE `design_back_cate_printmethod_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `design_background`
--

DROP TABLE IF EXISTS `design_background`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `design_background` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(30) DEFAULT NULL,
  `background_design_name` varchar(30) NOT NULL,
  `price` float(10,2) NOT NULL,
  `isScalable` enum('1','0') DEFAULT '1',
  `is_image` enum('0','1') NOT NULL DEFAULT '0',
  `color_value` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `design_background`
--

LOCK TABLES `design_background` WRITE;
/*!40000 ALTER TABLE `design_background` DISABLE KEYS */;
INSERT INTO `design_background` VALUES (1,'1.png','Black',0.00,'1','0','#000000'),(2,'2.png','Red',0.00,'1','0','#f00909'),(3,'3.png','Green',0.00,'1','0','#0ae755'),(4,'4.jpg','Tie Dye',0.00,'1','1','');
/*!40000 ALTER TABLE `design_background` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `design_background_category`
--

DROP TABLE IF EXISTS `design_background_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `design_background_category` (
  `category_id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `design_background_category`
--

LOCK TABLES `design_background_category` WRITE;
/*!40000 ALTER TABLE `design_background_category` DISABLE KEYS */;
INSERT INTO `design_background_category` VALUES (1,'color'),(2,'image');
/*!40000 ALTER TABLE `design_background_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `design_background_tags`
--

DROP TABLE IF EXISTS `design_background_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `design_background_tags` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `design_background_tags`
--

LOCK TABLES `design_background_tags` WRITE;
/*!40000 ALTER TABLE `design_background_tags` DISABLE KEYS */;
INSERT INTO `design_background_tags` VALUES (1,'black'),(2,'red'),(3,'green'),(4,'tie');
/*!40000 ALTER TABLE `design_background_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `design_background_tags_rel`
--

DROP TABLE IF EXISTS `design_background_tags_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `design_background_tags_rel` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `background_id` int(5) NOT NULL,
  `tag_id` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `design_background_tags_rel`
--

LOCK TABLES `design_background_tags_rel` WRITE;
/*!40000 ALTER TABLE `design_background_tags_rel` DISABLE KEYS */;
INSERT INTO `design_background_tags_rel` VALUES (1,1,1),(2,2,2),(3,3,3),(4,4,4);
/*!40000 ALTER TABLE `design_background_tags_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `design_category_printmethod_rel`
--

DROP TABLE IF EXISTS `design_category_printmethod_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `design_category_printmethod_rel` (
  `print_method_id` int(11) NOT NULL,
  `design_category_id` int(11) NOT NULL,
  `is_enable` enum('1','0') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `design_category_printmethod_rel`
--

LOCK TABLES `design_category_printmethod_rel` WRITE;
/*!40000 ALTER TABLE `design_category_printmethod_rel` DISABLE KEYS */;
INSERT INTO `design_category_printmethod_rel` VALUES (1,2,'0'),(1,1,'0'),(3,1,'0'),(3,2,'0');
/*!40000 ALTER TABLE `design_category_printmethod_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `design_category_sub_category_rel`
--

DROP TABLE IF EXISTS `design_category_sub_category_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `design_category_sub_category_rel` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `design_id` int(5) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `sub_category_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `design_category_sub_category_rel`
--

LOCK TABLES `design_category_sub_category_rel` WRITE;
/*!40000 ALTER TABLE `design_category_sub_category_rel` DISABLE KEYS */;
INSERT INTO `design_category_sub_category_rel` VALUES (2,1,1,1),(10,5,1,2),(4,6,1,2),(9,5,2,0);
/*!40000 ALTER TABLE `design_category_sub_category_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `design_state`
--

DROP TABLE IF EXISTS `design_state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `design_state` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `json_data` longtext NOT NULL,
  `date_created` datetime NOT NULL,
  `status` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `design_state`
--

LOCK TABLES `design_state` WRITE;
/*!40000 ALTER TABLE `design_state` DISABLE KEYS */;
/*!40000 ALTER TABLE `design_state` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `designs`
--

DROP TABLE IF EXISTS `designs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `designs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(50) NOT NULL DEFAULT '0',
  `design_name` varchar(100) NOT NULL DEFAULT '0',
  `price` double(10,2) NOT NULL DEFAULT '0.00',
  `tags` varchar(240) DEFAULT NULL,
  `is_shape` enum('1','0') NOT NULL DEFAULT '0',
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `isScalable` enum('1','0') NOT NULL DEFAULT '1',
  `no_of_colors` tinyint(4) NOT NULL DEFAULT '0',
  `is_svgasfile` enum('1','0') NOT NULL DEFAULT '0',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `date_modified` datetime DEFAULT NULL,
  `aheight` float(10,2) NOT NULL,
  `awidth` float(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `designs`
--

LOCK TABLES `designs` WRITE;
/*!40000 ALTER TABLE `designs` DISABLE KEYS */;
INSERT INTO `designs` VALUES (1,'1.svg','dog',1.00,'','0','1','0',0,'0',0,'0000-00-00 00:00:00',0.00,0.00),(5,'5.svg','same',2.00,'','0','1','1',0,'0',0,'0000-00-00 00:00:00',0.00,0.00),(6,'6.svg','fdgf',4.00,'','0','1','0',0,'0',0,'0000-00-00 00:00:00',0.00,0.00);
/*!40000 ALTER TABLE `designs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discont_range`
--

DROP TABLE IF EXISTS `discont_range`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `discont_range` (
  `pk_id` int(10) NOT NULL AUTO_INCREMENT,
  `discount_id` int(10) NOT NULL,
  `from_range` int(10) NOT NULL,
  `to_range` int(10) NOT NULL,
  `discount_price` float(5,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`pk_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discont_range`
--

LOCK TABLES `discont_range` WRITE;
/*!40000 ALTER TABLE `discont_range` DISABLE KEYS */;
/*!40000 ALTER TABLE `discont_range` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discount`
--

DROP TABLE IF EXISTS `discount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `discount` (
  `pk_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL,
  PRIMARY KEY (`pk_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discount`
--

LOCK TABLES `discount` WRITE;
/*!40000 ALTER TABLE `discount` DISABLE KEYS */;
/*!40000 ALTER TABLE `discount` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `distress`
--

DROP TABLE IF EXISTS `distress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `distress` (
  `distress_id` int(11) NOT NULL AUTO_INCREMENT,
  `id` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `file_name` varchar(100) NOT NULL,
  `price` double(12,2) DEFAULT '0.00',
  PRIMARY KEY (`distress_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `distress`
--

LOCK TABLES `distress` WRITE;
/*!40000 ALTER TABLE `distress` DISABLE KEYS */;
INSERT INTO `distress` VALUES (10,'1d9f8e43f896173b602aad10c936ab4b','d3','1d9f8e43f896173b602aad10c936ab4b.jpg',0.00),(9,'240720704dc038565eca1434b88b49dd','d2','240720704dc038565eca1434b88b49dd.jpg',0.00),(8,'2845417d882df2fe02d42a13c2d3e39d','d1','2845417d882df2fe02d42a13c2d3e39d.jpg',0.00);
/*!40000 ALTER TABLE `distress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `domain_store_rel`
--

DROP TABLE IF EXISTS `domain_store_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domain_store_rel` (
  `pk_id` int(2) NOT NULL AUTO_INCREMENT,
  `domain_name` varchar(100) NOT NULL,
  `store_id` tinyint(2) NOT NULL,
  PRIMARY KEY (`pk_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `domain_store_rel`
--

LOCK TABLES `domain_store_rel` WRITE;
/*!40000 ALTER TABLE `domain_store_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `domain_store_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `effect_list`
--

DROP TABLE IF EXISTS `effect_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `effect_list` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(100) NOT NULL,
  `date_modified` date NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `effect_list`
--

LOCK TABLES `effect_list` WRITE;
/*!40000 ALTER TABLE `effect_list` DISABLE KEYS */;
INSERT INTO `effect_list` VALUES (1,'bright','2014-08-18',1),(2,'desaturate','2014-08-18',1),(3,'grayscale','2014-08-18',1),(4,'green','2014-08-18',1),(5,'invert','2014-08-18',1),(6,'noise','2014-08-18',1),(7,'normal','2014-08-18',1),(8,'pixelate','2014-08-18',1),(9,'red','2014-08-18',1),(10,'saturate','2014-08-18',1),(11,'sepia','2014-08-18',1),(12,'sepia2','2014-08-18',1),(13,'yellow','2014-08-18',1);
/*!40000 ALTER TABLE `effect_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `features`
--

DROP TABLE IF EXISTS `features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `features` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'It indicates whether the feature is ON or OFF.',
  `mandatory_status` tinyint(1) NOT NULL COMMENT 'It indicates whether the feature is mandatory(ON) or can be set to OFF.',
  `product_level_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'It indicates wheather a feature can be set to ON/OFF product-wise.',
  `category_level_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'It indicates wheather a feature can be set to ON/OFF product-category-wise.',
  `tab_id` int(3) NOT NULL DEFAULT '0' COMMENT 'It indicates the tab of the subtab. Value is 0 for the feature which is not a subtab.',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `features`
--

LOCK TABLES `features` WRITE;
/*!40000 ALTER TABLE `features` DISABLE KEYS */;
INSERT INTO `features` VALUES (1,'Product','product',1,1,0,1,0),(2,'Product Canvas','productCanvas',1,1,0,1,0),(3,'Cart','cart',1,1,0,1,0),(4,'Designs','design',1,1,0,1,2),(5,'Plain Text','plainText',1,1,0,1,3),(6,'Image Edit','imageEdit',1,1,0,1,0),(7,'QR Code','qrCode',1,0,1,1,2),(8,'Shapes','shape',0,0,1,1,2),(9,'Hand Drawing','handDrawing',1,0,1,1,2),(10,'Distress Effect','distressEffect',1,0,1,0,2),(11,'Graphics Mask','imageMask',1,0,1,1,0),(12,'Graphics Filter','imageFilter',1,0,1,1,0),(13,'Curve Text','curveText',1,0,1,1,3),(14,'Text On Path','textOnPath',1,0,1,1,3),(15,'Text FX','textFX',1,0,1,1,3),(16,'Text Art','textArt',1,0,1,1,3),(17,'Word Cloud','wordCloud',1,0,1,1,3),(18,'Name & Number','nameNumber',1,0,1,1,0),(19,'Social Image','socialImage',0,0,1,1,0),(20,'Layers','layers',1,1,0,1,0),(21,'My Image','myImage',1,1,0,0,2),(22,'Reduce Color','reduceColor',1,0,1,1,0),(23,'Template','template',1,0,1,1,0),(44,'Background','background',0,0,1,1,2),(45,'Select Color','selectColor',1,0,1,1,0),(46,'Background Pattern','backgroundPattern',1,0,1,1,2),(47,'Image Settings','imageSettings',1,0,1,1,2),(48,'Variable Printing','variablePrinting',1,0,1,1,0);
/*!40000 ALTER TABLE `features` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `font_category`
--

DROP TABLE IF EXISTS `font_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `font_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `font_category`
--

LOCK TABLES `font_category` WRITE;
/*!40000 ALTER TABLE `font_category` DISABLE KEYS */;
INSERT INTO `font_category` VALUES (2,'Regular'),(6,'Funky');
/*!40000 ALTER TABLE `font_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `font_category_printmethod_rel`
--

DROP TABLE IF EXISTS `font_category_printmethod_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `font_category_printmethod_rel` (
  `print_method_id` int(11) NOT NULL,
  `font_category_id` int(11) NOT NULL,
  `is_enable` enum('1','0') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `font_category_printmethod_rel`
--

LOCK TABLES `font_category_printmethod_rel` WRITE;
/*!40000 ALTER TABLE `font_category_printmethod_rel` DISABLE KEYS */;
INSERT INTO `font_category_printmethod_rel` VALUES (1,6,'0'),(1,2,'0'),(3,2,'0'),(3,6,'0');
/*!40000 ALTER TABLE `font_category_printmethod_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `font_category_relation`
--

DROP TABLE IF EXISTS `font_category_relation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `font_category_relation` (
  `font_id` int(5) NOT NULL,
  `category_id` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `font_category_relation`
--

LOCK TABLES `font_category_relation` WRITE;
/*!40000 ALTER TABLE `font_category_relation` DISABLE KEYS */;
INSERT INTO `font_category_relation` VALUES (4,1),(3,2),(9,2),(8,6),(11,6),(12,2);
/*!40000 ALTER TABLE `font_category_relation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `font_tag_relation`
--

DROP TABLE IF EXISTS `font_tag_relation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `font_tag_relation` (
  `font_id` int(10) NOT NULL,
  `tag_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `font_tag_relation`
--

LOCK TABLES `font_tag_relation` WRITE;
/*!40000 ALTER TABLE `font_tag_relation` DISABLE KEYS */;
INSERT INTO `font_tag_relation` VALUES (1,2),(1,1),(4,4),(4,3),(3,4),(3,3),(9,5),(9,6),(10,7),(11,8),(12,8);
/*!40000 ALTER TABLE `font_tag_relation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fonts`
--

DROP TABLE IF EXISTS `fonts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fonts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `font_name` varchar(50) DEFAULT NULL,
  `font_label` varchar(100) DEFAULT NULL,
  `price` double(10,2) NOT NULL DEFAULT '0.00',
  `orgName` varchar(100) DEFAULT NULL,
  `is_delete` enum('0','1') NOT NULL DEFAULT '0',
  `sort_order` int(30) NOT NULL DEFAULT '0',
  `date_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fonts`
--

LOCK TABLES `fonts` WRITE;
/*!40000 ALTER TABLE `fonts` DISABLE KEYS */;
INSERT INTO `fonts` VALUES (8,'Lintsec','Lintsec',5.00,'Lintsec','0',0,'0000-00-00 00:00:00'),(9,'ChunkFive_Roman','ChunkFive_Roman',6.00,'ChunkFive_Roman','0',0,'0000-00-00 00:00:00'),(12,'ActionIs','ActionIs',7.00,'Action_Is','0',0,'0000-00-00 00:00:00');
/*!40000 ALTER TABLE `fonts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `general_setting`
--

DROP TABLE IF EXISTS `general_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `general_setting` (
  `id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `is_popup_enable` enum('0','1') NOT NULL DEFAULT '0',
  `terms_condition` text NOT NULL,
  `currency` varchar(20) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `is_direct_cart` enum('0','1') NOT NULL DEFAULT '0',
  `max_file_size` float(5,2) NOT NULL DEFAULT '0.00',
  `image_width` float(8,2) NOT NULL DEFAULT '0.00',
  `image_height` float(8,2) NOT NULL DEFAULT '0.00',
  `bounds` text NOT NULL,
  `price_suffix` varchar(30) NOT NULL,
  `price_prefix` varchar(30) NOT NULL,
  `font_size_min` int(5) NOT NULL,
  `font_size_max` int(5) NOT NULL,
  `step` int(5) NOT NULL,
  `notes` text NOT NULL,
  `no_of_chars` int(5) NOT NULL,
  `app_id` varchar(30) NOT NULL,
  `domain_name` varchar(30) NOT NULL,
  `site_url` varchar(30) NOT NULL,
  `is_terms_and_condition_allow` enum('1','0') NOT NULL,
  `img_terms_condition` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `general_setting`
--

LOCK TABLES `general_setting` WRITE;
/*!40000 ALTER TABLE `general_setting` DISABLE KEYS */;
INSERT INTO `general_setting` VALUES (1,'1','Please make sure you are uploading high resolution images when customizing your product.  If your artwork does not follow our recommended guidelines, we cannot guarantee the quality of the final printed product. Please make sure to check your design for SPELLING, CLARITY OF THE IMAGES and TEXT PLACEMENT. What you see on the screen is what we will be printing on the product. We are not responsible for the customers supplied artwork, spelling or wrong placement of the text.','$','inch','0',4.50,200.10,100.10,'{\"mask\":{\"left\":136,\"top\":36,\"path\":\"M358.901,46.65  c-6.568-6.567-14.52-9.85-23.854-9.85h-156.5c-9.333,0-17.3,3.284-23.899,9.85c-6.566,6.567-9.851,14.5-9.851,23.8V441.6  c0,9.304,3.284,17.22,9.851,23.75c6.6,6.567,14.566,9.854,23.899,9.854h156.5c9.334,0,17.283-3.283,23.854-9.854  c6.6-6.53,9.896-14.446,9.896-23.75V70.45C368.8,61.15,365.5,53.217,358.901,46.65z M227.2,87.5c0,10.467-5.067,15.7-15.2,15.7h-46  c-10.133,0-15.2-5.233-15.2-15.7V70.8c0-7.833,2.767-14.534,8.3-20.1c5.088-5.057,11.121-7.807,18.101-8.25L212,42.4  c10.133,0,15.2,5.233,15.2,15.7V87.5z\",\"width\":\"\",\"height\":\"\"},\"bounds\":{\"boundx\":146.726221434,\"boundy\":99.279911523,\"boundheight\":277.486730721,\"boundwidth\":196.119702668},\"customsize\":{\"left\":0,\"top\":0,\"width\":500,\"height\":500},\"custom_mask\": {\"left\": 136,\"top\": 36,\"path\": \"M358.901,46.65 c-6.568-6.567-14.52-9.85-23.854-9.85h-156.5c-9.333,0-17.3,3.284-23.899,9.85c-6.566,6.567-9.851,14.5-9.851,23.8V441.6  c0,9.304,3.284,17.22,9.851,23.75c6.6,6.567,14.566,9.854,23.899,9.854h156.5c9.334,0,17.283-3.283,23.854-9.854  c6.6-6.53,9.896-14.446,9.896-23.75V70.45C368.8,61.15,365.5,53.217,358.901,46.65z M227.2,87.5c0,10.467-5.067,15.7-15.2,15.7h-46  c-10.133,0-15.2-5.233-15.2-15.7V70.8c0-7.833,2.767-14.534,8.3-20.1c5.088-5.057,11.121-7.807,18.101-8.25L212,42.4  c10.133,0,15.2,5.233,15.2,15.7V87.5z\",\"width\": \"\",\"height\": \"\"},\"mask_height\":\"0.00\",\"mask_width\":\"0.00\",\"mask_price\":\"0.63\",\"scale_ratio\":\"1.00000\",\"side\":\"0\"}','','',8,24,2,'',0,'','','','1','');
/*!40000 ALTER TABLE `general_setting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `image_data`
--

DROP TABLE IF EXISTS `image_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `image_data` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `refid` bigint(20) DEFAULT '0',
  `customer_id` int(11) NOT NULL,
  `uid` varchar(70) NOT NULL,
  `image` varchar(50) NOT NULL,
  `thumbnail` varchar(70) NOT NULL,
  `type` varchar(4) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `image_data`
--

LOCK TABLES `image_data` WRITE;
/*!40000 ALTER TABLE `image_data` DISABLE KEYS */;
INSERT INTO `image_data` VALUES (1,0,0,'4477df237d7787defa998eda44dd5128','1.jpeg','thumb_1.jpeg','jpeg','2015-09-03 19:19:09'),(2,0,0,'4477df237d7787defa998eda44dd5128','2.jpeg','thumb_2.jpeg','jpeg','2015-09-03 19:19:17'),(3,0,0,'4477df237d7787defa998eda44dd5128','3.png','thumb_3.png','png','2015-09-03 19:20:18'),(4,0,0,'4477df237d7787defa998eda44dd5128','4.png','thumb_4.png','png','2015-09-03 19:23:43');
/*!40000 ALTER TABLE `image_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `image_edit_select_color`
--

DROP TABLE IF EXISTS `image_edit_select_color`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `image_edit_select_color` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `max_number_of_color` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `image_edit_select_color`
--

LOCK TABLES `image_edit_select_color` WRITE;
/*!40000 ALTER TABLE `image_edit_select_color` DISABLE KEYS */;
/*!40000 ALTER TABLE `image_edit_select_color` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items_per_module`
--

DROP TABLE IF EXISTS `items_per_module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items_per_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items_per_module`
--

LOCK TABLES `items_per_module` WRITE;
/*!40000 ALTER TABLE `items_per_module` DISABLE KEYS */;
INSERT INTO `items_per_module` VALUES (1,'Total',10),(2,'design',3),(3,'plainText',3),(4,'imageEdit',2),(5,'qrCode',1),(6,'shape',2),(7,'handDrawing',3),(8,'curveText',1),(9,'textOnPath',1),(10,'textFX',1),(11,'nameNumber',1),(12,'textArt',1),(13,'wordCloud',1);
/*!40000 ALTER TABLE `items_per_module` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `itextpattern`
--

DROP TABLE IF EXISTS `itextpattern`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itextpattern` (
  `id` int(11) NOT NULL,
  `json` varchar(4000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='this table keeps the JSON data for itest style';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itextpattern`
--

LOCK TABLES `itextpattern` WRITE;
/*!40000 ALTER TABLE `itextpattern` DISABLE KEYS */;
/*!40000 ALTER TABLE `itextpattern` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mask_data`
--

DROP TABLE IF EXISTS `mask_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mask_data` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mask_name` varchar(30) NOT NULL,
  `productid` varchar(50) DEFAULT NULL,
  `variantid` varchar(20) DEFAULT NULL,
  `side` varchar(6) NOT NULL,
  `mask_json_data` text,
  `bounds_json_data` varchar(2000) DEFAULT NULL,
  `custom_size_data` varchar(500) DEFAULT NULL,
  `mask_height` float(5,2) NOT NULL DEFAULT '0.00',
  `mask_width` float(5,2) NOT NULL DEFAULT '0.00',
  `mask_price` float(5,2) NOT NULL DEFAULT '0.00',
  `scale_ratio` float(10,5) NOT NULL DEFAULT '0.00000',
  `is_cropMark` enum('1','0') NOT NULL DEFAULT '0',
  `is_safeZone` enum('1','0') NOT NULL DEFAULT '0',
  `cropValue` float(5,2) NOT NULL,
  `safeValue` float(5,2) NOT NULL,
  `scaleRatio_unit` int(10) NOT NULL DEFAULT '1',
  `cust_min_height` float(5,2) NOT NULL DEFAULT '0.00',
  `cust_min_width` float(5,2) NOT NULL DEFAULT '0.00',
  `cust_max_height` float(5,2) NOT NULL DEFAULT '0.00',
  `cust_max_width` float(5,2) NOT NULL DEFAULT '0.00',
  `cust_bound_price` float(5,2) NOT NULL DEFAULT '0.00',
  `mask_id` int(20) NOT NULL,
  `custom_mask` text NOT NULL,
  `custom_mask_min_width` float(7,3) NOT NULL DEFAULT '0.000',
  `custom_mask_min_height` float(7,3) NOT NULL DEFAULT '0.000',
  `custom_mask_max_width` float(7,3) NOT NULL DEFAULT '0.000',
  `custom_mask_max_height` float(7,3) NOT NULL DEFAULT '0.000',
  `custom_boundary_unit` int(5) NOT NULL DEFAULT '0',
  `isBorderEnable` enum('1','0') NOT NULL DEFAULT '0',
  `isSidesAdded` enum('1','0') NOT NULL DEFAULT '0',
  `sidesAllowed` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mask_data`
--

LOCK TABLES `mask_data` WRITE;
/*!40000 ALTER TABLE `mask_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `mask_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mask_paths`
--

DROP TABLE IF EXISTS `mask_paths`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mask_paths` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) DEFAULT NULL,
  `svg_image` varchar(200) DEFAULT NULL,
  `thumb_image` varchar(200) DEFAULT NULL,
  `mask_id` varchar(100) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `mask_id` (`mask_id`),
  KEY `mask_id_2` (`mask_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mask_paths`
--

LOCK TABLES `mask_paths` WRITE;
/*!40000 ALTER TABLE `mask_paths` DISABLE KEYS */;
INSERT INTO `mask_paths` VALUES (3,'test6','1435824478667_0.svg','1435824478667_0.png','1435824478667','2015-07-02 08:08:03','2015-07-02 08:08:03'),(4,'test3','1435824623474_0.svg','1435824623474_0.png','1435824623474','2015-07-02 08:10:28','2015-07-02 08:10:28'),(6,'test6','1435824899851_0.svg','1435824899851_0.png','1435824899851','2015-07-02 08:15:04','2015-07-02 08:15:04');
/*!40000 ALTER TABLE `mask_paths` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `module_price`
--

DROP TABLE IF EXISTS `module_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '0',
  `status` enum('true','false') NOT NULL DEFAULT 'true',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `module_price`
--

LOCK TABLES `module_price` WRITE;
/*!40000 ALTER TABLE `module_price` DISABLE KEYS */;
INSERT INTO `module_price` VALUES (1,'Designs','true'),(2,'Webfonts','false');
/*!40000 ALTER TABLE `module_price` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `multi_bound_print_profile_rel`
--

DROP TABLE IF EXISTS `multi_bound_print_profile_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `multi_bound_print_profile_rel` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `product_id` varchar(50) NOT NULL DEFAULT '0',
  `side_index` int(10) NOT NULL DEFAULT '0',
  `parent_mask_id` int(10) NOT NULL DEFAULT '0',
  `child_mask_id` int(10) NOT NULL DEFAULT '0',
  `print_profile_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `multi_bound_print_profile_rel`
--

LOCK TABLES `multi_bound_print_profile_rel` WRITE;
/*!40000 ALTER TABLE `multi_bound_print_profile_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `multi_bound_print_profile_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `multiple_boundary`
--

DROP TABLE IF EXISTS `multiple_boundary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `multiple_boundary` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) NOT NULL,
  `svg_data` longtext NOT NULL,
  `thumb_image` varchar(200) NOT NULL,
  `mask_height` float(5,2) NOT NULL DEFAULT '0.00',
  `mask_width` float(5,2) NOT NULL DEFAULT '0.00',
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `multiple_boundary`
--

LOCK TABLES `multiple_boundary` WRITE;
/*!40000 ALTER TABLE `multiple_boundary` DISABLE KEYS */;
/*!40000 ALTER TABLE `multiple_boundary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `multiple_boundary_child`
--

DROP TABLE IF EXISTS `multiple_boundary_child`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `multiple_boundary_child` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_mask_id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `multiple_boundary_child`
--

LOCK TABLES `multiple_boundary_child` WRITE;
/*!40000 ALTER TABLE `multiple_boundary_child` DISABLE KEYS */;
/*!40000 ALTER TABLE `multiple_boundary_child` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `multiple_boundary_rel`
--

DROP TABLE IF EXISTS `multiple_boundary_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `multiple_boundary_rel` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `product_id` varchar(50) NOT NULL,
  `side_index` tinyint(4) NOT NULL,
  `parent_mask_id` int(10) NOT NULL,
  `child_mask_id` tinyint(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `multiple_boundary_rel`
--

LOCK TABLES `multiple_boundary_rel` WRITE;
/*!40000 ALTER TABLE `multiple_boundary_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `multiple_boundary_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `multiple_boundary_settings`
--

DROP TABLE IF EXISTS `multiple_boundary_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `multiple_boundary_settings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `boundary_rel_id` int(10) NOT NULL,
  `mask_data` longtext,
  `custom_size_data` text,
  `mask_height` float(5,2) DEFAULT '0.00',
  `mask_width` float(5,2) DEFAULT '0.00',
  `is_cropmark` enum('1','0') NOT NULL DEFAULT '0',
  `is_safezone` enum('1','0') NOT NULL DEFAULT '0',
  `restrict_design` enum('1','0') NOT NULL DEFAULT '0',
  `crop_value` float(5,2) DEFAULT '0.00',
  `safe_value` float(5,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `multiple_boundary_settings`
--

LOCK TABLES `multiple_boundary_settings` WRITE;
/*!40000 ALTER TABLE `multiple_boundary_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `multiple_boundary_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_list`
--

DROP TABLE IF EXISTS `order_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_list` (
  `refid` int(11) NOT NULL,
  `orderid` varchar(50) NOT NULL,
  `pid` varchar(20) NOT NULL,
  `order_date` datetime NOT NULL,
  `status` int(5) NOT NULL,
  PRIMARY KEY (`refid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_list`
--

LOCK TABLES `order_list` WRITE;
/*!40000 ALTER TABLE `order_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `palette_category`
--

DROP TABLE IF EXISTS `palette_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `palette_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `palette_category`
--

LOCK TABLES `palette_category` WRITE;
/*!40000 ALTER TABLE `palette_category` DISABLE KEYS */;
INSERT INTO `palette_category` VALUES (3,'flat',1),(4,'Glitter',1);
/*!40000 ALTER TABLE `palette_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `palette_category_rel`
--

DROP TABLE IF EXISTS `palette_category_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `palette_category_rel` (
  `palette_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `palette_category_rel`
--

LOCK TABLES `palette_category_rel` WRITE;
/*!40000 ALTER TABLE `palette_category_rel` DISABLE KEYS */;
INSERT INTO `palette_category_rel` VALUES (6,4),(7,3),(8,3),(9,3),(10,3),(11,3),(12,3),(13,3),(14,3),(15,3),(16,4),(17,4),(18,4),(19,4),(20,4),(21,4),(22,3),(23,3),(24,3),(25,3),(26,3),(27,3),(28,3),(29,3),(30,3),(31,4),(32,4),(33,4),(34,4),(35,4),(36,4),(37,4),(38,3);
/*!40000 ALTER TABLE `palette_category_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `palette_range_price`
--

DROP TABLE IF EXISTS `palette_range_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `palette_range_price` (
  `order_range_id` int(15) NOT NULL COMMENT 'It relates to printing order range in print_order_range table.',
  `num_palettes` int(5) NOT NULL COMMENT 'It indicates the range of palettes to be printed.',
  `price` double(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `palette_range_price`
--

LOCK TABLES `palette_range_price` WRITE;
/*!40000 ALTER TABLE `palette_range_price` DISABLE KEYS */;
INSERT INTO `palette_range_price` VALUES (1,2,1.00);
/*!40000 ALTER TABLE `palette_range_price` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `palettes`
--

DROP TABLE IF EXISTS `palettes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `palettes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(100) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `price` double(10,2) NOT NULL DEFAULT '0.00',
  `is_pattern` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT 'It indicates whether it is a color or pattern or cmyk.',
  `c` int(20) NOT NULL DEFAULT '0',
  `m` int(20) NOT NULL DEFAULT '0',
  `y` int(20) NOT NULL DEFAULT '0',
  `k` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `palettes`
--

LOCK TABLES `palettes` WRITE;
/*!40000 ALTER TABLE `palettes` DISABLE KEYS */;
INSERT INTO `palettes` VALUES (1,'#c02c2c','red',0.00,'0',0,0,0,0),(6,'p6.jpg','sample pattern 2',0.00,'1',0,0,0,0),(5,'#29bf4e','test',0.00,'0',0,0,0,0),(7,'p7.jpg','sample pattern 1',0.00,'1',0,0,0,0),(9,'#f40505','red',34.00,'0',0,0,0,0),(10,'#000000','Black',5.00,'0',0,0,0,0),(11,'#006400','DarkGreen',4.00,'0',0,0,0,0),(12,'#8b0000','DarkRed',5.00,'0',0,0,0,0),(13,'#008000','Green',6.00,'0',0,0,0,0),(14,'#808080','Grey',7.00,'0',0,0,0,0),(15,'#20b2aa','LightSeaGreen',0.00,'0',0,0,0,0),(17,'#f5fffa','MintCream',0.00,'0',0,0,0,0),(18,'#da70d6','Orchid',0.00,'0',0,0,0,0),(22,'#000000','Black',0.00,'2',45,0,100,100),(23,'#ff5a00','orange',0.00,'2',0,60,100,0),(24,'#ff0000','red',0.00,'2',0,100,100,20),(25,'#a7f700','greenyellow',0.00,'2',3,0,82,0),(26,'#7200b4','dark orchid',0.00,'2',25,76,0,45),(27,'#ff00ff','magenta',0.00,'2',0,100,0,0),(28,'#00ffff','cyan',0.00,'2',100,0,0,0),(29,'#117300','green',0.00,'2',85,0,100,55),(38,'p38.png','Pink',0.00,'1',0,0,0,0),(37,'p37.png','Gold',0.00,'1',0,0,0,0),(36,'p36.png','Blue',0.00,'1',0,0,0,0),(30,'#0000ff','Blue',0.00,'2',100,100,0,0);
/*!40000 ALTER TABLE `palettes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `preloaded_items`
--

DROP TABLE IF EXISTS `preloaded_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `preloaded_items` (
  `pk_id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `value` tinyint(2) NOT NULL,
  PRIMARY KEY (`pk_id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preloaded_items`
--

LOCK TABLES `preloaded_items` WRITE;
/*!40000 ALTER TABLE `preloaded_items` DISABLE KEYS */;
INSERT INTO `preloaded_items` VALUES (1,'Design',5),(2,'Shape',5),(3,'Product',5),(4,'Web_Font',5),(5,'Product_Variant',5),(6,'TextFx',5),(7,'Distress_Effect',5),(8,'Template',5),(30,'Background',5),(31,'Background_Pattern',5);
/*!40000 ALTER TABLE `preloaded_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `preview_image_data`
--

DROP TABLE IF EXISTS `preview_image_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `preview_image_data` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `refid` bigint(20) NOT NULL,
  `side` varchar(10) NOT NULL,
  `image` varchar(50) DEFAULT NULL,
  `svg` varchar(20) DEFAULT NULL,
  `preview_svg` varchar(200) DEFAULT NULL,
  `product_url` varchar(300) DEFAULT NULL,
  `type` varchar(4) NOT NULL,
  `image_generated` int(2) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `print_id` int(11) NOT NULL,
  `design_status` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `refid` (`refid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preview_image_data`
--

LOCK TABLES `preview_image_data` WRITE;
/*!40000 ALTER TABLE `preview_image_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `preview_image_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_image_upload_price`
--

DROP TABLE IF EXISTS `print_image_upload_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_image_upload_price` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `print_method_id` int(11) NOT NULL,
  `no_of_allowed` int(5) NOT NULL,
  `image_price` float(5,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_image_upload_price`
--

LOCK TABLES `print_image_upload_price` WRITE;
/*!40000 ALTER TABLE `print_image_upload_price` DISABLE KEYS */;
/*!40000 ALTER TABLE `print_image_upload_price` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_method`
--

DROP TABLE IF EXISTS `print_method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_method` (
  `pk_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `is_enable` enum('1','0') NOT NULL DEFAULT '1',
  `file_type` varchar(5) DEFAULT NULL,
  `added_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `is_delete` enum('1','0') NOT NULL DEFAULT '0',
  `text_fillcolor` varchar(20) DEFAULT NULL,
  `text_strokecolor` varchar(20) DEFAULT NULL,
  `wc_color1` varchar(20) DEFAULT NULL,
  `wc_color2` varchar(20) DEFAULT NULL,
  `wc_color3` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`pk_id`),
  UNIQUE KEY `pk_id` (`pk_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_method`
--

LOCK TABLES `print_method` WRITE;
/*!40000 ALTER TABLE `print_method` DISABLE KEYS */;
INSERT INTO `print_method` VALUES (1,'DTG','1','png','2015-07-02 06:21:16','0000-00-00 00:00:00','0','','','','',''),(3,'Screen','1','png','2015-09-03 18:35:05','0000-00-00 00:00:00','0','','','','','');
/*!40000 ALTER TABLE `print_method` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_method_color_area_price_rel`
--

DROP TABLE IF EXISTS `print_method_color_area_price_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_method_color_area_price_rel` (
  `print_method_id` int(10) NOT NULL,
  `print_size_id` int(10) NOT NULL,
  `price` float(10,2) NOT NULL,
  `percentage` float(5,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_method_color_area_price_rel`
--

LOCK TABLES `print_method_color_area_price_rel` WRITE;
/*!40000 ALTER TABLE `print_method_color_area_price_rel` DISABLE KEYS */;
INSERT INTO `print_method_color_area_price_rel` VALUES (3,8,0.00,0.00),(3,7,0.00,0.00),(3,6,0.00,0.00),(3,5,0.00,0.00),(3,4,0.00,0.00),(3,3,0.00,0.00),(3,2,0.00,0.00),(3,1,0.00,0.00),(1,8,0.00,0.00),(1,7,0.00,0.00),(1,6,0.00,0.00),(1,5,0.00,0.00),(1,4,0.00,0.00),(1,3,0.00,0.00),(1,2,0.00,0.00),(1,1,0.00,0.00);
/*!40000 ALTER TABLE `print_method_color_area_price_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_method_color_group_rel`
--

DROP TABLE IF EXISTS `print_method_color_group_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_method_color_group_rel` (
  `print_method_id` int(11) NOT NULL,
  `color_group_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_method_color_group_rel`
--

LOCK TABLES `print_method_color_group_rel` WRITE;
/*!40000 ALTER TABLE `print_method_color_group_rel` DISABLE KEYS */;
INSERT INTO `print_method_color_group_rel` VALUES (1,1),(3,2);
/*!40000 ALTER TABLE `print_method_color_group_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_method_design_rel`
--

DROP TABLE IF EXISTS `print_method_design_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_method_design_rel` (
  `print_method_id` int(11) NOT NULL,
  `design_id` int(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_method_design_rel`
--

LOCK TABLES `print_method_design_rel` WRITE;
/*!40000 ALTER TABLE `print_method_design_rel` DISABLE KEYS */;
INSERT INTO `print_method_design_rel` VALUES (1,7);
/*!40000 ALTER TABLE `print_method_design_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_method_feature_rel`
--

DROP TABLE IF EXISTS `print_method_feature_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_method_feature_rel` (
  `print_method_id` int(11) NOT NULL,
  `feature_id` smallint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_method_feature_rel`
--

LOCK TABLES `print_method_feature_rel` WRITE;
/*!40000 ALTER TABLE `print_method_feature_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `print_method_feature_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_method_fonts_rel`
--

DROP TABLE IF EXISTS `print_method_fonts_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_method_fonts_rel` (
  `print_method_id` int(11) NOT NULL,
  `font_id` int(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_method_fonts_rel`
--

LOCK TABLES `print_method_fonts_rel` WRITE;
/*!40000 ALTER TABLE `print_method_fonts_rel` DISABLE KEYS */;
INSERT INTO `print_method_fonts_rel` VALUES (1,12);
/*!40000 ALTER TABLE `print_method_fonts_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_method_palette_category`
--

DROP TABLE IF EXISTS `print_method_palette_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_method_palette_category` (
  `print_method_id` int(11) NOT NULL,
  `palette_category_id` int(10) NOT NULL,
  `is_enable` enum('1','0') NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_method_palette_category`
--

LOCK TABLES `print_method_palette_category` WRITE;
/*!40000 ALTER TABLE `print_method_palette_category` DISABLE KEYS */;
INSERT INTO `print_method_palette_category` VALUES (1,4,'0'),(1,3,'0'),(3,4,'0'),(3,3,'0');
/*!40000 ALTER TABLE `print_method_palette_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_method_palette_rel`
--

DROP TABLE IF EXISTS `print_method_palette_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_method_palette_rel` (
  `print_method_id` int(11) NOT NULL,
  `palette_id` int(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_method_palette_rel`
--

LOCK TABLES `print_method_palette_rel` WRITE;
/*!40000 ALTER TABLE `print_method_palette_rel` DISABLE KEYS */;
INSERT INTO `print_method_palette_rel` VALUES (1,6),(1,7),(1,1),(1,5);
/*!40000 ALTER TABLE `print_method_palette_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_method_quantity_range_rel`
--

DROP TABLE IF EXISTS `print_method_quantity_range_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_method_quantity_range_rel` (
  `pk_id` int(11) NOT NULL AUTO_INCREMENT,
  `print_method_id` int(11) NOT NULL,
  `print_quantity_range_id` int(11) NOT NULL,
  `no_of_colors` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `color_price` float(10,2) NOT NULL DEFAULT '0.00',
  `white_base_price` float(10,2) NOT NULL DEFAULT '0.00',
  `color_percentage` float(10,2) NOT NULL DEFAULT '0.00',
  `white_base_percentage` float(5,2) NOT NULL DEFAULT '0.00',
  `is_fixed` enum('0','1') NOT NULL DEFAULT '0',
  `is_check` enum('0','1') NOT NULL DEFAULT '0',
  `is_exist` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`pk_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_method_quantity_range_rel`
--

LOCK TABLES `print_method_quantity_range_rel` WRITE;
/*!40000 ALTER TABLE `print_method_quantity_range_rel` DISABLE KEYS */;
INSERT INTO `print_method_quantity_range_rel` VALUES (5,1,1,1,0.00,0.00,0.00,0.00,'0','1','0'),(3,3,3,1,0.00,0.00,0.00,0.00,'0','1','0'),(4,3,3,1,0.00,0.00,0.00,0.00,'0','0','0'),(6,1,1,1,0.00,0.00,0.00,0.00,'0','0','0');
/*!40000 ALTER TABLE `print_method_quantity_range_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_method_setting_rel`
--

DROP TABLE IF EXISTS `print_method_setting_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_method_setting_rel` (
  `print_method_id` int(11) unsigned NOT NULL,
  `print_setting_id` int(11) unsigned NOT NULL,
  `is_delete` enum('1','0') NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_method_setting_rel`
--

LOCK TABLES `print_method_setting_rel` WRITE;
/*!40000 ALTER TABLE `print_method_setting_rel` DISABLE KEYS */;
INSERT INTO `print_method_setting_rel` VALUES (1,1,'0'),(3,3,'0');
/*!40000 ALTER TABLE `print_method_setting_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_order_range`
--

DROP TABLE IF EXISTS `print_order_range`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_order_range` (
  `id` int(15) NOT NULL AUTO_INCREMENT COMMENT 'It is the range id which related to palette_price table.',
  `lower_limit` int(11) NOT NULL COMMENT 'It is the minimum order limit of a range.',
  `upper_limit` int(11) NOT NULL COMMENT 'It is the upper order limit of a range.',
  `printtype_id` int(11) NOT NULL COMMENT 'It relates to printing type in printing_details table.',
  `whitebase_price` double(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_order_range`
--

LOCK TABLES `print_order_range` WRITE;
/*!40000 ALTER TABLE `print_order_range` DISABLE KEYS */;
INSERT INTO `print_order_range` VALUES (1,50,100,1,1.00);
/*!40000 ALTER TABLE `print_order_range` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_quantity_range`
--

DROP TABLE IF EXISTS `print_quantity_range`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_quantity_range` (
  `pk_id` int(11) NOT NULL AUTO_INCREMENT,
  `from_range` int(1) NOT NULL DEFAULT '1',
  `to_range` int(1) NOT NULL DEFAULT '10',
  PRIMARY KEY (`pk_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_quantity_range`
--

LOCK TABLES `print_quantity_range` WRITE;
/*!40000 ALTER TABLE `print_quantity_range` DISABLE KEYS */;
INSERT INTO `print_quantity_range` VALUES (1,0,10),(2,0,10),(3,1,10);
/*!40000 ALTER TABLE `print_quantity_range` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_setting`
--

DROP TABLE IF EXISTS `print_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_setting` (
  `pk_id` int(11) NOT NULL AUTO_INCREMENT,
  `is_min_order` enum('1','0') NOT NULL DEFAULT '1',
  `min_order_quantity` int(20) NOT NULL DEFAULT '1',
  `is_white_base` enum('1','0') NOT NULL DEFAULT '1',
  `white_base_price` float(7,2) NOT NULL DEFAULT '0.00',
  `is_clip_art` enum('1','0') NOT NULL DEFAULT '1',
  `is_font` enum('1','0') NOT NULL DEFAULT '1',
  `is_additional_price` enum('1','0') NOT NULL DEFAULT '1',
  `additional_price` float(7,2) NOT NULL DEFAULT '0.00',
  `is_setup_cost` enum('1','0') NOT NULL DEFAULT '1',
  `setup_cost` float(7,2) NOT NULL DEFAULT '0.00',
  `is_scalling` enum('1','0') NOT NULL DEFAULT '0',
  `scalling_price` float(7,3) NOT NULL DEFAULT '0.000',
  `is_color_price_range` enum('1','0') NOT NULL DEFAULT '0',
  `is_percentage` enum('1','0') NOT NULL DEFAULT '1',
  `is_print_size` enum('1','0') NOT NULL DEFAULT '1',
  `is_used_colors` enum('1','0') NOT NULL DEFAULT '0',
  `is_color_chooser` enum('1','0') NOT NULL DEFAULT '0',
  `is_color_group_price` enum('1','0') NOT NULL DEFAULT '1',
  `is_product_side` enum('1','0') NOT NULL DEFAULT '0',
  `is_single_order` enum('1','0') NOT NULL DEFAULT '0',
  `is_no_of_used_colors` enum('1','0') NOT NULL DEFAULT '0',
  `other_color_group_price` float(7,2) NOT NULL DEFAULT '0.00',
  `is_default` enum('1','0') NOT NULL DEFAULT '0',
  `added_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `is_delete` enum('1','0') NOT NULL DEFAULT '0',
  `max_palettes` int(11) NOT NULL DEFAULT '3',
  `is_max_palettes` enum('0','1') NOT NULL DEFAULT '0',
  `is_gray_scale` enum('1','0','2') CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `is_qrcode_whitebase` enum('1','0') NOT NULL DEFAULT '0',
  `screen_cost` float(7,2) NOT NULL DEFAULT '0.00',
  `is_forcecolor` enum('1','0') NOT NULL DEFAULT '0',
  `is_palette` enum('1','0') NOT NULL DEFAULT '0',
  `is_color_area_price` enum('0','1') NOT NULL DEFAULT '0',
  `is_print_area_percentage` enum('0','1') NOT NULL DEFAULT '0',
  `is_multiline_text_price` enum('0','1') NOT NULL DEFAULT '0',
  `is_background` enum('1','0') NOT NULL DEFAULT '0',
  `is_image_upload_price` enum('1','0') NOT NULL DEFAULT '0',
  `is_calulate_multiple_side` enum('1','0') NOT NULL DEFAULT '0',
  `image_upload_price` float(7,2) NOT NULL DEFAULT '0.00',
  `is_engrave` enum('1','0','2') CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `is_browse_allow` enum('1','0') DEFAULT '0',
  `is_terms_and_condition_allow` enum('1','0') DEFAULT '0',
  `terms_condition` text,
  PRIMARY KEY (`pk_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_setting`
--

LOCK TABLES `print_setting` WRITE;
/*!40000 ALTER TABLE `print_setting` DISABLE KEYS */;
INSERT INTO `print_setting` VALUES (1,'1',1,'1',0.00,'1','1','1',0.00,'1',0.00,'0',0.000,'0','1','1','0','0','1','0','0','0',0.00,'1','2015-07-02 06:21:16','0000-00-00 00:00:00','0',3,'0','0','0',10.00,'0','0','0','0','0','0','0','0',0.00,'0','0','0',NULL),(3,'1',1,'1',0.00,'1','1','1',0.00,'1',0.00,'0',0.000,'1','1','0','0','0','1','0','0','0',0.00,'0','2015-09-03 18:35:05','0000-00-00 00:00:00','0',3,'0','0','0',0.00,'0','0','0','0','0','0','0','0',0.00,'0','0','0',NULL);
/*!40000 ALTER TABLE `print_setting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_size`
--

DROP TABLE IF EXISTS `print_size`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_size` (
  `pk_id` smallint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `width` float(10,2) NOT NULL,
  `height` float(10,2) NOT NULL,
  `is_user_defined` enum('1','0') NOT NULL DEFAULT '0',
  `is_default` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`pk_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_size`
--

LOCK TABLES `print_size` WRITE;
/*!40000 ALTER TABLE `print_size` DISABLE KEYS */;
INSERT INTO `print_size` VALUES (1,'A1',33.11,23.39,'0','0'),(2,'A2',23.39,16.54,'0','0'),(3,'A3',16.54,11.69,'0','0'),(4,'A4',11.69,8.27,'0','1'),(5,'A5',8.27,5.83,'0','0'),(6,'A6',5.83,4.13,'0','0'),(7,'A7',4.13,2.91,'0','0'),(8,'A8',2.91,2.05,'0','0');
/*!40000 ALTER TABLE `print_size` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_size_method_rel`
--

DROP TABLE IF EXISTS `print_size_method_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_size_method_rel` (
  `print_size_id` int(11) unsigned NOT NULL,
  `print_method_id` int(11) unsigned NOT NULL,
  `price` float(10,2) NOT NULL,
  `percentage` float(10,2) NOT NULL,
  `is_fixed` enum('0','1') NOT NULL DEFAULT '0',
  `is_whitebase` enum('0','1') NOT NULL DEFAULT '0',
  `print_size_range_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_size_method_rel`
--

LOCK TABLES `print_size_method_rel` WRITE;
/*!40000 ALTER TABLE `print_size_method_rel` DISABLE KEYS */;
INSERT INTO `print_size_method_rel` VALUES (0,1,0.00,0.00,'0','0',1),(8,1,0.00,0.00,'0','0',1),(7,1,0.00,0.00,'0','0',1),(6,1,0.00,0.00,'0','0',1),(5,1,0.00,0.00,'0','0',1),(4,1,0.00,0.00,'0','0',1),(3,1,0.00,0.00,'0','0',1),(2,1,0.00,0.00,'0','0',1),(1,3,0.00,0.00,'0','0',0),(2,3,0.00,0.00,'0','0',0),(3,3,0.00,0.00,'0','0',0),(4,3,0.00,0.00,'0','0',0),(5,3,0.00,0.00,'0','0',0),(6,3,0.00,0.00,'0','0',0),(7,3,0.00,0.00,'0','0',0),(8,3,0.00,0.00,'0','0',0),(1,1,0.00,0.00,'0','0',1);
/*!40000 ALTER TABLE `print_size_method_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_size_range`
--

DROP TABLE IF EXISTS `print_size_range`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_size_range` (
  `pk_id` int(10) NOT NULL AUTO_INCREMENT,
  `from_range` int(10) NOT NULL,
  `to_range` int(10) NOT NULL,
  PRIMARY KEY (`pk_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_size_range`
--

LOCK TABLES `print_size_range` WRITE;
/*!40000 ALTER TABLE `print_size_range` DISABLE KEYS */;
INSERT INTO `print_size_range` VALUES (1,1,1);
/*!40000 ALTER TABLE `print_size_range` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_textline_price_rel`
--

DROP TABLE IF EXISTS `print_textline_price_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_textline_price_rel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `print_method_id` int(10) NOT NULL,
  `text_price` float(10,2) NOT NULL,
  `no_of_allowed` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_textline_price_rel`
--

LOCK TABLES `print_textline_price_rel` WRITE;
/*!40000 ALTER TABLE `print_textline_price_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `print_textline_price_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `printing_details`
--

DROP TABLE IF EXISTS `printing_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `printing_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `print_type` varchar(100) NOT NULL DEFAULT 'digital_printing',
  `description` varchar(5000) NOT NULL,
  `status` enum('true','false') NOT NULL DEFAULT 'false' COMMENT 'It indicates which printing type is active.',
  `additional_price_status` enum('true','false') NOT NULL DEFAULT 'false' COMMENT 'It inducates whether a color variant(product) has extra price for the printing type or not.',
  `setup_price` double(10,2) NOT NULL DEFAULT '0.00' COMMENT 'It holds the setup fee/price for a printing type.',
  `palette_setup_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'It indicates whether the printing type has extra price table w.r.t. a palette-range or not.',
  `max_palettes_limit` int(5) NOT NULL DEFAULT '10' COMMENT 'It indicates the maximum number of palettes allowed for printing.',
  `min_quantity` int(11) NOT NULL DEFAULT '1' COMMENT 'It indicates the minimum mandatory limit to order printing.',
  `whitebase_price` double(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Not applicable for Screen-printing. Screen-printing has its own price table(print_order_range)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `printing_details`
--

LOCK TABLES `printing_details` WRITE;
/*!40000 ALTER TABLE `printing_details` DISABLE KEYS */;
INSERT INTO `printing_details` VALUES (1,'DTG Print','digital_printing','<b>Direct To Garment Print(DTG Print)</b> is an emerging digital print method, perfect for soft-to-the-touch full colour prints. Although less vibrant & durable than their full-colour digital transfer counterparts, direct to garment printing offers a more fashionable, light and \'wearable\' print finish.<br><br>You need to specify price to each size for ease.','true','true',20.00,1,2,1,1.25);
/*!40000 ALTER TABLE `printing_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `printing_dtg_details`
--

DROP TABLE IF EXISTS `printing_dtg_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `printing_dtg_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `size` varchar(50) NOT NULL,
  `width` double(5,2) NOT NULL DEFAULT '100.00',
  `height` double(5,2) NOT NULL DEFAULT '100.00',
  `price` double(10,2) NOT NULL DEFAULT '0.00',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `screenprint_percentage` double(10,2) NOT NULL DEFAULT '5.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COMMENT='It holds size and price data for DTG print.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `printing_dtg_details`
--

LOCK TABLES `printing_dtg_details` WRITE;
/*!40000 ALTER TABLE `printing_dtg_details` DISABLE KEYS */;
INSERT INTO `printing_dtg_details` VALUES (1,'A1',100.00,100.00,6.00,1,5.00),(2,'A2',100.00,100.00,5.00,1,5.00);
/*!40000 ALTER TABLE `printing_dtg_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `printmethod_additional_prices`
--

DROP TABLE IF EXISTS `printmethod_additional_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `printmethod_additional_prices` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `productid` varchar(50) NOT NULL,
  `price` double(10,2) NOT NULL DEFAULT '0.00',
  `print_method_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `printmethod_additional_prices`
--

LOCK TABLES `printmethod_additional_prices` WRITE;
/*!40000 ALTER TABLE `printmethod_additional_prices` DISABLE KEYS */;
/*!40000 ALTER TABLE `printmethod_additional_prices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `printtype_palette_rel`
--

DROP TABLE IF EXISTS `printtype_palette_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `printtype_palette_rel` (
  `print_type_id` int(11) NOT NULL,
  `palette_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `printtype_palette_rel`
--

LOCK TABLES `printtype_palette_rel` WRITE;
/*!40000 ALTER TABLE `printtype_palette_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `printtype_palette_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_additional_prices`
--

DROP TABLE IF EXISTS `product_additional_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_additional_prices` (
  `pk_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` varchar(50) NOT NULL,
  `variant_id` varchar(50) NOT NULL,
  `print_method_id` int(11) NOT NULL,
  `price` double(10,2) NOT NULL DEFAULT '0.00',
  `is_whitebase` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`pk_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_additional_prices`
--

LOCK TABLES `product_additional_prices` WRITE;
/*!40000 ALTER TABLE `product_additional_prices` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_additional_prices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_category_printmethod_rel`
--

DROP TABLE IF EXISTS `product_category_printmethod_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_category_printmethod_rel` (
  `print_method_id` int(11) NOT NULL,
  `product_category_id` int(11) NOT NULL,
  `is_enable` enum('1','0') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_category_printmethod_rel`
--

LOCK TABLES `product_category_printmethod_rel` WRITE;
/*!40000 ALTER TABLE `product_category_printmethod_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_category_printmethod_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_feature_rel`
--

DROP TABLE IF EXISTS `product_feature_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_feature_rel` (
  `product_id` varchar(50) NOT NULL,
  `feature_id` int(3) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_feature_rel`
--

LOCK TABLES `product_feature_rel` WRITE;
/*!40000 ALTER TABLE `product_feature_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_feature_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_print_discount_rel`
--

DROP TABLE IF EXISTS `product_print_discount_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_print_discount_rel` (
  `product_id` int(10) NOT NULL,
  `print_id` int(10) NOT NULL,
  `discount_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_print_discount_rel`
--

LOCK TABLES `product_print_discount_rel` WRITE;
/*!40000 ALTER TABLE `product_print_discount_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_print_discount_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_printarea_type`
--

DROP TABLE IF EXISTS `product_printarea_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_printarea_type` (
  `productid` varchar(50) NOT NULL,
  `mask` enum('true','false') NOT NULL DEFAULT 'false',
  `bounds` enum('true','false') NOT NULL DEFAULT 'true',
  `custom_size` enum('true','false') NOT NULL DEFAULT 'false',
  `unit_id` int(5) NOT NULL DEFAULT '1' COMMENT 'It indicates the unit of measurement from ''units'' table.',
  `price_per_unit` double(10,2) NOT NULL DEFAULT '0.00' COMMENT 'It holds the price per unit area for user defined size of product.',
  `max_height` double(10,2) NOT NULL DEFAULT '500.00',
  `max_width` double(10,2) NOT NULL DEFAULT '500.00',
  `custom_mask` enum('true','false') NOT NULL DEFAULT 'false',
  PRIMARY KEY (`productid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_printarea_type`
--

LOCK TABLES `product_printarea_type` WRITE;
/*!40000 ALTER TABLE `product_printarea_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_printarea_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_printmethod_rel`
--

DROP TABLE IF EXISTS `product_printmethod_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_printmethod_rel` (
  `pk_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` varchar(50) NOT NULL,
  `print_method_id` int(11) NOT NULL,
  PRIMARY KEY (`pk_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_printmethod_rel`
--

LOCK TABLES `product_printmethod_rel` WRITE;
/*!40000 ALTER TABLE `product_printmethod_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_printmethod_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_sides_sizes`
--

DROP TABLE IF EXISTS `product_sides_sizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_sides_sizes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `productid` varchar(50) NOT NULL,
  `side` tinyint(6) NOT NULL DEFAULT '0' COMMENT 'Side of a product irrespective of color-variants',
  `printsize` varchar(50) DEFAULT NULL COMMENT 'DTG size(A3/A4/A5/A6/Custom size) specification',
  `is_transition` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_sides_sizes`
--

LOCK TABLES `product_sides_sizes` WRITE;
/*!40000 ALTER TABLE `product_sides_sizes` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_sides_sizes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_temp_rel`
--

DROP TABLE IF EXISTS `product_temp_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_temp_rel` (
  `product_id` int(20) NOT NULL,
  `temp_id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_temp_rel`
--

LOCK TABLES `product_temp_rel` WRITE;
/*!40000 ALTER TABLE `product_temp_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_temp_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_temp_side`
--

DROP TABLE IF EXISTS `product_temp_side`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_temp_side` (
  `pk_id` int(50) NOT NULL AUTO_INCREMENT,
  `product_temp_id` int(50) NOT NULL,
  `side_name` varchar(200) NOT NULL,
  `sort_order` int(20) NOT NULL,
  `image` varchar(40) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`pk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_temp_side`
--

LOCK TABLES `product_temp_side` WRITE;
/*!40000 ALTER TABLE `product_temp_side` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_temp_side` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_template`
--

DROP TABLE IF EXISTS `product_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_template` (
  `pk_id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime DEFAULT NULL,
  `is_default` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`pk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_template`
--

LOCK TABLES `product_template` WRITE;
/*!40000 ALTER TABLE `product_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productcategory_feature_rel`
--

DROP TABLE IF EXISTS `productcategory_feature_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productcategory_feature_rel` (
  `product_category_id` int(5) NOT NULL,
  `feature_id` int(3) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productcategory_feature_rel`
--

LOCK TABLES `productcategory_feature_rel` WRITE;
/*!40000 ALTER TABLE `productcategory_feature_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `productcategory_feature_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `revision`
--

DROP TABLE IF EXISTS `revision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `revision` (
  `pk_id` int(11) NOT NULL AUTO_INCREMENT,
  `comment` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pk_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `revision`
--

LOCK TABLES `revision` WRITE;
/*!40000 ALTER TABLE `revision` DISABLE KEYS */;
INSERT INTO `revision` VALUES (1,'gsdgsdfgdsfg','2015-07-01 13:16:58'),(2,'Update from admin','2015-07-22 08:18:20');
/*!40000 ALTER TABLE `revision` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schema_version`
--

DROP TABLE IF EXISTS `schema_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schema_version` (
  `version` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schema_version`
--

LOCK TABLES `schema_version` WRITE;
/*!40000 ALTER TABLE `schema_version` DISABLE KEYS */;
INSERT INTO `schema_version` VALUES (54),(54);
/*!40000 ALTER TABLE `schema_version` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings_config`
--

DROP TABLE IF EXISTS `settings_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings_config` (
  `id` int(1) unsigned NOT NULL,
  `items_per_page` int(3) unsigned NOT NULL DEFAULT '10',
  `price_per_unit` double(10,2) NOT NULL DEFAULT '0.00' COMMENT 'It holds the price per unit area of product.',
  `price_per_unit_calculation` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'It indicates whether price is to be calculated per unit area or according to size(A3/A4 etc).',
  `is_whitebase` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'It indicates whether whitebase printing is available or not.',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings_config`
--

LOCK TABLES `settings_config` WRITE;
/*!40000 ALTER TABLE `settings_config` DISABLE KEYS */;
INSERT INTO `settings_config` VALUES (1,30,4.00,0,1);
/*!40000 ALTER TABLE `settings_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shape_cat`
--

DROP TABLE IF EXISTS `shape_cat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shape_cat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(50) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shape_cat`
--

LOCK TABLES `shape_cat` WRITE;
/*!40000 ALTER TABLE `shape_cat` DISABLE KEYS */;
INSERT INTO `shape_cat` VALUES (3,'Star',1),(5,'circle',1);
/*!40000 ALTER TABLE `shape_cat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shape_cat_rel`
--

DROP TABLE IF EXISTS `shape_cat_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shape_cat_rel` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `shape_id` int(5) NOT NULL DEFAULT '0',
  `category_id` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shape_cat_rel`
--

LOCK TABLES `shape_cat_rel` WRITE;
/*!40000 ALTER TABLE `shape_cat_rel` DISABLE KEYS */;
INSERT INTO `shape_cat_rel` VALUES (6,2,0),(7,2,0),(14,3,0),(15,3,0),(16,3,0),(17,3,0),(19,10,3),(22,14,5),(23,14,5),(24,13,5),(25,17,5);
/*!40000 ALTER TABLE `shape_cat_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shape_tag_rel`
--

DROP TABLE IF EXISTS `shape_tag_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shape_tag_rel` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `shape_id` int(5) NOT NULL DEFAULT '0',
  `tag_id` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shape_tag_rel`
--

LOCK TABLES `shape_tag_rel` WRITE;
/*!40000 ALTER TABLE `shape_tag_rel` DISABLE KEYS */;
INSERT INTO `shape_tag_rel` VALUES (1,1,1),(2,1,2),(10,2,3),(11,2,4),(18,3,3),(19,3,4),(20,3,3),(21,3,4),(24,5,1),(25,6,1),(26,7,1),(27,8,1),(28,9,1),(30,11,1),(32,12,1),(33,10,1),(35,14,1),(36,14,1),(37,13,1);
/*!40000 ALTER TABLE `shape_tag_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shape_tags`
--

DROP TABLE IF EXISTS `shape_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shape_tags` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT 'na',
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shape_tags`
--

LOCK TABLES `shape_tags` WRITE;
/*!40000 ALTER TABLE `shape_tags` DISABLE KEYS */;
INSERT INTO `shape_tags` VALUES (1,'test',1),(2,'test3',1),(3,'fgfh',1),(4,'huu',1),(5,'test7',1);
/*!40000 ALTER TABLE `shape_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shapes`
--

DROP TABLE IF EXISTS `shapes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shapes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(50) NOT NULL DEFAULT 'na',
  `shape_name` varchar(100) NOT NULL DEFAULT 'na',
  `price` double(10,2) NOT NULL DEFAULT '0.00',
  `status` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shapes`
--

LOCK TABLES `shapes` WRITE;
/*!40000 ALTER TABLE `shapes` DISABLE KEYS */;
INSERT INTO `shapes` VALUES (11,'s_11','test',5.00,1),(13,'s_13','test2',2.00,1),(14,'s_14','test2',2.00,1);
/*!40000 ALTER TABLE `shapes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `size_variant_additional_price`
--

DROP TABLE IF EXISTS `size_variant_additional_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `size_variant_additional_price` (
  `pk_id` int(10) NOT NULL AUTO_INCREMENT,
  `product_id` int(10) NOT NULL,
  `print_method_id` int(10) NOT NULL,
  `xe_size_id` int(10) NOT NULL,
  `percentage` float(5,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`pk_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `size_variant_additional_price`
--

LOCK TABLES `size_variant_additional_price` WRITE;
/*!40000 ALTER TABLE `size_variant_additional_price` DISABLE KEYS */;
/*!40000 ALTER TABLE `size_variant_additional_price` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `social_site_values`
--

DROP TABLE IF EXISTS `social_site_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `social_site_values` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `site_id` int(5) NOT NULL,
  `key_index` varchar(60) CHARACTER SET utf8 NOT NULL,
  `key_value` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_site_values`
--

LOCK TABLES `social_site_values` WRITE;
/*!40000 ALTER TABLE `social_site_values` DISABLE KEYS */;
INSERT INTO `social_site_values` VALUES (1,1,'client_id',NULL);
/*!40000 ALTER TABLE `social_site_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `social_sites`
--

DROP TABLE IF EXISTS `social_sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `social_sites` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_sites`
--

LOCK TABLES `social_sites` WRITE;
/*!40000 ALTER TABLE `social_sites` DISABLE KEYS */;
INSERT INTO `social_sites` VALUES (1,'Instagram');
/*!40000 ALTER TABLE `social_sites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `svg_data`
--

DROP TABLE IF EXISTS `svg_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `svg_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customerid` int(11) NOT NULL,
  `svg` varchar(50) NOT NULL,
  `date_created` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `svg_data`
--

LOCK TABLES `svg_data` WRITE;
/*!40000 ALTER TABLE `svg_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `svg_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `swatches`
--

DROP TABLE IF EXISTS `swatches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `swatches` (
  `pk_id` int(10) NOT NULL AUTO_INCREMENT,
  `attribute_id` bigint(10) NOT NULL DEFAULT '0',
  `hex_code` varchar(20) DEFAULT NULL,
  `image_name` varchar(35) DEFAULT NULL,
  PRIMARY KEY (`pk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `swatches`
--

LOCK TABLES `swatches` WRITE;
/*!40000 ALTER TABLE `swatches` DISABLE KEYS */;
/*!40000 ALTER TABLE `swatches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sync_order`
--

DROP TABLE IF EXISTS `sync_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sync_order` (
  `pk_id` int(10) NOT NULL AUTO_INCREMENT,
  `order_status` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0=>pending, 1=>printed',
  `orderId` varchar(10) NOT NULL,
  `fileName` varchar(80) DEFAULT NULL,
  `last_sync_on` datetime DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0=>failed, 1=>successful',
  PRIMARY KEY (`pk_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sync_order`
--

LOCK TABLES `sync_order` WRITE;
/*!40000 ALTER TABLE `sync_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `sync_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tabs`
--

DROP TABLE IF EXISTS `tabs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tabs` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `symbol` varchar(2) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'It indicates whether the tab is default or not.',
  `default_subtab_id` int(3) NOT NULL DEFAULT '0' COMMENT 'It specifies the deafault subtab of a tab. Value is 0 if the tab doesn''t contain any subtab.',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tabs`
--

LOCK TABLES `tabs` WRITE;
/*!40000 ALTER TABLE `tabs` DISABLE KEYS */;
INSERT INTO `tabs` VALUES (1,'Product','P',0,0),(2,'Graphics','D',1,4),(3,'Text','T',0,5);
/*!40000 ALTER TABLE `tabs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` VALUES (1,'jjkk'),(2,'khk'),(3,'hhh'),(4,'jhj'),(5,'test4'),(6,'test1'),(7,'test8'),(8,'test');
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `template_category`
--

DROP TABLE IF EXISTS `template_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `template_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `template_category`
--

LOCK TABLES `template_category` WRITE;
/*!40000 ALTER TABLE `template_category` DISABLE KEYS */;
INSERT INTO `template_category` VALUES (1,'Birthday'),(2,'Holiday'),(3,'Friendship Day'),(4,'Funny Tshirts'),(5,'Social Media'),(6,'Children');
/*!40000 ALTER TABLE `template_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `template_category_printmethod_rel`
--

DROP TABLE IF EXISTS `template_category_printmethod_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `template_category_printmethod_rel` (
  `print_method_id` int(11) NOT NULL,
  `temp_category_id` int(11) NOT NULL,
  `is_enable` enum('1','0') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `template_category_printmethod_rel`
--

LOCK TABLES `template_category_printmethod_rel` WRITE;
/*!40000 ALTER TABLE `template_category_printmethod_rel` DISABLE KEYS */;
INSERT INTO `template_category_printmethod_rel` VALUES (1,6,'0'),(1,5,'0'),(1,4,'0'),(1,3,'0'),(1,2,'0'),(1,1,'0'),(3,1,'0'),(3,2,'0'),(3,3,'0'),(3,4,'0'),(3,5,'0'),(3,6,'0');
/*!40000 ALTER TABLE `template_category_printmethod_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `template_product_rel`
--

DROP TABLE IF EXISTS `template_product_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `template_product_rel` (
  `template_id` int(11) NOT NULL,
  `product_id` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `template_product_rel`
--

LOCK TABLES `template_product_rel` WRITE;
/*!40000 ALTER TABLE `template_product_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `template_product_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `template_state`
--

DROP TABLE IF EXISTS `template_state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `template_state` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `json_data` longtext NOT NULL,
  `product_image` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `template_image` varchar(100) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `sub_id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `pvid` int(11) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `template_state`
--

LOCK TABLES `template_state` WRITE;
/*!40000 ALTER TABLE `template_state` DISABLE KEYS */;
/*!40000 ALTER TABLE `template_state` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `template_state_rel`
--

DROP TABLE IF EXISTS `template_state_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `template_state_rel` (
  `pk_id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_id` int(11) DEFAULT NULL,
  `temp_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`pk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `template_state_rel`
--

LOCK TABLES `template_state_rel` WRITE;
/*!40000 ALTER TABLE `template_state_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `template_state_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `template_subcategory`
--

DROP TABLE IF EXISTS `template_subcategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `template_subcategory` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `cat_id` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `template_subcategory`
--

LOCK TABLES `template_subcategory` WRITE;
/*!40000 ALTER TABLE `template_subcategory` DISABLE KEYS */;
/*!40000 ALTER TABLE `template_subcategory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `textart`
--

DROP TABLE IF EXISTS `textart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `textart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `textArtfontList` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `textart`
--

LOCK TABLES `textart` WRITE;
/*!40000 ALTER TABLE `textart` DISABLE KEYS */;
/*!40000 ALTER TABLE `textart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `textfx_charecters`
--

DROP TABLE IF EXISTS `textfx_charecters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `textfx_charecters` (
  `pk_id` int(10) NOT NULL AUTO_INCREMENT,
  `textfx_style_id` smallint(10) NOT NULL,
  `alphabate` varchar(50) NOT NULL,
  PRIMARY KEY (`pk_id`)
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `textfx_charecters`
--

LOCK TABLES `textfx_charecters` WRITE;
/*!40000 ALTER TABLE `textfx_charecters` DISABLE KEYS */;
INSERT INTO `textfx_charecters` VALUES (1,1,'a'),(2,1,'h'),(3,1,'f'),(4,1,'c'),(5,1,'j'),(6,1,'g'),(7,1,'b'),(8,1,'e'),(9,1,'k'),(10,1,'d'),(11,1,'i'),(12,1,'l'),(13,2,'a'),(14,2,'c'),(15,2,'d'),(16,2,'e'),(17,2,'f'),(18,2,'g'),(19,2,'h'),(20,2,'i'),(21,2,'j'),(22,2,'k'),(23,2,'m'),(24,2,'n'),(25,2,'o'),(26,2,'p'),(27,2,'q'),(28,2,'r'),(29,2,'s'),(30,2,'t'),(31,2,'u'),(32,2,'l'),(33,2,'b'),(34,2,'v'),(35,2,'w'),(36,2,'x'),(37,2,'y'),(38,2,'z'),(45,9,'a'),(46,9,'j'),(47,9,'t'),(48,9,'p'),(49,9,'c'),(50,9,'w'),(51,9,'i'),(52,9,'v'),(53,9,'n'),(54,9,'x'),(55,9,'b'),(56,9,'m'),(57,9,'y'),(58,9,'g'),(59,9,'h'),(60,9,'l'),(61,9,'r'),(62,9,'d'),(63,9,'s'),(64,9,'u'),(65,9,'k'),(66,9,'f'),(67,9,'o'),(68,9,'e'),(69,9,'q'),(70,9,'z');
/*!40000 ALTER TABLE `textfx_charecters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `textfx_style`
--

DROP TABLE IF EXISTS `textfx_style`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `textfx_style` (
  `pk_id` smallint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`pk_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `textfx_style`
--

LOCK TABLES `textfx_style` WRITE;
/*!40000 ALTER TABLE `textfx_style` DISABLE KEYS */;
INSERT INTO `textfx_style` VALUES (1,'style1'),(2,'style2'),(9,'style3');
/*!40000 ALTER TABLE `textfx_style` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `textonpath`
--

DROP TABLE IF EXISTS `textonpath`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `textonpath` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) DEFAULT NULL,
  `svg_image` varchar(200) DEFAULT NULL,
  `thumb_image` varchar(200) DEFAULT NULL,
  `price` double(12,2) NOT NULL,
  `textonpath_id` varchar(100) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `textonpath`
--

LOCK TABLES `textonpath` WRITE;
/*!40000 ALTER TABLE `textonpath` DISABLE KEYS */;
/*!40000 ALTER TABLE `textonpath` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `theme`
--

DROP TABLE IF EXISTS `theme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `theme` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(50) DEFAULT NULL,
  `theme_name` varchar(50) NOT NULL,
  `brand_primary` varchar(50) NOT NULL,
  `border_color` varchar(50) NOT NULL,
  `panel_color` varchar(50) NOT NULL,
  `stage_color` varchar(50) NOT NULL,
  `text_color` varchar(50) NOT NULL,
  `is_default` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `theme`
--

LOCK TABLES `theme` WRITE;
/*!40000 ALTER TABLE `theme` DISABLE KEYS */;
INSERT INTO `theme` VALUES (1,'theme-preview_03.jpg','Default','#0bb3a5','#c2d5d9','#f1f6f7','#ddee6e9','#58666e','1'),(2,'theme-preview_05.jpg','Charcoal','#5c5c5c','#a3a3a3','#f4f4f4','#e3e3e3','#595959','0'),(3,'theme-preview_09.jpg','Imperial Blue','#068dae','#a3a3a5','#f0f2f6','#dee3e8','#555d7f','0'),(4,'','Custom','#f61ae5','#c3bbbb','#f2e6e6','#e5e4e2','#f4f2f9','0');
/*!40000 ALTER TABLE `theme` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `translate`
--

DROP TABLE IF EXISTS `translate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translate` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `content_id` int(10) DEFAULT NULL,
  `translate_text` varchar(100) DEFAULT NULL,
  `language_id` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translate`
--

LOCK TABLES `translate` WRITE;
/*!40000 ALTER TABLE `translate` DISABLE KEYS */;
/*!40000 ALTER TABLE `translate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `units` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `view_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units`
--

LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
/*!40000 ALTER TABLE `units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `upload_space_details`
--

DROP TABLE IF EXISTS `upload_space_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `upload_space_details` (
  `customer_id` int(11) NOT NULL,
  `max_size` double(5,2) NOT NULL,
  `date_modified` date NOT NULL,
  `payment` varchar(100) NOT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `upload_space_details`
--

LOCK TABLES `upload_space_details` WRITE;
/*!40000 ALTER TABLE `upload_space_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `upload_space_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `question` varchar(255) DEFAULT NULL,
  `answer` varchar(255) DEFAULT NULL,
  `resetPasswordKey` varchar(50) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `userType` enum('0','1') NOT NULL DEFAULT '0',
  `token` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_privilege_rel`
--

DROP TABLE IF EXISTS `user_privilege_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_privilege_rel` (
  `u_id` int(10) NOT NULL,
  `p_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_privilege_rel`
--

LOCK TABLES `user_privilege_rel` WRITE;
/*!40000 ALTER TABLE `user_privilege_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_privilege_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_privileges`
--

DROP TABLE IF EXISTS `user_privileges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_privileges` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `privilege` varchar(30) NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_privileges`
--

LOCK TABLES `user_privileges` WRITE;
/*!40000 ALTER TABLE `user_privileges` DISABLE KEYS */;
INSERT INTO `user_privileges` VALUES (1,'Products','0'),(2,'Orders','0'),(3,'Graphics','0'),(4,'Color Palettes','0'),(5,'Text','0'),(6,'Settings','0'),(7,'Print Profile','0');
/*!40000 ALTER TABLE `user_privileges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_slot`
--

DROP TABLE IF EXISTS `user_slot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_slot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slot_id` int(11) DEFAULT '0',
  `user_id` varchar(50) DEFAULT NULL,
  `json_data` longtext,
  `status` enum('0','1') DEFAULT '0',
  `date_created` timestamp NULL DEFAULT NULL,
  `date_midified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `slot_image` varchar(20) DEFAULT '0.svg',
  `uid` varchar(70) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_slot`
--

LOCK TABLES `user_slot` WRITE;
/*!40000 ALTER TABLE `user_slot` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_slot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `variant_additional_prices`
--

DROP TABLE IF EXISTS `variant_additional_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `variant_additional_prices` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `productid` varchar(50) NOT NULL,
  `variantid` varchar(50) NOT NULL,
  `price` double(10,2) NOT NULL DEFAULT '0.00',
  `print_type` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `variant_additional_prices`
--

LOCK TABLES `variant_additional_prices` WRITE;
/*!40000 ALTER TABLE `variant_additional_prices` DISABLE KEYS */;
/*!40000 ALTER TABLE `variant_additional_prices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `version_manage`
--

DROP TABLE IF EXISTS `version_manage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `version_manage` (
  `pk_id` int(4) NOT NULL AUTO_INCREMENT,
  `current_version` varchar(10) NOT NULL,
  `schema_version` int(11) NOT NULL DEFAULT '19',
  `version_description` text NOT NULL,
  `installed_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL,
  PRIMARY KEY (`pk_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `version_manage`
--

LOCK TABLES `version_manage` WRITE;
/*!40000 ALTER TABLE `version_manage` DISABLE KEYS */;
INSERT INTO `version_manage` VALUES (1,'5.0.3',19,'Till upgrade system ready','2016-06-06',NULL);
/*!40000 ALTER TABLE `version_manage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wordcloud`
--

DROP TABLE IF EXISTS `wordcloud`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wordcloud` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `file_name` varchar(50) DEFAULT NULL,
  `price` double(12,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wordcloud`
--

LOCK TABLES `wordcloud` WRITE;
/*!40000 ALTER TABLE `wordcloud` DISABLE KEYS */;
INSERT INTO `wordcloud` VALUES (4,'test','w_4.png',0.00),(5,'test2','w_5.png',0.00),(7,'wordcloud sample3','w_7.png',0.00);
/*!40000 ALTER TABLE `wordcloud` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-01-09 12:09:31
