<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the remove activity action. It removes an activity from a profile
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesAjaxRemoveActivity extends FrontendBaseAJAXAction
{
	/**
	 * The activityId
	 * 
	 * @var int
	 */
	private $activityId;

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
		FrontendProfilesModel::deleteActivity($this->activityId);
		$this->output(self::OK, true);
	}

	/**
	 * Validate the data
	 */
	private function validateData()
	{
		// set values
		$postActivityId = SpoonFilter::getPostValue('activityId', null, '');
		$this->activityId = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($postActivityId) : SpoonFilter::htmlentities($postActivityId);

		// validate
		if($this->activityId == '') $this->output(self::BAD_REQUEST, null, 'activityId-parameter is missing.');
		if(!is_numeric($this->activityId)) $this->output(self::BAD_REQUEST, null, 'activityId-parameter is no number.');
	}
}
