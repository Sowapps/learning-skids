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
use Orpheus\InputController\OutputResponse;
use Orpheus\Rendering\HTMLRendering;

abstract class AbstractHttpController extends HTTPController {
	
	const SCOPE_PUBLIC = 'public';
	const SCOPE_MEMBER = 'member';
	const SCOPE_ADMIN = 'admin';
	const SCOPE_SYSTEM = 'system';
	
	protected string $scope;
	protected ?User $currentUser;
	protected ?FormToken $formToken = null;
	
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
