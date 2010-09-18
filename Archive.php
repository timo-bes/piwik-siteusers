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
	
	/** Log user action */
	public static function log($userName, $action, $idsite, $idvisit) {
		$model = Piwik_SiteUsers_Model::getInstance();
		
		switch ($action) {
		case 'login':
			$model->logLogin($userName, $idsite, $idvisit);
			break;
		case 'logout':
			$model->logLogout($userName, $idvisit);
			break;
		}
	}
	
	/** Build archive for a single day */
	public static function archiveDay(Piwik_ArchiveProcessing $archive) {
		
	}
	
	/** Build archive for a period */
	public static function archivePeriod(Piwik_ArchiveProcessing $archive) {
		
	}
	
}