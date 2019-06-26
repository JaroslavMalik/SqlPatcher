<?php

namespace App\Model\Database\Patches;

use Nette;

/**
 * \App\Model\Database\Patches\Patch
 * 
 * patch nahraný do DB
 * 
 * @property int $id
 * @property string $fileName
 * @property int $fileLastUpdate
 * @property string $fileHash
 * @property string $upgrade
 * @property string $downgrade
 * @property string $processed
 */
class Patch extends \App\Model\Database\Table\ActiveRow
{
	const
		/**  
		 *	CREATE TABLE `patches` (
		 *		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		 *		`file_name` varchar(250) UNIQUE NOT NULL COMMENT 'nazev souboru patche',
		 *		`file_last_update` int(11) NOT NULL COMMENT 'Datum poslední úpravy souboru = filemtime',
		 *		`file_hash` varchar(32) NOT NULL COMMENT 'md5_file',
		 *		`upgrade` TEXT NOT NULL COMMENT 'upgrade část patche',
		 *		`downgrade` TEXT NOT NULL COMMENT 'downgrade část patche',
		 *		`processed` int(11) NOT NULL COMMENT 'Datum nasazení',
		 *		PRIMARY KEY (`id`)
		 *	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tabulka nasazenych patch souboru';
		 */
		TABLE_NAME = 'patches',
		/** `id` int(11) unsigned NOT NULL AUTO_INCREMENT */
		COLUMN_ID = 'id',
		/** `file_name` varchar(250) UNIQUE NOT NULL COMMENT 'nazev souboru patche' */
		COLUMN_FILE_NAME = 'file_name',
		/** `file_last_update` int(11) NOT NULL COMMENT 'Datum poslední úpravy souboru = filemtime' */
		COLUMN_FILE_LAST_UPDATE = 'file_last_update',
		/** `file_hash` varchar(32) NOT NULL COMMENT 'md5_file' */
		COLUMN_FILE_HASH = 'file_hash',
		/** `upgrade` TEXT NOT NULL COMMENT 'upgrade část patche' */
		COLUMN_UPGRADE = 'upgrade',
		/** `downgrade` TEXT NOT NULL COMMENT 'downgrade část patche' */
		COLUMN_DOWNGRADE = 'downgrade',
		/** `processed` int(11) NOT NULL COMMENT 'Datum nasazení */
		COLUMN_PROCESSED = 'processed';
	
	/**
	 * @var \App\Model\Database\Patches\PatchFile 
	 */
	protected $patchFile;
	
	/**
	 * setPatchFile(\App\Model\Database\Patches\PatchFile $patchFile)
	 * 
	 * @param \App\Model\Database\Patches\PatchFile $patchFile
	 */
	public function setPatchFile(\App\Model\Database\Patches\PatchFile $patchFile)
	{
		$this->patchFile = $patchFile;
	}
	
	/**
	 * getPatchFile()
	 * 
	 * @return \App\Model\Database\Patches\PatchFile
	 */
	public function getPatchFile()
	{
		return $this->patchFile;
	}
	
	/**
	 * isDeleted()
	 * 
	 * @return boolean
	 * @throws Nette\InvalidStateException
	 */
	public function isDeleted()
	{
		if (!$this->patchFile) {
			throw new Nette\InvalidStateException('Patch file is not set.');
		}
		return (!$this->patchFile->isExists());
	}
	
	/**
	 * isModified()
	 * 
	 * @return boolean
	 * @throws Nette\InvalidStateException
	 */
	public function isModified()
	{
		if (!$this->patchFile) {
			throw new Nette\InvalidStateException('Patch file is not set.');
		}
		if (!$this->patchFile->isExists()) {
			return false;
		}
		if ($this->fileLastUpdate == $this->patchFile->fileLastUpdate) {
			return false;
		}
		if ($this->fileHash == $this->patchFile->fileHash) {
			return false;
		}
		return true;
	}
}

