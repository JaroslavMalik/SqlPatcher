<?php

namespace App\Model\Database\Table;

use Nette;

/**
 * Extend Nette\Database\Table\ActiveRow
 * to allow camelcase property name and getPropertyName() methods
 */
class ActiveRow extends Nette\Database\Table\ActiveRow
{
	/**
	 * __construct($dataOrActiveRow, Selection $table = null)
	 * 
	 * @param array|\Nette\Database\Table\ActiveRow $dataOrActiveRow
	 * @param \App\Model\Database\Patches\Selection $table
	 */
	public function __construct($dataOrActiveRow, Selection $table = null)
	{
		if ($dataOrActiveRow instanceof \Nette\Database\Table\ActiveRow) {
			$data = $dataOrActiveRow->toArray();
			$table = $dataOrActiveRow->getTable();
		} else {
			$data = $dataOrActiveRow;
		}
		parent::__construct($data, $table);
	}
	
	/**
	 * allow camelcase property
	 * 
	 * @param  string
	 * @return ActiveRow|mixed
	 * @throws Nette\MemberAccessException
	 */
	public function &__get($key)
	{
		// definované funkce get a is mají přednost
		$action = 'get' . ucfirst($key);
		if (method_exists($this, $action) && is_callable(array($this, $action))) {
			$result = $this->$action();
			return $result;
		}
		$action = 'is' . ucfirst($key);
		if (method_exists($this, $action) && is_callable(array($this, $action))) {
			$result = $this->$action();
			return $result;
		}
		try {
			return parent::__get($this->fromCamelCase($key));
		} catch (\Nette\MemberAccessException $exception) {
			return parent::__get($key);
		}
	}
	
	private function fromCamelCase($input) 
	{
		preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
		$ret = $matches[0];
		foreach ($ret as &$match) {
			$match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
		}
		return implode('_', $ret);
	}
	
	/**
	 * allow call getPropertyName()	 
	 * allow call isPropertyName()
	 * 
	 * @param string $name
	 * @param array $arguments
	 * @return mixed
	 * @throws \BadMethodCallException
	 */
    public function __call($name, $arguments)
    {
		if (strpos($name, "get") === 0) {
			return $this->__get(lcfirst(substr($name, 3)));
		}
		if (strpos($name, "is") === 0) {
			return (($this->__get(lcfirst(substr($name, 2)))) ? true : false);
		}
		throw new \BadMethodCallException();
    }
}

