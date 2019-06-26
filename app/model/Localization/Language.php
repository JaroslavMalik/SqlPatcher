<?php

namespace App\Model\Localization;

use Nette;

/**
 * \App\Model\Localization\Language
 * 
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $pluralRules
 * @property int $pluralCount
 * @property int $parentLanguageId
 * @property bool $visible
 */
class Language extends \App\Model\Database\Table\ActiveRow
{
	const
		/**  
		 *	CREATE TABLE `language` ( 
		 *		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		 *		`name` varchar(50) NOT NULL COMMENT 'název jazyku v jazyce jazyku',
		 *		`code` varchar(10) NOT NULL COMMENT 'oficialni kod jazyku dle iso',
		 *		`plural_rules` varchar(250) NOT NULL COMMENT 'vzorec pravidel pro skloňování',
		 *		`plural_count` tinyint(3) unsigned NOT NULL COMMENT 'počet možných plurálů',
		 *		`parent_language_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'id nadřazeného jazyka (pro funkcionalitu stylu: pokud nepřeložím do tohoto zkusím přeložit do rodičovského)',
		 *		`visible` tinyint(1) COLLATE utf8_czech_ci NOT NULL DEFAULT 0,
		 *		PRIMARY KEY (`id`),
		 *		FOREIGN KEY (`parent_language_id`) 
		 *			REFERENCES `language` (`id`) 
		 *			ON DELETE NO ACTION 
		 *			ON UPDATE NO ACTION
		 *		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tabulka jazyků';
		 */
		TABLE_NAME = 'language',
		/** `id` int(11) unsigned NOT NULL AUTO_INCREMENT */
		COLUMN_ID = 'id',
		/** `name` varchar(50) NOT NULL COMMENT 'název jazyku v jazyce jazyku' */
		COLUMN_NAME = 'name',
		/** `code` varchar(10) NOT NULL COMMENT 'oficialni kod jazyku dle iso' */
		COLUMN_CODE = 'code',
		/** `plural_rules` varchar(250) NOT NULL COMMENT 'vzorec pravidel pro skloňování' */
		COLUMN_PLURAL_COUNT = 'plural_count',
		/** `plural_count` tinyint(3) unsigned NOT NULL COMMENT 'počet možných plurálů' */
		COLUMN_ = '',
		/** `parent_language_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'id nadřazeného jazyka (pro funkcionalitu stylu: pokud nepřeložím do tohoto zkusím přeložit do rodičovského)' */
		COLUMN_PARENT_LANGUAGE_ID = 'parent_language_id',
		/** `visible` tinyint(1) COLLATE utf8_czech_ci NOT NULL DEFAULT 0 */
		COLUMN_VISIBLE = 'visible';
	
	const DEFAULT_LANGUAGE_ID = 0;
	
	/**
	 * @var \App\Model\Localization\Languages
	 */
	protected $languages;

	/**
	 * __construct(\Nette\Database\Table\ActiveRow $activeRow, Languages $languages)
	 * 
	 * @param \Nette\Database\Table\ActiveRow $activeRow
	 * @param \App\Model\Localization\Languages $languages
	 */
	public function __construct(\Nette\Database\Table\ActiveRow $activeRow, Languages $languages)
	{
		$this->languages = $languages;
		parent::__construct($activeRow);
	}
	
	/**
	 * getParentLanguage()
	 * 
	 * @return \App\Model\Localization\Language
	 */
	public function getParentLanguage()
	{
		return $this->languages->getLanguage($this->parentLanguageId);
	}
	
	/**
	 * isDefault()
	 * 
	 * jedná se o výchozí (kořenový) jazyk
	 * 
	 * @return boolean
	 */
	public function isDefault()
	{
		return ($this->id === self::DEFAULT_LANGUAGE_ID);
	}
	
	/**
	 * getPluralNumber($count = null)
	 *
	 * @param int $count		- ciselny pocet, podle ktereho se to ma sklonovat
	 * @return int				- vraci jaky tvar v prekladu se ma použít
	 */
	public function getPluralNumber($count = null)
	{
		// if not defined return as singular
		if ($count == null) {
			return 0;
		}
		//unify input count
		$count = abs(intval($count));
		
		//compute pluralform for lang
		return eval("\$n=$count; return " . $this->pluralRules . ';');
	}
}

