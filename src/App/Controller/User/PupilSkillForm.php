<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;


use App\Entity\LearningSheet;
use App\Entity\Person;
use App\Entity\PupilSkill;
use App\Entity\PupilSkillValue;
use Orpheus\Exception\UserException;
use Orpheus\InputController\InputRequest;

trait PupilSkillForm {
	
	public function processPupilSkillEdit(InputRequest $request, LearningSheet $learningSheet, array $pupilSkills, ?Person $pupilPerson = null) {
		$newPupilSkills = $request->getArrayData('pupilSkill');
		$created = 0;
		$updated = 0;
		$removed = 0;
		$isMultiPerson = !$pupilPerson;
		foreach( $newPupilSkills as $newPupilSkill ) {
			if( empty($newPupilSkill['status']) || empty($newPupilSkill['skill_id']) ) {
				continue;
			}
			if( $isMultiPerson ) {
				if( empty($newPupilSkill['person_id']) ) {
					continue;
				}
				$pupilPerson = Person::load($newPupilSkill['person_id'], true);
				if( !$pupilPerson || $pupilPerson->role != Person::ROLE_PUPIL ) {
					continue;
				}
			}
			try {
				$currentPupilSkill = $isMultiPerson ? ($pupilSkills[$pupilPerson->id()][$newPupilSkill['skill_id']] ?? null) : ($pupilSkills[$newPupilSkill['skill_id']] ?? null);
				if( $newPupilSkill['status'] === 'new' ) {
					if( $currentPupilSkill ) {
						// Should be new but there is one existing
						continue;
					}
					// Create new pupil Skill
					$pupilSkill = PupilSkill::createAndGet([
						'pupil_id'          => $pupilPerson,
						'skill_id'          => $newPupilSkill['skill_id'],
						'learning_sheet_id' => $learningSheet,
					]);
					// Create new pupil skill Value
					if( !empty($newPupilSkill['value']) ) {
						$this->addValueToPupilSkill($pupilSkill, $newPupilSkill['value']);
					}
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
					$this->addValueToPupilSkill($currentPupilSkill, $newPupilSkill['value']);
					$updated++;
				}
			} catch( UserException $e ) {
				reportError($e);
			}
		}
		endReportStream();
		if( $isMultiPerson ) {
			$this->storeSuccess('classSkillsUpdate', 'successClassSkillEdit',
				['changes' => $created + $updated + $removed], DOMAIN_CLASS);
		} else {
			$this->storeSuccess('pupilSkillsUpdate', 'successClassPupilSkillEdit',
				['name' => $pupilPerson->getLabel(), 'created' => $created, 'updated' => $updated], DOMAIN_CLASS);
		}
	}
	
	public function addValueToPupilSkill(PupilSkill $pupilSkill, ?string $value) {
		$activeValue = $pupilSkill->getActiveValue();
		if( $activeValue && $activeValue->value === $value ) {
			// Active value is equal
			return;
		}
		$pupilSkillValue = PupilSkillValue::createAndGet([
			'pupil_skill_id' => $pupilSkill,
			'value'          => $value,
		]);
		$pupilSkill->setActiveValue($pupilSkillValue);
		$pupilSkill->save();
	}
	
}
