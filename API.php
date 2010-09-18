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
	
}

?>