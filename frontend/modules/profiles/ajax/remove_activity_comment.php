<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the remove activity comment action. It removes a comment from an activity
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesAjaxRemoveActivityComment extends FrontendBaseAJAXAction
{
	/**
	 * The activityId
	 * 
	 * @var int
	 */
	private $commentId;

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
		FrontendProfilesModel::deleteActivityComment($this->commentId);
		$this->output(self::OK, true);
	}

	/**
	 * Validate the data
	 */
	private function validateData()
	{
		// set values
		$postCommentId = SpoonFilter::getPostValue('commentId', null, '');
		$this->commentId = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($postCommentId) : SpoonFilter::htmlentities($postCommentId);

		// validate
		if($this->commentId == '') $this->output(self::BAD_REQUEST, null, 'commentId-parameter is missing.');
		if(!is_numeric($this->commentId)) $this->output(self::BAD_REQUEST, null, 'commentId-parameter is no number.');
	}
}
