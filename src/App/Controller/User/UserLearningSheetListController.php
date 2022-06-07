<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use App\Entity\LearningSheet;
use App\Entity\LearningSheetUser;
use App\Entity\User;
use Orpheus\Exception\UserException;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpResponse;

class UserLearningSheetListController extends AbstractUserController {
	
	/**
	 * @param HttpRequest $request The input HTTP request
	 * @return HttpResponse The output HTTP response
	 */
	public function run($request): HttpResponse {
		
		$this->addRouteToBreadcrumb('user_learning_sheet_list');
		$this->setPageTitle(t('user_learning_sheet_list'));
		
		// Forms
		try {
			if( $request->hasData('submitCreate') ) {
				$learningSheet = LearningSheet::make($request->getArrayData('learningSheet'));
				reportSuccess(t('learningSheetCreated', DOMAIN_LEARNING_SKILL, $learningSheet));
			}
		} catch( UserException $e ) {
			reportError($e);
		}
		
		$learningSheets = User::getLoggedUser()->queryLearningSheets(LearningSheetUser::ROLE_ADMIN);
		
		return $this->renderHtml('user/user_learning_sheet_list', [
			'learningSheets' => $learningSheets,
		]);
	}
	
}
