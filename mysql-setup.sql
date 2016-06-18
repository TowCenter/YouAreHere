CREATE TABLE `stories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `story_phone_number` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `story_phone_number` (`story_phone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `responses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `story_id` int(11) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `twilio_id` varchar(255) DEFAULT NULL,
  `mp3_url` varchar(255) DEFAULT NULL,
  `mp3_downloaded` tinyint(4) DEFAULT '0',
  `duration` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `active` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
