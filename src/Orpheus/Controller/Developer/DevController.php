<?php

namespace Orpheus\Controller\Developer;

use Orpheus\Controller\Admin\AbstractAdminController;

abstract class DevController extends AbstractAdminController {
	
	public function prepare($request) {
		parent::prepare($request);
		
		$this->addRouteToBreadcrumb(ROUTE_DEV_HOME);
		
		$this->setOption('main_menu', 'developer');
		$this->setOption('main_title', 'devconsole_title');
		$this->setOption('invertedStyle', 0);
	}
	
}
