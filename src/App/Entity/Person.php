<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Entity;

use DateTime;
use Orpheus\EntityDescriptor\PermanentEntity;

/**
 * Class Person
 *
 * @package App\Entity
 *
 * @property DateTime $create_date
 * @property string $create_ip
 * @property DateTime $login_date
 * @property string $firstname
 * @property string $lastname
 * @property string $role
 */
class Person extends PermanentEntity {
	
	const ROLE_PUPIL = 'pupil';
	const ROLE_TEACHER = 'teacher';
	
	protected static $table = 'person';
	
	protected static $fields = null;
	protected static $validator = null;
	protected static $domain = null;
	
	public function getLabel() {
		return $this->firstname . ' ' . $this->lastname;
	}
	
	public function queryClasses() {
		return SchoolClass::get()
			->where('teacher_id', $this);
	}
	
	public static function getAllRoles() {
		return [self::ROLE_PUPIL, self::ROLE_TEACHER];
	}
	
}

Person::init();
