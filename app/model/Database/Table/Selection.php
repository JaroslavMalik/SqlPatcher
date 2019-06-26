<?php

namespace App\Model\Database\Table;

use Nette;

/**
 * Extend Nette\Database\Table\ActiveRow
 * to allow camelcase property name and getPropertyName() methods
 */
class Selection // extends Nette\Database\Table\Selection
{
    use \Nette\SmartObject;
	
	/**
	 * @var \Nette\Database\Table\Selection 
	 */
	protected $table;
	
	/**
	 * __construct(Selection $table )
	 * 
	 * @param \Nette\Database\Table\Selection $table
	 */
	public function __construct(\Nette\Database\Table\Selection $table)
	{
		$this->table = $table;
	}
}


