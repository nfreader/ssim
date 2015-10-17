-- Create syntax for TABLE 'ssim_govt'
CREATE TABLE `ssim_govt` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `type` enum('R','I','P') DEFAULT 'R' COMMENT 'Regular, Independent, Pirate',
  `color1` varchar(7) DEFAULT '#000000',
  `color2` varchar(7) DEFAULT '#FFFFFF',
  `isoname` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_log'
CREATE TABLE `ssim_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NULL DEFAULT NULL,
  `who` int(11) DEFAULT NULL,
  `what` longtext,
  `data` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_outf'
CREATE TABLE `ssim_outf` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `cost` int(11) DEFAULT NULL,
  `type` enum('M','W') DEFAULT NULL,
  `subtype` varchar(1) NOT NULL DEFAULT '0',
  `modifies` varchar(1) NOT NULL DEFAULT '',
  `value` varchar(3) DEFAULT NULL,
  `reload` int(2) DEFAULT NULL,
  `ammo` int(11) DEFAULT NULL,
  `description` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'ssim_pilot'
CREATE TABLE `ssim_pilot` (
  `uid` varchar(11) DEFAULT NULL,
  `user` varchar(11) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `legal` int(11) DEFAULT NULL,
  `govt` int(11) DEFAULT NULL,
  `vessel` int(11) DEFAULT NULL,
  `status` enum('L','S','J','F','D') NOT NULL DEFAULT 'S' COMMENT 'Landed, In Space, Jumping, Fresh pilot, Disabled',
  `credits` int(11) NOT NULL DEFAULT '0',
  `syst` int(11) DEFAULT NULL,
  `spob` int(11) DEFAULT NULL,
  `homeworld` int(11) DEFAULT NULL,
  `fingerprint` varchar(32) DEFAULT NULL,
  UNIQUE KEY `name` (`name`,`user`),
  UNIQUE KEY `name_2` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_pilotoutf'
CREATE TABLE `ssim_pilotoutf` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pilot` varchar(11) DEFAULT NULL,
  `outf` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'ssim_session'
CREATE TABLE `ssim_session` (
  `session_id` varchar(256) NOT NULL DEFAULT '',
  `session_data` longtext NOT NULL,
  `session_lastaccesstime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_ship'
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'ssim_spob'
CREATE TABLE `ssim_spob` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `parent` int(11) DEFAULT NULL,
  `type` enum('P','M','S','N') NOT NULL DEFAULT 'P',
  `techlevel` int(2) unsigned NOT NULL DEFAULT '1',
  `homeworld` tinyint(1) NOT NULL DEFAULT '0',
  `description` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_syst'
CREATE TABLE `ssim_syst` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `coord_x` int(11) DEFAULT NULL,
  `coord_y` int(11) DEFAULT NULL,
  `govt` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_user'
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

-- Create syntax for TABLE 'ssim_vessel'
CREATE TABLE `ssim_vessel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pilot` varchar(11) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `ship` int(11) DEFAULT NULL,
  `fuel` int(11) DEFAULT NULL,
  `registration` varchar(9) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_vesseloutf'
CREATE TABLE `ssim_vesseloutf` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `vessel` int(11) DEFAULT NULL,
  `outf` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;