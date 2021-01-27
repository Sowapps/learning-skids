<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Entity;

use DateTime;
use Orpheus\EntityDescriptor\PermanentEntity;
use Orpheus\SQLRequest\SQLSelectRequest;

/**
 * Class LearningSheet
 *
 * @package App\Entity
 *
 * @property DateTime $create_date
 * @property string $create_ip
 * @property int $create_user_id
 * @property string $name
 * @property string $level
 * @property int $owner_id
 */
class LearningSheet extends PermanentEntity {
	
	protected static $table = 'learning-sheet';
	
	protected static $fields = null;
	protected static $validator = null;
	protected static $domain = null;
	
	public function getTree() {
		$tree = (object) $this->asArray(OUTPUT_MODEL_EDITION);
		$tree->categories = [];
		foreach( $this->queryCategories() as $category ) {
			$categoryTree = (object) $category->asArray(OUTPUT_MODEL_EDITION);
			$categoryTree->skills = [];
			foreach( $category->querySkills() as $skill ) {
				$categoryTree->skills[] = (object) $skill->asArray(OUTPUT_MODEL_EDITION);
			}
			$tree->categories[] = $categoryTree;
		}
		
		return $tree;
	}
	
	public function asArray($model = self::OUTPUT_MODEL_ALL) {
		if( $model === OUTPUT_MODEL_USAGE || $model === OUTPUT_MODEL_EDITION ) {
			$data = parent::asArray(self::OUTPUT_MODEL_MINIMALS);
			if( OUTPUT_MODEL_EDITION ) {
				$data['name'] = $this->name;
			}
			$data['level'] = $this->level;
			
			return $data;
		}
		
		return parent::asArray($model);
	}
	
	/**
	 * @return LearningCategory[]|SQLSelectRequest
	 */
	public function queryCategories() {
		return LearningCategory::get()
			->where('learning_sheet_id', $this)
			->orderby('name ASC');
	}
	
	public function getLabel() {
		return $this->name;
	}
	
	public function getOwner(): User {
		return User::load($this->owner_id, false);
	}
	
	public function addUser(User $user, string $role): LearningSheetUser {
		return LearningSheetUser::createAndGet(['learning_sheet_id' => $this, 'user_id' => $user, 'role' => $role]);
	}
	
	public static function make(string $name, string $level, ?User $owner = null): LearningSheet {
		if( !$owner ) {
			$owner = User::getLoggedUser();
		}
		$learningSheet = static::createAndGet(['name' => $name, 'level' => $level, 'owner_id' => $owner]);
		$learningSheet->addUser($owner, LearningSheetUser::ROLE_ADMIN);
		
		return $learningSheet;
	}
	
}

LearningSheet::init();
