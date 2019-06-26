<?php

namespace App\Model\Database\Patches;

use Nette;

/**
 * \App\Model\Database\Patches\SqlPatcher
 * 
 * @property \App\Model\Messages\Message[] $messages
 */
class SqlPatcher
{
	use \Nette\SmartObject;
	
	/**
	 * @var \Nette\Database\Context
	 */
	protected $database;
	
	/**
	 * @var string
	 */
	protected $basePath;
	
	/**
	 * @var \App\Model\Database\Patches\Patch[string] associative array by fileName and ordered by processed 
	 */
	protected $deployedPatches;
	
	/**
	 * @var \App\Model\Database\Patches\PatchFile[string] associative array by fileName and ordered by fileName  
	 */
	protected $availablePatches;
	
	/**
	 * @var \App\Model\Messages\Messages
	 */
	protected $messages;
	
	/**
	 * __construct(\Nette\Database\Context $database, $basePath, \App\Model\Messages\Messager $messager)
	 * 
	 * DI constructor
	 * 
	 * @param \Nette\Database\Context $database
	 * @param string $basePath
	 * @param \App\Model\Messages\Messager $messager
	 */
	public function __construct(\Nette\Database\Context $database, $basePath, \App\Model\Messages\Messager $messager)
	{
		$this->database = $database;
		$this->basePath = $basePath;
		$this->deployedPatches = array();
		$this->availablePatches = array();
		$this->messages = $messager->newMessages();
	}
	
	/**
	 * getDeployedPatches()
	 * 
	 * @return \App\Model\Database\Patches\Patch[string] associative array by fileName and ordered by processed
	 */
	public function getDeployedPatches()
	{
		if (empty($this->deployedPatches)) {
			try {
				$patches = $this->database->table(Patch::TABLE_NAME)->order(Patch::COLUMN_FILE_NAME.' ASC');
				while ($patch = $patches->fetch()) {
					$patch = new Patch($patch);
					$patch->setPatchFile(new PatchFile($this->basePath, $patch->fileName));
					$this->deployedPatches[$patch->fileName] = $patch;
				}
			} catch (\Exception $exception) {
				// ignorovat vyjimku: Table 'patches' does not exist.
			}
		}
		return $this->deployedPatches;
	}
	
	/**
	 * getAvailablePatches()
	 * 
	 * @return \App\Model\Database\Patches\PatchFile[string] associative array by fileName and ordered by fileName
	 */
	public function getAvailablePatches()
	{
		if (empty($this->availablePatches)) {
			// otevre adresar a vylistuje všechny sql soubory
			$directory = opendir($this->basePath); 
			if ($directory === false) {
				return array();
			}
			while ($file = readdir($directory)) {
				if (strrchr($file,'.') == '.sql') {
					$this->availablePatches[$file] = new PatchFile($this->basePath, $file);
				}
			}
			// seradit vzestupne podle nazvu souboru
			asort($this->availablePatches);
		}
		return $this->availablePatches;
	}
	
	/**
	 * getNewPatches()
	 * 
	 * @return \App\Model\Database\Patches\PatchFile[string] associative array by fileName and ordered by fileName
	 */
	public function getNewPatches()
	{
		return array_diff_key($this->getAvailablePatches(), $this->getDeployedPatches());
	}
	
	/**
	 * getDeletedPatches()
	 * 
	 * @return \App\Model\Database\Patches\Patch[string] associative array by fileName and ordered by processed
	 */
	public function getDeletedPatches()
	{
		return array_diff_key($this->getDeployedPatches(), $this->getAvailablePatches());
	}
	
	/**
	 * getModifiedPatches()
	 * 
	 * @return \App\Model\Database\Patches\Patch[string] associative array by fileName and ordered by processed
	 */
	public function getModifiedPatches()
	{
		$this->getDeployedPatches();
		$modifiedPatches = array();
		foreach ($this->deployedPatches as $patch) {
			if ($patch->isModified()) {
				$modifiedPatches[$patch->fileName] = $patch;
			}
		}
		return $modifiedPatches;
	}

	/**
	 * upgradeAll()
	 * 
	 * @return boolean success?
	 */
	public function upgradeAll()
	{
		$newPatches = $this->getNewPatches();
		foreach ($newPatches as $file) {
			if (!$this->upgrade($file->fileName)) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * upgrade($fileName)
	 * 
	 * @return boolean success?
	 */
	public function upgrade($fileName)
	{
		$this->messages->addPrimaryMessage("SQL_PATCHER__START_UPGRADE", "UPGRADE %s", $fileName);
		
		if (isset($this->availablePatches[$fileName])) {
			$patchFile = $this->availablePatches[$fileName]; 
		} else {
			$patchFile = new PatchFile($this->basePath, $fileName);
		}
		
		if (!$patchFile->isExists()) {
			$this->messages->addErrorMessage("SQL_PATCHER__FILE_NOT_EXIST", "Soubor nenalezen.");
			return false;
		}
		
		$this->getDeployedPatches();
		if (isset($this->deployedPatches[$patchFile->fileName])) {
			$this->messages->addInfoMessage("SQL_PATCHER__PATCH_ALREADY_EXECUTED", "Patch je již nasazen.");
			return true;
		}
		
		$commands = $this->breakToCommands($patchFile->upgrade);
		
		if (!$this->executeAtomically($commands)) {
			return false;
		}

		$this->database->getStructure()->rebuild();
		$this->logAsUpgraded($fileName);
		$this->messages->addSuccessMessage("SQL_PATCHER__PATCH_EXECUTED", "Patch byl úspěšně nasazen.");
//		$this->database->query("INSERT INTO patches (fileName,processed) VALUES (".$this->database->getConnection()->quote($file).",UNIX_TIMESTAMP());");
		return true;
	}
	
	/**
	 * logAsUpgraded($fileName)
	 * 
	 * @return boolean success?
	 */
	public function logAsUpgraded($fileName)
	{
		$this->messages->addPrimaryMessage("SQL_PATCHER__START_LOG", "LOG %s", $fileName);
		
		if (isset($this->availablePatches[$fileName])) {
			$patchFile = $this->availablePatches[$fileName];
		} else {
			$patchFile = new PatchFile($this->basePath, $fileName);
		}
		
		if (!$patchFile->isExists()) {
			$this->messages->addErrorMessage("SQL_PATCHER__FILE_NOT_EXIST", "Soubor nenalezen.");
			return false;
		}
		
		$data = array(
			Patch::COLUMN_FILE_NAME => $patchFile->fileName, 
			Patch::COLUMN_FILE_LAST_UPDATE => $patchFile->fileLastUpdate,
			Patch::COLUMN_FILE_HASH => $patchFile->fileHash,
			Patch::COLUMN_UPGRADE => $patchFile->upgrade,
			Patch::COLUMN_DOWNGRADE => $patchFile->downgrade,
			Patch::COLUMN_PROCESSED => time()
		);
		
		$this->getDeployedPatches();
		if (isset($this->deployedPatches[$patchFile->fileName])) {
			$this->messages->addInfoMessage("SQL_PATCHER__PATCH_ALREADY_EXECUTED", "Patch je již nasazen.");
			$patch = new Patch($this->deployedPatches[$patchFile->fileName]->update($data));
		} else {
			$patch = new Patch($this->database->table(Patch::TABLE_NAME)->insert($data));
		}
		$patch->setPatchFile(new PatchFile($this->basePath, $patch->fileName));
		$this->deployedPatches[$patch->fileName] = $patch;
		$this->messages->addSuccessMessage("SQL_PATCHER__PATCH_LOGED", "Patch byl úspěšně zalogován.");
		return true;
	}
	
	/**
	 * downgrade($fileName)
	 * 
	 * @param string $fileName
	 * @return boolean
	 */
	public function downgrade($fileName)
	{
		$this->messages->addBasicMessage("SQL_PATCHER__START_DOWNGRADE", "DOWNGRADE %s", $fileName);
		
		if (isset($this->deployedPatches[$fileName])) {
			$patch = $this->deployedPatches[$fileName];
		} else {
			try {
				$patch = $this->database->table(Patch::TABLE_NAME)->where(Patch::COLUMN_FILE_NAME, $fileName)->fetch();
				$patch = new Patch($patch);
				$patch->setPatchFile(new PatchFile($this->basePath, $patch->fileName));
			} catch (\Exception $exception) {
				$patch = false;
			}
		}
		
		if ($patch == false) {
			$this->messages->addErrorMessage("SQL_PATCHER__PATCH_NOT_EXIST", "Patch nenalezen.");
			return false;
		}
		
		$commands = $this->breakToCommands($patch->downgrade);
		if (!$this->executeAtomically($commands)) {
			return false;
		}

		// nutné přepočítat db cache
		$this->database->getStructure()->rebuild();
		
		$this->logAsDowngraded($fileName);
		
		$this->messages->addSuccessMessage("SQL_PATCHER__PATCH_DOWNGRADED", "Patch byl úspěšně sesazen.");
		return true;
	}
	
	
	/**
	 * logAsUpgraded($fileName)
	 * 
	 * @return boolean success?
	 */
	public function logAsDowngraded($fileName)
	{
		$this->messages->addPrimaryMessage("SQL_PATCHER__START_UNLOG", "DELETE LOG %s", $fileName);
		
		if (isset($this->deployedPatches[$fileName])) {
			$patch = $this->deployedPatches[$fileName];
		} else {
			try {
				$patch = $this->database->table(Patch::TABLE_NAME)->where(Patch::COLUMN_FILE_NAME, $fileName)->fetch();
				$patch = new Patch($patch);
				$patch->setPatchFile(new PatchFile($this->basePath, $patch->fileName));
			} catch (\Exception $exception) {
				$patch = false;
			}
		}
		
		if ($patch == false) {
			$this->messages->addErrorMessage("SQL_PATCHER__PATCH_NOT_EXIST", "Patch nenalezen.");
			return false;
		}
		
		if ($fileName != '1970-01-01_patches.sql') {
			$patch->delete();
		}
		
		if (isset($this->deployedPatches[$fileName])) {
			unset($this->deployedPatches[$fileName]);
		}
		
		$this->messages->addSuccessMessage("SQL_PATCHER__PATCH_DOWNGRADED", "Patch byl úspěšně odlogován.");
		return true;
	}
	
	/**
	 * breakToCommands($sqlPlainText)
	 * 
	 * @param string $sqlPlainText
	 * @return string[]
	 */
	protected function breakToCommands($sqlPlainText)
	{
		if (strpos($sqlPlainText, 'DELIMITER') === false) {
			//delete comments // @todo upravit na automat, takto není plnohodnotné
			$lines = explode("\n", $sqlPlainText);
			$commands = '';
			foreach ($lines as $line) {
				if (strpos($line, '--')!==false) $line = substr($line, 0, strpos($line, '--'));
				$line = trim($line);
				if (!empty($line)) {
					$commands .= $line . "\n";
				}
			}
			//convert to array // taky nemusí být vždy pravda
			$commands = explode(";", $commands);
		} else {
			$commands = array($sqlPlainText);
		}
		return $commands;
	}
	
	/**
	 * executeAtomically($commands)
	 * 
	 * @param string $commands
	 * @return boolean success?
	 */
	protected function executeAtomically($commands)
	{
		$this->database->beginTransaction();
		foreach ($commands as $command) {
			if (trim($command)) {
				try {
					$this->messages->addInfoMessage("SQL_PATCHER__PATCH_EXECUTING", "Provádění: %s", $command);
					$this->database->query($command . ';');
				} catch (\Exception $exception) {
					$this->messages->addErrorMessage("SQL_PATCHER__PATCH_FAILED", "Skript selhal: %s", $exception->getMessage());
					$this->database->rollBack();
					//throw $exception;
					return false;
				}
			}
		}
		$this->database->commit();
		return true;
	}
	
	/**
	 * getMessages()
	 * 
	 * @return \App\Model\Messages\Message[]
	 */
	public function getMessages()
	{
		return $this->messages->getMessages();
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
		return $this->messages->getTranslatedMessages($translator, $typeSelector);
	}
}