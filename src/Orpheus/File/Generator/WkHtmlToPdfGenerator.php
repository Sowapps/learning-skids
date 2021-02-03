<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace Orpheus\File\Generator;

use Exception;

class WkHtmlToPdfGenerator {
	
	/** @var string */
	protected $tempBasePath;
	
	/** @var string */
	protected $bodyPath;
	
	/** @var string */
	protected $headerPath;
	
	/** @var string */
	protected $footerPath;
	
	/** @var string */
	protected $outputPath;
	
	/** @var string */
	protected $args;
	
	/** @var string */
	protected $title;
	
	/** @var string */
	protected $pageWidth;
	
	/** @var string */
	protected $pageHeight;
	
	/** @var string */
	protected $marginTop;
	
	/** @var string */
	protected $marginBottom;
	
	/** @var string */
	protected $marginLeft;
	
	/** @var string */
	protected $marginRight;
	
	/** @var string */
	protected $dpi;
	
	/** @var string */
	protected $zoom;
	
	/** @var string */
	protected $imageDpi;
	
	/** @var string */
	protected $imageQuality;
	
	/** @var string */
	protected $disableSmartShrinking;
	
	/** @var string */
	protected $commandCode;
	
	/** @var string */
	protected $commandOutput;
	
	protected $binary;
	
	public function __construct($bodyHtml = null) {
		if( defined('TEMP_PATH') ) {
			$tempPath = TEMP_PATH;
			
		} elseif( defined('STORE_PATH') ) {
			$tempPath = STORE_PATH . '/temp';
			
		} elseif( defined('STOREPATH') ) {
			$tempPath = STOREPATH . 'temp';
		}
		if( !is_dir($tempPath) ) {
			mkdir($tempPath);
		}
		do {
			$this->setTempBasePath($tempPath . '/pdf-' . generateRandomString(6));
		} while( file_exists($this->getTempBasePath()) );
		
		$this->setBinaryPath(defined('WKHTMLTOPDF_EXEC') ? WKHTMLTOPDF_EXEC : 'wkhtmltopdf');
		
		if( $bodyHtml ) {
			$this->setBodyHtml($bodyHtml);
		}
		$this->setMarginTop('20mm');
		$this->setMarginBottom('20mm');
	}
	
	public function getTempBasePath() {
		return $this->tempBasePath;
	}
	
	public function setTempBasePath($baseFilePath) {
		$this->tempBasePath = $baseFilePath;
		
		return $this;
	}
	
	public function setBinaryPath($binaryPath) {
		$this->binary = $binaryPath;
		
		return $this;
	}
	
	public function setBodyHtml($html) {
		file_put_contents($this->getBodyPath(), $html);
		
		return $this;
	}
	
	public function getBodyPath() {
		return $this->bodyPath ? $this->bodyPath : $this->getTempBasePath() . '.html';
	}
	
	public function setBodyPath($bodyFilePath) {
		$this->bodyPath = $bodyFilePath;
		
		return $this;
	}
	
	public function download($filename = null, $forceDownload = false) {
		
		$filePath = $this->getOutputPath();
		if( !file_exists($filePath) ) {
			throw new Exception('notGenerated');
		}
		if( !is_readable($filePath) ) {
			throw new Exception('unreadableFile');
		}
		if( !$filename ) {
			$filename = toSlug($this->getTitle()) . '.pdf';
		}
		
		// Start download, close session and end buffer
		session_write_close();
		ob_clean();
		
		header('Content-Type: application/pdf');
		if( $forceDownload ) {
			header('Content-Disposition: attachment; filename="' . $filename . '"');
		} else {
			header('Content-Disposition: inline; filename="' . $filename . '"');
		}
		header('Content-length: ' . filesize($filePath));
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT');
		// header('Cache-Control: public, max-age=3600, must-revalidate');
		header('Cache-Control: private, max-age=600');
		header('Pragma: public');
		
		readfile($filePath);
		die();
	}
	
	public function getOutputPath() {
		return $this->outputPath ?: $this->getTempBasePath() . '.pdf';
	}
	
	public function setOutputPath($outputPath) {
		$this->outputPath = $outputPath;
		
		return $this;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function setTitle($title) {
		$this->title = $title;
		
		return $this;
	}
	
	/**
	 * Generate pdf from provided html source
	 *
	 * @return bool
	 */
	public function generate() {
		
		exec($this->getCommand(), $this->commandOutput, $this->commandCode);
		
		$this->clean();
		
		return !$this->commandCode;
	}
	
	public function getCommand() {
		$args = $this->args;
		
		if( file_exists($this->getHeaderPath()) ) {
			$args .= ' --header-html ' . $this->getHeaderPath();
		}
		if( file_exists($this->getFooterPath()) ) {
			$args .= ' --footer-html ' . $this->getFooterPath();
		}
		
		if( $this->getMarginTop() !== null ) {
			$args .= ' --margin-top ' . $this->getMarginTop();
		}
		if( $this->getMarginBottom() !== null ) {
			$args .= ' --margin-bottom ' . $this->getMarginBottom();
		}
		if( $this->getMarginLeft() !== null ) {
			$args .= ' --margin-left ' . $this->getMarginLeft();
		}
		if( $this->getMarginRight() !== null ) {
			$args .= ' --margin-right ' . $this->getMarginRight();
		}
		
		if( $this->getDpi() !== null ) {
			$args .= ' --dpi ' . $this->getDpi();
		}
		if( $this->getZoom() !== null ) {
			$args .= ' --zoom ' . $this->getZoom();
		}
		
		if( $this->getImageDpi() !== null ) {
			$args .= ' --image-dpi ' . $this->getImageDpi();
		}
		if( $this->getImageQuality() !== null ) {
			$args .= ' --image-quality ' . $this->getImageQuality();
		}
		
		if( $this->getDisableSmartShrinking() ) {
			$args .= ' --disable-smart-shrinking';
		}
		
		// If page break is crazy, increase the margin (-B)
		return $this->binary . $args . ' ' . $this->getBodyPath() . ' ' . $this->getOutputPath();
	}
	
	public function getHeaderPath() {
		return $this->headerPath ? $this->headerPath : $this->getTempBasePath() . '-header.html';
	}
	
	public function setHeaderPath($headerPath) {
		$this->headerPath = $headerPath;
		
		return $this;
	}
	
	public function getFooterPath() {
		return $this->footerPath ? $this->footerPath : $this->getTempBasePath() . '-footer.html';
	}
	
	public function setFooterPath($footerPath) {
		$this->footerPath = $footerPath;
		
		return $this;
	}
	
	public function getMarginTop() {
		return $this->marginTop;
	}
	
	public function setMarginTop($marginTop) {
		$this->marginTop = $marginTop;
		
		return $this;
	}
	
	public function getMarginBottom() {
		return $this->marginBottom;
	}
	
	public function setMarginBottom($marginBottom) {
		$this->marginBottom = $marginBottom;
		
		return $this;
	}
	
	public function getMarginLeft() {
		return $this->marginLeft;
	}
	
	public function setMarginLeft($marginLeft) {
		$this->marginLeft = $marginLeft;
		
		return $this;
	}
	
	public function getMarginRight() {
		return $this->marginRight;
	}
	
	public function setMarginRight($marginRight) {
		$this->marginRight = $marginRight;
		
		return $this;
	}
	
	public function getDpi() {
		return $this->dpi;
	}
	
	public function setDpi($dpi) {
		$this->dpi = $dpi;
		
		return $this;
	}
	
	public function getZoom() {
		return $this->zoom;
	}
	
	public function setZoom($zoom) {
		$this->zoom = $zoom;
		
		return $this;
	}
	
	public function getImageDpi() {
		return $this->imageDpi;
	}
	
	public function setImageDpi($imageDpi) {
		$this->imageDpi = $imageDpi;
		
		return $this;
	}
	
	public function getImageQuality() {
		return $this->imageQuality;
	}
	
	public function setImageQuality($imageQuality) {
		$this->imageQuality = $imageQuality;
		
		return $this;
	}
	
	public function getDisableSmartShrinking() {
		return $this->disableSmartShrinking;
	}
	
	public function setDisableSmartShrinking($disableSmartShrinking) {
		$this->disableSmartShrinking = $disableSmartShrinking;
		
		return $this;
	}
	
	public function clean() {
		if( file_exists($this->getBodyPath()) ) {
			unlink($this->getBodyPath());
		}
		if( file_exists($this->getHeaderPath()) ) {
			unlink($this->getHeaderPath());
		}
		if( file_exists($this->getFooterPath()) ) {
			unlink($this->getFooterPath());
		}
	}
	
	public function getPageWidth() {
		return $this->pageWidth;
	}
	
	public function setPageWidth($pageWidth) {
		$this->pageWidth = $pageWidth;
		
		return $this;
	}
	
	public function getPageHeight() {
		return $this->pageHeight;
	}
	
	public function setPageHeight($pageHeight) {
		$this->pageHeight = $pageHeight;
		
		return $this;
	}
	
	public function getBinaryPath() {
		return $this->binary;
	}
	
	public function setHeaderHtml($html) {
		file_put_contents($this->getHeaderPath(), $html);
		
		return $this;
	}
	
	public function setFooterHtml($html) {
		file_put_contents($this->getFooterPath(), $html);
		
		return $this;
	}
	
	public function setMargins($top, $bottom, $left, $right) {
		$this->setMarginTop($top);
		$this->setMarginBottom($bottom);
		$this->setMarginLeft($left);
		$this->setMarginRight($right);
		
		return $this;
	}
	
	public function getCommandCode() {
		return $this->commandCode;
	}
	
	public function getCommandOutput() {
		return $this->commandOutput;
	}
	
}
