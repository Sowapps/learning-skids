<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Entity;

use DateTime;
use Orpheus\EntityDescriptor\PermanentEntity;

/**
 * Class PupilSkill
 *
 * @package App\Entity
 *
 * @property DateTime $create_date
 * @property string $create_ip
 * @property int $create_user_id
 * @property DateTime $update_date
 * @property int $pupil_id
 * @property int $skill_id
 * @property int $learning_sheet_id
 * @property int $active_value_id
 * @property DateTime $date
 * @property string $value Deprecated
 */
class PupilSkill extends PermanentEntity {
	
	protected static string $table = 'pupil-skill';
	
	protected static array $fields = [];
	
	protected static $validator = null;
	
	protected static string $domain = 'class';
	
	public function asArray($model = self::OUTPUT_MODEL_ALL) {
		if( $model === OUTPUT_MODEL_USAGE || $model === OUTPUT_MODEL_EDITION ) {
			$data = parent::asArray(self::OUTPUT_MODEL_MINIMALS);
			$activeValue = $this->getActiveValue();
			$data['activeValue'] = $activeValue ? $activeValue->asArray($model) : null;
			$data['date'] = $activeValue ? d($activeValue->getDate()) : d($this->date);
			$data['value'] = $activeValue ? $activeValue->value : null;
			$data['values'] = listAsArray($this->getValues(), $model);
			
			return $data;
		}
		
		return parent::asArray($model);
	}
	
	public function getPupil(): Person {
		return Person::load($this->pupil_id, false);
	}
	
	public function getLearningSkill(): LearningSkill {
		return LearningSkill::load($this->skill_id, false);
	}
	
	public function getValues(): array {
		return PupilSkillValue::select()
			->where('pupil_skill_id', $this)
			->orderby('id DESC')
			->run();
	}
	
	public function getActiveValue(): ?PupilSkillValue {
		if( !$this->active_value_id ) {
			return null;
		}
		
		return PupilSkillValue::load($this->active_value_id, true);
	}
	
	public function setActiveValue(PupilSkillValue $pupilSkillValue) {
		$this->active_value_id = $pupilSkillValue->id();
		$this->date = $pupilSkillValue->getDate();
		$this->update_date = now();
	}
	
	public function addValue(?string $value, DateTime $dateTime = null): bool {
		$activeValue = $this->getActiveValue();
		if( $activeValue && $activeValue->value === $value ) {
			// Active value is equal
			return false;
		}
		$data = [
			'pupil_skill_id' => $this,
			'value'          => $value,
			'date'           => $dateTime,
		];
		$pupilSkillValue = PupilSkillValue::createAndGet($data);
		if( !$activeValue || $pupilSkillValue->getDate() > $activeValue->getDate() || ($pupilSkillValue->getDate() == $activeValue->getDate() && $pupilSkillValue->id() > $activeValue->id()) ) {
			$this->setActiveValue($pupilSkillValue);
		}
		$this->save();
		
		return true;
	}
	
}

PupilSkill::init();
