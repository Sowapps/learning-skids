<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use App\Entity\LearningSheet;
use App\Entity\SchoolClass;
use App\Entity\User;
use Orpheus\Exception\UserException;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpResponse;
use Orpheus\InputController\HttpController\RedirectHttpResponse;

class UserClassCreateController extends AbstractUserController {
	
	/**
	 * @param HttpRequest $request The input HTTP request
	 * @return HttpResponse The output HTTP response
	 */
	public function run($request): HttpResponse {
		
		$this->addRouteToBreadcrumb('user_class_list');
		$this->addRouteToBreadcrumb('user_class_new');
		$this->setPageTitle(t('user_class_new'));
		
		try {
			if( $request->hasData('submitCreate') ) {
				$classInput = $request->getData('class');
				$classFields = ['name', 'year', 'level', 'open_date', 'teacher_id'];
				SchoolClass::checkUserInput($classInput, $classFields);
				$classFields[] = 'learning_sheet_id';
				$learningSheet = null;
				if( !empty($classInput['name']) && !empty($classInput['level']) ) {
					$learningSheet = LearningSheet::make($classInput);
				}
				$classInput['teacher_id'] = User::getLoggedUser()->getPerson()->id();
				$classInput['learning_sheet_id'] = $learningSheet;
				$classId = SchoolClass::create($classInput, $classFields);
				
				return new RedirectHttpResponse(u('user_class_edit', ['classId' => $classId]));
			}
		} catch( UserException $e ) {
			reportError($e);
		}
		
		return $this->renderHtml('class/class_create');
	}
	
}
