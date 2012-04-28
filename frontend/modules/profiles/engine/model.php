<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using with profiles.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Jan Moesen <jan.moesen@netlash.com>
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class FrontendProfilesModel
{
	const MAX_DISPLAY_NAME_CHANGES = 2;

	/**
	 * Delete a setting.
	 *
	 * @param int $id Profile id.
	 * @param string $name Setting name.
	 * @return int
	 */
	public static function deleteSetting($id, $name)
	{
		return (int) FrontendModel::getDB(true)->delete('profiles_settings', 'profile_id = ? AND name = ?', array((int) $id, (string) $name));
	}

	/**
	 * Check if a profile exists by email address.
	 *
	 * @param string $email Email to check for existence.
	 * @param int[optional] $ignoreId Profile id to ignore.
	 * @return bool
	 */
	public static function existsByEmail($email, $ignoreId = null)
	{
		return (bool) FrontendModel::getDB()->getVar(
			'SELECT 1
			 FROM profiles AS p
			 WHERE p.email = ? AND p.id != ?
			 LIMIT 1',
			array((string) $email, (int) $ignoreId)
		);
	}

	/**
	 * Check if a display name exists.
	 *
	 * @param string $displayName Display name to check for existence.
	 * @param int[optional] $id Profile id to ignore.
	 * @return bool
	 */
	public static function existsDisplayName($displayName, $id = null)
	{
		return (bool) FrontendModel::getDB()->getVar(
			'SELECT 1
			 FROM profiles AS p
			 WHERE p.id != ? AND p.display_name = ?
			 LIMIT 1',
			array((int) $id, (string) $displayName)
		);
	}

	/**
	 * Get profile by its id.
	 *
	 * @param int $profileId Id of the wanted profile.
	 * @return FrontendProfilesProfile
	 */
	public static function get($profileId)
	{
		return new FrontendProfilesProfile((int) $profileId);
	}

	/**
	 * Gets the needed info for the dropdown
	 * 
	 * @param int $id ID of the logged in profile
	 * @return array
	 */
	public static function getDropdownInfo($id)
	{
		$item = (array) FrontendModel::getDB()->getRecord(
			'SELECT p.display_name, p.url, COUNT(pms.id) AS count, ps.value AS avatar, ps1.value AS facebook_id FROM profiles AS p
			 LEFT JOIN profiles_message_status AS pms ON p.id = pms.receiver_id AND pms.status="unread"
			 LEFT JOIN profiles_settings AS ps ON p.id = ps.profile_id AND ps.name = "avatar"
			 LEFT JOIN profiles_settings AS ps1 ON p.id = ps1.profile_id AND ps1.name = "facebook_id"
			 WHERE p.id = ?', 
			(int) $id
		);

		$item['avatar'] = unserialize($item['avatar']);

		return $item;
	}

	/**
	 * Get an encrypted string.
	 *
	 * @param string $string String to encrypt.
	 * @param string $salt Salt to add to the string.
	 * @return string
	 */
	public static function getEncryptedString($string, $salt)
	{
		return md5(sha1(md5((string) $string)) . sha1(md5((string) $salt)));
	}

	/**
	 * Gets profile id by it's display name
	 * 
	 * @param string $display_name
	 * @return int
	 */
	public static function getIdByDisplayName($display_name)
	{
		return (int) FrontendModel::getDB()->getVar('SELECT p.id FROM profiles AS p WHERE p.display_name = ?', (string) $display_name);
	}

	/**
	 * Get profile id by email.
	 *
	 * @param string $email Email address.
	 * @return int
	 */
	public static function getIdByEmail($email)
	{
		return (int) FrontendModel::getDB()->getVar('SELECT p.id FROM profiles AS p WHERE p.email = ?', (string) $email);
	}

	/**
	 * Get profile id by setting.
	 *
	 * @param string $name Setting name.
	 * @param string $value Value of the setting.
	 * @return int
	 */
	public static function getIdBySetting($name, $value)
	{
		return (int) FrontendModel::getDB()->getVar(
			'SELECT ps.profile_id
			 FROM profiles_settings AS ps
			 WHERE ps.name = ? AND ps.value = ?',
			array((string) $name, serialize((string) $value))
		);
	}

	/**
	 * Get's the profile id by it's url
	 * 
	 * @param string $url The url
	 * @return int
	 */
	public static function getIdByUrl($url)
	{
		return (int) FrontendModel::getDB()->getVar('SELECT p.id FROM profiles AS p WHERE p.url = ?', (string) $url);
	}

	/**
	 * Gets latest message by thread Id
	 * 
	 * @param int $threadId
	 * @return array
	 */
	private static function getLatestMessageByThreadId($threadId)
	{
		$message = (array) FrontendModel::getDB()->getRecord(
			'SELECT pm.created_on, pm.text, ps.value AS first_name, ps2.value AS last_name
			 FROM  profiles_message AS pm
			 INNER JOIN profiles_message_status AS pms ON pm.id = pms.message_id
			 INNER JOIN profiles_settings AS ps ON pm.created_by = ps.profile_id AND ps.name = "first_name"
			 INNER JOIN profiles_settings AS ps2 ON pm.created_by = ps2.profile_id AND ps2.name = "last_name"
			 WHERE pm.thread_id = ?
			 ORDER BY pm.created_on DESC
			 LIMIT 1',
			(int) $threadId
		);

		$message['first_name'] = unserialize($message['first_name']);
		$message['last_name'] = unserialize($message['last_name']);

		return $message;
	}

	/**
	 * Get message_threads and it's latest messages for the given user
	 * 
	 * @param int $id
	 * @param int[optional] $limit The number of items to get.
	 * @param int[optional] $offset The offset.
	 * @return array
	 */
	public static function getLatestThreadsByUserId($id, $limit = 5, $offset = 0)
	{
		$latestThreads = (array) FrontendModel::getDB()->getRecords(
			'SELECT pmt.id
			 FROM profiles_message_thread AS pmt
			 INNER JOIN profiles_message AS pm ON pmt.id = pm.thread_id
			 INNER JOIN profiles_message_status AS pms ON pm.id = pms.message_id
			 WHERE pms.receiver_id = ?
			 GROUP BY pmt.id
			 ORDER BY pmt.latest_message_time DESC
			 LIMIT ?,?',
			array((int) $id, (int) $offset, (int) $limit)
		);

		foreach ($latestThreads as &$thread) {
			$thread['latestMessage'] = FrontendProfilesModel::getLatestMessageByThreadId($thread['id']);
		}

		return $latestThreads;
	}

	/**
	 * Gets the messages of the given thread, Newest first
	 * 
	 * @param int $id The id of the thread
	 * @return array The messages in the thread
	 */
	public static function getMessagesByThreadId($id)
	{
		$messages = (array) FrontendModel::getDB()->getRecords(
			'SELECT pm.created_on, p.display_name, ps.value AS first_name, ps2.value AS last_name, pm.text 
			 FROM profiles_message AS pm INNER JOIN profiles_message_status pms ON pm.id = pms.message_id
			 INNER JOIN profiles AS p ON p.id = pm.created_by
			 INNER JOIN profiles_settings AS ps ON pm.created_by = ps.profile_id AND ps.name = "first_name"
			 INNER JOIN profiles_settings AS ps2 ON pm.created_by = ps2.profile_id AND ps2.name = "last_name"
			 WHERE pm.thread_id = ?
			 GROUP BY pm.id
			 ORDER BY pm.created_on DESC', (int) $id
		);

		foreach($messages as &$message)
		{
			$message['first_name'] = unserialize($message['first_name']);
			$message['last_name'] = unserialize($message['last_name']);
		}

		return $messages;
	}

	/**
	 * Get's all the profiles starting with the given lettre
	 * 
	 * @param string $lettre The lettre
	 * @return array
	 */
	public static function getProfilesByFirstLettre($lettre)
	{
		$profiles = (array) FrontendModel::getDB()->getRecords(
			'SELECT p.url, ps1.value AS first_name, ps2.value AS last_name, ps3.value AS facebook_id, ps4.value AS avatar
			 FROM profiles AS p
			 INNER JOIN profiles_settings AS ps1 ON p.id = ps1.profile_id AND ps1.name = "first_name"
			 INNER JOIN profiles_settings AS ps2 ON p.id = ps2.profile_id AND ps2.name = "last_name"
			 LEFT JOIN profiles_settings AS ps3 ON p.id = ps3.profile_id AND ps3.name = "facebook_id"
			 LEFT JOIN profiles_settings AS ps4 ON p.id = ps4.profile_id AND ps4.name = "avatar"
			 WHERE ps1.value LIKE ?', 's:%:"' . $lettre . '%";'
		);

		foreach($profiles as &$profile)
		{
			$profile['first_name'] = unserialize($profile['first_name']);
			$profile['last_name'] = unserialize($profile['last_name']);
			$profile['facebook_id'] = unserialize($profile['facebook_id']);
			$profile['avatar'] = unserialize($profile['avatar']);
		}

		return $profiles;
	}

	/**
	 * Generate a random string.
	 *
	 * @param int[optional] $length Length of random string.
	 * @param bool[optional] $numeric Use numeric characters.
	 * @param bool[optional] $lowercase Use alphanumeric lowercase characters.
	 * @param bool[optional] $uppercase Use alphanumeric uppercase characters.
	 * @param bool[optional] $special Use special characters.
	 * @return string
	 */
	public static function getRandomString($length = 15, $numeric = true, $lowercase = true, $uppercase = true, $special = true)
	{
		// init
		$characters = '';
		$string = '';

		// possible characters
		if($numeric) $characters .= '1234567890';
		if($lowercase) $characters .= 'abcdefghijklmnopqrstuvwxyz';
		if($uppercase) $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		if($special) $characters .= '-_.:;,?!@#&=)([]{}*+%$';

		// get random characters
		for($i = 0; $i < $length; $i++)
		{
			// random index
			$index = mt_rand(0, strlen($characters));

			// add character to salt
			$string .= mb_substr($characters, $index, 1, SPOON_CHARSET);
		}

		return $string;
	}

	/**
	 * Get a setting for a profile.
	 *
	 * @param int $id Profile id.
	 * @param string $name Setting name.
	 * @return string
	 */
	public static function getSetting($id, $name)
	{
		return unserialize((string) FrontendModel::getDB()->getVar(
			'SELECT ps.value
			 FROM profiles_settings AS ps
			 WHERE ps.profile_id = ? AND ps.name = ?',
			array((int) $id, (string) $name))
		);
	}

	/**
	 * Get all settings for a profile.
	 *
	 * @param int $id Profile id.
	 * @return array
	 */
	public static function getSettings($id)
	{
		// get settings
		$settings = (array) FrontendModel::getDB()->getPairs(
			'SELECT ps.name, ps.value
			 FROM profiles_settings AS ps
			 WHERE ps.profile_id = ?',
			(int) $id
		);

		// unserialize values
		foreach($settings as $key => &$value) $value = unserialize($value);

		// return
		return $settings;
	}

	/**
	 * Retrieve a unique URL for a profile based on the display name.
	 *
	 * @param string $displayName The display name to base on.
	 * @param int[optional] $id The id of the profile to ignore.
	 * @return string
	 */
	public static function getUrl($displayName, $id = null)
	{
		// decode specialchars
		$displayName = SpoonFilter::htmlspecialcharsDecode((string) $displayName);

		// urlise
		$url = (string) SpoonFilter::urlise($displayName);

		// get db
		$db = FrontendModel::getDB();

		// new item
		if($id === null)
		{
			// get number of profiles with this URL
			$number = (int) $db->getVar(
				'SELECT 1
				 FROM profiles AS p
				 WHERE p.url = ?
				 LIMIT 1',
				(string) $url
			);

			// already exists
			if($number != 0)
			{
				// add number
				$url = FrontendModel::addNumber($url);

				// try again
				return self::getURL($url);
			}
		}

		// current profile should be excluded
		else
		{
			// get number of profiles with this URL
			$number = (int) $db->getVar(
				'SELECT 1
				 FROM profiles AS p
				 WHERE p.url = ? AND p.id != ?
				 LIMIT 1',
				array((string) $url, (int) $id)
			);

			// already exists
			if($number != 0)
			{
				// add number
				$url = FrontendModel::addNumber($url);

				// try again
				return self::getURL($url, $id);
			}
		}

		return $url;
	}

	/**
	 * Insert a new profile.
	 *
	 * @param array $values Profile data.
	 * @return int
	 */
	public static function insert(array $values)
	{
		return (int) FrontendModel::getDB(true)->insert('profiles', $values);
	}

	/**
	 * Inserts a new message in an existing thread
	 * 
	 * @param int $threadId The id of the thread
	 * @param int $senderId The profileId of the sender
	 * @param string $text The text in the message
	 * @return int
	 */
	public static function insertMessageInExistingThread($threadId, $senderId, $text)
	{
		$time = date('Y-m-d H:i:s');
		$db = FrontendModel::getDB(true);

		// get al the receiving users of the thread
		$receivingUsers = (array) $db->getRecords(
			'SELECT pms.receiver_id AS id
			 FROM profiles_message_status AS pms
			 INNER JOIN profiles_message AS pm ON pms.message_id = pm.id
			 WHERE pm.thread_id = ?
			 GROUP BY id', (int) $threadId
		);

		// get the user id of the user starting the thread
		$receivingUsers[] = (array) $db->getRecord(
			'SELECT pm.created_by AS id
			 FROM profiles_message AS pm
			 WHERE pm.thread_id = ?', (int) $threadId
		);

		// insert the message
		$messageId = (int) $db->insert(
			'profiles_message', 
			array(
				'thread_id' => $threadId,
				'created_by' => $senderId,
				'created_on' => $time,
				'text' => $text
			)
		);

		// for every receiving user of the thread that isn't you, add a message_status
		foreach($receivingUsers as $receivingUser)
		{
			if($receivingUser['id'] != $senderId)
			{
				// insert message_status
				$db->insert(
					'profiles_message_status',
					array(
						'message_id' => $messageId,
						'receiver_id' => $receivingUser['id']
					)
				);
			}
		}

		return $messageId;
	}

	/**
	 * Inserts a new message thread
	 * 
	 * @param int $id The user id of the person that started the thread
	 * @param array $receivers The user ids of the persons that will receive the message
	 * @param string $text The text in the message
	 * @return boolean
	 */
	public static function insertMessageThread($id, $receivers, $text)
	{
		$time = date('Y-m-d H:i:s');
		$db = FrontendModel::getDB(true);

		// insert thread
		$threadId = (int) $db->insert('profiles_message_thread', array('latest_message_time' => $time));

		// insert message
		$messageId = (int) $db->insert(
			'profiles_message', 
			array(
				'thread_id' => $threadId,
				'created_by' => $id,
				'created_on' => $time,
				'text' => $text
			)
		);

		// insert message_status for every receiver
		foreach ($receivers as $receiver) {
			(int) $db->insert(
				'profiles_message_status',
				array(
					'message_id' => $messageId,
					'receiver_id' => $receiver
				)
			);
		}

		return true;
	}

	/**
	 * Parse the general profiles info into the template.
	 */
	public static function parse()
	{
		// get the template
		$tpl = Spoon::get('template');

		// logged in
		if(FrontendProfilesAuthentication::isLoggedIn())
		{
			// get profile
			$profile = FrontendProfilesAuthentication::getProfile();

			// display name set?
			if($profile->getDisplayName() != '') $tpl->assign('profileDisplayName', $profile->getDisplayName());

			// no display name -> use email
			else $tpl->assign('profileDisplayName', $profile->getEmail());

			// show logged in
			$tpl->assign('isLoggedIn', true);
		}

		// ignore these url's in the querystring
		$ignoreUrls = array(
			FrontendNavigation::getURLForBlock('profiles', 'login'),
			FrontendNavigation::getURLForBlock('profiles', 'register'),
			FrontendNavigation::getURLForBlock('profiles', 'forgot_password')
		);

		// querystring
		$queryString = (isset($_GET['queryString'])) ? SITE_URL . '/' . urldecode($_GET['queryString']) : SELF;

		// check all ignore urls
		foreach($ignoreUrls as $url)
		{
			// querystring contains a boeboe url
			if(stripos($queryString, $url) !== false)
			{
				$queryString = '';
				break;
			}
		}

		// no need to add this if its empty
		$queryString = ($queryString != '') ? '?queryString=' . urlencode($queryString) : '';

		// useful urls
		$tpl->assign('loginUrl', FrontendNavigation::getURLForBlock('profiles', 'login') . $queryString);
		$tpl->assign('registerUrl', FrontendNavigation::getURLForBlock('profiles', 'register'));
		$tpl->assign('forgotPasswordUrl', FrontendNavigation::getURLForBlock('profiles', 'forgot_password'));
	}

	/**
	 * The function used to search users
	 * 
	 * @param string $term
	 * @return array Users
	 */
	public static function search($term)
	{
		$items = (array) FrontendModel::getDB()->getRecords(
			'SELECT p.display_name, p.url, ps1.value AS first_name, ps2.value AS last_name
			 FROM profiles AS p
			 INNER JOIN profiles_settings AS ps1 ON p.id = ps1.profile_id AND ps1.name = "first_name"
			 INNER JOIN profiles_settings AS ps2 ON p.id = ps2.profile_id AND ps2.name = "last_name"
			 WHERE ps1.value LIKE ?
			 OR ps2.value LIKE ?
			 OR display_name LIKE ?', 
			array(
				(string) 's:%:"%' . $term . '%";', 
				(string) 's:%:"%' . $term . '%";', 
				(string) '%' . $term . '%'
			)
		);

		if(!empty($items))
		{
			foreach($items as &$item)
			{
				$item['url'] = FrontendNavigation::getURLForBlock('profiles') . '/' . $item['url'];
			}
		}

		return $items;
	}

	/**
	 * Insert or update a single profile setting.
	 *
	 * @param int $id Profile id.
	 * @param string $name Setting name.
	 * @param mixed $value New setting value.
	 */
	public static function setSetting($id, $name, $value)
	{
		// insert or update
		FrontendModel::getDB(true)->execute(
			'INSERT INTO profiles_settings(profile_id, name, value)
			 VALUES(?, ?, ?)
			 ON DUPLICATE KEY UPDATE value = ?',
			array((int) $id, $name, serialize($value), serialize($value))
		);
	}

	/**
	 * Insert or update multiple profile settings.
	 *
	 * @param int $id Profile id.
	 * @param array $values Settings in key=>valye form.
	 */
	public static function setSettings($id, array $values)
	{
		// go over settings
		foreach($values as $key => $value) self::setSetting($id, $key, $value);
	}

	/**
	 * Update a profile.
	 *
	 * @param int $id The profile id.
	 * @param array $values The values to update.
	 * @return int
	 */
	public static function update($id, array $values)
	{
		return (int) FrontendModel::getDB(true)->update('profiles', $values, 'id = ?', (int) $id);
	}
}
