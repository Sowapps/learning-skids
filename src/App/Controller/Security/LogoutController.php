<?php

namespace App\Controller\Security;

use App\Controller\AbstractHttpController;
use App\Entity\User;
use Orpheus\InputController\HTTPController\HTTPController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\RedirectHTTPResponse;

class LogoutController extends AbstractHttpController {
	
	protected string $scope = self::SCOPE_MEMBER;
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return RedirectHTTPResponse
	 * @see HTTPController::run()
	 */
	public function run($request): HttpResponse {
		
		$user = User::getLoggedUser();
		if( $user ) {
			$user->logout();
		}
		
		return new RedirectHTTPResponse(DEFAULT_ROUTE);
	}
	
	
}
