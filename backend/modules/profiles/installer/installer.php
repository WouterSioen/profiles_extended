<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the profiles module.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Davy Van Vooren <davy.vanvooren@netlash.com>
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class ProfilesInstaller extends ModuleInstaller
{
	/**
	 * Install the module.
	 */
	public function install()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'profiles' as a module
		$this->addModule('profiles');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// module rights
		$this->setModuleRights(1, 'profiles');

		// action rights
		$this->setActionRights(1, 'profiles', 'add');
		$this->setActionRights(1, 'profiles', 'add_group');
		$this->setActionRights(1, 'profiles', 'add_profile_group');
		$this->setActionRights(1, 'profiles', 'add_user_info');
		$this->setActionRights(1, 'profiles', 'block');
		$this->setActionRights(1, 'profiles', 'delete_group');
		$this->setActionRights(1, 'profiles', 'delete_profile_group');
		$this->setActionRights(1, 'profiles', 'delete');
		$this->setActionRights(1, 'profiles', 'edit_group');
		$this->setActionRights(1, 'profiles', 'edit_profile_group');
		$this->setActionRights(1, 'profiles', 'edit');
		$this->setActionRights(1, 'profiles', 'groups');
		$this->setActionRights(1, 'profiles', 'index');
		$this->setActionRights(1, 'profiles', 'mass_action');
		$this->setActionRights(1, 'profiles', 'user_info');

		// set navigation
		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationProfilesId = $this->setNavigation($navigationModulesId, 'Profiles');
		$this->setNavigation($navigationProfilesId, 'Overview', 'profiles/index', array(
			'profiles/add',
			'profiles/edit',
			'profiles/add_profile_group',
			'profiles/edit_profile_group'
		));
		$this->setNavigation($navigationProfilesId, 'Groups', 'profiles/groups', array(
			'profiles/add_group',
			'profiles/edit_group'
		));
		$this->setNavigation($navigationProfilesId, 'Userinfo', 'profiles/user_info', array(
			'profiles/add_user_info'
		));

		// add extra
		$activateId = $this->insertExtra('profiles', 'block', 'Activate', 'activate', null, 'N', 5000);
		$forgotPasswordId = $this->insertExtra('profiles', 'block', 'ForgotPassword', 'forgot_password', null, 'N', 5001);
		$indexId = $this->insertExtra('profiles', 'block', 'Dashboard', null, null, 'N', 5002);
		$loginId = $this->insertExtra('profiles', 'block', 'Login', 'login', null, 'N', 5003);
		$logoutId = $this->insertExtra('profiles', 'block', 'Logout', 'logout', null, 'N', 5004);
		$passwordEmailId = $this->insertExtra('profiles', 'block', 'PasswordEmail', 'password_email', null, 'N', 5005);
		$alertsNewsletterId = $this->insertExtra('profiles', 'block','AlertsNewsletter', 'alerts_newsletter', null, 'N', 5006);
		$settingsId = $this->insertExtra('profiles', 'block', 'Settings', 'settings', null, 'N', 5007);
		$registerId = $this->insertExtra('profiles', 'block', 'Register', 'register', null, 'N', 5008);
		$resetPasswordId = $this->insertExtra('profiles', 'block', 'ResetPassword', 'reset_password', null, 'N', 5009);
		$resendActivationId = $this->insertExtra('profiles', 'block', 'ResendActivation', 'resend_activation', null, 'N', 5010);
		$overviewId = $this->insertExtra('profiles', 'block', 'Overview', 'overview', null, 'N', 5011);
		$facebookSettingsId = $this->insertExtra('profiles', 'block', 'FacebookSettings', 'facebook_settings', null, 'N', 5012);
		$messagesId = $this->insertExtra('profiles', 'block', 'Messages', 'messages', null, 'N', 5013);
		$newMessageId = $this->insertExtra('profiles', 'block', 'NewMessage', 'new_message', null, 'N', 5014);
		$messageDetailId = $this->insertExtra('profiles', 'block', 'MessageDetail', 'message_detail', null, 'N', 5015);
		$profileDropdownId = $this->insertExtra('profiles', 'widget', 'ProfileDropdown', 'dropdown', null, 'N', 5016);

		// get search widget id
		$searchId = (int) $this->getDB()->getVar('SELECT id FROM modules_extras WHERE module = ? AND action = ?', array('search', 'form'));

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// only add pages if profiles isnt linked anywhere
			// @todo refactor me, syntax sucks atm
			if(!(bool) $this->getDB()->getVar(
				'SELECT 1
				 FROM pages AS p
				 INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
				 INNER JOIN modules_extras AS e ON e.id = b.extra_id
				 WHERE e.module = ? AND p.language = ?
				 LIMIT 1',
				array('profiles', $language)))
			{
				// activate page
				$this->insertPage(
					array(
						'title' => 'Activate',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $profileDropdownId, 'position' => 'top'),
					array('extra_id' => $activateId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top'),
					array('extra_id' => $profileDropdownId, 'position' => 'top')
				);

				// forgot password page
				$this->insertPage(
					array(
						'title' => 'Forgot password',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $profileDropdownId, 'position' => 'top'),
					array('extra_id' => $forgotPasswordId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top'),
					array('extra_id' => $profileDropdownId, 'position' => 'top')
				);

				// reset password page
				$this->insertPage(
					array(
						'title' => 'Reset password',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $profileDropdownId, 'position' => 'top'),
					array('extra_id' => $resetPasswordId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top'),
					array('extra_id' => $profileDropdownId, 'position' => 'top')
				);

				// resend activation email page
				$this->insertPage(
					array(
						'title' => 'Resend activation e-mail',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $profileDropdownId, 'position' => 'top'),
					array('extra_id' => $resendActivationId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top'),
					array('extra_id' => $profileDropdownId, 'position' => 'top')
				);

				// login page
				$this->insertPage(
					array(
						'title' => 'Login',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $profileDropdownId, 'position' => 'top'),
					array('extra_id' => $loginId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top'),
					array('extra_id' => $profileDropdownId, 'position' => 'top')
				);

				// register page
				$this->insertPage(
					array(
						'title' => 'Register',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $profileDropdownId, 'position' => 'top'),
					array('extra_id' => $registerId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top'),
					array('extra_id' => $profileDropdownId, 'position' => 'top')
				);

				// logout page
				$this->insertPage(
					array(
						'title' => 'Logout',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $profileDropdownId, 'position' => 'top'),
					array('extra_id' => $logoutId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top'),
					array('extra_id' => $profileDropdownId, 'position' => 'top')
				);

				// insert terms and conditions
				$this->insertPage(
					array(
						'title' => SpoonFilter::ucfirst($this->getLocale('TermsAndConditions', 'profiles', $language, 'lbl', 'frontend')),
						'type' => 'root',
						'language' => $language
					),
					array('data' => array('seo_index' => 'noindex', 'seo_follow' => 'nofollow')),
					array('html' => '<h3>Terms and Conditions</h3><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin dui odio, placerat non mattis eget, bibendum sed orci. Ut eu tempus urna. Suspendisse sed lorem augue, in ullamcorper nulla. Curabitur tincidunt risus faucibus urna porttitor rhoncus. Aenean convallis velit libero, ac vulputate urna. Donec volutpat libero sed orci lacinia a bibendum nisi egestas. Vivamus nec tellus eu dolor ornare posuere. Nam sapien ante, semper id tempus sit amet, ornare at diam. Donec consectetur, tortor ut auctor laoreet, magna libero ultricies libero, vitae posuere dolor nibh quis ante. Aliquam odio quam, sodales non ullamcorper sit amet, cursus a est. Ut aliquet, ligula viverra tincidunt mattis, leo tortor dictum massa, molestie dapibus nisl magna consequat metus. Mauris vitae accumsan felis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce tincidunt lacus aliquet mauris pharetra sit amet hendrerit velit vestibulum. Fusce porta laoreet nibh, in ultrices diam posuere vitae.</p>'),
					array('extra_id' => $searchId, 'position' => 'top'),
					array('extra_id' => $profileDropdownId, 'position' => 'top')
				);

				// insert privacy policy
				$this->insertPage(
					array(
						'title' => SpoonFilter::ucfirst($this->getLocale('PrivacyPolicy', 'profiles', $language, 'lbl', 'frontend')),
						'type' => 'root',
						'language' => $language
					),
					array('data' => array('seo_index' => 'noindex', 'seo_follow' => 'nofollow')),
					array('html' => '<h3>Privacy Policy</h3><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin dui odio, placerat non mattis eget, bibendum sed orci. Ut eu tempus urna. Suspendisse sed lorem augue, in ullamcorper nulla. Curabitur tincidunt risus faucibus urna porttitor rhoncus. Aenean convallis velit libero, ac vulputate urna. Donec volutpat libero sed orci lacinia a bibendum nisi egestas. Vivamus nec tellus eu dolor ornare posuere. Nam sapien ante, semper id tempus sit amet, ornare at diam. Donec consectetur, tortor ut auctor laoreet, magna libero ultricies libero, vitae posuere dolor nibh quis ante. Aliquam odio quam, sodales non ullamcorper sit amet, cursus a est. Ut aliquet, ligula viverra tincidunt mattis, leo tortor dictum massa, molestie dapibus nisl magna consequat metus. Mauris vitae accumsan felis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce tincidunt lacus aliquet mauris pharetra sit amet hendrerit velit vestibulum. Fusce porta laoreet nibh, in ultrices diam posuere vitae.</p>'),
					array('extra_id' => $searchId, 'position' => 'top'),
					array('extra_id' => $profileDropdownId, 'position' => 'top')
				);

				// settings main page
				$settingsOverviewId = $this->insertPage(
					array(
						'title' => 'Settings',
						'type' => 'root',
						'language' => $language
					),
					null,
					null
				);

				// settings page
				$this->insertPage(
					array(
						'title' => 'Profile settings',
						'parent_id' => $settingsOverviewId,
						'language' => $language
					),
					null,
					array('extra_id' => $profileDropdownId, 'position' => 'top'),
					array('extra_id' => $settingsId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top'),
					array('extra_id' => $profileDropdownId, 'position' => 'top')
				);

				// alerts and newsletter settings page
				$this->insertPage(
					array(
						'title' => 'Alerts and newsletter',
						'parent_id' => $settingsOverviewId,
						'language' => $language
					),
					null,
					array('extra_id' => $profileDropdownId, 'position' => 'top'),
					array('extra_id' => $alertsNewsletterId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top'),
					array('extra_id' => $profileDropdownId, 'position' => 'top')
				);
				

				// change password and email page
				$this->insertPage(
					array(
						'title' => 'Password and email',
						'parent_id' => $settingsOverviewId,
						'language' => $language
					),
					null,
					array('extra_id' => $profileDropdownId, 'position' => 'top'),
					array('extra_id' => $passwordEmailId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top'),
					array('extra_id' => $profileDropdownId, 'position' => 'top')
				);

				// change password and email page
				$this->insertPage(
					array(
						'title' => 'Facebook settings',
						'parent_id' => $settingsOverviewId,
						'language' => $language,
						'hidden' => 'Y'
					),
					null,
					array('extra_id' => $profileDropdownId, 'position' => 'top'),
					array('extra_id' => $facebookSettingsId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top'),
					array('extra_id' => $profileDropdownId, 'position' => 'top')
				);

				// profiles overview
				$this->insertPage(
					array(
						'title' => 'Profielen',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $profileDropdownId, 'position' => 'top'),
					array('extra_id' => $overviewId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top'),
					array('extra_id' => $profileDropdownId, 'position' => 'top')
				);

				// index page
				$this->insertPage(
					array(
						'title' => 'Profile',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $profileDropdownId, 'position' => 'top'),
					array('extra_id' => $indexId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top'),
					array('extra_id' => $profileDropdownId, 'position' => 'top')
				);

				// messages page
				$this->insertPage(
					array(
						'title' => 'My Messages',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $profileDropdownId, 'position' => 'top'),
					array('extra_id' => $messagesId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top'),
					array('extra_id' => $profileDropdownId, 'position' => 'top')
				);

				// new message page
				$this->insertPage(
					array(
						'title' => 'New Message',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $profileDropdownId, 'position' => 'top'),
					array('extra_id' => $newMessageId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top'),
					array('extra_id' => $profileDropdownId, 'position' => 'top')
				);

				// message detail
				$this->insertPage(
					array(
						'title' => 'Message Detail',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $profileDropdownId, 'position' => 'top'),
					array('extra_id' => $messageDetailId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top'),
					array('extra_id' => $profileDropdownId, 'position' => 'top')
				);
			}
		}
	}
}
