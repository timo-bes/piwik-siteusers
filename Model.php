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
	
	/** Get current time */
	private static function now() {
		$date = Piwik_Date::factory('now');
		return $date->getDatetime();
	}
	
	/** Log a login */
	public function logLogin($userName, $idsite, $idvisit) {
		if (!$this->userLoggedIn($userName, $idvisit)) {
			$this->logOutCurrentUser($idvisit);
			$bind = array(
				':userName' => $userName,
				':idsite' => intval($idsite),
				':idvisit' => intval($idvisit),
				':now' => self::now()
			);
			$sql = '
				INSERT INTO
					'.self::loginTable().'
					(idsite, idvisit, username, duration, date, datetime_login)
				VALUES
					(:idsite, :idvisit, :userName, 0, :now, :now)
			';
			Piwik_Query($sql, $bind);
			return true;
		}
		return false;
	}
	
	/** Log out the current user for a visit */
	private function logOutCurrentUser($idvisit) {
		$bind = array(':idvisit' => $idvisit);
		$sql = '
			SELECT
				username
			FROM
				'.self::loginTable().' AS login
			WHERE
				idvisit = :idvisit AND
				datetime_logout = "0000-00-00 00:00:00"
		';
		$username = Piwik_FetchOne($sql, $bind);
		if ($username) {
			$this->logLogout($username, $idvisit);
		}
	}
	
	/** Log a logout */
	public function logLogout($userName, $idvisit) {
		$bind = array(
			':userName' => $userName,
			':idvisit' => intval($idvisit),
			':now' => self::now()
		);
		$sql = '
			UPDATE
				'.self::loginTable().' AS login
			SET
				datetime_logout = :now,
				duration = TIMESTAMPDIFF(MINUTE, datetime_login, :now)
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
				'.self::loginTable().' AS login
			WHERE
				username = :userName AND
				idvisit = :idvisit AND
				datetime_logout = "0000-00-00 00:00:00"
		';
		return Piwik_FetchOne($sql, $bind);
	}
	
	/** Analyze logins for a day */
	public function dayAnalyzeLogins($idsite, $startDate, $endDate) {
		$bind = array(
			':idsite' => $idsite,
			':startDate' => $startDate,
			':endDate' => $endDate
		);
		$sql = '
			SELECT
				COUNT(DISTINCT idvisit) AS visits_with_logins,
				COUNT(idlogin) AS total_logins,
				username AS label,
				SUM(
					CASE
						WHEN datetime_logout != "0000-00-00 00:00:00" THEN duration
						ELSE (
							SELECT
								TIMESTAMPDIFF(MINUTE, login.datetime_login,
										visit.visit_last_action_time) AS duration
							FROM
								'.Piwik_Common::prefixTable('log_visit').' AS visit
							WHERE
								visit.idvisit = login.idvisit
						)
					END
				) AS duration
			FROM
				'.self::loginTable().' AS login
			WHERE
				idsite = :idsite AND
				datetime_login > :startDate AND
				datetime_login < :endDate
			GROUP BY
				username
		';
		return $data = Piwik_FetchAll($sql, $bind);
	}
	
}