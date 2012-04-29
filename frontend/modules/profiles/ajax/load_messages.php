<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the autosuggest-action, it will output a list of users
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesAjaxLoadMessages extends FrontendBaseAJAXAction
{
	/**
	 * The offset
	 * 
	 * @var int $offset
	 */
	private $offset;

	/**
	 * The id of the thread
	 * 
	 * @var int $threadId
	 */
	private $threadId;

	/**
	 * The messages
	 * 
	 * @var array $messages
	 */
	private $messages;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->validateData();
		$this->getData();
		$this->parse();
	}

	/**
	 * gets the data
	 */
	private function getData()
	{
		$this->messages = array_reverse(FrontendProfilesModel::getMessagesByThreadId($this->threadId, 4, $this->offset));
		$count = FrontendProfilesModel::getCountMessagesInThread($this->threadId);
		$this->messages['amount'] = $count;
	}

	/**
	 * parses the data
	 */
	private function parse()
	{
		// output
		$this->output(self::OK, $this->messages);
	}

	/**
	 * Validate the data
	 */
	private function validateData()
	{
		// set values
		$postOffset = SpoonFilter::getPostValue('offset', null, '');
		$this->offset = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($postOffset) : SpoonFilter::htmlentities($postOffset);
		$postThreadId = SpoonFilter::getPostValue('threadId', null, '');
		$this->threadId = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($postThreadId) : SpoonFilter::htmlentities($postThreadId);

		// validate
		if($this->offset == '') $this->output(self::BAD_REQUEST, null, 'offset-parameter is missing.');
		if(!is_numeric($this->offset)) $this->output(self::BAD_REQUEST, null, 'offset-parameter is no number.');
		if($this->threadId == '') $this->output(self::BAD_REQUEST, null, 'threadId-parameter is missing.');
		if(!is_numeric($this->threadId)) $this->output(self::BAD_REQUEST, null, 'threadId-parameter is no number.');
	}
}
