<?php

/**
 * Site Users Plugin
 * API
 *
 * Author:   Timo Besenreuther
 *           EZdesign.de
 * Created:  2010-09-18
 * Modified: 2010-09-18
 */

class Piwik_SiteUsers_API {
	
	// singleton instance
	static private $instance = null;
	
	/** Get singleton instance
	 * @return Piwik_SiteUsers_API */
	static public function getInstance() {
		if (self::$instance == null) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	/** Get login statistics
	 * @return Piwik_DataTable */
	public function getLogins($idSite, $period, $date) {
		return Piwik_SiteUsers_Archive::getDataTable(
				'logins', $idSite, $period, $date);
	}
	
}

?>