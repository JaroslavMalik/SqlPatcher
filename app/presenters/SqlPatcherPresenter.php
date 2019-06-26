<?php

namespace App\Presenters;

use Nette;


class SqlPatcherPresenter extends Nette\Application\UI\Presenter
{
	/** @var \App\Model\SqlPatcher */
    protected $sqlPatcher;
	
	public function __construct(\App\Model\Database\Patches\SqlPatcher $sqlPatcher)
    {
        $this->sqlPatcher = $sqlPatcher;
    }
	
	public function renderDefault() 
	{
		$this->template->deployedPatches = $this->sqlPatcher->getDeployedPatches();
		$this->template->newPatches = $this->sqlPatcher->getNewPatches();
	}
	
	public function actionUpgrade($id = 'all') 
	{
		if ($id == 'all') {
			$this->sqlPatcher->upgradeAll();
		} else {
			$this->sqlPatcher->upgrade($id);
		}
		$this->setView('info');
		$messages = $this->sqlPatcher->getMessages();
		$this->template->messages = array();
		$messageGroup = array();
		foreach ($messages as $message) {
			if ($message->key == 'SQL_PATCHER__START_UPGRADE' && !empty($messageGroup)) {
				$this->template->messages[] = $messageGroup;
				$messageGroup = array();
			}
			$messageGroup[] = $message; 
		}
		$this->template->messages[] = $messageGroup;
		$this->setView('default');
		//$this->redirect("default");
	}
	
	public function actionLogAsUpgraded($id) 
	{
		$this->sqlPatcher->logAsUpgraded($id);
		$this->setView('info');
		$this->template->messages = array();
		$this->template->messages[] = $this->sqlPatcher->getMessages();
		$this->setView('default');
		//$this->redirect("default");
	}
	
	public function actionDowngrade($id) 
	{
		$this->sqlPatcher->downgrade($id);
		$this->setView('info');
		$this->template->messages = array();
		$this->template->messages[] = $this->sqlPatcher->getMessages();
		$this->setView('default');
		//$this->redirect("default");
	}
	
	public function actionLogAsDowngraded($id) 
	{
		$this->sqlPatcher->logAsDowngraded($id);
		$this->setView('info');
		$this->template->messages = array();
		$this->template->messages[] = $this->sqlPatcher->getMessages();
		$this->setView('default');
		//$this->redirect("default");
	}
}
