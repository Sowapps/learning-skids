<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use App\Entity\SchoolClass;
use App\Entity\User;
use Orpheus\Exception\UserException;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPResponse;
use Orpheus\InputController\HTTPController\RedirectHTTPResponse;

class UserClassCreateController extends AbstractUserController {
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 */
	public function run($request) {
		
		try {
			if( $request->hasData('submitCreate') ) {
				$classInput = $request->getData('class');
				$classInput['teacher_id'] = User::getLoggedUser()->getPerson()->id();
				$classId = SchoolClass::create($classInput, ['name', 'year', 'level', 'openDate', 'teacher_id']);
				
				return new RedirectHTTPResponse(u('user_class_edit', ['classId' => $classId]));
			}
		} catch( UserException $e ) {
			reportError($e);
		}
		
		return $this->renderHTML('user/user_class_create');
	}
	
}
