<?php

namespace App\Model\Messages;

/**
 * \App\Model\Messages\Message
 * 
 * @property string $message
 * @property string $key
 * @property int $type
 * @property array $data
 */
class Message
{	
    use \Nette\SmartObject;
	
	// typy zprav
	const BASIC = 1;
	const PRIMARY = 2;
	const SUCCESS = 4;
	const INFO = 8;
	const TIP = 16;
	const WARNING = 32;
	const DANGER = 64;
	const ERROR = 128;
	
	/** @var int */
	private $type;			// charakter zpravy viz konstanty typu zprav
	/** @var string */
	private $key;			// klic pro prekladovou konstantu
	/** @var string */
	private $message;		// defaultni text zpravy
	/** @var array */
	private $data;			// prilozena data, lze vyuzit pri prekladu jako parametry
	
	/**
	 * __construct($type,$key,$message,$data)
	 * 
	 * @param int $type          charakter zpravy viz konstanty typu zprav
	 * @param string $key        klic pro prekladovou konstantu
	 * @param string $message    defaultni text zpravy
	 * @param array $data         prilozena data, lze vyuzit pri prekladu jako parametry
	 */
	public function __construct($type,$key,$message,$data = null) 
	{
		$this->type = $type;
		$this->key = $key;
		$this->message = $message;
		$this->data = $data;
	}
	
	/**
	 * getKey()
	 * 
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}
	
	/**
	 * getMessage()
	 * 
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}
	
	/**
	 * getTranslatedMessage(\Nette\Localization\ITranslator $translator)
	 * 
	 * zavolani funkce translate na danem translatoru
	 * 
	 * @param \Nette\Localization\ITranslator $translator
	 * @return string
	 */
	public function getTranslatedMessage(\Nette\Localization\ITranslator $translator)
	{
		$args = array();
		$args[] = $this->getKey();
		$args[] = $this->getMessage();
		// pridani promenych z dat
		foreach ($this->data as $arg) {
			$args[] = $arg;
		}
		// zavolani funkce translate na danem translatoru
		return call_user_func_array(array($translator,"translate"),$args);
	}
	
	/**
	 * getData()
	 * 
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}
	
	/**
	 * getType()
	 * 
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}
	
	// shortcuts
	
	/** @return bool */
	public function isBasic() { return $this->type == self::BASIC; }
	/** @return bool */
	public function isPrimary() { return $this->type == self::PRIMARY; }
	/** @return bool */
	public function isSuccess() { return $this->type == self::SUCCESS; }
	/** @return bool */
	public function isInfo() { return $this->type == self::INFO; }
	/** @return bool */
	public function isTip() { return $this->type == self::TIP; }
	/** @return bool */
	public function isWarning() { return $this->type == self::WARNING; }
	/** @return bool */
	public function isDanger() { return $this->type == self::DANGER; }
	/** @return bool */
	public function isError() { return $this->type == self::ERROR; }
}