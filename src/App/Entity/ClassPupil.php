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
	
	protected static string $table = 'class-pupil';
	
	protected static array $fields = [];
	
	protected static $validator = null;
	
	protected static string $domain;
	
	public function getSchoolClass(): SchoolClass {
		return SchoolClass::load($this->class_id, false);
	}
	
	public function getPerson(): Person {
		return Person::load($this->pupil_id, false);
	}
	
	public function asArray($model = self::OUTPUT_MODEL_ALL) {
		if( $model === OUTPUT_MODEL_USAGE ) {
			$data = parent::asArray(self::OUTPUT_MODEL_MINIMALS);
			$person = $this->getPerson();
			$data['firstname'] = $person->firstname;
			$data['lastname'] = $person->lastname;
			$data['label'] = $person->getLabel();
			
			return $data;
		}
		
		return parent::asArray($model);
	}
	
}

ClassPupil::init();
