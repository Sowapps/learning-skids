<?php

namespace Orpheus\Controller\Developer;

use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPResponse;

class DevSystemController extends DevController {
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 */
	public function run($request): HttpResponse {
		$this->addThisToBreadcrumb();
		
		return $this->renderHTML('developer/dev_system');
	}
	
}
