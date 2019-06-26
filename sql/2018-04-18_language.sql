CREATE TABLE `language` ( 
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL COMMENT 'název jazyku v jazyce jazyku',
	`code` varchar(10) NOT NULL COMMENT 'oficialni kod jazyku dle iso',
	`plural_rules` varchar(250) NOT NULL COMMENT 'vzorec pravidel pro skloňování',
	`plural_count` tinyint(3) unsigned NOT NULL COMMENT 'počet možných plurálů',
	`parent_language_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'id nadřazeného jazyka (pro funkcionalitu stylu: pokud nepřeložím do tohoto zkusím přeložit do rodičovského)',
	`visible` tinyint(1) COLLATE utf8_czech_ci NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	UNIQUE KEY `name_UNIQUE` (`name`),
	UNIQUE KEY `code_UNIQUE` (`code`),
	FOREIGN KEY (`parent_language_id`) 
		REFERENCES `language` (`id`) 
		ON DELETE NO ACTION 
		ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tabulka jazyků';

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

INSERT INTO `language` (`id`, `name`, `code`, `plural_rules`, `plural_count`, `parent_language_id`, `visible`) VALUES
(0, 'Default', 'default', '($n==1)? 0 : (($n>=2 && $n<=4)? 1 : 2)', 3, 0, 0),
(1, 'Čeština', 'cs', '($n==1)? 0 : (($n>=2 && $n<=4)? 1 : 2)', 3, 0, 1),
(2, 'English', 'en', '($n==1)? 0 : 1', 2, 0, 1),
(3, 'Slovenčina', 'sk', '($n==1)? 0 : (($n>=2 && $n<=4)? 1 : 2)', 3, 1, 1),
(4, 'Polski', 'pl', '($n==1) ? 0 : (($n%10>=2 && $n%10<=4 && ($n%100<10 || $n%100>=20)) ? 1 : 2)', 3, 2, 1),
(5, 'Deutsch', 'de', '($n==1)? 0 : 1', 2, 2, 1),
(6, 'Român', 'ro', '($n==1 ? 0 : ($n==0 || ($n%100 > 0 && $n%100 < 20)) ? 1 : 2)', 3, 2, 1),
(7, 'Русский', 'ru', '($n%10==1 && $n%100!=11 ? 0 : $n%10>=2 && $n%10<=4 && ($n%100<10 || $n%100>=20) ? 1 : 2)', 3, 2, 0),
(8, 'Magyar', 'hu', '($n==1)? 0 : 1', 2, 2, 1),
(9, 'Français', 'fr', '($n==1)? 0 : 1', 2, 2, 0),
(10, 'Italiano', 'it', '($n==1)? 0 : 1', 2, 2, 0),
(11, 'Slovenski', 'sl', '($n%100==1 ? 1 : $n%100==2 ? 2 : $n%100==3 || $n%100==4 ? 3 : 0)', 4, 2, 0);

/* DOWNGRADE
DROP TABLE `language`;
-- */