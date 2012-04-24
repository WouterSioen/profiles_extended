<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the facebook-action, it will handle Facebook authentication changes.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendCoreAjaxFacebookChangeStatus extends FrontendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get parameters
		$type = SpoonFilter::getPostValue('type', array('login', 'logout'), '');

		switch($type)
		{
			case 'login':
				$facebook = Spoon::get('facebook');
				$data = $facebook->getCookie();

				if($data !== false)
				{
					if(!SpoonSession::exists('facebook_user_data'))
					{
						$data = $facebook->get('/me', array('metadata' => 0));
						SpoonSession::set('facebook_user_data', $data);
					}
					else $data = SpoonSession::get('facebook_user_data');
				}
				else SpoonSession::delete('facebook_user_data');
				break;

			case 'logout':
				CommonCookie::delete('facebook_user_data');
				$data = null;
				break;
		}


		// output
		$this->output(self::OK, $data);
	}
}
