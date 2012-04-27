<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the overview action, you can find other profiles here
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesOverview extends FrontendBaseBlock
{
	/**
	 * The profiles to display.
	 *
	 * @var array
	 */
	private $profiles;

	/**
	 * The lettre to select
	 * 
	 * @var string
	 */
	private $lettre = 'A';

	/**
	 * FrontendForm instance.
	 *
	 * @var	FrontendForm
	 */
	private $frm;

	/**
	 * Execute the extra.
	 */
	public function execute()
	{
		
		// only logged in profiles can find profiles
		if(FrontendProfilesAuthentication::isLoggedIn())
		{
			// call the parent
			parent::execute();
			$this->loadTemplate();
			$this->getData();
			$this->loadForm();
			$this->parse();
		}
		else $this->redirect(FrontendNavigation::getURLForBlock('profiles', 'login'));
	}

	/**
	 * Gets the data
	 */
	private function getData()
	{
		// create array to create nav and to check parameter
		$this->alphabet = array(array('lettre' => '0-9'));
		foreach(range('A', 'Z') as $part)
		{
			$this->alphabet[] = array('lettre' => $part);
		}

		// get lettre out of the url
		$lettreParam = strtoupper($this->URL->getParameter('lettre'));
		if($lettreParam && ((strlen($lettreParam) == 1 && in_array($lettreParam, range('A', 'Z'))) || $lettreParam == '0-9')) $this->lettre = $lettreParam;

		// get all the profiles with this lettre
		$this->profiles = FrontendProfilesModel::getProfilesByFirstLettre($this->lettre);
	}

	/**
	 * Loads the form
	 */
	private function loadForm()
	{
		$this->frm = new FrontendForm('search', null, null, 'searchForm');
		$this->frm->addText('profile', '', 255, 'inputText profilesAutoSuggest redirect');
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		$this->frm->parse($this->tpl);
		$this->tpl->assign('alphabet', $this->alphabet);
		$this->tpl->assign('profiles', $this->profiles);
	}

	/**
	 * Validates the form
	 */
	private function validateForm()
	{
		// search the profile
	}
}