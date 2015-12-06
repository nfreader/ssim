# ************************************************************
# Sequel Pro SQL dump
# Version 4499
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.6.25-3+deb.sury.org~trusty+1)
# Database: spacesim
# Generation Time: 2015-12-02 03:28:15 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table ssim_beacon
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_beacon`;

CREATE TABLE `ssim_beacon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `placedby` varchar(11) DEFAULT NULL,
  `type` enum('A','D') NOT NULL DEFAULT 'A',
  `content` longtext,
  `syst` int(11) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ssim_cargopilot
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_cargopilot`;

CREATE TABLE `ssim_cargopilot` (
  `pilot` varchar(11) DEFAULT NULL,
  `commod` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `lastsyst` int(11) DEFAULT NULL,
  `lastchange` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `pilot` (`pilot`,`commod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ssim_commod
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_commod`;

CREATE TABLE `ssim_commod` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `class` enum('R','M','S') NOT NULL DEFAULT 'R',
  `basesupply` int(11) unsigned DEFAULT NULL,
  `baseprice` int(11) unsigned DEFAULT NULL,
  `techlevel` int(11) unsigned NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ssim_commodspob
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_commodspob`;

CREATE TABLE `ssim_commodspob` (
  `commod` int(11) DEFAULT NULL,
  `spob` int(11) DEFAULT NULL,
  `supply` int(11) DEFAULT NULL,
  UNIQUE KEY `commod` (`commod`,`spob`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ssim_commodtransact
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_commodtransact`;

CREATE TABLE `ssim_commodtransact` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NULL DEFAULT NULL,
  `commod` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `who` varchar(11) DEFAULT NULL,
  `syst` int(11) DEFAULT NULL,
  `spob` int(11) DEFAULT NULL,
  `type` enum('B','S','P','J') DEFAULT NULL COMMENT 'Bought, Sold, Pirated, Jettisoned',
  `value` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ssim_dropme
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_dropme`;

CREATE TABLE `ssim_dropme` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ssim_govt
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_govt`;

CREATE TABLE `ssim_govt` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `type` enum('R','I','P') DEFAULT 'R' COMMENT 'Regular, Independent, Pirate',
  `color1` varchar(7) DEFAULT '#000000',
  `color2` varchar(7) DEFAULT '#FFFFFF',
  `isoname` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table ssim_govtranks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_govtranks`;

CREATE TABLE `ssim_govtranks` (
  `govt` int(11) NOT NULL DEFAULT '0',
  `pilot` varchar(11) NOT NULL DEFAULT '',
  `rank` enum('P','VP','C','M','A') NOT NULL DEFAULT 'A',
  PRIMARY KEY (`govt`,`pilot`,`rank`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ssim_govtrelations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_govtrelations`;

CREATE TABLE `ssim_govtrelations` (
  `target` int(11) DEFAULT NULL,
  `subject` int(11) DEFAULT NULL,
  `relation` enum('A','W','N') NOT NULL DEFAULT 'N',
  `reciprocal` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ssim_jump
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_jump`;

CREATE TABLE `ssim_jump` (
  `origin` int(11) unsigned NOT NULL,
  `dest` int(11) unsigned NOT NULL,
  `type` enum('R','W') NOT NULL DEFAULT 'R',
  UNIQUE KEY `origin` (`origin`,`dest`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ssim_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_log`;

CREATE TABLE `ssim_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NULL DEFAULT NULL,
  `who` varchar(11) DEFAULT NULL,
  `aspilot` varchar(11) DEFAULT NULL,
  `what` longtext,
  `data` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ssim_message
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_message`;

CREATE TABLE `ssim_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `msgto` varchar(11) DEFAULT NULL,
  `msgfrom` varchar(11) DEFAULT '0',
  `messagebody` longtext,
  `sendnode` varchar(128) DEFAULT NULL,
  `recvnode` varchar(128) DEFAULT NULL,
  `fromoverride` varchar(64) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ssim_misn
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_misn`;

CREATE TABLE `ssim_misn` (
  `status` enum('N') DEFAULT NULL,
  `pickup` int(11) DEFAULT NULL,
  `dest` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `commod` int(11) DEFAULT NULL,
  `reward` int(11) DEFAULT NULL,
  `uid` varchar(32) DEFAULT NULL,
  `pilot` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ssim_outf
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_outf`;

CREATE TABLE `ssim_outf` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `cost` int(11) DEFAULT NULL,
  `type` varchar(1) DEFAULT NULL,
  `subtype` varchar(1) NOT NULL DEFAULT '0',
  `flag` varchar(1) DEFAULT NULL,
  `modifies` varchar(1) NOT NULL DEFAULT '',
  `value` varchar(3) DEFAULT NULL,
  `reload` int(2) DEFAULT NULL,
  `ammo` int(11) DEFAULT NULL,
  `description` longtext,
  `techlevel` int(11) DEFAULT NULL,
  `govt` int(11) DEFAULT NULL,
  `image` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ssim_pilot
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_pilot`;

CREATE TABLE `ssim_pilot` (
  `uid` varchar(11) DEFAULT NULL,
  `user` varchar(11) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `legal` int(11) DEFAULT NULL,
  `govt` int(11) DEFAULT NULL,
  `vessel` int(11) DEFAULT NULL,
  `status` enum('L','S','J','F','D','B') NOT NULL DEFAULT 'S' COMMENT 'Landed, In Space, Jumping, Fresh pilot, Disabled',
  `credits` int(11) NOT NULL DEFAULT '0',
  `syst` int(11) DEFAULT NULL,
  `spob` int(11) DEFAULT NULL,
  `homeworld` int(11) DEFAULT NULL,
  `fingerprint` varchar(32) DEFAULT NULL,
  `jumpstarted` timestamp NULL DEFAULT NULL,
  `jumpeta` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `name` (`name`,`user`),
  UNIQUE KEY `name_2` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table ssim_pilotoutf
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_pilotoutf`;

CREATE TABLE `ssim_pilotoutf` (
  `pilot` varchar(11) DEFAULT NULL,
  `outfit` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `pilot` (`pilot`,`outfit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ssim_ping
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_ping`;

CREATE TABLE `ssim_ping` (
  `pilot` varchar(11) NOT NULL DEFAULT '',
  `key` varchar(16) NOT NULL DEFAULT '',
  `value` varchar(256) NOT NULL DEFAULT '',
  `timestamp` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ssim_session
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_session`;

CREATE TABLE `ssim_session` (
  `session_id` varchar(256) NOT NULL DEFAULT '',
  `session_data` longtext NOT NULL,
  `session_lastaccesstime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table ssim_ship
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_ship`;

CREATE TABLE `ssim_ship` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `shipwright` varchar(32) DEFAULT NULL,
  `fueltank` int(11) DEFAULT NULL,
  `cargobay` int(11) DEFAULT NULL,
  `expansion` int(11) DEFAULT NULL,
  `accel` int(11) DEFAULT NULL,
  `turn` int(11) DEFAULT NULL,
  `mass` int(11) DEFAULT NULL,
  `shields` int(11) DEFAULT NULL,
  `armor` int(11) DEFAULT NULL,
  `class` enum('S','F','C','R') DEFAULT NULL COMMENT 'Shuttle, Fighter, Cargo Freighter, Frigate',
  `cost` int(11) DEFAULT NULL,
  `starter` tinyint(1) NOT NULL DEFAULT '0',
  `description` longtext,
  `outf` longtext,
  `image` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ssim_spob
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_spob`;

CREATE TABLE `ssim_spob` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `parent` int(11) DEFAULT NULL,
  `type` enum('P','M','S','N') NOT NULL DEFAULT 'P',
  `techlevel` int(2) unsigned NOT NULL DEFAULT '1',
  `homeworld` tinyint(1) NOT NULL DEFAULT '0',
  `description` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table ssim_star
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_star`;

CREATE TABLE `ssim_star` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `coord_x` int(11) DEFAULT NULL,
  `coord_y` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ssim_syst
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_syst`;

CREATE TABLE `ssim_syst` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(32) DEFAULT NULL,
  `coord_x` int(11) DEFAULT NULL,
  `coord_y` int(11) DEFAULT NULL,
  `govt` int(11) DEFAULT '1',
  `star` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table ssim_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_user`;

CREATE TABLE `ssim_user` (
  `uid` varchar(11) DEFAULT NULL,
  `username` varchar(32) NOT NULL DEFAULT '',
  `password` longtext NOT NULL,
  `email` varchar(64) NOT NULL DEFAULT '',
  `status` tinyint(1) DEFAULT NULL,
  `rank` enum('A','M','P') NOT NULL DEFAULT 'P',
  `created` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `username` (`username`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table ssim_vessel
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_vessel`;

CREATE TABLE `ssim_vessel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pilot` varchar(11) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `ship` int(11) DEFAULT NULL,
  `fuel` int(11) DEFAULT NULL,
  `registration` varchar(9) DEFAULT NULL,
  `purchased` timestamp NULL DEFAULT NULL,
  `armordam` int(11) NOT NULL DEFAULT '0',
  `shielddam` int(11) NOT NULL DEFAULT '0',
  `status` enum('A','D') DEFAULT 'A',
  `expansion` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ssim_vesseloutf
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ssim_vesseloutf`;

CREATE TABLE `ssim_vesseloutf` (
  `vessel` int(11) DEFAULT NULL,
  `outfit` int(11) DEFAULT NULL,
  `quantity` int(11) unsigned NOT NULL DEFAULT '0',
  `timestamp` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `vessel` (`vessel`,`outfit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
