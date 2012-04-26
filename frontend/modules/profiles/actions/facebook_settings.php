<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the facebook settings action
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesFacebookSettings extends FrontendBaseBlock
{
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
		}

		// profile not logged in
		else $this->redirect(FrontendNavigation::getURLForBlock('profiles', 'login'));
	}
}