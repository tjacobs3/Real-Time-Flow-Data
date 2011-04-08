DROP TABLE IF EXISTS `Annotation`;
CREATE TABLE IF NOT EXISTS `Annotation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location` varchar(255) NOT NULL,
  `chart_type` varchar(255) NOT NULL,
  `series_name` varchar(255) NOT NULL,
  `time` bigint(255) NOT NULL,
  `annotation` text NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;