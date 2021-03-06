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
	 * The activities of the user
	 * 
	 * @var array
	 */
	private $activities;

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
	 * The site specific settings
	 * 
	 * @var arrar
	 */
	private $customSettings;

	/**
	 * The Age of the in profile
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

		// profile not logged in
		else $this->redirect(FrontendNavigation::getURLForBlock('profiles', 'login'));
	}

	/**
	 * Loads the data
	 */
	private function getData()
	{
		// validate incoming parameters
		if($this->URL->getParameter(0) === null) $this->redirect(FrontendNavigation::getURL(404));

		// check if profile exists
		$id = FrontendProfilesModel::getIdByUrl($this->URL->getParameter(0));
		if($id != 0)
		{
			$this->profile = FrontendProfilesModel::get($id);
			$this->settings = $this->profile->getSettings();
			// calculate age
			if(!empty($this->settings['birth_date']))
			{
				list($year, $month, $day) = explode("-", $this->settings['birth_date']);
				$this->age = (date("md") < $month . $day ? date("Y") - $year - 1 : date("Y") - $year);
			}
			else $this->age = '';
			$this->customSettings = FrontendProfilesModel::getCustomSettings($id);
			$this->activities = FrontendProfilesModel::getUserActivities($id);
			// change the action of the activity to the right label
			foreach($this->activities as &$activity) $activity['action'] = FL::lbl($activity['action']);
		}
		else $this->redirect(FrontendNavigation::getURL(404));
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		$this->tpl->assign('hideContentTitle', true);
		$this->tpl->assign('profile', $this->profile);
		$this->tpl->assign('settings', $this->settings);
		$this->tpl->assign('info', $this->customSettings);
		$this->tpl->assign('age', $this->age);
		$this->tpl->assign('activities', $this->activities);
		$this->tpl->assign('loggedInProfileId', FrontendProfilesAuthentication::getProfile()->getId());
		// boolean to check if delete buttons should be added to activities
		$this->tpl->assign('deletable', (FrontendProfilesAuthentication::getProfile()->getId() == $this->profile->getId()));
	}
}
