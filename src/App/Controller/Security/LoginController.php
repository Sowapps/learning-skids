<?php

namespace App\Controller\Security;

use App\Controller\AbstractHttpController;
use App\Entity\User;
use Orpheus\Exception\UserException;
use Orpheus\InputController\HTTPController\HTTPController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPResponse;
use Orpheus\InputController\HTTPController\RedirectHTTPResponse;

class LoginController extends AbstractHttpController {
	
	protected string $scope = self::SCOPE_PUBLIC;
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse
	 * @see HTTPController::run()
	 */
	public function run($request) {
		if( User::isLogged() ) {
			return new RedirectHTTPResponse(u(getHomeRoute()));
		}
		
		/* @var User $user */
		$projectUserInvitation = null;
		
		try {
			$this->validateFormToken($request);
			if( $request->hasParameter('ac') && is_id($userID = $request->getParameter('u')) ) {
				$user = User::load($userID);
				if( !$user || $user->activation_code != $request->getParameter('ac') ) {
					User::throwException('invalidActivationCode');
				}
				$user->activate();
				$user->login();
				
				return new RedirectHTTPResponse($projectUserInvitation ? $projectUserInvitation->getLink() : u(DEFAULT_ROUTE_USER));
				
			} elseif( $request->hasData('submitLogin') && $data = $request->getData('login') ) {
				User::userLogin($data, 'email');
				
				return new RedirectHTTPResponse(u(getHomeRoute()));
				
			} elseif( $request->hasData('submitRegister') && ($data = $request->getData('user')) ) {
				startReportStream('register');
				$data['published'] = 0;
				$data['activation_code'] = generatePassword(30);
				$user = User::createAndGet($data, ['fullname', 'email', 'password', 'published', 'activation_code']);
				//				sendUserRegistrationEmail($user, $projectUserInvitation);
				unset($user);
				reportSuccess(User::text('successRegister'));
				
			}
		} catch( UserException $e ) {
			reportError($e);
			endReportStream();
		}
		
		return $this->renderHTML('security/login');
	}
	
	
}
