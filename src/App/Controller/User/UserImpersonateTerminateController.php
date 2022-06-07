<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use App\Controller\AbstractHttpController;
use App\Entity\User;
use Exception;
use Orpheus\InputController\HttpController\RedirectHttpResponse;
use Orpheus\InputController\OutputResponse;

class UserImpersonateTerminateController extends AbstractHttpController {
	
	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public function run($request): OutputResponse {
		try {
			User::terminateImpersonate();
		} catch( Exception $e ) {
		}
		
		return new RedirectHttpResponse(u(getHomeRoute()), false);
	}
	
}
