<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Importer;

use App\Exception\ParseException;
use stdClass;

class ClassPupilListImporter extends AbstractCsvImporter {
	
	protected array $pupils = [];
	
	protected array $itemAliases = [
		'eleve' => 'pupil',
	];
	protected array $fieldAliases = [
		'prenom' => 'firstname',
		'nom'    => 'lastname',
	];
	
	/**
	 * @return bool
	 */
	public function didAnyChanges(): bool {
		return !!$this->pupils;
	}
	
	/**
	 * @return stdClass[]
	 */
	public function getPupils(): array {
		return $this->pupils;
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
		// Pupil's firstname & lastname are required, else row is ignored
		if( empty($data->pupil->firstname) || empty($data->pupil->lastname) ) {
			return null;
		}
		
		return $data;
	}
	
	/**
	 * @param stdClass $item
	 */
	protected function processItem(stdClass $item): void {
		$this->pupils[] = (array) $item->pupil;
	}
	
}
