<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the login-action.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesLogin extends FrontendBaseBlock
{
	/**
	 * FrontendForm instance.
	 *
	 * @var	FrontendForm
	 */
	private $frm;

	/**
	 * Execute.
	 */
	public function execute()
	{
		parent::execute();

 		// profile not logged in
		if(!FrontendProfilesAuthentication::isLoggedIn())
		{
			$this->loadTemplate();
			$this->loadForm();
			$this->validateForm();
			$this->parse();
		}

		// profile already logged in
		else $this->redirect(FrontendNavigation::getURLForBlock('profiles') . '/' . FrontendProfilesAuthentication::getProfile()->getUrl());
	}

	/**
	 * Load the form.
	 */
	private function loadForm()
	{
		$this->frm = new FrontendForm('login', null, null, 'loginForm');
		$this->frm->addText('email');
		$this->frm->addPassword('password');
		$this->frm->addCheckbox('remember', true);
	}

	/**
	 * Parse the data into the template.
	 */
	private function parse()
	{
		$this->frm->parse($this->tpl);
	}

	/**
	 * Validate the form.
	 */
	private function validateForm()
	{
		// is the form submitted
		if($this->frm->isSubmitted())
		{
			// get fields
			$txtEmail = $this->frm->getField('email');
			$txtPassword = $this->frm->getField('password');
			$chkRemember = $this->frm->getField('remember');

			// this will be set to true when there is no fb data and the fields should be validated
			$validateFields = false;

			if(FACEBOOK_HAS_APP)
			{
				$facebook = Spoon::get('facebook');
				$data = $facebook->getCookie();

				if($data !== false)
				{
					if(!SpoonSession::exists('facebook_user_data'))	// @todo	clear me when user logs out
					{
						$data = $facebook->get('/me', array('metadata' => 0));
						SpoonSession::set('facebook_user_data', $data);
					}
					else $data = SpoonSession::get('facebook_user_data');
				}
				else SpoonSession::delete('facebook_user_data');

				if(!isset($data['email'])) $validateFields = true;
			}
			else $validateFields = true;

			// validate not facebook fields
			if($validateFields == true)
			{
				// required fields
				$txtEmail->isFilled(FL::getError('EmailIsRequired'));
				$txtPassword->isFilled(FL::getError('PasswordIsRequired'));
	
				// both fields filled in
				if($txtEmail->isFilled() && $txtPassword->isFilled())
				{
					// valid email?
					if($txtEmail->isEmail(FL::getError('EmailIsInvalid')))
					{
						// get the status for the given login
						$loginStatus = FrontendProfilesAuthentication::getLoginStatus($txtEmail->getValue(), $txtPassword->getValue());
	
						// valid login?
						if($loginStatus !== FrontendProfilesAuthentication::LOGIN_ACTIVE)
						{
							// get the error string to use
							$errorString = sprintf(FL::getError('Profiles' . SpoonFilter::toCamelCase($loginStatus) . 'Login'), FrontendNavigation::getURLForBlock('profiles', 'resend_activation'));
	
							// add the error to stack
							$this->frm->addError($errorString);
	
							// add the error to the template variables
							$this->tpl->assign('loginError', $errorString);
						}
					}
				}
			}
			// validate facebook fields
			else
			{
				// check if the profile already registered
				if(FrontendProfilesModel::getIdBySetting('facebook_id', $data['id']) == 0)
				{
					$errorString = FL::getError('notRegistered');
					$this->frm->addError($errorString);
					$this->tpl->assign('loginError', $errorString);
				}
			}

			// valid login
			if($this->frm->isCorrect())
			{
				// through facebook
				if(isset($data['email']))
				{
					$profileId = FrontendProfilesModel::getIdBySetting('facebook_id', $data['id']);
				}
				else
				{
					// get profile id
					$profileId = FrontendProfilesModel::getIdByEmail($txtEmail->getValue());
				}

				// login
				FrontendProfilesAuthentication::login($profileId, $chkRemember->getChecked());

				// update salt and password for Dieter's security features
				FrontendProfilesAuthentication::updatePassword($profileId, $txtPassword->getValue());

				// trigger event
				FrontendModel::triggerEvent('profiles', 'after_logged_in', array('id' => $profileId));

				$redirect = FrontendProfilesAuthentication::getProfile()->getUrl();

				// redirect
				$this->redirect(FrontendNavigation::getURLForBlock('profiles') . '/' . $redirect);
			}
		}
	}
}
