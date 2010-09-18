<?php

/**
 * Site Users Plugin
 *
 * Author:   Timo Besenreuther
 *           EZdesign.de
 * Created:  2010-09-18
 * Modified: 2010-09-18
 */

class Piwik_SiteUsers extends Piwik_Plugin {

	/** Information about this plugin */
	public function getInformation() {
		return array(
			'description' => Piwik_Translate('SiteUsers_PluginDescription'),
			'author' => 'Timo Besenreuther, EZdesign',
			'author_homepage' => 'http://www.ezdesign.de/',
			'version' => '0.0.2',
			'translationAvailable' => true,
			'TrackerPlugin' => true
		);
	}
	
	/** Install the plugin */
	public function install() {
		try {
			Zend_Registry::get('db')->query('
				CREATE TABLE `'.Piwik_SiteUsers_Model::loginTable().'` (
					`idlogin` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`idsite` INT NOT NULL,
					`idvisit` INT NOT NULL,
					`iduser` VARCHAR(64) NOT NULL,
					`username` VARCHAR(255) NOT NULL,
					`duration` SMALLINT(5) NOT NULL,
					`date` DATE NOT NULL,
					`datetime_login` DATETIME NOT NULL,
					`datetime_logout` DATETIME NOT NULL
				) ENGINE = MYISAM
			');
		} catch (Exception $e) {}
	}
	
	/** Uninstall the plugin */
	public function uninstall() {
		try {
			Zend_Registry::get('db')->query('
				DROP TABLE `'.Piwik_SiteUsers_Model::loginTable().'`;
			');
		} catch (Exception $e) {}
	}
	
	/** Register Hooks */
	public function getListHooksRegistered() {
        $hooks = array(
        	'Menu.add' => 'addMenu',
			'Tracker.Action.record' => 'log',
            'ArchiveProcessing_Day.compute' => 'archiveDay',
        	'ArchiveProcessing_Period.compute' => 'archivePeriod'
        );
        return $hooks;
    }
    
    /** Menu hook */
	public function addMenu() {
		Piwik_AddMenu('General_Visitors', 'SiteUsers_Logins',
				array('module' => 'SiteUsers', 'action' => 'logins'));
	}
	
	/** Build archive for a day */
    public function archiveDay($notification) {
		$archiveProcessing = $notification->getNotificationObject();
		Piwik_SiteUsers_Archive::archiveDay($archiveProcessing);
	}
	
	/** Build archive for a period */
	public function archivePeriod($notification) {
		$archiveProcessing = $notification->getNotificationObject();
		Piwik_SiteUsers_Archive::archivePeriod($archiveProcessing);
	}
	
	/** Logger hook */
	public function log($notification) {
		$data = Piwik_Common::getRequestVar('data', '');
		$data = html_entity_decode($data);
		$data = json_decode($data, true);
		
		if (!isset($data['SiteUsers_Action'])) {
			return false;
		}
		
		$action = $notification->getNotificationObject();
		$idaction = $action->getIdActionUrl();
		
		$info = $notification->getNotificationInfo();
		$idsite = $info['idSite'];
		$idvisit = $info['idVisit'];
		
		include_once(dirname(__FILE__).'/Model.php');
		include_once(dirname(dirname(dirname(__FILE__))).'/core/Date.php');
		$model = Piwik_SiteUsers_Model::getInstance();
		
		$logAction = $data['SiteUsers_Action'];
		
		if ($logAction == 'logout') {
			return $model->logLogout($idvisit);
		}
		
		if (!isset($data['SiteUsers_UserID']) || !isset($data['SiteUsers_UserName'])) {
			return false;
		}
		
		$iduser = $data['SiteUsers_UserID'];
		$userName = $data['SiteUsers_UserName'];
		
		if ($logAction == 'login') {
			$model->logLogin($iduser, $userName, $idsite, $idvisit);
		}
	}
	
}