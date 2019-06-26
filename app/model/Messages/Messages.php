<?php

namespace App\Model\Messages;

/**
 * \App\Model\Messages\Messages
 * 
 * @property \App\Model\Messages\Message[] $messages
 */
class Messages
{	
    use \Nette\SmartObject;
	
	/**
	 * @var \App\Model\Messages\Messager 
	 */
	protected $messager;
	
	/**
	 * @var \App\Model\Messages\Message[]
	 */
	protected $messages;

	/**
	 * __construct(\App\Model\Messages\Messager $messager)
	 * 
	 * @param \App\Model\Messages\Messager $messager
	 */
	public function __construct(\App\Model\Messages\Messager $messager)
    {
		$this->messager = $messager;
        $this->messages = array();
	}
	
	/**
	 * getMessages()
	 * 
	 * @return \App\Model\Messages\Message[]
	 */
	public function getMessages()
	{
		return $this->messages;
	}
	
	/**
	 * getTranslatedMessages(\Nette\Localization\ITranslator $translator, $typeSelector = NULL)
	 * 
	 * @param \Nette\Localization\ITranslator $translator
	 * @param int $typeSelector pokud null tak vrátí vše
	 * @return string[]
	 */
	public function getTranslatedMessages(\Nette\Localization\ITranslator $translator, $typeSelector = NULL)
	{
		$translatedMessages = array();
		foreach ($this->messages as $message) {
			if ($typeSelector == NULL || ($message->getType() & $typeSelector) || ($message->getType() == $typeSelector)) {
				$translatedMessages[] = $message->getTranslatedMessage($translator);
			}
		}
		return $translatedMessages;
	}
	
	/**
	 * clearMessages()
	 * 
	 * smaže všechny zpravy v kolekci
	 * 
	 * @return \App\Model\Messages\Messages
	 */
	public function clearMessages()
	{
		$this->messages = array();
		return $this;
	}
	
	/**
	 * addMessage($message)
	 * 
	 * @param \App\Model\Messages\Message $message
	 * @return \App\Model\Messages\Messages
	 */
	public function add(\Model\App\Message $message)
	{
		$this->messages[] = $message;
		return $this;
	}
	
	// zkratky
	
	/**
	 * addMessage($type, $key, $message, $data = null)
	 * 
	 * @param int $type          charakter zpravy viz konstanty typu zprav
	 * @param string $key        klic pro prekladovou konstantu
	 * @param string $message    defaultni text zpravy
	 * @param array $data        prilozena data, lze vyuzit pri prekladu jako parametry
	 * @return \App\Model\Messages\Messages
	 */
	public function addMessage($type, $key, $message, $data = null)
	{
		$this->messages[] = $this->messager->createMessage($type, $key, $message, $data);
		return $this;
	}
	
	/**
	 * addBasicMessage($key, $message, $data = null)
	 * 
	 * @param string $key        klic pro prekladovou konstantu
	 * @param string $message    defaultni text zpravy
	 * @param array $data        prilozena data, lze vyuzit pri prekladu jako parametry
	 * @return \App\Model\Messages\Messages
	 */
	public function addBasicMessage($key, $message, $data = null)
	{
		$this->messages[] = $this->messager->createBasicMessage($key, $message, $data);
		return $this;
	}
	
	/**
	 * addPrimaryMessage($key, $message, $data = null)
	 * 
	 * @param string $key        klic pro prekladovou konstantu
	 * @param string $message    defaultni text zpravy
	 * @param array $data        prilozena data, lze vyuzit pri prekladu jako parametry
	 * @return \App\Model\Messages\Messages
	 */
	public function addPrimaryMessage($key, $message, $data = null)
	{
		$this->messages[] = $this->messager->createPrimaryMessage($key, $message, $data);
		return $this;
	}
	
	/**
	 * addSuccessMessage($key, $message, $data = null)
	 * 
	 * @param string $key        klic pro prekladovou konstantu
	 * @param string $message    defaultni text zpravy
	 * @param array $data        prilozena data, lze vyuzit pri prekladu jako parametry
	 * @return \App\Model\Messages\Messages
	 */
	public function addSuccessMessage($key, $message, $data = null)
	{
		$this->messages[] = $this->messager->createSuccessMessage($key, $message, $data);
		return $this;
	}
	
	/**
	 * addInfoMessage($key, $message, $data = null)
	 * 
	 * @param string $key        klic pro prekladovou konstantu
	 * @param string $message    defaultni text zpravy
	 * @param array $data        prilozena data, lze vyuzit pri prekladu jako parametry
	 * @return \App\Model\Messages\Messages
	 */
	public function addInfoMessage($key, $message, $data = null)
	{
		$this->messages[] = $this->messager->createInfoMessage($key, $message, $data);
		return $this;
	}
	
	/**
	 * addTipMessage($key, $message, $data = null)
	 * 
	 * @param string $key        klic pro prekladovou konstantu
	 * @param string $message    defaultni text zpravy
	 * @param array $data        prilozena data, lze vyuzit pri prekladu jako parametry
	 * @return \App\Model\Messages\Messages
	 */
	public function addTipMessage($key, $message, $data = null)
	{
		$this->messages[] = $this->messager->createTipMessage($key, $message, $data);
		return $this;
	}
	
	/**
	 * addWarningMessage($key, $message, $data = null)
	 * 
	 * @param string $key        klic pro prekladovou konstantu
	 * @param string $message    defaultni text zpravy
	 * @param array $data        prilozena data, lze vyuzit pri prekladu jako parametry
	 * @return \App\Model\Messages\Messages
	 */
	public function addWarningMessage($key, $message, $data = null)
	{
		$this->messages[] = $this->messager->createWarningMessage($key, $message, $data);
		return $this;
	}
	
	/**
	 * addDangerMessage($key, $message, $data = null)
	 * 
	 * @param string $key        klic pro prekladovou konstantu
	 * @param string $message    defaultni text zpravy
	 * @param array $data        prilozena data, lze vyuzit pri prekladu jako parametry
	 * @return \App\Model\Messages\Messages
	 */
	public function addDangerMessage($key, $message, $data = null)
	{
		$this->messages[] = $this->messager->createDangerMessage($key, $message, $data);
		return $this;
	}
	
	/**
	 * addErrorMessage($key, $message, $data = null)
	 * 
	 * @param string $key        klic pro prekladovou konstantu
	 * @param string $message    defaultni text zpravy
	 * @param array $data        prilozena data, lze vyuzit pri prekladu jako parametry
	 * @return \App\Model\Messages\Messages
	 */
	public function addErrorMessage($key, $message, $data = null)
	{
		$this->messages[] = $this->messager->createErrorMessage($key, $message, $data);
		return $this;
	}
}