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
		echo $view->render();
	}
	
}