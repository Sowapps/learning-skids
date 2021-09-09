<?php

namespace Orpheus\File;

class GZFile extends AbstractFile {
	
	public function isCompressible(): bool {
		return false;
	}
	
	function open() {
		// https://www.php.net/manual/fr/function.gzopen.php
		$this->handle = gzopen($this->getPath(), $this->getMode());
	}
	
	function getNextLine() {
		$this->ensureOpen('r');
		
		return gzgets($this->handle);
	}
	
	function write($data) {
		$this->ensureOpen('w9');
		gzwrite($this->handle, $data);
	}
	
	function getContents() {
		$this->ensureClosed();
		
		return file_get_contents('compress.zlib://' . $this->getPath());
	}
	
	function close(): bool {
		$r = gzclose($this->handle);
		$this->handle = null;
		
		return $r;
	}
	
}
