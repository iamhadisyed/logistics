/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 10.3.10-MariaDB : Database - smarttrack_staging
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`smarttrack_staging` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `smarttrack_staging`;

/*Table structure for table `user_market_places_subscribe` */

DROP TABLE IF EXISTS `user_market_places_subscribe`;

CREATE TABLE `user_market_places_subscribe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `market_places_mapping_id` int(11) NOT NULL,
  `market_places_id` int(11) NOT NULL,
  `user_account_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `date_time` datetime DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `is_delete` tinyint(1) DEFAULT 0,
  `active_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `pending_req` tinyint(4) DEFAULT 0,
  `is_reject` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=396 DEFAULT CHARSET=utf8;

/*Data for the table `user_market_places_subscribe` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
