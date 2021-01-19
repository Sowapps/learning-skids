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
 * @property int $pupil_id
 * @property int $skill_id
 * @property string $value
 */
class PupilSkill extends PermanentEntity {
	
	protected static $table = 'learning-skill';
	
	protected static $fields = null;
	protected static $validator = null;
	protected static $domain = null;
	
	public function getPupil(): Person {
		return Person::load($this->pupil_id, false);
	}
	
	public function getLearningSkill(): LearningSkill {
		return LearningSkill::load($this->skill_id, false);
	}
	
}

PupilSkill::init();
