<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Entity;

use DateTime;
use Orpheus\EntityDescriptor\PermanentEntity;

/**
 * Class PupilSkillValue
 *
 * @package App\Entity
 *
 * @property DateTime $create_date
 * @property string $create_ip
 * @property int $create_user_id
 * @property int $pupil_skill_id
 * @property string $value
 */
class PupilSkillValue extends PermanentEntity {
	
	protected static $table = 'pupil-skill-value';
	
	protected static $fields = null;
	protected static $validator = null;
	protected static $domain = 'class';
	
	public function getPupil(): PupilSkill {
		return PupilSkill::load($this->pupil_skill_id, false);
	}
	
	public function asArray($model = self::OUTPUT_MODEL_ALL) {
		if( $model === OUTPUT_MODEL_USAGE || $model === OUTPUT_MODEL_EDITION ) {
			$data = [
				'id' => $this->id(),
			];
			$data['date'] = d($this->create_date);
			$data['value'] = $this->value;
			
			return $data;
		}
		
		return parent::asArray($model);
	}
	
}

PupilSkillValue::init();
