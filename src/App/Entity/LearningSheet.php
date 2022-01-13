<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Entity;

use DateTime;
use Orpheus\EntityDescriptor\PermanentEntity;
use Orpheus\SqlRequest\SqlSelectRequest;

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
 * @property bool $enabled
 */
class LearningSheet extends PermanentEntity {
	
	protected static string $table = 'learning-sheet';
	
	protected static array $fields = [];
	
	protected static $validator = null;
	
	protected static string $domain;
	
	public function hasSkill(LearningSkill $skill): bool {
		return $this->hasSkillCategory($skill->getLearningCategory());
	}
	
	public function hasSkillCategory(LearningCategory $category): bool {
		return $category->learning_sheet_id === $this->id();
	}
	
	public function setEnabled(bool $enabled) {
		$this->enabled = $enabled;
	}
	
	public function remove(): int {
		foreach( $this->queryCategories() as $category ) {
			$category->remove();
		}
		
		return parent::remove();
	}
	
	/**
	 * @return LearningCategory[]|SqlSelectRequest
	 */
	public function queryCategories() {
		return LearningCategory::get()
			->where('learning_sheet_id', $this)
			->orderby('name ASC');
	}
	
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
	
	public function getLabel(): string {
		return $this->name;
	}
	
	public function getOwner(): User {
		return User::load($this->owner_id, false);
	}
	
	public static function make(array $input, ?User $owner = null): LearningSheet {
		$input = array_filterbykeys($input, ['name', 'level', 'owner_id']);
		if( empty($input['owner_id']) || $owner ) {
			if( !$owner ) {
				$owner = User::getLoggedUser();
			}
			$input['owner_id'] = $owner;
		}
		$learningSheet = static::createAndGet($input);
		$learningSheet->addUser($owner, LearningSheetUser::ROLE_ADMIN);
		
		return $learningSheet;
	}
	
	public function addUser(User $user, string $role): LearningSheetUser {
		return LearningSheetUser::createAndGet(['learning_sheet_id' => $this, 'user_id' => $user, 'role' => $role]);
	}
	
}

LearningSheet::init();
