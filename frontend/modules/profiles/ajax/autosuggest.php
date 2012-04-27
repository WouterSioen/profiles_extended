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
class FrontendProfilesAjaxAutosuggest extends FrontendBaseAJAXAction
{
	/**
	 * The searchterm
	 * 
	 * @var string
	 */
	private $term;

	/**
	 * The found items
	 * 
	 * @var array
	 */
	private $items;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->validateForm();
		$this->getData();
		$this->parse();
	}

	/**
	 * Load the data
	 */
	private function getData()
	{
		// no search term = no search
		if(!$this->term) return;

		// get items
		$this->items = FrontendProfilesModel::search($this->term);
	}

	public function parse()
	{
		// format data
		foreach($this->items as &$item)
		{
			$item['first_name'] = unserialize($item['first_name']);
			$item['last_name'] = unserialize($item['last_name']);
		}

		// output
		$this->output(self::OK, $this->items);
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		// set values
		$searchTerm = SpoonFilter::getPostValue('term', null, '');
		$this->term = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($searchTerm) : SpoonFilter::htmlentities($searchTerm);

		// validate
		if($this->term == '') $this->output(self::BAD_REQUEST, null, 'term-parameter is missing.');
	}
}