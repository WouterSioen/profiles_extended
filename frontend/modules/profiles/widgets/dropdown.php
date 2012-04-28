<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget which shows the subpages.
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesWidgetDropdown extends FrontendBaseWidget
{
	/**
	 * The profile.
	 *
	 * @var	array
	 */
	private $profile;

	/**
	 * Execute the extra
	 */
	public function execute()
	{
		parent::execute();

		if(FrontendProfilesAuthentication::isLoggedIn()) $this->getData();
		else $this->profile = '';
		$this->loadTemplate();
		$this->parse();
	}

	/**
	 * Gets the data
	 */
	private function getData()
	{
		$this->profile = FrontendProfilesModel::getDropdownInfo(FrontendProfilesAuthentication::getProfile()->getId());
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		// add the css file
		$this->addCSS('profiles.css');

		$this->tpl->assign('profile', $this->profile);
		$this->tpl->assign('profile_url', FrontendNavigation::getURLForBlock('profiles'));
		$this->tpl->assign('messages_url', FrontendNavigation::getURLForBlock('profiles', 'messages'));
		$this->tpl->assign('settings_url', FrontendNavigation::getURLForBlock('profiles', 'settings'));
		$this->tpl->assign('logout_url', FrontendNavigation::getURLForBlock('profiles', 'logout'));
		$this->tpl->assign('register_url', FrontendNavigation::getURLForBlock('profiles', 'register'));
		$this->tpl->assign('login_url', FrontendNavigation::getURLForBlock('profiles', 'login'));
	}
}
