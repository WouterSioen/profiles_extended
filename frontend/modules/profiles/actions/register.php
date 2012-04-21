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
		elseif($this->URL->getParameter('sent') == 'true')
		{
			$this->getData();
			$this->loadFormPartTwo();
			$this->validateFormPartTwo();
			$this->parse();
		}

		// part two is done so show succes message
		elseif($this->URL->getParameter('done') == 'true') $this->parse();

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
	}

	/**
	 * Load the form.
	 */
	private function loadForm()
	{
		$this->frm = new FrontendForm('register', null, null, 'registerForm');
		$this->frm->addText('first_name');
		$this->frm->addText('last_name');
		$this->frm->addText('email');
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
		// e-mail was sent?
		if($this->URL->getParameter('sent') == 'true')
		{
			// hide first register form
			$this->tpl->assign('registerHideForm', true);

			// parse second form
			$this->frmPartTwo->parse($this->tpl);
		}

		//part two is done?
		elseif($this->URL->getParameter('done') == 'true')
		{
			// show message
			$this->tpl->assign('registerIsSuccess', true);

			// hide both forms
			$this->tpl->assign('registerHideForm', true);
			$this->tpl->assign('registerPartTwoHideForm', true);
		}

		else
		{
			// hide second form
			$this->tpl->assign('registerPartTwoHideForm', true);

			// parse the form
			$this->frm->parse($this->tpl);
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

			// check fields
			if($txtFirstName->isFilled(FL::getError('FirstNameIsRequired')));
			if($txtLastName->isFilled(FL::getError('LastNameIsRequired')));
			if($chkAcceptTerms->isChecked(FL::getError('AcceptTermsIsRequired')));

			// check email
			if($txtEmail->isFilled(FL::getError('EmailIsRequired')))
			{
				// valid email?
				if($txtEmail->isEmail(FL::getError('EmailIsInvalid')))
				{
					// email already exists?
					if(FrontendProfilesModel::existsByEmail($txtEmail->getValue()))
					{
						// set error
						$txtEmail->setError(FL::getError('EmailExists'));
					}
				}
			}

			// check password
			$txtPassword->isFilled(FL::getError('PasswordIsRequired'));

			// no errors
			if($this->frm->isCorrect())
			{
				// generate salt
				$salt = FrontendProfilesModel::getRandomString();

				// init values
				$values = array();

				// values
				$values['email'] = $txtEmail->getValue();
				$values['password'] = FrontendProfilesModel::getEncryptedString($txtPassword->getValue(), $salt);
				$values['status'] = 'inactive';
				$values['display_name'] = $txtFirstName->getValue() . ' ' . $txtLastName->getValue();
				$values['registered_on'] = FrontendModel::getUTCDate();

				/*
				 * Add a profile.
				 * We use a try-catch statement to catch errors when more users sign up simultaneously.
				 */
				try
				{
					// insert profile
					$profileId = FrontendProfilesModel::insert($values);

					// use the profile id as url until we have an actual url
					FrontendProfilesModel::update($profileId, array('url' => FrontendProfilesModel::getUrl($profileId)));

					// trigger event
					FrontendModel::triggerEvent('profiles', 'after_register', array('id' => $profileId));

					// generate activation key
					$activationKey = FrontendProfilesModel::getEncryptedString($profileId . microtime(), $salt);

					// set settings
					FrontendProfilesModel::setSetting($profileId, 'salt', $salt);
					FrontendProfilesModel::setSetting($profileId, 'activation_key', $activationKey);

					// login
					FrontendProfilesAuthentication::login($profileId);

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

					$this->getData();

					// update settings
					$this->profile->setSetting('first_name', $txtFirstName->getValue());
					$this->profile->setSetting('last_name', $txtLastName->getValue());

					// trigger event
					FrontendModel::triggerEvent('profiles', 'after_saved_settings', array('id' => $this->profile->getId()));

					// redirect
					$this->redirect(SELF . '?sent=true');
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

				//update settings
				$this->profile->setSetting('birth_date', $birthDate);
				$this->profile->setSetting('gender', $ddmGender->getValue());

				// trigger event
				FrontendModel::triggerEvent('profiles', 'after_saved_settings', array('id' => $this->profile->getId()));

				// subscribe to newsletter if checked
				if($chkNewslettre->isChecked())
				{
					// check if the e-mailaddress is subscribed
					if(!FrontendMailmotorModel::isSubscribed($this->profile->getEmail()))
					{
						$item = array();
						$item['email'] = $this->profile->getEmail();
						$item['source'] = 'registration';
						$item['created_on'] = null;
						FrontendMailmotorModel::insertAddress($item);
					}
				}

				// redirect
				$this->redirect(str_replace('sent=true', '', SELF) . 'done=true');
			}
		}
	}
}