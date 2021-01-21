<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Entity;

use DateTime;
use Orpheus\EntityDescriptor\PermanentEntity;

/**
 * Class LearningSheetClass
 *
 * @package App\Entity
 *
 * @property DateTime $create_date
 * @property string $create_ip
 * @property int $create_user_id
 * @property int $class_id
 * @property int $learning_sheet_id
 */
class LearningSheetClass extends PermanentEntity {
	
	protected static $table = 'learning-sheet-class';
	
	protected static $fields = null;
	protected static $validator = null;
	protected static $domain = null;
	
	public function getSchoolClass(): SchoolClass {
		return SchoolClass::load($this->class_id, false);
	}
	
	public function getLearningSheet(): LearningSheet {
		return LearningSheet::load($this->learning_sheet_id, false);
	}
	
}

LearningSheetClass::init();
