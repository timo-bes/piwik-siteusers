<?php

/**
 * Site Users Plugin
 * Model
 *
 * Author:   Timo Besenreuther
 *           EZdesign.de
 * Created:  2010-09-18
 * Modified: 2010-09-18
 */

class Piwik_SiteUsers_Model {
	
	private static $instance;
	/** Get singleton instance
	 * @return Piwik_SiteUsers_Model */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	/** Get login table name */
	public static function loginTable() {
		return Piwik_Common::prefixTable('log_siteusers_logins');
	}
	
	/** Log a login */
	public function logLogin($userName, $idsite, $idvisit) {
		if (!$this->userLoggedIn($userName, $idvisit)) {
			$bind = array(
				':userName' => $userName,
				':idsite' => intval($idsite),
				':idvisit' => intval($idvisit)
			);
			$sql = '
				INSERT INTO
					'.self::loginTable().'
					(idsite, idvisit, username, duration, date, datetime_login)
				VALUES
					(:idsite, :idvisit, :userName, 0, CURRENT_DATE(), NOW())
			';
			Piwik_Query($sql, $bind);
			return true;
		}
		return false;
	}
	
	/** Log a logout */
	public function logLogout($userName, $idvisit) {
		$bind = array(
			':userName' => $userName,
			':idvisit' => intval($idvisit)
		);
		$sql = '
			UPDATE
				'.self::loginTable().'
			SET
				datetime_logout = NOW(),
				duration = TIMESTAMPDIFF(MINUTE, datetime_login, NOW())
			WHERE
				username = :userName AND
				idvisit = :idvisit AND
				datetime_logout = "0000-00-00 00:00:00"
		';
		return Piwik_Query($sql, $bind);
	}
	
	/** Check whether the user is already logged in */
	private function userLoggedIn($userName, $idvisit) {
		$bind = array(
			':userName' => $userName,
			':idvisit' => intval($idvisit)
		);
		$sql = '
			SELECT
				idlogin
			FROM
				'.self::loginTable().'
			WHERE
				username = :userName AND
				idvisit = :idvisit AND
				datetime_logout = "0000-00-00 00:00:00"
		';
		return Piwik_FetchOne($sql, $bind);
	}
	
}