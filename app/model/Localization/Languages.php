<?php
namespace App\Model\Localization;

use Nette;

/**
 * \App\Model\Localization\Languages
 */
class Languages extends \App\Model\Database\Table\Selection
{
	use \Nette\SmartObject;
	
	/**
	 * @var \App\Model\Localization\Language[int] cache: associative array by id
	 */
	protected $visibleLanguages;
	
	/**
	 * @var \App\Model\Localization\Language[int] cache: associative array by id 
	 */
	protected $languages;
	
	/**
	 * @var \App\Model\Localization\Language[string] cache: associative array by code
	 */
	protected $languagesByCode;
	
	/**
	 * __construct(\Nette\Database\Context $database)
	 * 
	 * DI constructor
	 * 
	 * @param \Nette\Database\Context $database
	 * @param string $basePath
	 */
	public function __construct(\Nette\Database\Context $database)
	{
		parent::__construct($database->table(Language::TABLE_NAME));
		$this->visibleLanguages = array();
		$this->languages = array();
		$this->languagesByCode = array();
	}
	
	/**
	 * getLanguage($id = Language::DEFAULT_LANGUAGE_ID)
	 * 
	 * @param int $id
	 * @return \App\Model\Localization\Language
	 * @throws \Nette\InvalidArgumentException
	 */
	public function getLanguage($id = Language::DEFAULT_LANGUAGE_ID)
	{
		$this->getAllLanguages();
		if (!isset($this->languages[$id])) {
			throw new \Nette\InvalidArgumentException("Language with id = $id doesn't exist.");
		}
		return $this->languages[$id];
	}
	
	/**
	 * getLanguageByCode($code)
	 * 
	 * @param string $code
	 * @return \App\Model\Localization\Language
	 * @throws \Nette\InvalidArgumentException
	 */
	public function getLanguageByCode($code)
	{
		$this->getAllLanguages();
		if (!isset($this->languagesByCode[$code])) {
			throw new \Nette\InvalidArgumentException("Language with code = $code doesn't exist.");
		}
		return $this->languagesByCode[$code];
	}
	
	/**
	 * getAllLanguages($forceReloadFromDb = false)
	 * 
	 * @param bool $forceReloadFromDb
	 * @return \App\Model\Localization\Language[int]
	 */
	public function getAllLanguages($forceReloadFromDb = false)
	{
		if (empty($this->languages) || $forceReloadFromDb) {
			$this->visibleLanguages = array();
			$this->languages = array();
			$this->languagesByCode = array();
			$languages = $this->getLanguageSelection(true, true)
				->order(
					"(?name = ?) DESC, ?name ASC;", 
					Language::COLUMN_ID, 
					Language::DEFAULT_LANGUAGE_ID, 
					Language::COLUMN_NAME
				);
			while ($language = $languages->fetch()) {
				$language = new Language($language, $this);
				$this->languages[$language->id] = $language;
				$this->languagesByCode[$language->code] = $language;
				if ($language->isVisible()) {
					$this->visibleLanguages[$language->id] = $language;
				}
			}
		}
		
		return $this->languages;
	}
	
	/**
	 * getLanguages($includingHiddenLanguages = false, $includingDefaultLanguage = false)
	 * 
	 * @param bool $includingHiddenLanguages
	 * @param bool $includingDefaultLanguage
	 * @return \App\Model\Localization\Language[int]
	 */
	public function getLanguages($includingHiddenLanguages = false, $includingDefaultLanguage = false)
	{
		$this->getAllLanguages();
		$languages = $this->visibleLanguages;
		if ($includingHiddenLanguages) {
			$languages = $this->languages;
			if (!$includingDefaultLanguage) {
				$languages = array_diff($languages, array(Language::DEFAULT_LANGUAGE_ID => $this->getLanguage(Language::DEFAULT_LANGUAGE_ID)));
			}
		} elseif ($includingDefaultLanguage) {
			$languages = array_merge(array(Language::DEFAULT_LANGUAGE_ID => $this->getLanguage(Language::DEFAULT_LANGUAGE_ID)), $languages);
		}
		return $languages;
	}
	
	/**
	 * getLanguageSelection($includingHiddenLanguages = false, $includingDefaultLanguage = false)
	 * 
	 * @param bool $includingHiddenLanguages
	 * @param bool $includingDefaultLanguage
	 * @return Nette\Database\Table\Selection
	 */
	public function getLanguageSelection($includingHiddenLanguages = false, $includingDefaultLanguage = false)
	{
		$languageSelection = $this->table;
		if (!$includingHiddenLanguages) {
			$languageSelection = $languageSelection->where(Language::COLUMN_VISIBLE, false);
		}
		if (!$includingDefaultLanguage) {
			$languageSelection = $languageSelection->where(Language::COLUMN_ID . ' <> ?', Language::DEFAULT_LANGUAGE_ID);
		}
		return $languageSelection;
	}
}

