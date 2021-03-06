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
	 * Delete an activity
	 * 
	 * @param int $id The id of the activity
	 * @return int
	 */
	public static function deleteActivity($id)
	{
		FrontendModel::getDB(true)->delete('profiles_activity', 'id = ?', (int) $id);
		// also remove the comments
		FrontendModel::getDB(true)->delete('profiles_activity_comments', 'activity_id = ?', (int) $id);
	}

	/**
	 * Delete a comment from an activity
	 * 
	 * @param int $id The id of the comment
	 * @return int
	 */
	public static function deleteActivityComment($id)
	{
		FrontendModel::getDB(true)->delete('profiles_activity_comments', 'id = ?', (int) $id);
	}

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
	 * Gets the commetns for an activity
	 * 
	 * @param int $activity_id The id of the activity
	 * @return array
	 */
	public static function getActivityComments($activity_id)
	{
		$comments = (array) FrontendModel::getDB()->getRecords(
			'SELECT pac.id, pac.text, UNIX_TIMESTAMP(pac.created_on) AS created_on, pac.user_id
			 FROM profiles_activity_comments AS pac
			 WHERE pac.activity_id = ? AND pac.status = ?
			 ORDER BY pac.created_on ASC', 
			array((int) $activity_id, (string) 'visible')
		);

		// add userinfo
		foreach($comments as &$comment)
		{
			$profile = FrontendProfilesModel::get($comment['user_id']);
			$comment['username'] = $profile->getDisplayName();
			$comment['avatar'] = $profile->getSetting('avatar');
			$comment['facebook_id'] = $profile->getSetting('facebook_id');
			$comment['url'] = $profile->getUrl();
			$comment['deletable'] = ($profile->getId() == FrontendProfilesAuthentication::getProfile()->getId());
		}

		return $comments;
	}

	/**
	 * Get Count messages in a thread
	 * 
	 * @param int $id The id of the thread
	 * @return int
	 */
	public static function getCountMessagesInThread($threadId)
	{
		return (int) FrontendModel::getDB()->getVar(
			'SELECT count(pm.id)
			 FROM profiles_message AS pm
			 WHERE pm.thread_id = ?', (int) $threadId
		);
	}

	/**
	 * Get's all the amount of profiles starting with the given lettre
	 * 
	 * @param string $lettre The lettre
	 * @return array
	 */
	public static function getCountProfilesByFirstLettre($lettre)
	{
		return (int) FrontendModel::getDB()->getVar(
			'SELECT count(p.id) AS count
			 FROM profiles AS p
			 INNER JOIN profiles_settings AS ps ON p.id = ps.profile_id AND ps.name = "last_name"
			 WHERE ps.value LIKE ?', 's:%:"' . $lettre . '%";'
		);
	}

	/**
	 * Get count threads for a user
	 * 
	 * @param int $id The id of the user
	 * @return int
	 */
	public static function getCountThreadsByUserId($id)
	{
		return (int) FrontendModel::getDB()->getVar(
			'SELECT count(pts.id)
			 FROM profiles_thread_status AS pts
			 WHERE pts.receiver_id = ?', (int) $id
		);
	}

	/**
	 * Get site specific settings
	 * 
	 * @param int $id The id of the user
	 * @return array
	 */
	public static function getCustomSettings($id)
	{
		$customSettings = (array) FrontendModel::getDB()->getRecords(
			'SELECT pi.title, pi.db_title, pi.datatype, pi.values FROM profiles_info AS pi'
		);

		// get the data for the given user
		foreach($customSettings as &$customSetting) $customSetting['value'] = FrontendProfilesModel::getSetting($id, $customSetting['db_title']);

		return $customSettings;
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
			'SELECT p.display_name, p.url, COUNT(pts.id) AS count, ps.value AS avatar, ps1.value AS facebook_id FROM profiles AS p
			 LEFT JOIN profiles_thread_status AS pts ON p.id = pts.receiver_id AND pts.status="unread"
			 LEFT JOIN profiles_settings AS ps ON p.id = ps.profile_id AND ps.name = "avatar"
			 LEFT JOIN profiles_settings AS ps1 ON p.id = ps1.profile_id AND ps1.name = "facebook_id"
			 WHERE p.id = ?', 
			(int) $id
		);

		$item['avatar'] = unserialize($item['avatar']);
		$item['facebook_id'] = unserialize($item['facebook_id']);

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
	 * Get message_threads and it's latest messages for the given user
	 * 
	 * @param int $id
	 * @param int[optional] $limit The number of items to get.
	 * @param int[optional] $offset The offset.
	 * @return array
	 */
	public static function getLatestThreadsByUserId($id, $limit = 4, $offset = 0)
	{
		$threads = (array) FrontendModel::getDB()->getRecords(
			'SELECT pt.id, pm.text, pm.created_on, IF(pts.status = "read", 1, 0) AS status
			 FROM profiles_thread AS pt
			 INNER JOIN profiles_message AS pm ON pt.latest_message_id = pm.id
			 INNER JOIN profiles_thread_status AS pts ON pt.id = pts.thread_id
			 WHERE pts.receiver_id = ? and pts.status != "deleted"
			 GROUP BY pt.id
			 ORDER BY pm.created_on DESC
			 LIMIT ?,?',
			array((int) $id, (int) $offset, (int) $limit)
		);

		// get participating users for each thread
		foreach($threads as &$thread)
		{
			$thread['receivers'] = FrontendProfilesModel::getProfilesInThread($thread['id'], $id);
			$thread['status'] = (int) $thread['status'];
		}

		return $threads;
	}

	/**
	 * Gets the messages of the given thread, Newest first
	 * 
	 * @param int $id The id of the thread
	 * @param int[optional] $limit The number of items to get.
	 * @param int[optional] $offset The offset.
	 * @return array The messages in the thread
	 */
	public static function getMessagesByThreadId($id, $limit = 4, $offset = 0)
	{
		$messages = (array) FrontendModel::getDB()->getRecords(
			'SELECT pm.created_on, pm.text, p.display_name, p.url, ps.value AS facebook_id, ps1.value AS avatar
			 FROM profiles_message AS pm
			 INNER JOIN profiles AS p ON pm.user_id = p.id
			 LEFT JOIN profiles_settings AS ps ON p.id = ps.profile_id AND ps.name = "facebook_id"
			 LEFT JOIN profiles_settings AS ps1 ON p.id = ps1.profile_id AND ps1.name = "avatar"
			 WHERE pm.thread_id = ?
			 ORDER BY pm.created_on DESC
			 LIMIT ?,?', array((int) $id, (int) $offset, (int) $limit)
		);

		foreach($messages as &$message)
		{
			$message['facebook_id'] = unserialize($message['facebook_id']);
			$message['avatar'] = unserialize($message['avatar']);
		}

		return array_reverse($messages);
	}

	/**
	 * Get's all the profiles starting with the given lettre
	 * 
	 * @param string $lettre The lettre
	 * @param int[optional] $limit The number of items to get.
	 * @param int[optional] $offset The offset.
	 * @return array
	 */
	public static function getProfilesByFirstLettre($lettre, $limit = 15, $offset = 0)
	{
		$profiles = (array) FrontendModel::getDB()->getRecords(
			'SELECT p.url, ps1.value AS first_name, ps2.value AS last_name, ps3.value AS facebook_id, ps4.value AS avatar
			 FROM profiles AS p
			 INNER JOIN profiles_settings AS ps1 ON p.id = ps1.profile_id AND ps1.name = "first_name"
			 INNER JOIN profiles_settings AS ps2 ON p.id = ps2.profile_id AND ps2.name = "last_name"
			 LEFT JOIN profiles_settings AS ps3 ON p.id = ps3.profile_id AND ps3.name = "facebook_id"
			 LEFT JOIN profiles_settings AS ps4 ON p.id = ps4.profile_id AND ps4.name = "avatar"
			 WHERE ps2.value LIKE ?
			 LIMIT ?,?', array((string) 's:%:"' . $lettre . '%";', (int) $offset, (int) $limit)
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
	 * Get's all the profiles in a thread
	 * 
	 * @param int $threadId The id of the thread
	 * @param int $exclude The user to exclude (normally the user getting this information)
	 * @return array An array with display names and id's
	 */
	public static function getProfilesInThread($threadId, $exclude)
	{
		return (array) FrontendModel::getDB()->getRecords(
			'SELECT p.display_name, p.id
			 FROM profiles_thread_status AS pts
			 INNER JOIN profiles AS p ON p.id = pts.receiver_id
			 WHERE pts.thread_id = ? AND pts.receiver_id != ?
			 GROUP BY pts.receiver_id', array((int) $threadId, (int) $exclude)
		);
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
	 * Gets the activity stream for a user
	 * 
	 * @param int $user_id
	 * @param int [optional] $offset The offset
	 * @param int [optional] $limit The limit
	 * @return array
	 */
	public static function getUserActivities($user_id, $offset = 0, $limit = 10)
	{
		$activities = (array) FrontendModel::getDB()->getRecords(
			'SELECT pa.id, pa.user_id, pa.action, pa.title, pa.url, pa.status, UNIX_TIMESTAMP(pa.created_on) AS created_on
			 FROM profiles_activity AS pa
			 WHERE pa.user_id = ?
			 ORDER BY pa.created_on DESC
			 LIMIT ?,?', 
			array(
				(int) $user_id,
				(int) $offset,
				(int) $limit
			)
		);

		// add comments
		foreach($activities as &$activity) $activity['comments'] = FrontendProfilesModel::getActivityComments($activity['id']);

		return $activities;
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
	 * Inserst a comment
	 * 
	 * @param int $activity_id
	 * @param int $user_id
	 * @param string $text
	 * @return int
	 */
	public static function insertComment($activity_id, $user_id, $text)
	{
		$values = array(
			'activity_id' => $activity_id,
			'user_id' => $user_id,
			'text' => $text,
			'created_on' => date('Y-m-d H:i:s'),
			'status' => 'visible'
		);

		return FrontendModel::getDB(true)->insert('profiles_activity_comments', $values);
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
		$receivingUsers = FrontendProfilesModel::getProfilesInThread($threadId, 0);

		// insert the message
		$messageId = (int) $db->insert(
			'profiles_message', 
			array(
				'thread_id' => $threadId,
				'user_id' => $senderId,
				'created_on' => $time,
				'text' => $text
			)
		);

		// for every receiving user of the thread that isn't you, update the status to unread
		foreach($receivingUsers as $receivingUser)
		{
			if($receivingUser['id'] != $senderId)
			{
				$db->update(
					'profiles_thread_status',
					array('status' => 'unread'),
					'thread_id = ' . $threadId . ' AND receiver_id = ' . $receivingUser['id']
				);
			}
		}

		// update thread
		$db->update('profiles_thread', array('latest_message_id' => $messageId), 'id = ' . $threadId);

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
		$threadId = (int) $db->insert('profiles_thread', array('latest_message_id' => 0));

		// insert message
		$messageId = (int) $db->insert(
			'profiles_message', 
			array(
				'thread_id' => $threadId,
				'user_id' => $id,
				'created_on' => $time,
				'text' => $text
			)
		);

		// update thread
		$db->update('profiles_thread', array('latest_message_id' => $messageId), 'id = ' . $threadId);

		// insert thread_status for every receiver
		foreach($receivers as $receiver)
		{
			$db->insert(
				'profiles_thread_status',
				array(
					'thread_id' => $threadId,
					'receiver_id' => $receiver
				)
			);
		}

		// insert thread status for sender
		$db->insert(
			'profiles_thread_status',
			array(
				'thread_id' => $threadId,
				'receiver_id' => $id,
				'status' => 'read'
			)
		);

		return true;
	}

	/**
	 * Marks a thread as read/unread/deleted for a certain user
	 * 
	 * @param int $threadId The id of the thread
	 * @param int $receiverId The profile id of the receiving user
	 * @param string $status
	 * @return int
	 */
	public static function markThreadAs($threadId, $receiverId, $status)
	{
		return FrontendModel::getDB()->execute(
			'UPDATE profiles_thread_status AS pts
			 SET pts.status = ?
			 WHERE pts.thread_id = ?
			 AND pts.receiver_id = ?',
			array(
				(string) $status,
				(int) $threadId,
				(int) $receiverId
			)
		);
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
	 * Publish to a uses activity stream
	 * 
	 * @param int $user_id
	 * @param string $action
	 * @param string $title
	 * @param string $url
	 * @return int
	 */
	public static function publishToActivityStream($user_id, $action, $title, $url)
	{
		// check if action already exists
		$item = FrontendModel::getDB()->getVar(
			'SELECT pa.id
			 FROM profiles_activity AS pa
			 WHERE pa.user_id = ? AND pa.action = ? AND pa.title = ?',
			array(
				(int) $user_id,
				(string) $action,
				(string) $title
			)
		);

		$values = array(
			'user_id' => $user_id,
			'action' => $action,
			'title' => $title,
			'url' => $url,
			'created_on' => date('Y-m-d H:i:s', time())
		);

		if($item > 0) return FrontendModel::getDB(true)->update('profiles_activity', $values, 'id = ?', (int) $item);
		else return FrontendModel::getDB(true)->insert('profiles_activity', $values);
	}

	/**
	 * Reports a comment as inappropriate
	 * 
	 * @param int $id The id of the comment
	 * @return int
	 */
	public static function reportComment($id)
	{
		return (int) FrontendModel::getDB(true)->update('profiles_activity_comments', array('status' => 'marked'), 'id = ?', (int) $id);
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
			 OR display_name LIKE ?
			 LIMIT 8', 
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
