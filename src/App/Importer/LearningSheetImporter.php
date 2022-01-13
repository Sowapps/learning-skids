<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Importer;

use App\Entity\LearningCategory;
use App\Entity\LearningSheet;
use App\Entity\LearningSkill;
use App\Exception\ParseException;
use stdClass;

class LearningSheetImporter extends AbstractCsvImporter {
	
	protected int $categoryChanges;
	
	protected int $skillChanges;
	
	protected LearningSheet $learningSheet;
	
	protected ?LearningCategory $previousCategory;
	
	/** @var LearningSkill[] */
	protected array $previousCategorySkills;
	
	protected array $itemAliases = [
		'dom'  => 'category',
		'comp' => 'skill',
	];
	
	protected array $fieldAliases = [
		'nom'    => 'name',
		'cle'    => 'key',
		'valeur' => 'valuable',
	];
	
	public function initialize(LearningSheet $learningSheet) {
		$this->learningSheet = $learningSheet;
		$this->previousCategory = null;
		$this->previousCategorySkills = [];
	}
	
	protected function formatItemParseError(object $item, ParseException $e, int $rowNumber): array {
		return [
			'exception' => $e->getMessage(),
			'row'       => $rowNumber,
			'category'  => !empty($item->category->name) ? $item->category->name : 'N/A',
			'skill'     => !empty($item->skill->name) ? $item->skill->name : 'N/A',
		];
	}
	
	/**
	 * @param object $data
	 * @return stdClass|null
	 */
	protected function formatRowData(object $data): ?stdClass {
		// Skill name is required, else row is ignored
		// Category name is defaulting to previous one
		// Skill key and category key are guessed from name
		if( empty($data->skill->name) ) {
			return null;
		}
		if( empty($data->category->name) ) {
			$data->category = null;
		} elseif( empty($data->category->key) ) {
			$data->category->key = LearningCategory::slugName($data->category->name);
		}
		if( empty($data->skill->key) ) {
			$data->skill->key = LearningCategory::slugName($data->skill->name);
		}
		$data->skill->valuable = boolval($data->skill->valuable);
		
		return $data;
	}
	
	/**
	 * @param stdClass $item
	 */
	protected function processItem(stdClass $item): void {
		// Category - can only be inserted if new
		if( !$item->category ) {
			$category = $this->previousCategory;
		} else {
			$category = $this->getCategoryByKey($item->category->key);
			$justCreated = false;
			if( !$category ) {
				if( empty($item->category->name) ) {
					throw new ParseException('category.name.empty');
				}
				$category = LearningCategory::createAndGet([
					'key'               => $item->category->key,
					'name'              => $item->category->name,
					'learning_sheet_id' => $this->learningSheet->id(),
				]);
				$this->categoryChanges++;
				$justCreated = true;
			}
			// Category's skill
			$this->setPreviousCategory($category, $justCreated);
		}
		
		$skill = $this->previousCategorySkills[$item->skill->key] ?? null;
		if( $skill ) {
			// Update existing
			if( $item->skill->name !== $skill->name || $item->skill->valuable !== boolval($skill->valuable) ) {
				$skill->update([
					'name'     => $item->skill->name,
					'valuable' => $item->skill->valuable,
				], ['name', 'valuable']);
				$this->skillChanges++;
			}
		} else {
			// Create new one
			// Skill - Always considered as new
			LearningSkill::create([
				'key'                  => $item->skill->key,
				'name'                 => $item->skill->name,
				'learning_category_id' => $category->id(),
				'valuable'             => $item->skill->valuable,
			]);
			$this->skillChanges++;
		}
	}
	
	/**
	 * @param string $key
	 * @return LearningCategory|null
	 */
	protected function getCategoryByKey(string $key): ?LearningCategory {
		return LearningCategory::get()
			->where('learning_sheet_id', $this->learningSheet)
			->where('key', $key)
			->where('learning_sheet_id', $this->learningSheet)
			->asObject()
			->run();
	}
	
	protected function setPreviousCategory(LearningCategory $category, bool $justCreated): void {
		if( $category->equals($this->previousCategory) ) {
			return;
		}
		$this->previousCategory = $category;
		$this->previousCategorySkills = [];
		if( !$justCreated ) {
			$skillKeys = $category->querySkills()
				->fields(LearningSkill::ei('key'))
				->run();
			foreach( $skillKeys as $skill ) {
				/** @var LearningSkill $skill */
				$this->previousCategorySkills[$skill->key] = $skill;
			}
		}
	}
	
	/**
	 * @return bool
	 */
	public function didAnyChanges(): bool {
		return $this->categoryChanges || $this->skillChanges;
	}
	
	/**
	 * @return int
	 */
	public function getCategoryChanges(): int {
		return $this->categoryChanges;
	}
	
	/**
	 * @return int
	 */
	public function getSkillChanges(): int {
		return $this->skillChanges;
	}
	
}
