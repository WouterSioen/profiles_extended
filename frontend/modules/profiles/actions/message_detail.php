<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the message detail action
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesMessageDetail extends FrontendBaseBlock
{
	/**
	 * FrontendForm instance.
	 *
	 * @var	FrontendForm
	 */
	private $frm;

	/**
	 * The thread
	 * 
	 * @var array
	 */
	private $thread;

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
			$this->getData();
			$this->loadForm();
			$this->parse();
			$this->validateForm();
		}

		// profile not logged in
		else $this->redirect(FrontendNavigation::getURLForBlock('profiles', 'login'));
	}

	/**
	 * Get's the thread and marks is at unread
	 */
	private function getData()
	{
		$threadId = $this->URL->getParameter(0);
		if($threadId)
		{
			$this->thread = FrontendProfilesModel::getMessagesByThreadId($threadId);
		}
		else $this->redirect(FrontendNavigation::getURLForBlock('profiles', 'messages'));

		FrontendProfilesModel::markThreadAs($threadId, FrontendProfilesAuthentication::getProfile()->getId(), 'read');
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		$this->frm = new FrontendForm('message', null, null, 'messageForm');
		$this->frm->addTextarea('message');
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		$this->tpl->assign('thread', $this->thread);
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
			$txtMessage = $this->frm->getField('message');

			$txtMessage->isFilled(FL::getError('MessageIsRequired'));

			// send the message
			if($this->frm->isCorrect())
			{
				$messageId = FrontendProfilesModel::insertMessageInExistingThread($this->URL->getParameter(0), FrontendProfilesAuthentication::getProfile()->getId(), $txtMessage->getValue());
				if($messageId != 0)
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