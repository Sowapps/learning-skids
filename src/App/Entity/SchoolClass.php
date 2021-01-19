<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Entity;

use DateTime;
use Orpheus\EntityDescriptor\PermanentEntity;

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
	protected static $domain = null;
	
	public function getTeacher(): Person {
		return Person::load($this->teacher_id, false);
	}
	
	public static function getAllLevels() {
		return [self::LEVEL_KID_LOW, self::LEVEL_KID_MIDDLE, self::LEVEL_KID_HIGH];
	}
	
}

SchoolClass::init();
