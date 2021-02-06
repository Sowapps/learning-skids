<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Entity;

use DateTime;
use Orpheus\EntityDescriptor\PermanentEntity;
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
	
	public function addPupil(Person $person): void {
		$exists = $this->queryPupils()
			->where('pupil_id', $person)
			->exists();
		if( $exists ) {
			self::throwException('pupilPersonAlreadyAssigned');
		}
		ClassPupil::create([
			'class_id' => $this->id(),
			'pupil_id' => $person->id(),
		]);
	}
	
	public function queryPupils(): SQLSelectRequest {
		return ClassPupil::get()
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
	
	public static function onEdit(array &$data, $object) {
		if( !empty($data['learning_sheet_id']) ) {
			$learningSheet = LearningSheet::load($data['learning_sheet_id'], false);
			if( !$learningSheet->enabled ) {
				static::throwException('canNotUseArchivedLearningSheet');
			}
		}
		parent::onEdit($data, $object);
	}
	
	public static function getAllLevels() {
		return [self::LEVEL_KID_LOW, self::LEVEL_KID_MIDDLE, self::LEVEL_KID_HIGH];
	}
	
}

SchoolClass::init();
