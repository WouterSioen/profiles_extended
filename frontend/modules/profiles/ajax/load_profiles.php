<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the load messages actions, it loads extra messages
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesAjaxLoadProfiles extends FrontendBaseAJAXAction
{
	/**
	 * The offset
	 * 
	 * @var int $offset
	 */
	private $offset;

	/**
	 * The limit
	 * 
	 * @var int $limit
	 */
	private $limit;

	/**
	 * The lettre
	 * 
	 * @var string $lettre
	 */
	private $lettre;

	/**
	 * The profiles
	 * 
	 * @var array $profiles
	 */
	private $profiles;

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
		$this->profiles = FrontendProfilesModel::getProfilesByFirstLettre($this->lettre, $this->limit, $this->offset);
		$count = FrontendProfilesModel::getCountProfilesByFirstLettre($this->lettre);
		$this->profiles['amount'] = $count;
	}

	/**
	 * parses the data
	 */
	private function parse()
	{
		// output
		$this->output(self::OK, $this->profiles);
	}

	/**
	 * Validate the data
	 */
	private function validateData()
	{
		// set values
		$postOffset = SpoonFilter::getPostValue('offset', null, '');
		$this->offset = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($postOffset) : SpoonFilter::htmlentities($postOffset);
		$postLimit = SpoonFilter::getPostValue('limit', null, '');
		$this->limit = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($postLimit) : SpoonFilter::htmlentities($postLimit);
		$postLettre = SpoonFilter::getPostValue('lettre', null, '');
		$this->lettre = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($postLettre) : SpoonFilter::htmlentities($postLettre);

		// validate
		if($this->offset == '') $this->output(self::BAD_REQUEST, null, 'offset-parameter is missing.');
		if(!is_numeric($this->offset)) $this->output(self::BAD_REQUEST, null, 'offset-parameter is no number.');
		if($this->limit == '') $this->output(self::BAD_REQUEST, null, 'limit-parameter is missing.');
		if(!is_numeric($this->limit)) $this->output(self::BAD_REQUEST, null, 'limit-parameter is no number.');
		if($this->lettre == '') $this->output(self::BAD_REQUEST, null, 'lettre-parameter is missing.');
	}
}
