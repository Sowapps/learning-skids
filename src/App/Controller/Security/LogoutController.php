<?php

namespace App\Controller\Security;

use App\Controller\AbstractHttpController;
use App\Entity\User;
use Orpheus\InputController\HttpController\HttpController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpResponse;
use Orpheus\InputController\HttpController\RedirectHttpResponse;

class LogoutController extends AbstractHttpController {
	
	protected string $scope = self::SCOPE_MEMBER;
	
	/**
	 * @param HttpRequest $request The input HTTP request
	 * @return RedirectHttpResponse
	 * @see HttpController::run()
	 */
	public function run($request): HttpResponse {
		$user = User::getLoggedUser();
		if( $user ) {
			$user->logout();
		}
		
		return new RedirectHttpResponse(DEFAULT_ROUTE);
	}
	
	
}
