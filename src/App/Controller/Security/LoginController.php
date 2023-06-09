<?php

namespace App\Controller\Security;

use App\Controller\AbstractHttpController;
use App\Entity\Person;
use App\Entity\User;
use Orpheus\Exception\UserException;
use Orpheus\InputController\HttpController\HttpController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpResponse;
use Orpheus\InputController\HttpController\RedirectHttpResponse;
use Orpheus\Time\DateTime;

class LoginController extends AbstractHttpController {
	
	const PANEL_LOGIN = 'login';
	const PANEL_REGISTER = 'register';
	const PANEL_RECOVERY = 'recovery';
	
	protected string $scope = self::SCOPE_PUBLIC;
	
	/**
	 * @param HttpRequest $request The input HTTP request
	 * @return HttpResponse
	 * @see HttpController::run()
	 */
	public function run($request): HttpResponse {
		
		if( User::isLogged() ) {
			return new RedirectHttpResponse(u(getHomeRoute()));
		}
		
		/* @var User $user */
		$panel = self::PANEL_LOGIN;
		
		try {
			if( $request->hasParameter('ac') && is_id($userId = $request->getParameter('u')) ) {
				// User activation
				$user = User::load($userId);
				if( !$user || $user->activation_code !== $request->getParameter('ac') ) {
					User::throwException('invalidActivationCode');
				}
				$user->activate();
				$user->login();
				
				return new RedirectHttpResponse(u(getHomeRoute()));
			}
			
			if( $request->hasData('submitLogin') && $loginInput = $request->getData('login') ) {
				// Login
				$this->validateFormToken($request);
				User::userLogin($loginInput);
				
				return new RedirectHttpResponse(u(getHomeRoute()));
			}
			
			if( $request->hasData('submitRecovery') ) {
				startReportStream('recovery');
				$panel = self::PANEL_RECOVERY;
				$this->validateFormToken($request);
				$email = $request->getData('email');
				
				$user = User::getByEmail($email);
				if( $user->recovery_date && $user->recovery_date > new DateTime('-1 hour') ) {
					// Delay 1 hour before you can send it again
					User::throwException('recoverPassword_delayError');
				}
				
				$user->recovery_code = generateRandomString(30);
				$user->recovery_date = new DateTime();
				sendUserRecoveryEmail($user);
				unset($user);
				endReportStream();
				$this->storeSuccess('recovery', 'recoverPassword_success', [], DOMAIN_USER);
			}
			
			if( $request->hasData('submitRegister') && ($userInput = $request->getData('user')) ) {
				// Register
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
		
		$this->consumeSuccess('recovery', 'recovery');
		
		return $this->renderHtml('security/login', [
			'panel' => $panel,
		]);
	}
	
	
}
