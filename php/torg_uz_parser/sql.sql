CREATE TABLE IF NOT EXISTS `torg_db` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `contacts` text NOT NULL,
  `href` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;