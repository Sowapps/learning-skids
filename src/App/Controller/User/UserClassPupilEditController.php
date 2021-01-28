<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use App\Entity\ClassPupil;
use App\Entity\PupilSkill;
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
		
		try {
			if( $request->hasData('submitUpdate') ) {
				$person->update($request->getArrayData('person'), ['firstname', 'lastname']);
				
				$this->storeSuccess('pupilEdit', 'successClassPupilEdit', ['name' => $person->getLabel()], DOMAIN_CLASS);
				
				return $this->redirectToSelf();
				
			} elseif( $request->hasData('submitUpdateSkills') ) {
				startReportStream('pupilSkillsUpdate');
				$newPupilSkills = $request->getArrayData('pupilSkill');
				$created = 0;
				$updated = 0;
				$removed = 0;
				foreach( $newPupilSkills as $newPupilSkill ) {
					if( empty($newPupilSkill['status']) || empty($newPupilSkill['skill_id']) ) {
						continue;
					}
					try {
						$currentPupilSkill = $pupilSkills[$newPupilSkill['skill_id']] ?? null;
						if( $newPupilSkill['status'] === 'new' ) {
							if( $currentPupilSkill ) {
								// Should be new but there is one existing
								continue;
							}
							// Create new pupil skill
							PupilSkill::create([
								'pupil_id'          => $person,
								'skill_id'          => $newPupilSkill['skill_id'],
								'learning_sheet_id' => $learningSheet,
								'value'             => $newPupilSkill['value'] ?? null,
							]);
							$created++;
							
						} elseif( $newPupilSkill['status'] === 'remove' ) {
							// Remove existing pupil skill
							if( !$currentPupilSkill ) {
								// Should be existing, may have already been removed by another request
								continue;
							}
							$currentPupilSkill->remove();
							$removed++;
							
						} else {
							// Update existing pupil skill
							if( !$currentPupilSkill ) {
								// Should be existing, may have been removed by another request
								continue;
							}
							$currentPupilSkill->update($newPupilSkill, ['value']);
							$updated++;
						}
					} catch( UserException $e ) {
						reportError($e);
					}
				}
				endReportStream();
				$this->storeSuccess('pupilSkillsUpdate', 'successClassPupilSkillEdit',
					['name' => $person->getLabel(), 'created' => $created, 'updated' => $updated], DOMAIN_CLASS);
				
				return $this->redirectToSelf();
			}
		} catch( UserException $e ) {
			reportError($e);
		}
		
		return $this->renderHTML('user/user_class_pupil_edit', [
			'class'       => $class,
			'pupil'       => $pupil,
			'person'      => $person,
			'pupilSkills' => $pupilSkills,
		]);
	}
	
}
