<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use App\Entity\LearningSheet;
use App\Entity\PupilSkill;
use App\Entity\SchoolClass;
use App\Entity\User;
use Orpheus\Exception\ForbiddenException;
use Orpheus\Exception\UserException;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPResponse;

class UserClassPupilsSheetController extends AbstractUserController {
	
	use PupilSkillForm;
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 */
	public function run($request) {
		$class = SchoolClass::load($request->getPathValue('classId'), false);
		
		if( !User::getLoggedUser()->canClassManage($class) ) {
			throw new ForbiddenException();
		}
		
		$learningSheet = $class->getLearningSheet();
		
		$this->addRouteToBreadcrumb('user_class_list');
		$this->addRouteToBreadcrumb('user_class_edit', t('class_label', DOMAIN_CLASS, $class->getLabel()), ['classId' => $class->id()]);
		$this->addRouteToBreadcrumb('user_class_pupils_sheet', t('user_class_pupils_sheet'), ['classId' => $class->id()]);
		
		$this->consumeSuccess('classPupilsSheet');
		$this->setPageTitle(t('user_class_pupils_sheet') . ' / ' . t('class_label', DOMAIN_CLASS, $class->getLabel()));
		//		$this->setContentTitle($class);
		
		$pupils = $class->queryPupilPersons()
			->asObjectList()->run();
		$pupilSkills = $this->getPupilSkills($learningSheet, $pupils);
		
		try {
			if( $request->hasData('submitUpdateSkills') ) {
				$this->processPupilSkillEdit($request, $learningSheet, $pupilSkills);
				
				return $this->redirectToSelf();
			}
		} catch( UserException $e ) {
			reportError($e);
		}
		
		return $this->renderHTML('class/class_pupils_sheet', [
			'class'         => $class,
			'learningSheet' => $learningSheet,
			'pupils'        => $pupils,
			'pupilSkills'   => $pupilSkills,
		]);
	}
	
	/**
	 * @param LearningSheet $learningSheet
	 * @param array $pupils
	 * @return array
	 */
	public function getPupilSkills(LearningSheet $learningSheet, array $pupils): array {
		if( !$pupils ) {
			// No pupil in class yet
			return [];
		}
		// Query pupils' skills for this learning sheet
		$pupilSkillQuery = PupilSkill::select()
			->where('pupil_id', $pupils)
			->where('learning_sheet_id', $learningSheet);
		// Build array grouping all skills by pupil
		$pupilSkills = [];
		/** @var PupilSkill $pupilSkill */
		foreach( $pupilSkillQuery as $pupilSkill ) {
			if( !array_key_exists($pupilSkill->pupil_id, $pupilSkills) ) {
				$pupilSkills[$pupilSkill->pupil_id] = [];
			}
			$pupilSkills[$pupilSkill->pupil_id][$pupilSkill->skill_id] = $pupilSkill;
		}
		
		return $pupilSkills;
	}
	
}
