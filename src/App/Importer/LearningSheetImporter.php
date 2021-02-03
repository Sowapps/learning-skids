<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Importer;

use App\Entity\LearningCategory;
use App\Entity\LearningSheet;
use App\Entity\LearningSkill;
use App\Exception\ParseException;
use Orpheus\File\UploadedFile;
use SplFileObject;
use stdClass;

class LearningSheetImporter {
	
	protected array $headers;
	protected int $categoryChanges;
	protected int $skillChanges;
	protected int $rowCount;
	
	protected LearningSheet $learningSheet;
	protected ?LearningCategory $previousCategory;
	protected array $previousCategorySkillKeys;
	
	protected array $errors;
	protected array $itemAliases = [
		'dom'  => 'category',
		'comp' => 'skill',
	];
	protected array $fieldAliases = [
		'nom'    => 'name',
		'cle'    => 'key',
		'valeur' => 'valuable',
	];
	
	public function import(LearningSheet $learningSheet, UploadedFile $file) {
		$file = $file->getSplFileInfo()->openFile();
		$this->parseHeaders($this->nextRow($file));
		$rowNumber = 2;// Start after headers
		
		$this->learningSheet = $learningSheet;
		$this->errors = [];
		$this->categoryChanges = 0;
		$this->skillChanges = 0;
		$this->rowCount = 0;
		$this->previousCategory = null;
		$this->previousCategorySkillKeys = [];
		
		while( $row = $this->nextRow($file) ) {
			$item = $this->parseRow($row);
			if( !$item ) {
				// Ignore empty line
				continue;
			}
			try {
				$this->processItem($item);
			} catch( ParseException $e ) {
				$this->errors[] = (object) [
					'exception' => $e->getMessage(),
					'row'       => $rowNumber,
					'category'  => !empty($item->category->name) ? $item->category->name : 'N/A',
					'skill'     => !empty($item->skill->name) ? $item->skill->name : 'N/A',
				];
			}
			$rowNumber++;
			$this->rowCount++;
		}
	}
	
	/**
	 * @param string[] $headers
	 */
	protected function parseHeaders(array $headers) {
		$this->headers = [];
		foreach( $headers as $name ) {
			$header = new stdClass();
			[$header->item, $header->property] = explode('_', strtolower(utf8_encode($name)), 2);
			if( array_key_exists($header->item, $this->itemAliases) ) {
				$header->item = $this->itemAliases[$header->item];
			}
			if( array_key_exists($header->property, $this->fieldAliases) ) {
				$header->property = $this->fieldAliases[$header->property];
			}
			$this->headers[] = $header;
		}
	}
	
	/**
	 * @param SplFileObject $file
	 * @return array|false
	 */
	protected function nextRow(SplFileObject $file) {
		return $file->fgetcsv(';');
	}
	
	/**
	 * @param array $row
	 * @return stdClass|null
	 */
	protected function parseRow(array $row): ?stdClass {
		$data = new stdClass();
		foreach( $row as $column => $value ) {
			$header = $this->headers[$column];
			if( empty($data->{$header->item}) ) {
				$data->{$header->item} = new stdClass();
			}
			$data->{$header->item}->{$header->property} = utf8_encode($value);
		}
		
		return $this->formatRowData($data);
	}
	
	/**
	 * @param object $data
	 * @return stdClass|null
	 */
	protected function formatRowData(object $data): ?stdClass {
		// Skill name is required, row is ignored
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
			$this->setPreviousCategory($category, $justCreated);
		}
		
		if( array_key_exists($item->skill->key, $this->previousCategorySkillKeys) ) {
			// Exact same Skill already exists
			return;
		}
		// Skill - Always considered as new
		LearningSkill::create([
			'key'                  => $item->skill->key,
			'name'                 => $item->skill->name,
			'learning_category_id' => $category->id(),
			'valuable'             => boolval($item->skill->valuable),
		]);
		$this->skillChanges++;
	}
	
	/**
	 * @param string $key
	 * @return LearningCategory|null
	 */
	protected function getCategoryByKey(string $key): ?LearningCategory {
		return LearningCategory::get()
			->where('learning_sheet_id', $this->learningSheet)
			->where('key', $key)
			->asObject()
			->run();
	}
	
	protected function setPreviousCategory(LearningCategory $category, bool $justCreated): void {
		if( $category->equals($this->previousCategory) ) {
			return;
		}
		$this->previousCategory = $category;
		$this->previousCategorySkillKeys = [];
		if( !$justCreated ) {
			$skillKeys = $category->querySkills()
				->fields(LearningSkill::ei('key'))
				->asArrayList()
				->run();
			foreach( $skillKeys as $skillArray ) {
				$this->previousCategorySkillKeys[$skillArray['key']] = 1;
			}
		}
	}
	
	/**
	 * @return int
	 */
	public function getRowCount(): int {
		return $this->rowCount;
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
	
	/**
	 * @return array
	 */
	public function getErrors(): array {
		return $this->errors;
	}
	
}
