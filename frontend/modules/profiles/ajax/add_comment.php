<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add comment action, it adds a comment to a thread
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesAjaxAddComment extends FrontendBaseAJAXAction
{
	/**
	 * The id of the activity
	 * 
	 * @var int
	 */
	private $activity_id;

	/**
	 * The id of the user
	 * 
	 * @var int
	 */
	private $user_id;

	/**
	 * The commentText
	 * 
	 * @var string
	 */
	private $text;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->validateForm();
		$this->parse();
	}

	/**
	 * parse
	 */
	private function parse()
	{
		// insert comment
		$data = FrontendProfilesModel::insertComment($this->activity_id, $this->user_id, $this->text);

		// get profile info to display comment
		$profile = FrontendProfilesModel::get($this->user_id);
		$settings = $profile->getSettings();
		$settings['url'] = $profile->getUrl();
		$settings['id'] = $data;
		$this->output(self::OK, $settings);
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		// set values
		$post = SpoonFilter::getPostValue('activity', null, '');
		$this->activity_id = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($post) : SpoonFilter::htmlentities($post);
		$post = SpoonFilter::getPostValue('userId', null, '');
		$this->user_id = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($post) : SpoonFilter::htmlentities($post);
		$post = SpoonFilter::getPostValue('text', null, '');
		$this->text = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($post) : SpoonFilter::htmlentities($post);

		// validate
		if($this->text == '') $this->output(self::BAD_REQUEST, null, 'text-parameter is missing.');
	}
}