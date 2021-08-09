<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Importer;

use App\Exception\ParseException;
use Orpheus\File\UploadedFile;
use SplFileObject;
use stdClass;

abstract class AbstractCsvImporter {
	
	protected array $headers;
	protected int $rowCount;
	
	protected array $errors = [];
	protected array $itemAliases = [];
	protected array $fieldAliases = [];
	
	public function import(UploadedFile $file) {
		$file = $file->getSplFileInfo()->openFile();
		$this->parseHeaders($this->nextRow($file));
		$rowNumber = 2;// Start after headers
		
		$this->errors = [];
		$this->categoryChanges = 0;
		$this->skillChanges = 0;
		$this->rowCount = 0;
		
		while( $row = $this->nextRow($file) ) {
			$item = $this->parseRow($row);
			if( !$item ) {
				// Ignore empty line
				continue;
			}
			try {
				$this->processItem($item);
			} catch( ParseException $e ) {
				$this->errors[] = (object) $this->formatItemParseError($item, $e, $rowNumber);
			}
			$rowNumber++;
			$this->rowCount++;
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
	public abstract function didAnyChanges(): bool;
	
	/**
	 * @return array
	 */
	public function getErrors(): array {
		return $this->errors;
	}
	
	protected function formatItemParseError(object $item, ParseException $e, int $rowNumber): array {
		return [
			'exception' => $e->getMessage(),
			'row'       => $rowNumber,
		];
	}
	
	/**
	 * @param string[] $headers
	 */
	protected function parseHeaders(array $headers) {
		$this->headers = [];
		foreach( $headers as $name ) {
			if( !$name || strpos($name, '_') === false ) {
				$this->headers[] = null;
				continue;
			}
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
			if( !$header ) {
				continue;
			}
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
	protected abstract function formatRowData(object $data): ?stdClass;
	
	/**
	 * @param stdClass $item
	 */
	protected abstract function processItem(stdClass $item): void;
	
}
