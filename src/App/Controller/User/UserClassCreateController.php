<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use App\Entity\LearningSheet;
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
		
		$this->addRouteToBreadcrumb('user_class_list');
		$this->addRouteToBreadcrumb('user_class_new');
		$this->setPageTitle(t('user_class_new'));
		
		try {
			if( $request->hasData('submitCreate') ) {
				$classInput = $request->getData('class');
				if( !empty($classInput['name']) && !empty($classInput['level']) && !empty($classInput['learning_sheet_id']) && $classInput['learning_sheet_id'] === 'new' ) {
					$classInput['learning_sheet_id'] = LearningSheet::make($classInput);
				}
				$classInput['teacher_id'] = User::getLoggedUser()->getPerson()->id();
				$classId = SchoolClass::create($classInput, ['name', 'year', 'level', 'openDate', 'teacher_id', 'learning_sheet_id']);
				
				return new RedirectHTTPResponse(u('user_class_edit', ['classId' => $classId]));
			}
		} catch( UserException $e ) {
			reportError($e);
		}
		
		return $this->renderHTML('class/class_create');
	}
	
}
