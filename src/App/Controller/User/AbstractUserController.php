<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use Orpheus\Controller\Admin\AbstractAdminController;

abstract class AbstractUserController extends AbstractAdminController {
	
	protected string $scope = self::SCOPE_MEMBER;
	
	public function prepare($request) {
		parent::prepare($request);
		
		$this->addRouteToBreadcrumb('user_home');
		$this->setOption('mainmenu', 'user_menu');
	}
	
}
