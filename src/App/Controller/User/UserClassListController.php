<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use App\Entity\User;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPResponse;

class UserClassListController extends AbstractUserController {
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 */
	public function run($request) {
		
		$this->addRouteToBreadcrumb('user_class_list');
		
		$classes = User::getLoggedUser()->getPerson()->queryClasses();
		
		return $this->renderHTML('user/user_class_list', [
			'classes' => $classes,
		]);
	}
	
}
