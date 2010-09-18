<?php

/**
 * Site Users Plugin
 * Controller
 *
 * Author:   Timo Besenreuther
 *           EZdesign.de
 * Created:  2010-09-18
 * Modified: 2010-09-18
 */

class Piwik_SiteUsers_Controller extends Piwik_Controller {
	
	/** The plugin index */
	public function logins() {
		$view = new Piwik_View('SiteUsers/templates/logins.tpl');
		$view->logins = $this->getLoginsTable(true);
		echo $view->render();
	}
	
	/** Logins overview */
	public function getLoginsTable($return=false) {
		$view = new Piwik_ViewDataTable_HtmlTable();
		$view->init($this->pluginName,  __FUNCTION__, 'SiteUsers.getLogins');
		
		$columns = array(
			'username',
			'iduser',
			'total_logins',
			'visits_with_logins',
			'duration'
		);
		
		foreach ($columns as $column) {
	    	$view->setColumnTranslation($column,
	    			Piwik_Translate('SiteUsers_Col_'.$column));
    	}
    	
    	$view->setColumnsToDisplay($columns);
		
		$view->setSortedColumn('total_logins', 'desc');
		$view->disableFooter();
		
		$result = $this->renderView($view, true);
		if ($return) return $result;
		echo $result;
	}
	
}