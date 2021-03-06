<?php

namespace App\Controller\Security;

use App\Controller\AbstractHttpController;
use App\Entity\Person;
use App\Entity\User;
use Orpheus\Exception\UserException;
use Orpheus\InputController\HTTPController\HTTPController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPResponse;
use Orpheus\InputController\HTTPController\RedirectHTTPResponse;

class LoginController extends AbstractHttpController {
	
	const PANEL_LOGIN = 'login';
	const PANEL_REGISTER = 'register';
	const PANEL_RECOVERY = 'recovery';
	
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
		$panel = self::PANEL_LOGIN;
		
		try {
			if( $request->hasParameter('ac') && is_id($userID = $request->getParameter('u')) ) {
				$user = User::load($userID);
				if( !$user || $user->activation_code !== $request->getParameter('ac') ) {
					User::throwException('invalidActivationCode');
				}
				$user->activate();
				$user->login();
				
				return new RedirectHTTPResponse(u(getHomeRoute()));
				
			} elseif( $request->hasData('submitLogin') && $loginInput = $request->getData('login') ) {
				$this->validateFormToken($request);
				User::userLogin($loginInput);
				
				return new RedirectHTTPResponse(u(getHomeRoute()));
				
			} elseif( $request->hasData('submitRegister') && ($userInput = $request->getData('user')) ) {
				startReportStream('register');
				$panel = self::PANEL_REGISTER;
				$this->validateFormToken($request);
				
				$user = User::make($userInput, Person::ROLE_TEACHER);
				sendAdminRegistrationEmail($user);
				sendUserActivationEmail($user);
				unset($user);
				$panel = self::PANEL_LOGIN;
				endReportStream();
				reportSuccess(User::text('successRegister'));
				
			}
		} catch( UserException $e ) {
			reportError($e);
			endReportStream();
		}
		
		return $this->renderHTML('security/login', [
			'panel' => $panel,
		]);
	}
	
	
}
