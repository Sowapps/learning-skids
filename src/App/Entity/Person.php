<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Entity;

use DateTime;
use Orpheus\EntityDescriptor\PermanentEntity;
use Orpheus\SQLRequest\SQLSelectRequest;

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
	
	/**
	 * @param string|string[],null $role
	 * @return SQLSelectRequest
	 */
	public function queryLearningSheets($role = null): SQLSelectRequest {
		$query = LearningSheet::get()
			->join(LearningSheetUser::class, $userAlias, null, 'learning_sheet_id', true);
		if( $role ) {
			$query->where($userAlias . '.role', $role);
		}
		
		return $query;
	}
	
	public function getLabel() {
		return $this->firstname . ' ' . $this->lastname;
	}
	
	public function queryClasses($enabled = null) {
		$query = SchoolClass::get()
			->where('teacher_id', $this)
			->orderby('year DESC');
		if( $enabled !== null ) {
			$query->where('enabled', $enabled);
		}
		
		return $query;
	}
	
	public static function getAllRoles() {
		return [self::ROLE_PUPIL, self::ROLE_TEACHER];
	}
	
}

Person::init();
