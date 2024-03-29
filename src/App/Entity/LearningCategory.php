<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Entity;

use DateTime;
use Orpheus\EntityDescriptor\PermanentEntity;
use Orpheus\Publisher\SlugGenerator;
use Orpheus\SqlRequest\SqlSelectRequest;

/**
 * Class LearningCategory
 *
 * @package App\Entity
 *
 * @property DateTime $create_date
 * @property string $create_ip
 * @property int $create_user_id
 * @property string $name
 * @property int $learning_sheet_id
 */
class LearningCategory extends PermanentEntity {
	
	protected static string $table = 'learning-category';
	
	protected static array $fields = [];
	
	protected static $validator = null;
	
	protected static string $domain;
	
	public function remove() {
		foreach( $this->querySkills() as $skill ) {
			$skill->remove();
		}
		
		return parent::remove();
	}
	
	/**
	 * @return LearningSkill[]|SqlSelectRequest
	 */
	public function querySkills() {
		return LearningSkill::get()
			->where('learning_category_id', $this)
			->orderby('name ASC');
	}
	
	public function asArray($model = self::OUTPUT_MODEL_ALL) {
		if( $model === OUTPUT_MODEL_USAGE || $model === OUTPUT_MODEL_EDITION ) {
			$data = parent::asArray(self::OUTPUT_MODEL_MINIMALS);
			if( OUTPUT_MODEL_EDITION ) {
				$data['name'] = $this->name;
			}
			
			return $data;
		}
		
		return parent::asArray($model);
	}
	
	public function getLabel(): string {
		return $this->name;
	}
	
	public function getLearningSheet(): LearningSheet {
		return LearningSheet::load($this->learning_sheet_id, false);
	}
	
	public static function onEdit(array &$data, $object) {
		parent::onEdit($data, $object);
		if( isset($data['name']) && !isset($data['key']) ) {
			$data['key'] = LearningCategory::slugName($data['name']);
		}
	}
	
	public static function slugName($name) {
		static $slugGenerator;
		if( !isset($slugGenerator) ) {
			$slugGenerator = new SlugGenerator();
			$slugGenerator
				->setMaxLength(50)
				->setCaseProcessing(SlugGenerator::CASE_LOWER);
		}
		
		return $slugGenerator->format($name);
	}
	
}

LearningCategory::init();
