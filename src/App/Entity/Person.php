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
	
	protected static string $table = 'person';
	
	protected static array $fields = [];
	
	protected static $validator = null;
	
	protected static string $domain;
	
	public function queryClassPupils(): SQLSelectRequest {
		return ClassPupil::select()
			->where('pupil_id', $this)
			->orderby('id DESC');
	}
	
	public function querySchoolClasses(): SQLSelectRequest {
		return SchoolClass::select()
			->join(ClassPupil::class, $pupilAlias, null, 'class_id', true)
			->where($pupilAlias . '.pupil_id', $this)
			->orderby('year DESC');
	}
	
	public function getSchoolClass(int $year): ?SchoolClass {
		return $this->querySchoolClasses()
			->where('year', $year)
			->asObject()->run();
	}
	
	/**
	 * @param LearningSheet $learningSheet
	 * @return PupilSkill[]
	 */
	public function getPupilSkills(LearningSheet $learningSheet): array {
		$query = PupilSkill::get()
			->where('pupil_id', $this)
			->where('learning_sheet_id', $learningSheet);
		$pupilSkills = [];
		foreach( $query as $pupilSkill ) {
			$pupilSkills[$pupilSkill->skill_id] = $pupilSkill;
		}
		
		return $pupilSkills;
	}
	
	public function getLabel(): string {
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
	
	public static function getAllRoles(): array {
		return [self::ROLE_PUPIL, self::ROLE_TEACHER];
	}
	
	public static function getByName(string $firstName, string $lastName, ?string $role = null): array {
		$query = Person::get()
			->where('firstname', 'LIKE', $firstName)
			->where('lastname', 'LIKE', $lastName);
		if( $role ) {
			$query->where('role', 'LIKE', $role);
		}
		
		return $query->asObjectList()->run();
	}
	
}

Person::init();
