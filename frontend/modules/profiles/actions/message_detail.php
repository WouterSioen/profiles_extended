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
	 * The messages
	 * 
	 * @var array
	 */
	private $messages;

	/**
	 * The id of the thread
	 * 
	 * @var int
	 */
	private $threadId;

	/**
	 * The amount of messages in the thread
	 * 
	 * @var int
	 */
	private $count;

	/**
	 * Execute the extra.
	 */
	public function execute()
	{
		// only logged in profiles can see profiles
		if(FrontendProfilesAuthentication::isLoggedIn())
		{
			$this->threadId = $this->URL->getParameter(0);

			if(is_numeric($this->threadId))
			{
				if($this->getAllowed())
				{
					// call the parent
					parent::execute();
					$this->loadTemplate();
					$this->getData();
					$this->loadForm();
					$this->parse();
					$this->validateForm();
				}
				// not allowed to see it
				else $this->redirect(FrontendNavigation::getURLForBlock('profiles', 'messages'));
			}
			// no correct parameter
			else $this->redirect(FrontendNavigation::getURL(404));
		}
		// profile not logged in
		else $this->redirect(FrontendNavigation::getURLForBlock('profiles', 'login'));
	}

	/**
	 * Check if the user is allowed here
	 */
	private function getAllowed()
	{
		$receivers = FrontendProfilesModel::getProfilesInThread($this->threadId, 0);
		$userId = FrontendProfilesAuthentication::getProfile()->getId();

		foreach($receivers as $receiver)
		{
			if($receiver['id'] == $userId) return true;
		}

		return false;
	}

	/**
	 * Get's the thread and marks is at unread
	 */
	private function getData()
	{
		if($this->threadId)
		{
			$this->messages = FrontendProfilesModel::getMessagesByThreadId($this->threadId);
			$this->count = FrontendProfilesModel::getCountMessagesInThread($this->threadId);
		}
		else $this->redirect(FrontendNavigation::getURLForBlock('profiles', 'messages'));

		FrontendProfilesModel::markThreadAs($this->threadId, FrontendProfilesAuthentication::getProfile()->getId(), 'read');
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
		$this->tpl->assign('messages', $this->messages);
		$this->tpl->assign('load_more', ($this->count > 5));
		$this->tpl->assign('thread_id', $this->threadId);
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
				$messageId = FrontendProfilesModel::insertMessageInExistingThread($this->threadId, FrontendProfilesAuthentication::getProfile()->getId(), $txtMessage->getValue());
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