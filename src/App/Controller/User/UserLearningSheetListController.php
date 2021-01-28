<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use App\Entity\LearningSheetUser;
use App\Entity\User;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPResponse;

class UserLearningSheetListController extends AbstractUserController {
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 */
	public function run($request) {
		
		$this->addRouteToBreadcrumb('user_learning_sheet_list');
		$this->setPageTitle(t('user_learning_sheet_list'));
		
		$learningSheets = User::getLoggedUser()->getPerson()->queryLearningSheets(LearningSheetUser::ROLE_ADMIN);
		
		return $this->renderHTML('user/user_learning_sheet_list', [
			'learningSheets' => $learningSheets,
		]);
	}
	
}
