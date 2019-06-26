CREATE TABLE `patches` ( 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `file_name` varchar(250) UNIQUE NOT NULL COMMENT 'nazev souboru patche',
  `file_last_update` int(11) NOT NULL COMMENT 'Datum poslední úpravy souboru = filemtime ',
  `file_hash` varchar(32) NOT NULL COMMENT 'md5_file',
  `upgrade` TEXT NOT NULL COMMENT 'upgrade část patche',
  `downgrade` TEXT NOT NULL COMMENT 'downgrade část patche',
  `processed` int(11) NOT NULL COMMENT 'Datum nasazení',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tabulka nasazenych patch souboru';

/* DOWNGRADE
DROP TABLE `patches`;
-- */
