<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller;

use App\Entity\User;
use Exception;
use Orpheus\Form\FormToken;
use Orpheus\InputController\HttpController\HttpController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\RedirectHttpResponse;
use Orpheus\InputController\OutputResponse;
use Orpheus\Rendering\HtmlRendering;

abstract class AbstractHttpController extends HttpController {
	
	const SCOPE_PUBLIC = 'public';
	const SCOPE_MEMBER = 'member';
	const SCOPE_ADMIN = 'admin';
	const SCOPE_SYSTEM = 'system';
	
	const SESSION_SUCCESS = 'SUCCESS';
	
	protected string $scope;
	
	protected ?User $currentUser;
	
	protected ?FormToken $formToken = null;
	
	public function redirectToSelf(): RedirectHttpResponse {
		return new RedirectHttpResponse($this->getCurrentUrl());
	}
	
	public function getCurrentUrl(): string {
		return $this->getRequest()->getURL();// With QueryString
		//		return $this->getRoute()->formatURL((array) $this->getRequest()->getPathValues());
	}
	
	public function storeSuccess(string $key, string $message, array $params = [], ?string $domain = null) {
		if( !isset($_SESSION[self::SESSION_SUCCESS][$key]) ) {
			$_SESSION[self::SESSION_SUCCESS][$key] = [];
		}
		$_SESSION[self::SESSION_SUCCESS][$key][] = t($message, $domain, $params);
	}
	
	public function consumeSuccess(string $key, ?string $stream = null) {
		if( isset($_SESSION[self::SESSION_SUCCESS][$key]) ) {
			if( $stream ) {
				startReportStream($stream);
			}
			foreach( $_SESSION[self::SESSION_SUCCESS][$key] as $report ) {
				reportSuccess($report);
			}
			if( $stream ) {
				endReportStream();
			}
		}
		unset($_SESSION[self::SESSION_SUCCESS][$key]);
	}
	
	public function prepare($request) {
		$this->formToken = new FormToken();
		
		parent::prepare($request);
	}
	
	public function validateFormToken(HttpRequest $request) {
		if( $request->hasData() ) {
			$this->formToken->validateForm($request);
		}
	}
	
	/**
	 * @param HttpRequest $request
	 * @return OutputResponse|void|null
	 * @throws Exception
	 */
	public function preRun($request): ?OutputResponse {
		$this->currentUser = User::getLoggedUser();
		HtmlRendering::setDefaultTheme('admin');
		
		return null;
	}
	
	/**
	 * @return string
	 */
	public function getScope(): string {
		return $this->scope;
	}
	
	public function render($response, $layout, $values = []): OutputResponse {
		$values['currentUser'] = $this->currentUser;
		//		$values['hasAdminPermissions'] = $this->hasProjectAdminPermissions();
		if( $this->formToken ) {
			$values['formToken'] = $this->formToken;
		}
		
		return parent::render($response, $layout, $values);
	}
	
	//	public function hasProjectAdminPermissions() {
	//		return $this->currentUser && $this->currentUser->accesslevel;
	//	}
}
