<?php

namespace App\Model\Messages;

/**
 * \App\Model\Messages\Result
 * 
 * @property boolean result
 */
class Result extends Messages
{	
	
	use \Nette\SmartObject;
	
	/** @var boolean */
	protected $result;	
		
	/**
	 * __construct(\App\Model\Messages\Messager $messager, $result) 
	 * 
	 * @param \App\Model\Messages\Messager $messager
	 * @param boolean $result
	 */
	public function __construct(\App\Model\Messages\Messager $messager, $result) 
	{
		parent::__construct($messager);
		$this->result = $result;
	}
	
	/**
	 * setResult($result)
	 * 
	 * @param boolean $result
	 * @return \App\Model\Messages\Result
	 */
	public function setResult($result)
	{
		$this->result = $result;
		return $this;
	}
	
	/**
	 * setTrue()
	 * 
	 * @return \App\Model\Messages\Result
	 */
	public function setTrue()
	{
		return $this->setResult(true);
	}
	
	/**
	 * setFalse()
	 * 
	 * @return \App\Model\Messages\Result
	 */
	public function setFalse()
	{
		return $this->setResult(false);
	}
	
	/**
	 * and()
	 * 
	 * @param boolean $result
	 * @return \App\Model\Messages\Result
	 */
	public function andResult($result)
	{
		return $this->setResult($this->getResult() && $result);
	}
	
	/**
	 * or()
	 * 
	 * @param boolean $result
	 * @return \App\Model\Messages\Result
	 */
	public function orResult($result)
	{
		return $this->setResult($this->getResult() || $result);
	}
	
	/**
	 * getResult()
	 * 
	 * @return boolean
	 */
	public function getResult()
	{
		return $this->result;
	}
	
	/**
	 * __toString()
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->result;
	}
}