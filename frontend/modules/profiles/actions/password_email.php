<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Change the password of email of the current logged in profile.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesPasswordEmail extends FrontendBaseBlock
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
		else $this->redirect(FrontendNavigation::getURL(404));
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
		$this->frm = new FrontendForm('updatePassword', null, null, 'updatePasswordForm');
		$this->frm->addPassword('new_password', null, null, 'inputText');
		$this->frm->addPassword('repeat_new_password', null, null, 'inputText');
		$this->frm->addText('email', $this->profile->getEmail());
		$this->frm->addPassword('current_password');
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
			$txtCurrentPassword = $this->frm->getField('current_password');
			$txtNewPassword = $this->frm->getField('new_password');
			$txtRepeatNewPassword = $this->frm->getField('repeat_new_password');
			$txtEmail = $this->frm->getField('email');

			// current password filled in?
			if($txtCurrentPassword->isFilled(FL::getError('PasswordIsRequired')))
			{
				// current password correct?
				if(FrontendProfilesAuthentication::getLoginStatus($this->profile->getEmail(), $txtCurrentPassword->getValue()) !== FrontendProfilesAuthentication::LOGIN_ACTIVE)
				{
					// set error
					$txtCurrentPassword->addError(FL::getError('InvalidPassword'));
				}

				// if new password is filled in, is it the same as the repeat new password?
				if($txtNewPassword->isFilled() && $txtNewPassword->getValue() != $txtRepeatNewPassword->getValue())
				{
					$txtRepeatNewPassword->addError(FL::getError('InvalidRepeatPassword'));
				}

				// if e-mail is filled in, it should be a valid email address
				if($txtEmail->isFilled())
				{
					// valid email?
					if($txtEmail->isEmail(FL::getError('EmailIsInvalid')))
					{
						// email already exists?
						if(FrontendProfilesModel::existsByEmail($txtEmail->getValue(), $this->profile->getId()))
						{
							// set error
							$txtEmail->setError(FL::getError('EmailExists'));
						}
					}
				}
			}

			// no errors
			if($this->frm->isCorrect())
			{
				if($txtNewPassword->isFilled())
				{
					// update password
					FrontendProfilesAuthentication::updatePassword($this->profile->getId(), $txtNewPassword->getValue());

					// trigger event
					FrontendModel::triggerEvent('profiles', 'after_change_password', array('id' => $this->profile->getId()));

					// redirect
					$this->redirect(SITE_URL . FrontendNavigation::getURLForBlock('profiles', 'password_email') . '?sent=true');
				}

				if($txtEmail->isFilled())
				{
					// update email
					FrontendProfilesModel::update($this->profile->getId(), array('email' => $txtEmail->getValue()));

					// trigger event
					FrontendModel::triggerEvent('profiles', 'after_change_email', array('id' => $this->profile->getId()));

					// redirect
					$this->redirect(SITE_URL . FrontendNavigation::getURLForBlock('profiles', 'password_email') . '?sent=true');
				}
			}

			// show errors
			else $this->tpl->assign('updatePasswordHasFormError', true);
		}
	}
}
