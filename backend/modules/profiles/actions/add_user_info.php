<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add userinfo-action, it let's you add custom user info to the site
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class BackendProfilesAddUserinfo extends BackendBaseActionEdit
{
	/**
	 * Execute the action.
	 */
	public function execute()
	{
		parent::execute();
		$this->loadForm();
		$this->validateForm();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the form.
	 */
	private function loadForm()
	{
		$this->frm = new BackendForm('addUserInfo');
		$this->frm->addText('title');
		$this->frm->addDropdown('type', 
		array(
			'Varchar' => 'Text (short)', 
			'Text' => 'Text (long)', 
			'Number' => 'Number',
			'Enum' => 'Dropdown',
			'Bool' => 'Checkbox'),
		null, null, 'showValues');
		$this->frm->addText('values');
	}

	/**
	 * Validate the form.
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// get fields
			$txtTitle = $this->frm->getField('title');
			$ddmType = $this->frm->getField('type');
			$txtValues = $this->frm->getField('values');

			$txtTitle->isFilled(BL::getError('TitleIsRequired'));
			if($ddmType->getValue() == 'Enum') $txtValues->isFilled(BL::getError('ValuesAreRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// create values
				$values['title'] = $txtTitle->getValue();
				$values['db_title'] = SpoonFilter::urlise($txtTitle->getValue());
				$values['datatype'] = $ddmType->getValue();
				if($ddmType->getValue() == 'Enum') $values['values'] = ' ;' . $txtValues->getValue();

				$infoId = BackendProfilesModel::insertUserInfo($values);
			}
		}
	}
}