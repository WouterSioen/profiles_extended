<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the messages action
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesMessages extends FrontendBaseBlock
{
	/**
	 * The threads
	 * 
	 * @var array
	 */
	private $threads;

	/**
	 * Execute the extra.
	 */
	public function execute()
	{
		// only logged in profiles can see profiles
		if(FrontendProfilesAuthentication::isLoggedIn())
		{
			// call the parent
			parent::execute();
			$this->loadTemplate();
			$this->getData();
			$this->parse();
		}

		// profile not logged in
		else $this->redirect(FrontendNavigation::getURLForBlock('profiles', 'login'));
	}

	/**
	 * Get's the threads
	 */
	private function getData()
	{
		$profile = FrontendProfilesAuthentication::getProfile();
		$this->threads = FrontendProfilesModel::getLatestThreadsByUserId($profile->getId());
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		if($this->URL->getParameter('sent') == "true")
		{
			$this->tpl->assign('sentMessage', 'MessageSent');
		}
		$this->tpl->assign('threads', $this->threads);
	}
}