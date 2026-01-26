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
) ENGINE=InnoDB AUTO_INCREMENT=424 DEFAULT CHARSET=utf8;

/*Data for the table `user_market_places_subscribe` */

insert  into `user_market_places_subscribe`(`id`,`market_places_mapping_id`,`market_places_id`,`user_account_id`,`parent_id`,`date_time`,`status`,`is_delete`,`active_date`,`expiry_date`,`pending_req`,`is_reject`) values 
(400,371,1,2258,148,'2020-12-07 06:47:36',1,0,NULL,NULL,0,0),
(401,411,88,2258,148,'2020-12-07 06:47:55',2,0,NULL,NULL,2,0),
(402,343,3,2258,148,'2020-12-07 06:48:18',1,0,NULL,NULL,0,0),
(403,412,59,2258,148,'2020-12-07 07:07:43',2,0,NULL,NULL,2,0),
(404,350,60,2258,148,'2020-12-07 07:08:05',1,0,NULL,NULL,0,0),
(405,352,61,2258,148,'2020-12-07 07:08:29',3,0,NULL,NULL,0,3),
(406,360,63,2258,148,'2020-12-07 07:09:34',2,0,NULL,NULL,2,0),
(407,353,64,2258,148,'2020-12-07 07:10:21',3,0,NULL,NULL,0,3),
(408,354,65,2258,148,'2020-12-07 07:10:45',2,0,NULL,NULL,2,0),
(409,381,66,2258,148,'2020-12-07 07:11:07',3,0,NULL,NULL,0,3),
(410,355,67,2258,148,'2020-12-07 07:11:29',1,0,NULL,NULL,0,0),
(411,293,68,2258,148,'2020-12-07 07:11:49',1,0,'2020-11-03',NULL,0,0),
(412,359,69,2258,148,'2020-12-07 07:12:10',3,0,'2020-12-08',NULL,0,3),
(413,383,70,2258,148,'2020-12-07 07:12:45',1,0,NULL,NULL,0,0),
(414,131,71,2258,148,'2020-12-07 07:13:05',1,0,NULL,NULL,0,0),
(415,382,72,2258,148,'2020-12-07 07:13:26',1,0,NULL,NULL,0,0),
(416,406,73,2258,148,'2020-12-07 07:13:47',2,0,NULL,NULL,2,0),
(417,134,74,2258,148,'2020-12-07 07:14:07',2,0,NULL,NULL,2,0),
(418,135,75,2258,148,'2020-12-07 07:14:26',1,0,'2020-11-17',NULL,0,0),
(419,407,76,2258,148,'2020-12-07 07:14:47',0,1,'2020-11-17',NULL,0,0),
(420,137,77,2258,148,'2020-12-07 07:15:08',1,0,'2020-12-20','2020-12-22',0,0),
(421,371,1,2258,148,'2020-12-07 07:22:57',1,0,'2020-11-30','2020-12-16',0,0),
(422,423,75,2258,148,'2020-12-07 10:18:15',3,0,NULL,NULL,0,3),
(423,407,76,2258,148,'2020-12-07 10:31:02',3,0,NULL,NULL,0,3);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
