<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Entity;

use App\Exception\AlreadyAssignedException;
use DateTime;
use Exception;
use Orpheus\EntityDescriptor\PermanentEntity;
use Orpheus\Exception\UserException;
use Orpheus\SQLRequest\SQLSelectRequest;

/**
 * Class SchoolClass
 *
 * @package App\Entity
 *
 * @property DateTime $create_date
 * @property string $create_ip
 * @property int $create_user_id
 * @property string $name
 * @property string $level
 * @property int $year
 * @property int $teacher_id
 * @property int $learning_sheet_id
 * @property DateTime $openDate
 * @property boolean $enabled
 */
class SchoolClass extends PermanentEntity {
	
	const LEVEL_KID_LOW = 'kid_low';
	const LEVEL_KID_MIDDLE = 'kid_middle';
	const LEVEL_KID_HIGH = 'kid_high';
	
	protected static $table = 'school-class';
	
	protected static $fields = null;
	protected static $validator = null;
	protected static $domain = DOMAIN_CLASS;
	
	public function hasPupilPersons(Person $person): bool {
		return $this->queryPupils()
			->where('pupil_id', $person)
			->exists();
	}
	
	public function queryPupilPersons() {
		return Person::select()
			->join(ClassPupil::class, $pupilAlias, null, 'pupil_id', true)
			->where($pupilAlias . '.class_id', $this)
			->orderby('person.id ASC');
	}
	
	/**
	 * @throws AlreadyAssignedException
	 */
	public function addPupil(Person $person): void {
		$schoolClass = $person->getSchoolClass($this->year);
		if( $schoolClass ) {
			//		if( $this->hasPupilPersons($person) ) {
			throw new AlreadyAssignedException('pupilPersonAlreadyAssigned');
		}
		ClassPupil::create([
			'class_id' => $this->id(),
			'pupil_id' => $person->id(),
		]);
	}
	
	public function queryPupils(): SQLSelectRequest {
		return ClassPupil::select()
			->where('class_id', $this)
			->orderby('id ASC');
	}
	
	public function getLabel() {
		return $this->name;
	}
	
	public function getLearningSheet(): ?LearningSheet {
		return $this->hasLearningSheet() ? LearningSheet::load($this->learning_sheet_id, true) : null;
	}
	
	public function hasLearningSheet(): bool {
		return !!$this->learning_sheet_id;
	}
	
	public function getTeacher(): Person {
		return Person::load($this->teacher_id, false);
	}
	
	public function checkPupilList(array $pupils, array &$outputList, bool $confirmed): ?bool {
		if( !$pupils ) {
			return null;
		}
		$outputList = [];
		$requireValidation = false;
		foreach( $pupils as $pupilIndex => $pupilInput ) {
			if( empty($pupilInput['firstname']) || empty($pupilInput['lastname']) ) {
				continue;
			}
			$pupilOutput = [
				'firstname' => $pupilInput['firstname'],
				'lastname'  => $pupilInput['lastname'],
			];
			if( $confirmed ) {
				if( empty($pupilInput['personId']) ) {
					$status = 'cancel';
				} elseif( $pupilInput['personId'] === 'new' ) {
					$status = 'new';
				} else {
					$status = 'existing';
					$person = Person::load($pupilInput['personId'], true);
					if( !$person ) {
						// Existing was finally not found (removed or hack)
						$status = 'missing';
						$requireValidation = true;
					} else {
						$pupilOutput['person'] = $person;
						// Get class for same year
						$pupilOutput['class'] = $person->getSchoolClass($this->year);
						if( $pupilOutput['class'] ) {
							$status = 'assigned';
						}
					}
				}
				$pupilOutput['status'] = $status;
			} else {
				$persons = Person::getByName($pupilInput['firstname'], $pupilInput['lastname'], Person::ROLE_PUPIL);
				$exactSameClass = false;
				$pupilOutput['persons'] = array_map(function (Person $person) use (&$exactSameClass) {
					$class = $yearClass = $person->getSchoolClass($this->year);
					if( !$class ) {
						$class = $person->querySchoolClasses()->asObject()->run();
					}
					if( $yearClass && $yearClass->equals($this) ) {
						$exactSameClass = true;
					}
					
					// Get last class
					return [$person, $class, !$yearClass];
				}, $persons);
				// Assigned only if the same name is already present in the same class
				// Else multiple pupils could have the same name through multiple classes, and we allow to select another or create new one.
				$pupilOutput['status'] = $exactSameClass ? 'assignedSelf' : ($persons ? 'existing' : 'new');
				if( $persons && !$exactSameClass ) {
					$requireValidation = true;
				}
			}
			$outputList[$pupilIndex] = $pupilOutput;
		}
		
		return $requireValidation;
	}
	
	/**
	 * Add pupil list without any check
	 * Call #checkPupilList before this one to ensure data are rights and require user validation
	 *
	 * @param array $pupils
	 * @param array $outputList
	 * @return string|null
	 * @throws Exception
	 */
	public function addPupilList(array &$pupils) {
		if( !$pupils ) {
			return 0;
		}
		$successCount = 0;
		foreach( $pupils as &$pupilData ) {
			if( $pupilData['status'] === 'cancel' ) {
				continue;
			}
			try {
				if( isset($pupilData['person']) ) {
					$person = $pupilData['person'];
				} else {
					$pupilData['role'] = Person::ROLE_PUPIL;
					$person = Person::createAndGet($pupilData, ['firstname', 'lastname', 'role']);
					$pupilData['personId'] = $person->id();
					$pupilData['person'] = $person;
				}
				$this->addPupil($person);
				$successCount++;
				$status = 'added';
			} catch( UserException $e ) {
				$status = 'error';
				reportError($e);
			}
			$pupilData['status'] = $status;
		}
		
		return $successCount;
	}
	
	public static function onEdit(array &$data, $object) {
		if( !empty($data['learning_sheet_id']) ) {
			$learningSheet = LearningSheet::load($data['learning_sheet_id'], false);
			if( !$learningSheet->enabled ) {
				static::throwException('canNotUseArchivedLearningSheet');
			}
		}
		parent::onEdit($data, $object);
	}
	
	public static function getAllLevels(): array {
		return [self::LEVEL_KID_LOW, self::LEVEL_KID_MIDDLE, self::LEVEL_KID_HIGH];
	}
	
}

SchoolClass::init();
