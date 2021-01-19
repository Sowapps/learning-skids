<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Entity;

use DateTime;
use Orpheus\EntityDescriptor\PermanentEntity;

/**
 * Class LearningSheet
 *
 * @package App\Entity
 *
 * @property DateTime $create_date
 * @property string $create_ip
 * @property int $create_user_id
 * @property string $name
 * @property int $owner_id
 */
class LearningSheet extends PermanentEntity {
	
	protected static $table = 'learning-sheet';
	
	protected static $fields = null;
	protected static $validator = null;
	protected static $domain = null;
	
	public function getLabel() {
		return $this->name;
	}
	
	public function getOwner(): User {
		return User::load($this->owner_id, false);
	}
	
}

LearningSheet::init();
