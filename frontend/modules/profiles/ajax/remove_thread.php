<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the mark as read action, it marks a thread as read for a certain user
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesAjaxRemoveThread extends FrontendBaseAJAXAction
{
	/**
	 * The user
	 * 
	 * @var int
	 */
	private $userId;

	/**
	 * The threadId
	 * 
	 * @var int
	 */
	private $threadId;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->validateData();
		$this->parse();
	}

	/**
	 * parses the data
	 */
	private function parse()
	{
		FrontendProfilesModel::markThreadAs($this->threadId, $this->userId, 'deleted');
		$this->output(self::OK, true);
	}

	/**
	 * Validate the data
	 */
	private function validateData()
	{
		// set values
		$postThreadId = SpoonFilter::getPostValue('threadId', null, '');
		$this->threadId = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($postThreadId) : SpoonFilter::htmlentities($postThreadId);
		$postUserId = SpoonFilter::getPostValue('userId', null, '');
		$this->userId = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($postUserId) : SpoonFilter::htmlentities($postUserId);

		// validate
		if($this->threadId == '') $this->output(self::BAD_REQUEST, null, 'threadId-parameter is missing.');
		if(!is_numeric($this->threadId)) $this->output(self::BAD_REQUEST, null, 'threadId-parameter is no number.');
		if($this->userId == '') $this->output(self::BAD_REQUEST, null, 'userId-parameter is missing.');
		if(!is_numeric($this->userId)) $this->output(self::BAD_REQUEST, null, 'userId-parameter is no number.');
	}
}
