<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class defines the frontend, it is the core. Everything starts here.
 * We create all needed instances.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Frontend
{
	public function __construct()
	{
		new FrontendURL();
		new FrontendTemplate();
		new FrontendPage();
	}
}
