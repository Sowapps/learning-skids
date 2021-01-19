<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Entity;

use DateTime;
use Orpheus\EntityDescriptor\PermanentEntity;

/**
 * Class LearningSkill
 *
 * @package App\Entity
 *
 * @property DateTime $create_date
 * @property string $create_ip
 * @property int $create_user_id
 * @property string $name
 * @property int $learning_category_id
 * @property boolean $valuable
 */
class LearningSkill extends PermanentEntity {
	
	protected static $table = 'learning-skill';
	
	protected static $fields = null;
	protected static $validator = null;
	protected static $domain = null;
	
	public function getLabel() {
		return $this->name;
	}
	
	public function getLearningCategory(): LearningCategory {
		return LearningCategory::load($this->learning_category_id, false);
	}
	
}

LearningSkill::init();
