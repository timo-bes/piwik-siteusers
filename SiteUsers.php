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
			'version' => '0.0.0',
			'translationAvailable' => true,
			'TrackerPlugin' => true
		);
	}
	
	/** Install the plugin */
	public function install() {
		$query = '';
		try {
			Zend_Registry::get('db')->query($query);
		} catch (Exception $e) {}
	}
	
	/** Uninstall the plugin */
	public function uninstall() {
		
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
		Piwik_AddMenu('Visitors_Visitors', 'SiteUsers_Logins',
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
		$action = $notification->getNotificationObject();
		
		$data = Piwik_Common::getRequestVar('data', '');
		$data = html_entity_decode($data);
		$data = json_decode($data, true);
	}
	
}