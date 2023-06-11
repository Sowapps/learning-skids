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
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpResponse;

/**
 * Controller for one pupil in class
 */
class UserClassPupilController extends AbstractUserController {
	
	use PupilSkillForm;
	
	/**
	 * @param HttpRequest $request The input HTTP request
	 * @return HttpResponse The output HTTP response
	 */
	public function run($request): HttpResponse {
		$class = SchoolClass::load($request->getPathValue('classId'), false);
		$pupil = ClassPupil::load($request->getPathValue('pupilId'), false);
		$readOnly = $class->isArchived() || $this->getOption('readonly', false);
		$person = $pupil->getPerson();
		$learningSheet = $class->getLearningSheet();
		
		if( !$pupil->getSchoolClass()->equals($class) ) {
			throw new NotFoundException();
		}
		if( !User::getLoggedUser()->canClassManage($class) ) {
			throw new ForbiddenException();
		}
		
		$this->addRouteToBreadcrumb('user_class_list');
		$this->addRouteToBreadcrumb('user_class_edit', t('class_label', DOMAIN_CLASS, $class->getLabel()), ['classId' => $class->id()]);
		$this->addRouteToBreadcrumb('user_class_pupil_edit', t('pupil_label', DOMAIN_CLASS, $person->getLabel()), ['classId' => $class->id(), 'pupilId' => $pupil->id()]);
		$this->setPageTitle(t('pupil_label', DOMAIN_CLASS, $person->getLabel()));
		$this->setContentTitle($person);
		$this->consumeSuccess('pupilEdit');
		$this->consumeSuccess('pupilSkillsUpdate', 'pupilSkillsUpdate');
		
		$pupilSkills = $person->getPupilSkills($learningSheet);
		
		if( !$readOnly ) {
			try {
				if( $request->hasData('submitUpdate') ) {
					$person->update($request->getArrayData('person'), ['firstname', 'lastname']);
					
					$this->storeSuccess('pupilEdit', 'successClassPupilEdit', ['name' => $person->getLabel()], DOMAIN_CLASS);
					
					return $this->redirectToSelf();
					
				} elseif( $request->hasData('submitNotePublicSave') ) {
					$pupil->update($request->getArrayData('pupil'), ['note_public']);
					
					$this->storeSuccess('pupilEdit', 'notePublic_success', [], DOMAIN_CLASS);
					
					return $this->redirectToSelf();
					
				} elseif( $request->hasData('submitNotePrivateSave') ) {
					$pupil->update($request->getArrayData('pupil'), ['note_private']);
					
					$this->storeSuccess('pupilEdit', 'notePrivate_success', [], DOMAIN_CLASS);
					
					return $this->redirectToSelf();
					
				} elseif( $request->hasData('submitUpdateSkills') ) {
					startReportStream('pupilSkillsUpdate');
					
					$this->processPupilSkillEdit($request, $learningSheet, $pupilSkills, $person);
					
					return $this->redirectToSelf();
				}
			} catch( UserException $e ) {
				reportError($e);
			}
		}
		$classPupils = $person->queryClassPupils();
		
		return $this->renderHtml('class/class_pupil_edit', [
			'readOnly'    => $readOnly,
			'class'       => $class,
			'pupil'       => $pupil,
			'person'      => $person,
			'pupilSkills' => $pupilSkills,
			'classPupils' => $classPupils,
		]);
	}
	
}
