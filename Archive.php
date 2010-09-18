<?php

/**
 * Site Users Plugin
 * Archive
 *
 * Author:   Timo Besenreuther
 *           EZdesign.de
 * Created:  2010-09-18
 * Modified: 2010-09-18
 */

class Piwik_SiteUsers_Archive {
	
	private static $instance;
	/** Get singleton instance
	 * @return Piwik_SiteUsers_Archive */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	/** Build archive for a single day */
	public static function archiveDay(Piwik_ArchiveProcessing $archive) {
		
	}
	
	/** Build archive for a period */
	public static function archivePeriod(Piwik_ArchiveProcessing $archive) {
		
	}
	
}