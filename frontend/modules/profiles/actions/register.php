<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Register a profile.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesRegister extends FrontendBaseBlock
{
	/**
	 * FrontendForm instance.
	 *
	 * @var	FrontendForm
	 */
	private $frm, $frmPartTwo;

	/**
	 * The current profile.
	 *
	 * @var FrontendProfilesProfile
	 */
	private $profile;

	/**
	 * is mailmotor set up?
	 * 
	 * @var boolean
	 */
	private $allowNewslettre = false;

	/**
	 * Execute the extra.
	 */
	public function execute()
	{
		parent::execute();

		$this->loadTemplate();

		// profile not logged in
		if(!FrontendProfilesAuthentication::isLoggedIn())
		{
			$this->loadForm();
			$this->validateForm();
			$this->parse();
		}

		// just registered so show success message
		elseif($this->URL->getParameter('part') == 'two')
		{
			$this->getData();
			$this->loadFormPartTwo();
			$this->validateFormPartTwo();
			$this->parse();
		}

		// already logged in, so you can not register
		else $this->redirect(SITE_URL);
	}

	/**
	 * Get profile data.
	 */
	private function getData()
	{
		// get profile
		$this->profile = FrontendProfilesAuthentication::getProfile();

		// check if a campagin monitor was linked already and/or client ID was set
		$this->allowNewslettre = (FrontendModel::getModuleSetting('mailmotor', 'cm_client_id') != null);
	}

	/**
	 * Load the form.
	 */
	private function loadForm()
	{
		$email = (SpoonCookie::exists('email')) ? (string) SpoonCookie::get('email') : null;

		$this->frm = new FrontendForm('register', null, null, 'registerForm');
		$this->frm->addText('first_name');
		$this->frm->addText('last_name');
		$this->frm->addText('email', $email);
		$this->frm->addPassword('password', null, null, 'inputText showPasswordInput');
		$this->frm->addCheckbox('show_password');
		$this->frm->addCheckbox('accept_terms');
	}

	/**
	 * Load the second form
	 */
	private function loadFormPartTwo()
	{
		// gender dropdown values
		$genderValues = array(
			'male' => SpoonFilter::ucfirst(FL::getLabel('Male')),
			'female' => SpoonFilter::ucfirst(FL::getLabel('Female'))
		);

		// birthdate dropdown values
		$days = range(1, 31);
		$months = SpoonLocale::getMonths(FRONTEND_LANGUAGE);
		$years = range(date('Y'), 1900);

		$this->frmPartTwo = new FrontendForm('registerPartTwo', null, null, 'registerPartTwoForm');
		$this->frmPartTwo->addDropdown('gender', $genderValues);
		$this->frmPartTwo->addDropdown('day', array_combine($days, $days));
		$this->frmPartTwo->addDropdown('month', $months);
		$this->frmPartTwo->addDropdown('year', array_combine($years, $years));
		$this->frmPartTwo->addCheckbox('newslettre');

		// set default elements dropdowns
		$this->frmPartTwo->getField('day')->setDefaultElement('');
		$this->frmPartTwo->getField('month')->setDefaultElement('');
		$this->frmPartTwo->getField('year')->setDefaultElement('');
	}

	/**
	 * Parse the data into the template.
	 */
	private function parse()
	{
		// part one was done?
		if($this->URL->getParameter('part') == 'two')
		{
			// hide first register form
			$this->tpl->assign('registerHideForm', true);

			// parse second form
			$this->frmPartTwo->parse($this->tpl);
		}
		else
		{
			// hide second form
			$this->tpl->assign('registerPartTwoHideForm', true);

			// parse the form
			$this->frm->parse($this->tpl);

			$this->tpl->assign('allowNewslettre', $this->allowNewslettre);
		}
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
			$txtFirstName = $this->frm->getField('first_name');
			$txtLastName = $this->frm->getField('last_name');
			$txtEmail = $this->frm->getField('email');
			$txtPassword = $this->frm->getField('password');
			$chkAcceptTerms = $this->frm->getField('accept_terms');

			$email = $txtEmail->getValue();

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

					$email = $data['email'];
				}
				else SpoonSession::delete('facebook_user_data');

				if(!isset($data['name']) || !isset($data['email']))
				{
					// validate required fields
					$txtFirstName->isFilled(FL::getError('FirstNameIsRequired'));
					$txtLastName->isFilled(FL::getError('LastNameIsRequired'));
					$chkAcceptTerms->isChecked(FL::getError('AcceptTermsIsRequired'));

					// check password
					$txtPassword->isFilled(FL::getError('PasswordIsRequired'));
				}
			}
			else
			{
				// validate required fields
				$txtFirstName->isFilled(FL::getError('FirstNameIsRequired'));
				$txtLastName->isFilled(FL::getError('LastNameIsRequired'));
				$txtEmail->isEmail(FL::getError('EmailIsInvalid'));
				$chkAcceptTerms->isChecked(FL::getError('AcceptTermsIsRequired'));

				// check password
				$txtPassword->isFilled(FL::getError('PasswordIsRequired'));
			}

			// email should be unique
			if(FrontendProfilesModel::getIdByEmail($email) != 0)
			{
				$this->tpl->assign('registerHasEmailExistsError', true);
				$txtEmail->addError(FL::getError('EmailExists'));
			}

			// no errors
			if($this->frm->isCorrect())
			{
				// init values
				$values = array();
				$values['registered_on'] = FrontendModel::getUTCDate();

				// through facebook
				if(isset($data['first_name']) && isset($data['last_name']) && isset($data['email']))
				{
					$first_name = $data['first_name'];
					$last_name = $data['last_name'];
					$values['email'] = $data['email'];
					$values['display_name'] = $data['username'];
					$extraData['facebook_id'] = $data['id'];
				}
				else
				{
					// generate salt
					$salt = FrontendProfilesModel::getRandomString();

					// values
					$values['email'] = $txtEmail->getValue();
					$values['password'] = FrontendProfilesModel::getEncryptedString($txtPassword->getValue(), $salt);
					$values['status'] = 'inactive';
					$values['display_name'] = $txtFirstName->getValue() . ' ' . $txtLastName->getValue();
				}

				// create a url that doesn't exist yet
				$url = SpoonFilter::urlise($values['display_name']);
				$extra = null;
				while(empty($values['url']))
				{
					if(FrontendProfilesModel::getIdByUrl($url . $extra) == 0) $values['url'] = $url . $extra;
					else $extra++;
				}

				/*
				 * Add a profile.
				 * We use a try-catch statement to catch errors when more users sign up simultaneously.
				 */
				try
				{
					// insert profile
					$profileId = FrontendProfilesModel::insert($values);

					// trigger event
					FrontendModel::triggerEvent('profiles', 'after_register', array('id' => $profileId));

					// login
					FrontendProfilesAuthentication::login($profileId);

					$this->getData();

					$redirectURL = SELF . '?part=two';

					// through facebook
					if(isset($data['first_name']) && isset($data['last_name']) && isset($data['email']))
					{
						// update settings
						$this->profile->setSetting('first_name', $data['first_name']);
						$this->profile->setSetting('last_name', $data['last_name']);
						$this->profile->setSetting('facebook_id', $data['id']);
						$birthdayParts = explode('/', $data['birthday']);
						$birthday = $birthdayParts[2] . '-' . $birthdayParts[1] . '-' . $birthdayParts[0];
						$this->profile->setSetting('birth_date', $birthday);
						$this->profile->setSetting('gender', $data['gender']);
						$this->profile->setSetting('city', $data['location']['name']);

						$redirectURL = FrontendNavigation::getURLForBlock('profiles', 'settings');
					}
					else
					{
						// generate activation key
						$activationKey = FrontendProfilesModel::getEncryptedString($profileId . microtime(), $salt);

						// set settings
						FrontendProfilesModel::setSetting($profileId, 'salt', $salt);
						FrontendProfilesModel::setSetting($profileId, 'activation_key', $activationKey);
						$this->profile->setSetting('first_name', $txtFirstName->getValue());
						$this->profile->setSetting('last_name', $txtLastName->getValue());

						// activation URL
						$mailValues['activationUrl'] = SITE_URL . FrontendNavigation::getURLForBlock('profiles', 'activate') . '/' . $activationKey;
	
						// send email
						FrontendMailer::addEmail(
							FL::getMessage('RegisterSubject'),
							FRONTEND_MODULES_PATH . '/profiles/layout/templates/mails/register.tpl',
							$mailValues,
							$values['email'],
							''
						);
					}

					// trigger event
					FrontendModel::triggerEvent('profiles', 'after_saved_settings', array('id' => $this->profile->getId()));

					// redirect
					$this->redirect($redirectURL);
				}

				// catch exceptions
				catch(Exception $e)
				{
					// when debugging we need to see the exceptions
					if(SPOON_DEBUG) throw $e;

					// show error
					$this->tpl->assign('registerHasFormError', true);
				}
			}

			// show errors
			else $this->tpl->assign('registerHasFormError', true);
		}
	}

	/**
	 * Validate the second form
	 */
	private function validateFormPartTwo()
	{
		// is the form submitted
		if($this->frmPartTwo->isSubmitted())
		{
			// get fields
			$ddmGender = $this->frmPartTwo->getField('gender');
			$ddmDay = $this->frmPartTwo->getField('day');
			$ddmMonth = $this->frmPartTwo->getField('month');
			$ddmYear = $this->frmPartTwo->getField('year');
			$chkNewslettre = $this->frmPartTwo->getField('newslettre');

			// birthdate is not required but if one is filled we need all
			if($ddmMonth->isFilled() || $ddmDay->isFilled() || $ddmYear->isFilled())
			{
				// valid birth date?
				if(!checkdate($ddmMonth->getValue(), $ddmDay->getValue(), $ddmYear->getValue()))
				{
					// set error
					$ddmYear->addError(FL::getError('DateIsInvalid'));
				}
			}

			// no errors
			if($this->frmPartTwo->isCorrect())
			{
				// init values
				$values = array();

				// bday is filled in
				if($ddmYear->isFilled())
				{
					// mysql format
					$birthDate = $ddmYear->getValue() . '-';
					$birthDate .= str_pad($ddmMonth->getValue(), 2, '0', STR_PAD_LEFT) . '-';
					$birthDate .= str_pad($ddmDay->getValue(), 2, '0', STR_PAD_LEFT);
				}

				// not filled in
				else $birthDate = null;

				// update settings
				$this->profile->setSetting('birth_date', $birthDate);
				$this->profile->setSetting('gender', $ddmGender->getValue());

				// trigger event
				FrontendModel::triggerEvent('profiles', 'after_saved_settings', array('id' => $this->profile->getId()));

				// subscribe to newsletter if checked
				if($this->allowNewslettre && $chkNewslettre->isChecked())
				{
					// check if the e-mailaddress is subscribed
					if(!FrontendMailmotorModel::isSubscribed($this->profile->getEmail()))
					{
						FrontendMailmotorModel::subscribe($this->profile->getEmail());
						
						// trigger event
						FrontendModel::triggerEvent('mailmotor', 'after_subscribe', array('email' => $email->getValue()));
					}
				}

				// redirect
				$this->redirect(FrontendNavigation::getURLForBlock('profiles', 'settings') . '?registered=true');
			}
		}
	}
}