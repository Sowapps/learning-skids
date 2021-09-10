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
 * @property int $learning_sheet_id
 * @property int $user_id
 * @property string $role
 */
class LearningSheetUser extends PermanentEntity {
	
	const ROLE_USAGE = 'usage';
	const ROLE_ADMIN = 'admin';
	
	protected static string $table = 'learning-sheet-user';
	
	protected static array $fields = [];
	
	protected static $validator = null;
	
	protected static string $domain;
	
	public function canAdminLearningSheet(): bool {
		return $this->role === self::ROLE_ADMIN;
	}
	
	public function getUser(): User {
		return User::load($this->user_id, false);
	}
	
	public function getLearningSheet(): LearningSheet {
		return LearningSheet::load($this->learning_sheet_id, false);
	}
	
	public static function getAllRoles() {
		return [self::ROLE_USAGE, self::ROLE_ADMIN];
	}
	
}

LearningSheetUser::init();
