<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the load threads action, it loads extra threads
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesAjaxLoadThreads extends FrontendBaseAJAXAction
{
	/**
	 * The offset
	 * 
	 * @var int
	 */
	private $offset;

	/**
	 * The id of the user
	 * 
	 * @var int
	 */
	private $userId;

	/**
	 * The messages
	 * 
	 * @var array
	 */
	private $threads;

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
		$this->threads = FrontendProfilesModel::getLatestThreadsByUserId($this->userId, 4, $this->offset);
		$count = FrontendProfilesModel::getCountThreadsByUserId($this->userId);
		$this->threads['amount'] = $count;
	}

	/**
	 * parses the data
	 */
	private function parse()
	{
		// output
		$this->output(self::OK, $this->threads);
	}

	/**
	 * Validate the data
	 */
	private function validateData()
	{
		// set values
		$postOffset = SpoonFilter::getPostValue('offset', null, '');
		$this->offset = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($postOffset) : SpoonFilter::htmlentities($postOffset);
		$postUserId = SpoonFilter::getPostValue('userId', null, '');
		$this->userId = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($postUserId) : SpoonFilter::htmlentities($postUserId);

		// validate
		if($this->offset == '') $this->output(self::BAD_REQUEST, null, 'offset-parameter is missing.');
		if(!is_numeric($this->offset)) $this->output(self::BAD_REQUEST, null, 'offset-parameter is no number.');
		if($this->userId == '') $this->output(self::BAD_REQUEST, null, 'userId-parameter is missing.');
		if(!is_numeric($this->userId)) $this->output(self::BAD_REQUEST, null, 'userId-parameter is no number.');
	}
}
