<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Change the settings for the current logged in profile.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesSettings extends FrontendBaseBlock
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
	 * The url to the avatar/ the facebook id to get the avatar.
	 *
	 * @var 
	 */
	private $avatar, $facebook_id;

	/**
	 * The site specific settings
	 * 
	 * @var array
	 */
	private $settings;

	/**
	 * The custom fields
	 * 
	 * @var array
	 */
	private $customFields;

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

		// avatar
		$this->avatar = $this->profile->getSetting('avatar');
		if(empty($this->avatar)) $this->avatar = '';
		$this->facebook_id = $this->profile->getSetting('facebook_id');
		if(empty($this->facebook_id)) $this->facebook_id = '';

		// custom settings
		$this->settings = FrontendProfilesModel::getCustomSettings($this->profile->getId());
	}

	/**
	 * Load the form.
	 */
	private function loadForm()
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

		// get settings
		$birthDate = $this->profile->getSetting('birth_date');
		$nameChanges = (int) $this->profile->getSetting('display_name_changes');

		// get day, month and year
		if($birthDate) list($birthYear, $birthMonth, $birthDay) = explode('-', $birthDate);

		// no birth date setting
		else
		{
			$birthDay = '';
			$birthMonth = '';
			$birthYear = '';
		}

		// create the form
		$this->frm = new FrontendForm('updateSettings', null, null, 'updateSettingsForm');

		// create & add elements
		$this->frm->addImage('avatar');
		$this->frm->addText('display_name', $this->profile->getDisplayName());
		$this->frm->addText('first_name', $this->profile->getSetting('first_name'));
		$this->frm->addText('last_name', $this->profile->getSetting('last_name'));
		$this->frm->addText('street', $this->profile->getSetting('street'));
		$this->frm->addText('number', $this->profile->getSetting('number'));
		$this->frm->addText('postal_code', $this->profile->getSetting('postal_code'));
		$this->frm->addText('city', $this->profile->getSetting('city'));
		$this->frm->addDropdown('country', SpoonLocale::getCountries(FRONTEND_LANGUAGE), $this->profile->getSetting('country'));
		$this->frm->addDropdown('gender', $genderValues, $this->profile->getSetting('gender'));
		$this->frm->addDropdown('day', array_combine($days, $days), $birthDay);
		$this->frm->addDropdown('month', $months, $birthMonth);
		$this->frm->addDropdown('year', array_combine($years, $years), (int) $birthYear);

		// set default elements dropdowns
		$this->frm->getField('gender')->setDefaultElement('');
		$this->frm->getField('day')->setDefaultElement('');
		$this->frm->getField('month')->setDefaultElement('');
		$this->frm->getField('year')->setDefaultElement('');
		$this->frm->getField('country')->setDefaultElement('');

		// add custom fields
		$this->customFields = array();
		foreach($this->settings as $field)
		{
			if($field['datatype'] == 'Varchar' || $field['datatype'] == 'Number')
			{
				$this->customFields[] = array(
					'label' => $field['title'],
					'txt' => $this->frm->addText($field['db_title'], $field['value'])->parse()
				);
			}
			else if($field['datatype'] == 'Text')
			{
				$this->customFields[] = array(
					'label' => $field['title'],
					'txt' => $this->frm->addTextarea($field['db_title'], $field['value'])->parse()
				);
			}
			else if($field['datatype'] == 'Enum')
			{
				$field['values'] = explode(';', $field['values']);
				$this->customFields[] = array(
					'label' => $field['title'],
					'txt' => $this->frm->addDropdown($field['db_title'], $field['values'], $field['value'])->parse()
				);
			}
			else if($field['datatype'] == 'Bool')
			{
				$this->customFields[] = array(
					'label' => $field['title'],
					'txt' => $this->frm->addCheckbox($field['db_title'], $field['value'])->parse()
				);
			}
		}

		// when user exceeded the number of name changes set field disabled
		if($nameChanges >= FrontendProfilesModel::MAX_DISPLAY_NAME_CHANGES) $this->frm->getField('display_name')->setAttribute('disabled', 'disabled');
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
			$this->tpl->assign('updateSettingsSuccess', true);
		}

		// is the user just registered
		if($this->URL->getParameter('registered') == 'true')
		{
			$this->tpl->assign('registerIsSuccess', true);
		}

		// parse the form
		$this->frm->parse($this->tpl);

		// add avatar
		$this->tpl->assign('avatar', $this->avatar);
		$this->tpl->assign('facebook_id', $this->facebook_id);

		// display name changes
		$this->tpl->assign('maxDisplayNameChanges', FrontendProfilesModel::MAX_DISPLAY_NAME_CHANGES);
		$this->tpl->assign('displayNameChangesLeft', FrontendProfilesModel::MAX_DISPLAY_NAME_CHANGES - $this->profile->getSetting('display_name_changes'));

		// add custom fields
		$this->tpl->assign('fields', $this->customFields);
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
			$txtDisplayName = $this->frm->getField('display_name');
			$txtFirstName = $this->frm->getField('first_name');
			$txtLastName = $this->frm->getField('last_name');
			$txtStreet = $this->frm->getField('street');
			$txtNumber = $this->frm->getField('number');
			$txtPostalCode = $this->frm->getField('postal_code');
			$txtCity = $this->frm->getField('city');
			$ddmCountry = $this->frm->getField('country');
			$ddmGender = $this->frm->getField('gender');
			$ddmDay = $this->frm->getField('day');
			$ddmMonth = $this->frm->getField('month');
			$ddmYear = $this->frm->getField('year');
			$imgAvatar = $this->frm->getField('avatar');

			// get number of display name changes
			$nameChanges = (int) FrontendProfilesModel::getSetting($this->profile->getId(), 'display_name_changes');

			// has there been a valid display name change request?
			if($this->profile->getDisplayName() !== $txtDisplayName->getValue() && $nameChanges <= FrontendProfilesModel::MAX_DISPLAY_NAME_CHANGES)
			{
				// display name filled in?
				if($txtDisplayName->isFilled(FL::getError('FieldIsRequired')))
				{
					// display name exists?
					if(FrontendProfilesModel::existsDisplayName($txtDisplayName->getValue(), $this->profile->getId()))
					{
						// set error
						$txtDisplayName->addError(FL::getError('DisplayNameExists'));
					}
				}
			}

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

			// number and postal code are not required but need to be integers
			if($txtNumber->isFilled() && !$txtNumber->isInteger())
			{
				$txtNumber->addError(FL::getError('NumberIsInvalid'));
			}
			if($txtPostalCode->isFilled() && !$txtPostalCode->isInteger())
			{
				$txtPostalCode->addError(FL::getError('PostalCodeIsInvalid'));
			}

			// validate avatar
			if($imgAvatar->isFilled())
			{
				// correct extension
				if($imgAvatar->isAllowedExtension(array('jpg', 'jpeg', 'gif', 'png'), FL::getError('JPGGIFAndPNGOnly')))
				{
					// correct mimetype?
					$imgAvatar->isAllowedMimeType(array('image/gif', 'image/jpg', 'image/jpeg', 'image/png'), FL::getError('JPGGIFAndPNGOnly'));
				}
			}

			// validate custom fields
			foreach($this->settings as $field)
			{
				// get field and validate if it's a number
				$formField = $this->frm->getField($field['db_title']);
				if($field['datatype'] == 'Number') $formField->isNumeric(FL::getError('NumberIsInvalid'));
			}

			// no errors
			if($this->frm->isCorrect())
			{
				// init
				$values = array();

				// has there been a valid display name change request?
				if($this->profile->getDisplayName() !== $txtDisplayName->getValue() && $nameChanges <= FrontendProfilesModel::MAX_DISPLAY_NAME_CHANGES)
				{
					// get display name value
					$values['display_name'] = $txtDisplayName->getValue();

					// update url based on the new display name
					$values['url'] = FrontendProfilesModel::getUrl($txtDisplayName->getValue(), $this->profile->getId());

					// update display name count
					$this->profile->setSetting('display_name_changes', $nameChanges + 1);
				}

				// update values
				if(!empty($values)) FrontendProfilesModel::update($this->profile->getId(), $values);

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
				$this->profile->setSetting('first_name', $txtFirstName->getValue());
				$this->profile->setSetting('last_name', $txtLastName->getValue());
				$this->profile->setSetting('city', $txtCity->getValue());
				$this->profile->setSetting('country', $ddmCountry->getValue());
				$this->profile->setSetting('gender', $ddmGender->getValue());
				$this->profile->setSetting('birth_date', $birthDate);
				$this->profile->setSetting('number', $txtNumber->getValue());
				$this->profile->setSetting('postal_code', $txtPostalCode->getValue());
				$this->profile->setSetting('street', $txtStreet->getValue());

				// has the user submitted an avatar?
				if($imgAvatar->isFilled())
				{
					// init vars
					$avatarsPath = FRONTEND_FILES_PATH . '/profiles/avatars';

					// delete old avatar if it isn't the default-image
					SpoonFile::delete($avatarsPath . '/source/' . $this->avatar);
					SpoonFile::delete($avatarsPath . '/128x128/' . $this->avatar);
					SpoonFile::delete($avatarsPath . '/64x64/' . $this->avatar);
					SpoonFile::delete($avatarsPath . '/32x32/' . $this->avatar);

					// create new filename
					$filename = rand(0,3) . '_' . $this->profile->getId() . '.' . $imgAvatar->getExtension();

					// add into settings to update
					$this->profile->setSetting('avatar', $filename);

					// resize
					$imgAvatar->createThumbnail($avatarsPath . '/128x128/' . $filename, 128, 128, true, false, 100);
					$imgAvatar->createThumbnail($avatarsPath . '/64x64/' . $filename, 64, 64, true, false, 100);
					$imgAvatar->createThumbnail($avatarsPath . '/32x32/' . $filename, 32, 32, true, false, 100);
				}

				// update custom fields
				foreach($this->settings as $field)
				{
					// get field and update
					$formField = $this->frm->getField($field['db_title']);
					if($formField->isFilled()) $this->profile->setSetting($field['db_title'], $formField->getValue());
				}

				// trigger event
				FrontendModel::triggerEvent('profiles', 'after_saved_settings', array('id' => $this->profile->getId()));

				// redirect
				$this->redirect(SITE_URL . FrontendNavigation::getURLForBlock('profiles', 'settings') . '?sent=true');
			}

			// show errors
			else $this->tpl->assign('updateSettingsHasFormError', true);
		}
	}
}
