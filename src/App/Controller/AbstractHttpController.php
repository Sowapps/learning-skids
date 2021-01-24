<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller;

use App\Entity\User;
use Exception;
use Orpheus\Form\FormToken;
use Orpheus\InputController\HTTPController\HTTPController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\RedirectHTTPResponse;
use Orpheus\InputController\OutputResponse;
use Orpheus\Rendering\HTMLRendering;

abstract class AbstractHttpController extends HTTPController {
	
	const SCOPE_PUBLIC = 'public';
	const SCOPE_MEMBER = 'member';
	const SCOPE_ADMIN = 'admin';
	const SCOPE_SYSTEM = 'system';
	
	const SESSION_SUCCESS = 'SUCCESS';
	
	protected string $scope;
	protected ?User $currentUser;
	protected ?FormToken $formToken = null;
	
	public function redirectToSelf() {
		return new RedirectHTTPResponse($this->getCurrentUrl());
	}
	
	public function getCurrentUrl() {
		return $this->getRoute()->formatURL((array) $this->getRequest()->getPathValues());
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
	
	public function validateFormToken(HTTPRequest $request) {
		if( $request->hasData() ) {
			$this->formToken->validateForm($request);
		}
	}
	
	/**
	 * @param HTTPRequest $request
	 * @return OutputResponse|void|null
	 * @throws Exception
	 */
	public function preRun($request) {
		$this->currentUser = User::getLoggedUser();
		HTMLRendering::setDefaultTheme('admin');
	}
	
	/**
	 * @return string
	 */
	public function getScope() {
		return $this->scope;
	}
	
	public function render($response, $layout, $values = []) {
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
