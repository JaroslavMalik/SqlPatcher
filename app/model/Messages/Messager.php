<?php

namespace App\Model\Messages;

class Messager
{
	/**
	 * createMessage($type,$key,$message,$data = null)
	 * 
	 * vytvori obecnou zpravu
	 * 
	 * @param int $type          charakter zpravy viz konstanty typu zprav
	 * @param string $key        klic pro prekladovou konstantu
	 * @param string $message    defaultni text zpravy
	 * @param array $data         prilozena data, lze vyuzit pri prekladu jako parametry
	 * @return \App\Model\Messages\Message
	 */
	public function createMessage($type,$key,$message,$data = null)
	{
		return new Message($type,$key,$message,$data);
	}
	
	/**
	 * createBasicMessage($key,$message,$data = null)
	 * 
	 * vytvori zpravu typu BASIC
	 * 
	 * @param string $key        klic pro prekladovou konstantu
	 * @param string $message    defaultni text zpravy
	 * @param array $data         prilozena data, lze vyuzit pri prekladu jako parametry
	 * @return \App\Model\Messages\Message
	 */
	public function createBasicMessage($key,$message,$data = null)
	{
		return new Message(Message::BASIC,$key,$message,$data);
	}
	
	/**
	 * createPrimaryMessage($key,$message,$data = null)
	 * 
	 * vytvori zpravu typu PRIMARY
	 * 
	 * @param string $key        klic pro prekladovou konstantu
	 * @param string $message    defaultni text zpravy
	 * @param array $data         prilozena data, lze vyuzit pri prekladu jako parametry
	 * @return \App\Model\Messages\Message
	 */
	public function createPrimaryMessage($key,$message,$data = null)
	{
		return new Message(Message::PRIMARY,$key,$message,$data);
	}
	
	/**
	 * createSuccessMessage($key,$message,$data = null)
	 * 
	 * vytvori zpravu typu SUCCESS
	 * 
	 * @param string $key        klic pro prekladovou konstantu
	 * @param string $message    defaultni text zpravy
	 * @param array $data         prilozena data, lze vyuzit pri prekladu jako parametry
	 * @return \App\Model\Messages\Message
	 */
	public function createSuccessMessage($key,$message,$data = null)
	{
		return new Message(Message::SUCCESS,$key,$message,$data);
	}
	
	/**
	 * createInfoMessage($key,$message,$data = null)
	 * 
	 * vytvori zpravu typu INFO
	 * 
	 * @param string $key        klic pro prekladovou konstantu
	 * @param string $message    defaultni text zpravy
	 * @param array $data         prilozena data, lze vyuzit pri prekladu jako parametry
	 * @return \App\Model\Messages\Message
	 */
	public function createInfoMessage($key,$message,$data = null)
	{
		return new Message(Message::INFO,$key,$message,$data);
	}
	
	/**
	 * createTipMessage($key,$message,$data = null)
	 * 
	 * vytvori zpravu typu TIP
	 * 
	 * @param string $key        klic pro prekladovou konstantu
	 * @param string $message    defaultni text zpravy
	 * @param array $data         prilozena data, lze vyuzit pri prekladu jako parametry
	 * @return \App\Model\Messages\Message
	 */
	public function createTipMessage($key,$message,$data = null)
	{
		return new Message(Message::TIP,$key,$message,$data);
	}
	
	/**
	 * createWarningMessage($key,$message,$data = null)
	 * 
	 * vytvori zpravu typu WARNING
	 * 
	 * @param string $key        klic pro prekladovou konstantu
	 * @param string $message    defaultni text zpravy
	 * @param array $data         prilozena data, lze vyuzit pri prekladu jako parametry
	 * @return \App\Model\Messages\Message
	 */
	public function createWarningMessage($key,$message,$data = null)
	{
		return new Message(Message::WARNING,$key,$message,$data);
	}
	
	/**
	 * createDangerMessage($key,$message,$data = null)
	 * 
	 * vytvori zpravu typu DANGER
	 * 
	 * @param string $key        klic pro prekladovou konstantu
	 * @param string $message    defaultni text zpravy
	 * @param array $data         prilozena data, lze vyuzit pri prekladu jako parametry
	 * @return \App\Model\Messages\Message
	 */
	public function createDangerMessage($key,$message,$data = null)
	{
		return new Message(Message::DANGER,$key,$message,$data);
	}
	
	/**
	 * createErrorMessage($key,$message,$data = null)
	 * 
	 * vytvori zpravu typu ERROR
	 * 
	 * @param string $key        klic pro prekladovou konstantu
	 * @param string $message    defaultni text zpravy
	 * @param array $data         prilozena data, lze vyuzit pri prekladu jako parametry
	 * @return \App\Model\Messages\Message
	 */
	public function createErrorMessage($key,$message,$data = null)
	{
		return new Message(Message::ERROR,$key,$message,$data);
	}
	
	/**
	 * newResult($result)
	 * 
	 * vytvoří novou kolekci zpráv
	 * 
	 * @return \App\Model\Messages\Messages
	 */
	public function newMessages()
	{
		return new Messages($this);
	}
	
	/**
	 * newMessages()
	 * 
	 * vytvoří nový výsledek
	 * 
	 * @param boolean $result
	 * @return \App\Model\Messages\Result
	 */
	public function newResult($result)
	{
		return new Result($this, $result);
	}
}