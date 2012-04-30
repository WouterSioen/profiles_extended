<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the userinfo-action, it will display the overview of custom userinfo
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class BackendProfilesUserinfo extends BackendBaseActionIndex
{

	/**
	 * Execute the action.
	 */
	public function execute()
	{
		parent::execute();
		$this->loadDataGrid();
		$this->parse();
		$this->display();
	}

	/**
	 * Loads the datagrid
	 */
	private function loadDataGrid()
	{
		$this->dgUserInfo = new BackendDataGridDB('SELECT pi.* FROM profiles_info AS pi');
	}

	/**
	 * Parse & display the page.
	 */
	protected function parse()
	{
		parent::parse();

		// parse datagrid
		$this->tpl->assign('dgUserInfo', ($this->dgUserInfo->getNumResults() != 0) ? $this->dgUserInfo->getContent() : false);
	}
}