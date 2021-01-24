<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use App\Entity\ClassPupil;
use App\Entity\SchoolClass;
use App\Entity\User;
use Orpheus\Exception\ForbiddenException;
use Orpheus\Exception\NotFoundException;
use Orpheus\Exception\UserException;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPResponse;

class UserClassPupilEditController extends AbstractUserController {
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 */
	public function run($request) {
		$class = SchoolClass::load($request->getPathValue('classId'), false);
		$pupil = ClassPupil::load($request->getPathValue('pupilId'), false);
		$person = $pupil->getPerson();
		
		if( !$pupil->getSchoolClass()->equals($class) ) {
			throw new NotFoundException();
		}
		if( !User::getLoggedUser()->canClassManage($class) ) {
			throw new ForbiddenException();
		}
		
		$this->addRouteToBreadcrumb('user_class_list');
		$this->addRouteToBreadcrumb('user_class_edit', t('class_label', DOMAIN_CLASS, $class->getLabel()), ['classId' => $class->id()]);
		$this->addRouteToBreadcrumb('user_class_pupil_edit', t('pupil_label', DOMAIN_CLASS, $person->getLabel()), ['classId' => $class->id(), 'pupilId' => $pupil->id()]);
		$this->setContentTitle($person);
		$this->consumeSuccess('pupilEdit');
		
		try {
			if( $request->hasData('submitUpdate') ) {
				$person->update($request->getArrayData('person'), ['firstname', 'lastname']);
				
				$this->storeSuccess('pupilEdit', 'successClassPupilEdit', ['name' => $person->getLabel()], DOMAIN_CLASS);
				
				return $this->redirectToSelf();
			}
		} catch( UserException $e ) {
			reportError($e);
		}
		
		return $this->renderHTML('user/user_class_pupil_edit', [
			'class'  => $class,
			'pupil'  => $pupil,
			'person' => $person,
		]);
	}
	
}
