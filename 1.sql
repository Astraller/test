CREATE TABLE IF NOT EXISTS `counters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` char(2) NOT NULL DEFAULT '0',
  `event` enum('play','view','click') NOT NULL,
  `counter` int(11) NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`country`,`event`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=latin1;