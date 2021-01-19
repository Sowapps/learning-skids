<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Entity;

use DateTime;
use Orpheus\EntityDescriptor\PermanentEntity;

/**
 * Class LearningCategory
 *
 * @package App\Entity
 *
 * @property DateTime $create_date
 * @property string $create_ip
 * @property int $create_user_id
 * @property string $name
 * @property int $learning_sheet_id
 */
class LearningCategory extends PermanentEntity {
	
	protected static $table = 'learning-category';
	
	protected static $fields = null;
	protected static $validator = null;
	protected static $domain = null;
	
	public function getLabel() {
		return $this->name;
	}
	
	public function getLearningSheet(): LearningSheet {
		return LearningSheet::load($this->learning_sheet_id, false);
	}
	
}

LearningCategory::init();
