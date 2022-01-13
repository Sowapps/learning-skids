<?php

namespace Orpheus\Controller\Developer;

use Application;
use Orpheus\Config\AppConfig;
use Orpheus\Exception\UserException;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPResponse;
use User;

class DevConfigController extends DevController {
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 */
	public function run($request): HttpResponse {
		
		/* @var User $USER */
		
		$this->addThisToBreadcrumb();
		$application = Application::get();
		$config = AppConfig::instance();
		
		try {
			if( $request->hasData('submitRemove') ) {
				$key = $request->getData('submitRemove');
				$config->remove($key);
				$config->save();
				reportSuccess(sprintf('We removed key "%s"', $key));
			}
			
			$initializedCount = $application->initializeConfig();
			if( $initializedCount ) {
				reportSuccess(sprintf('We initialized %d configurations', $initializedCount));
			}
			
			if( $data = $request->getArrayData('row') ) {
				// Sanitize value
				$value = str_replace("\r\n", "\n", $data['value']);// Windows
				$value = str_replace("\r", "\n", $value);// Mac OS
				$config->set($data['key'], $value);
				$config->save();
			}
			
		} catch( UserException $e ) {
			reportError($e);
		}
		
		return $this->renderHTML('developer/dev_config', [
			'config' => $config,
		]);
	}
	
}
