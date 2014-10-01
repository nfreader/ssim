-- Create syntax for TABLE 'ssim_beacon'
CREATE TABLE `ssim_beacon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `placedby` int(11) DEFAULT NULL,
  `syst` int(11) DEFAULT NULL,
  `content` varchar(256) DEFAULT '',
  `type` enum('D','R','P') DEFAULT 'R' COMMENT 'Distress, Regular, Propaganda',
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_cargopilot'
CREATE TABLE `ssim_cargopilot` (
  `pilot` int(11) DEFAULT NULL,
  `commod` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `lastsyst` int(11) DEFAULT NULL,
  `lastchange` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `pilot` (`pilot`,`commod`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_commod'
CREATE TABLE `ssim_commod` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `class` enum('R','S','M','D') DEFAULT 'S' COMMENT 'Regular, Special, Mission, Disabled',
  `techlevel` int(11) DEFAULT '1',
  `baseprice` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_commodspob'
CREATE TABLE `ssim_commodspob` (
  `spob` int(11) NOT NULL,
  `commod` int(11) NOT NULL,
  `supply` int(11) NOT NULL,
  UNIQUE KEY `spob` (`spob`,`commod`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_commodstats'
CREATE TABLE `ssim_commodstats` (
  `commod` int(11) DEFAULT NULL,
  `avgprice` int(11) DEFAULT NULL,
  `totalsupply` int(11) DEFAULT NULL,
  `avgsupply` int(11) DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_document'
CREATE TABLE `ssim_document` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pilot` int(11) DEFAULT NULL,
  `type` varchar(2) DEFAULT '',
  `data` longtext,
  `duid` varchar(128) DEFAULT NULL COMMENT 'Document unique id, sha1($data)',
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_dropme'
CREATE TABLE `ssim_dropme` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_govt'
CREATE TABLE `ssim_govt` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `isoname` varchar(2) DEFAULT NULL,
  `type` enum('I','R','P') NOT NULL DEFAULT 'R',
  `homesyst` int(11) DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL,
  `color2` varchar(7) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_govtrelations'
CREATE TABLE `ssim_govtrelations` (
  `govt1` int(11) DEFAULT NULL,
  `govt2` int(11) DEFAULT NULL,
  `relations` enum('F','V','N','U') DEFAULT NULL COMMENT 'Free travel, Visa required, No Travel, Unrecognized (Free Travel)'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_jump'
CREATE TABLE `ssim_jump` (
  `origin` int(11) DEFAULT NULL,
  `dest` int(11) DEFAULT NULL,
  `type` enum('R','W') NOT NULL DEFAULT 'R' COMMENT 'Regular, Warp(unused)'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_log'
CREATE TABLE `ssim_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `who` int(11) DEFAULT NULL,
  `what` varchar(2) NOT NULL DEFAULT 'O',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_message'
CREATE TABLE `ssim_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `msgto` int(11) DEFAULT NULL,
  `msgfrom` int(11) DEFAULT '0',
  `messagebody` longtext NOT NULL,
  `sendnode` varchar(128) DEFAULT NULL,
  `recvnode` varchar(128) DEFAULT NULL,
  `fromoverride` varchar(64) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `convo` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_misn'
CREATE TABLE `ssim_misn` (
  `pilot` int(11) DEFAULT NULL,
  `status` enum('N','T','D','P') NOT NULL DEFAULT 'N',
  `pickup` int(11) DEFAULT NULL,
  `dest` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `commod` int(11) DEFAULT NULL,
  `reward` int(11) NOT NULL DEFAULT '1000',
  `uid` varchar(32) DEFAULT NULL,
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_pilot'
CREATE TABLE `ssim_pilot` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `syst` int(11) DEFAULT NULL,
  `spob` int(11) DEFAULT NULL,
  `ship` int(11) DEFAULT NULL,
  `vessel` varchar(64) DEFAULT NULL,
  `homeworld` int(11) DEFAULT NULL,
  `credits` int(11) DEFAULT NULL,
  `legal` int(11) DEFAULT NULL,
  `govt` int(11) DEFAULT NULL,
  `fuel` int(11) DEFAULT NULL,
  `status` enum('L','S','J') NOT NULL DEFAULT 'S' COMMENT 'Landed, (in) Space, Jumping',
  `armordam` int(11) NOT NULL DEFAULT '0',
  `shielddam` int(11) NOT NULL DEFAULT '0',
  `timestamp` timestamp NULL DEFAULT NULL,
  `lastjump` timestamp NULL DEFAULT NULL,
  `jumpeta` timestamp NULL DEFAULT NULL,
  `fingerprint` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_piloterrata'
CREATE TABLE `ssim_piloterrata` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pilot` int(11) DEFAULT NULL,
  `key` varchar(32) DEFAULT NULL,
  `value` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

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
  `cost` int(11) DEFAULT NULL,
  `fueltank` int(11) DEFAULT NULL,
  `cargobay` int(11) DEFAULT NULL,
  `shields` int(11) DEFAULT NULL,
  `armor` int(11) DEFAULT NULL,
  `starter` tinyint(1) NOT NULL DEFAULT '0',
  `class` enum('S','F','H') DEFAULT NULL COMMENT 'Shuttle, Fighter, Hauler(freighter)',
  `shipwright` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_spob'
CREATE TABLE `ssim_spob` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `parent` int(11) NOT NULL,
  `homeworld` tinyint(1) DEFAULT '0' COMMENT 'boolean: can be a homeworld',
  `type` enum('P','M','S','N') NOT NULL DEFAULT 'P' COMMENT 'Planet, Moon, Station, No title',
  `techlevel` int(2) NOT NULL,
  `description` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_syst'
CREATE TABLE `ssim_syst` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `coord_x` int(11) DEFAULT NULL,
  `coord_y` int(11) DEFAULT NULL,
  `govt` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'ssim_user'
CREATE TABLE `ssim_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) DEFAULT NULL,
  `password` varchar(256) DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `salt` varchar(256) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `rank` enum('U','M','A') NOT NULL DEFAULT 'U' COMMENT 'User, Moderator, Admin',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Activation bool',
  `spamdex` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;