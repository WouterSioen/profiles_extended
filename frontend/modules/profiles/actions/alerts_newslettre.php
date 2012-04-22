<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Change the alert and newslettre settings of the current logged in profile.
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesAlertsNewslettre extends FrontendBaseBlock
{
	/**
	 * FrontendForm instance.
	 *
	 * @var	FrontendForm
	 */
	private $frm;

	/**
	 * The current profile.
	 *
	 * @var FrontendProfilesProfile
	 */
	private $profile;

	/**
	 * Execute the extra.
	 */
	public function execute()
	{
		// profile logged in
		if(FrontendProfilesAuthentication::isLoggedIn())
		{
			parent::execute();
			$this->getData();
			$this->loadTemplate();
			$this->loadForm();
			$this->validateForm();
			$this->parse();
		}

		// profile not logged in
		else $this->redirect(FrontendNavigation::getURLForBlock('profiles', 'login'));
	}

	/**
	 * Get profile data.
	 */
	private function getData()
	{
		// get profile
		$this->profile = FrontendProfilesAuthentication::getProfile();
	}

	/**
	 * Load the form.
	 */
	private function loadForm()
	{
		$this->frm = new FrontendForm('updateAlert', null, null, 'updateAlertForm');
		$this->frm->addCheckbox('alerts', $this->profile->getSetting('alerts', true));
		$this->frm->addCheckbox('newslettre', FrontendMailmotorModel::isSubscribed($this->profile->getEmail()));
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		// have the settings been saved?
		if($this->URL->getParameter('sent') == 'true')
		{
			// show success message
			$this->tpl->assign('updateSuccess', true);
		}

		// check if profile is active, cause it won't work with a non-active profile
		$this->tpl->assign('nonactive', $this->profile->getStatus() != 'active');

		// parse the form
		$this->frm->parse($this->tpl);
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		// is the form submitted
		if($this->frm->isSubmitted())
		{
			// get fields
			$chkAlerts = $this->frm->getField('alerts');
			$chkNewslettre = $this->frm->getField('newslettre');

			// set the alerts setting
			FrontendProfilesModel::setSetting($this->profile->getId(), 'alerts', $chkAlerts->isFilled());

			/*if($chkNewslettre->isFilled())
			{
				FrontendMailmotorModel::subscribe($this->profile->getEmail());

				// trigger event
				FrontendModel::triggerEvent('mailmotor', 'after_subscribe', array('email' => $this->profile->getEmail()));
			}
			else
			{
				try
				{
					// unsubscribe the user from our default group
					FrontendMailmotorModel::unsubscribe($this->profile->getEmail());

					// trigger event
					FrontendModel::triggerEvent('mailmotor', 'after_unsubscribe', array('email' => $this->profile->getEmail()));
				}
				catch(Exception $e)
				{
					// when debugging we need to see the exceptions
					if(SPOON_DEBUG) throw $e;

					// show error
					$this->tpl->assign('unsubscribeHasError', true);
				}
			}*/
		}
	}
}