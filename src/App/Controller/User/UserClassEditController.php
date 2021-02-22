<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use App\Entity\ClassPupil;
use App\Entity\LearningSheet;
use App\Entity\Person;
use App\Entity\SchoolClass;
use App\Entity\User;
use Orpheus\Exception\ForbiddenException;
use Orpheus\Exception\UserException;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPResponse;
use Orpheus\InputController\HTTPController\RedirectHTTPResponse;

class UserClassEditController extends AbstractUserController {
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 */
	public function run($request) {
		$class = SchoolClass::load($request->getPathValue('classId'), false);
		
		if( !User::getLoggedUser()->canClassManage($class) ) {
			throw new ForbiddenException();
		}
		
		$this->addRouteToBreadcrumb('user_class_list');
		$this->addRouteToBreadcrumb('user_class_edit', t('class_label', DOMAIN_CLASS, $class->getLabel()), ['classId' => $class->id()]);
		
		$this->consumeSuccess('classEdit');
		$this->consumeSuccess('pupilList', 'pupilList');
		$this->setPageTitle(t('class_label', DOMAIN_CLASS, $class->getLabel()));
		$this->setContentTitle($class);
		
		try {
			if( $request->hasData('submitUpdate') ) {
				$classInput = $request->getData('class');
				if( !empty($classInput['name']) && !empty($classInput['level']) && !empty($classInput['learning_sheet_id']) && $classInput['learning_sheet_id'] === 'new' ) {
					$classInput['learning_sheet_id'] = LearningSheet::make($classInput);
				}
				$class->update($classInput, ['name', 'year', 'level', 'openDate', 'learning_sheet_id']);
				
				$this->storeSuccess('classEdit', 'successClassEdit', ['name' => $class->getLabel()], DOMAIN_CLASS);
				
				return new RedirectHTTPResponse(u('user_class_edit', ['classId' => $class->id()]));
				
			} elseif( $request->hasData('submitAddMultiplePupils') ) {
				startReportStream('pupilList');
				$pupilListInput = $request->getData('pupil');
				foreach( $pupilListInput as $pupilInput ) {
					try {
						if( empty($pupilInput['firstname']) || empty($pupilInput['lastname']) ) {
							continue;
						}
						$pupilInput['role'] = Person::ROLE_PUPIL;
						$person = Person::createAndGet($pupilInput, ['firstname', 'lastname', 'role']);
						$class->addPupil($person);
						reportSuccess(t('successNewPupil', DOMAIN_CLASS, ['pupil' => $person->getLabel()]));
					} catch( UserException $e ) {
						reportError($e);
					}
				}
				endReportStream();
				
			} elseif( $request->hasData('submitRemovePupil') ) {
				startReportStream('pupilList');
				$pupil = ClassPupil::load($request->getData('submitRemovePupil'), false);
				if( !$pupil->getSchoolClass()->equals($class) ) {
					throw new ForbiddenException();
				}
				$pupil->remove();
				endReportStream();
				
				//			} elseif( $request->hasData('submitUpdatePupil') ) {
				//				startReportStream('pupilList');
				//				$pupil = ClassPupil::load($request->getData('pupilId'), false);
				//				if( !$pupil->getSchoolClass()->equals($class) ) {
				//					throw new ForbiddenException();
				//				}
				//				$person = $pupil->getPerson();
				//				$person->update($request->getArrayData('person'), ['firstname', 'lastname']);
				//				endReportStream();
				//
				//				$this->storeSuccess('pupilList', 'successClassPupilEdit', ['name' => $class->getLabel()], DOMAIN_CLASS);
				//				return new RedirectHTTPResponse(u('user_class_edit', ['classId' => $class->id()]));
			}
		} catch( UserException $e ) {
			reportError($e);
		}
		
		return $this->renderHTML('class/class_edit', [
			'class' => $class,
		]);
	}
	
}
