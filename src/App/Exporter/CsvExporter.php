<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Exporter;

use RuntimeException;

class CsvExporter {
	
	protected array $headers = [];
	
	/** @var resource|null */
	protected $stream = null;
	
	protected string $separator = ';';
	
	protected $previous = null;
	
	public function isInitialized(): bool {
		return !!$this->stream;
	}
	
	public function initialize() {
		if( $this->isInitialized() ) {
			return;
		}
		$this->stream = tmpfile();
	}
	
	/**
	 * @param string $header
	 * @param string|callable $parser
	 * @return self
	 */
	public function addHeader(string $header, $parser): self {
		$this->headers[] = [$header, $parser];
		
		return $this;
	}
	
	protected function writeRow(array $fields) {
		return fputcsv($this->stream, $fields, $this->separator);
	}
	
	public function writeHeaders() {
		$this->initialize();
		$fields = [];
		foreach( $this->headers as [$header] ) {
			$fields[] = utf8_decode($header);
		}
		
		return $this->writeRow($fields);
	}
	
	public function addRow($item) {
		if( !$this->isInitialized() ) {
			throw new RuntimeException('Exporter is not initialized yet, please call writeHeaders before adding any row');
		}
		$fields = [];
		foreach( $this->headers as [, $parser] ) {
			if( is_string($parser) ) {
				$value = $item->$parser;
			} else {
				$value = call_user_func($parser, $item, $this->previous);
			}
			$fields[] = utf8_decode($value);
		}
		$this->previous = $item;
		
		return $this->writeRow($fields);
	}
	
	public function terminate(): self {
		rewind($this->stream);
		
		return $this;
	}
	
	/**
	 * @return resource|null
	 */
	public function getStream() {
		return $this->stream;
	}
	
	/**
	 * @param resource|null $stream
	 */
	public function setStream($stream): void {
		$this->stream = $stream;
	}
	
	/**
	 * @return array
	 */
	public function getHeaders(): array {
		return $this->headers;
	}
	
	/**
	 * @return string
	 */
	public function getSeparator(): string {
		return $this->separator;
	}
	
}
