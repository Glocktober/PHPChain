DROP TABLE IF EXISTS `nlogins`;
CREATE TABLE `nlogins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iv` varchar(24) NOT NULL DEFAULT '',
  `userid` int(11) NOT NULL DEFAULT 0,
  `catid` int(11) NOT NULL DEFAULT 0,
  `login` text DEFAULT NULL,
  `password` text DEFAULT NULL,
  `site` text DEFAULT NULL,
  `url` text DEFAULT NULL,
  `noteid` int(11) DEFAULT 0,
  `created` int(11) DEFAULT 0,
  `modified` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `catid` (`catid`)
) ENGINE=InnoDB AUTO_INCREMENT=5127 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `nuser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `teststring` text DEFAULT NULL,
  `iv` varchar(24) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;