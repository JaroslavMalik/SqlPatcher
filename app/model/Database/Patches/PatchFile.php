<?php

namespace App\Model\Database\Patches;

use Nette;

/**
 * \App\Model\Database\Patches\PatchFile
 * 
 * fyzický soubor s DB patchem na disku
 * 
 * @property string $fileName
 * @property int $fileLastUpdate
 * @property string $fileHash
 * @property string $upgrade
 * @property string $downgrade
 */
class PatchFile
{
	use \Nette\SmartObject;
	
	/**
	 * @var string
	 */
	protected $fileName;
	
	/**
	 * @var string
	 */
	protected $filePath;

	/**
	 * __construct($basePath, $fileName)
	 * 
	 * @param string $basePath
	 * @param string $fileName
	 */
	public function __construct($basePath, $fileName)
	{
		$this->fileName = $fileName;
		$this->filePath = $basePath . $fileName;
	}
	
	/**
	 * __toString()
	 * 
	 * @return string
	 */
	public function __toString() 
	{
		return $this->fileName;
	}
	
	/**
	 * isExists()
	 * 
	 * @return boolean
	 */
	public function isExists() 
	{
		return file_exists($this->filePath);
	}
	
	/**
	 * getContent()
	 * 
	 * @return string
	 */
	public function getContent()
	{
		return file_get_contents($this->filePath);
	}
	
	/**
	 * getFileName()
	 * 
	 * @return string
	 */
	public function getFileName() 
	{
		return $this->fileName;
	}
	
	/**
	 * getFileLastUpdate()
	 * 
	 * @return int unixtime
	 */
	public function getFileLastUpdate() 
	{
		return filemtime($this->filePath);
	}
	
	/**
	 * getFileHash()
	 * 
	 * @return string md5
	 */
	public function getFileHash() 
	{
		return md5_file($this->filePath);
	}
	
	/**
	 * getUpgrade()
	 * 
	 * get upgrade SQL query
	 * 
	 * @return string
	 */
	public function getUpgrade() 
	{
		$content = $this->getContent();

		$upgrade = $content;
		//$downgrade = ''; 
		// jednoducha separace patch souboru
		if (strpos($content, '/* DOWNGRADE')) {
			$parts = explode('/* DOWNGRADE', $content);
			$upgrade = $parts[0];
			//$downgrade = str_replace("-- */", "", $parts[1]); 
		}
		return $upgrade;
	}
	
	/**
	 * getDowngrade()
	 * 
	 * get downgrade SQL query
	 * 
	 * @return string
	 */
	public function getDowngrade() 
	{
		$content = $this->getContent();
		
		//$upgrade = $content;
		$downgrade = ''; 
		// jednoducha separace patch souboru
		if (strpos($content, '/* DOWNGRADE')) {
			$parts = explode('/* DOWNGRADE', $content);
			//$upgrade = $parts[0];
			$downgrade = str_replace("-- */", "", $parts[1]);
		}
		return $downgrade;
	}
}