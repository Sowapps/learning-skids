<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use App\Entity\SchoolClass;
use App\Entity\User;
use Orpheus\Exception\ForbiddenException;
use Orpheus\Exception\NotFoundException;
use Orpheus\Exception\UserException;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpResponse;

class UserClassAddPupilsController extends AbstractUserController {
	
	/**
	 * @param HttpRequest $request The input HTTP request
	 * @return HttpResponse The output HTTP response
	 */
	public function run($request): HttpResponse {
		$class = SchoolClass::load($request->getPathValue('classId'), false);
		$token = $request->getPathValue('token');
		$pupilValidation = $_SESSION['class_add_pupils'][$token] ?? null;
		
		if( !User::getLoggedUser()->canClassManage($class) ) {
			throw new ForbiddenException();
		}
		if( !$pupilValidation || $pupilValidation->classId !== $class->id() ) {
			throw new NotFoundException();
		}
		
		$pupils = $pupilValidation->pupils;
		
		$this->addRouteToBreadcrumb('user_class_list');
		$this->addRouteToBreadcrumb('user_class_edit', t('class_label', DOMAIN_CLASS, $class->getLabel()), ['classId' => $class->id()]);
		$this->addThisToBreadcrumb();
		
		$this->setPageTitle(t('class_label', DOMAIN_CLASS, $class->getLabel()));
		$this->setContentTitle($class);
		
		$outputPupils = [];
		try {
			if( $request->hasData('submitValidate') ) {
				$requireValidation = $class->checkPupilList($request->getData('pupil'), $outputPupils, true);
				if( !$requireValidation ) {
					$class->addPupilList($outputPupils);
				}
			} else {
				$class->checkPupilList($pupils, $outputPupils, false);
			}
		} catch( UserException $e ) {
			reportError($e);
		}
		
		return $this->renderHTML('class/class_add_pupils', [
			'token'  => $token,
			'class'  => $class,
			'pupils' => $outputPupils,
		]);
	}
	
}
