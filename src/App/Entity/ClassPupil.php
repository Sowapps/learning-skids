<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Entity;

use DateTime;
use Orpheus\EntityDescriptor\PermanentEntity;

/**
 * Class ClassPupil
 *
 * @package App\Entity
 *
 * @property DateTime $create_date
 * @property string $create_ip
 * @property int $create_user_id
 * @property int $class_id
 * @property int $pupil_id
 */
class ClassPupil extends PermanentEntity {
	
	protected static $table = 'class-pupil';
	
	protected static $fields = null;
	protected static $validator = null;
	protected static $domain = null;
	
	public function getSchoolClass(): SchoolClass {
		return SchoolClass::load($this->class_id, false);
	}
	
	public function getPupil(): Person {
		return Person::load($this->pupil_id, false);
	}
	
}

ClassPupil::init();
