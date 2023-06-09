<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use App\Entity\LearningSheet;
use App\Entity\LearningSkill;
use App\Entity\PupilSkill;
use App\Entity\SchoolClass;
use App\Entity\User;
use Orpheus\Exception\ForbiddenException;
use Orpheus\Exception\UserException;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpResponse;

/**
 * Controller for whole pupil's sheet of class
 */
class UserClassPupilsSheetController extends AbstractUserController {
	
	use PupilSkillForm;
	
	/**
	 * @param HttpRequest $request The input HTTP request
	 * @return HttpResponse The output HTTP response
	 */
	public function run($request): HttpResponse {
		$class = SchoolClass::load($request->getPathValue('classId'), false);
		$readOnly = $class->isArchived();
		
		if( !User::getLoggedUser()->canClassManage($class) ) {
			throw new ForbiddenException();
		}
		
		$learningSheet = $class->getLearningSheet();
		
		$this->addRouteToBreadcrumb('user_class_list');
		$this->addRouteToBreadcrumb('user_class_edit', t('class_label', DOMAIN_CLASS, $class->getLabel()), ['classId' => $class->id()]);
		$this->addRouteToBreadcrumb('user_class_pupils_sheet', t('user_class_pupils_sheet'), ['classId' => $class->id()]);
		
		$this->consumeSuccess('classPupilsSheet');
		$this->setPageTitle(t('user_class_pupils_sheet') . ' / ' . t('class_label', DOMAIN_CLASS, $class->getLabel()));
		
		$pupils = $class->queryPupilPersons()
			->asObjectList()->run();
		$pupilSkills = $this->getPupilSkills($learningSheet, $pupils);
		
		if( !$readOnly ) {
			try {
				if( $request->hasData('submitUpdateSkills') ) {
					if( !$class->enabled ) {
						throw new ForbiddenException();
					}
					$this->processPupilSkillEdit($request, $learningSheet, $pupilSkills);
					
					return $this->redirectToSelf();
				}
			} catch( UserException $e ) {
				reportError($e);
			}
		}
		
		$pageUrl = $request->getURL();
		
		if( $request->hasParameter('skills') ) {
			/** @var LearningSkill[] $tempSkills */
			/** @var LearningSkill[] $skills */
			$tempSkills = LearningSkill::select()
				->where('id', $request->getParameter('skills', []));
			$selfQueryString = '';
			$skills = [];
			foreach( $tempSkills as $skill ) {
				$skills[] = $skill;
				$selfQueryString .= ($selfQueryString ? '&' : '?') . sprintf('skills[]=%d', $skill->id());
			}
			$this->addRouteToBreadcrumb('user_class_pupils_sheet', t('user_class_pupils_sheet_by_skills'), $pageUrl . $selfQueryString);
			
			return $this->renderHtml('class/class_pupils_sheet_by_skills', [
				'readOnly'      => $readOnly,
				'class'         => $class,
				'learningSheet' => $learningSheet,
				'pupils'        => $pupils,
				'pupilSkills'   => $pupilSkills,
				'skills'        => $skills,
			]);
		}
		
		return $this->renderHtml('class/class_pupils_sheet', [
			'readOnly'      => $readOnly,
			'class'         => $class,
			'learningSheet' => $learningSheet,
			'pupils'        => $pupils,
			'pupilSkills'   => $pupilSkills,
			'pageUrl'       => $pageUrl,
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
