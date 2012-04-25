<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action, it can be used as a dashboard.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesIndex extends FrontendBaseBlock
{
	/**
	 * The current profile.
	 *
	 * @var FrontendProfilesProfile
	 */
	private $profile;

	/**
	 * The settings of the profile
	 * 
	 * @var Array
	 */
	private $settings;

	/**
	 * The Age of the logged in profile
	 * 
	 * @var int
	 */
	private $age;

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

		// only if you are logged in, baby.
		else $this->redirect(FrontendNavigation::getURL(404));
	}

	/**
	 * Loads the data
	 */
	private function getData()
	{
		// validate incoming parameters
		if($this->URL->getParameter(0) === null) $this->redirect(FrontendNavigation::getURL(404));

		// check if profile exists
		$id = FrontendProfilesModel::getIdByDisplayName($this->URL->getParameter(0));
		if($id != 0)
		{
			$this->profile = FrontendProfilesModel::get($id);
			$this->settings = $this->profile->getSettings();
			// calculate age
			if($this->settings['birth_date'])
			{
				list($year, $month, $day) = explode("-", $this->settings['birth_date']);
				$this->age = ( date("md") < $month.$day ? date("Y") - $year-1 : date("Y") - $year );
			}
		}
		else
		{
			$this->redirect(FrontendNavigation::getURL(404));
		}
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		$this->tpl->assign('profile', $this->profile);
		$this->tpl->assign('settings', $this->settings);
		$this->tpl->assign('age', $this->age);
	}
}
