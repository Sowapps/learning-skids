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
	
	public function asArray($model = self::OUTPUT_MODEL_ALL) {
		if( $model === OUTPUT_MODEL_USAGE || $model === OUTPUT_MODEL_EDITION ) {
			$data = parent::asArray(self::OUTPUT_MODEL_MINIMALS);
			$data['valuable'] = $this->valuable;
			if( OUTPUT_MODEL_EDITION ) {
				$data['name'] = $this->name;
			}
			
			return $data;
		}
		
		return parent::asArray($model);
	}
	
	public function getLabel() {
		return $this->name;
	}
	
	public function getLearningCategory(): LearningCategory {
		return LearningCategory::load($this->learning_category_id, false);
	}
	
	public static function onEdit(array &$data, $object) {
		parent::onEdit($data, $object);
		if( isset($data['name']) && !isset($data['key']) ) {
			$data['key'] = LearningCategory::slugName($data['name']);
		}
	}
	
}

LearningSkill::init();
