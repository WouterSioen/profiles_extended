<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the new Message action.
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesNewMessage extends FrontendBaseBlock
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
		if(FrontendProfilesAuthentication::isLoggedIn())
		{
			$this->loadTemplate();
			$this->loadForm();
			$this->validateForm();
			$this->parse();
		}

		// profile not logged in
		else $this->redirect(FrontendNavigation::getURLForBlock('profiles', 'login'));
	}

	/**
	 * Load the form.
	 */
	private function loadForm()
	{
		$this->frm = new FrontendForm('message', null, null, 'messageForm');
		$this->frm->addText('to');
		$this->frm->addTextarea('message');
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		$this->frm->parse($this->tpl);
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			// get fields
			$txtTo = $this->frm->getField('to');
			$txtMessage = $this->frm->getField('message');

			$txtTo->isFilled(FL::getError('ToIsRequired'));
			$txtMessage->isFilled(FL::getError('MessageIsRequired'));

			// create receivers array
			$receivers = explode(';', $txtTo->getValue());

			// get their id and check if they exist
			foreach($receivers as &$receiver)
			{
				$receiver = FrontendProfilesModel::getIdByDisplayName(trim($receiver));
				// check if the to field has an existing profile
				if($receiver == 0)
				{
					$txtTo->addError(FL::getError('InvalidUsername'));
				}
			}

			// send the message
			if($this->frm->isCorrect())
			{
				$messageId = FrontendProfilesModel::insertMessageThread(FrontendProfilesAuthentication::getProfile()->getId(), $receivers, $txtMessage->getValue());
				if($messageId == true)
				{
					// redirect
					$this->redirect(FrontendNavigation::getURLForBlock('profiles', 'messages') . '?sent=true');
				}
				else
				{
					// error :(
					$this->tpl->assign('sendError', 'FormError');
				}
			}
		}
	}
}