<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Exception;
use Orpheus\Controller\Admin\AbstractAdminController;
use Orpheus\Exception\ForbiddenException;
use Orpheus\Exception\NotFoundException;
use Orpheus\Exception\UserException;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpResponse;

class AdminUserEditController extends AbstractAdminController {
	
	/**
	 * @param HttpRequest $request The input HTTP request
	 * @return HttpResponse The output HTTP response
	 * @throws NotFoundException
	 * @throws Exception
	 */
	public function run($request): HttpResponse {
		
		/* @var User $USER */
		global $USER, $formData;
		$userDomain = User::getDomain();
		
		$user = User::load($request->getPathValue('userId'));
		
		if( !$user ) {
			User::throwNotFound();
		}
		
		$this->addRouteToBreadcrumb(ROUTE_ADM_USERS);
		$this->addThisToBreadcrumb($user->getLabel());
		$this->setContentTitle($user->getLabel());
		
		$allowUserUpdate = $USER->canUserUpdate(CRAC_CONTEXT_RESOURCE, $user);
		$allowUserPasswordChange = $USER->canUserPassword(CRAC_CONTEXT_RESOURCE, $user);
		$allowUserDelete = $USER->canUserDelete(CRAC_CONTEXT_RESOURCE, $user);
		$allowUserGrant = $USER->canUserGrant(CRAC_CONTEXT_RESOURCE, $user);
		$allowImpersonate = $USER->canUserImpersonate(CRAC_CONTEXT_RESOURCE, $user);
		
		try {
			if( $request->hasData('submitUpdate') ) {
				if( !$allowUserUpdate ) {
					throw new ForbiddenException();
				}
				$userInput = $request->getData('user');
				$userFields = ['fullname', 'email'];
				if( $allowUserPasswordChange && !empty($userInput['password']) ) {
					$userInput['password_conf'] = $userInput['password'];
					$userFields[] = 'password';
				}
				if( $allowUserGrant ) {
					$userFields[] = 'accesslevel';
				}
				$result = $user->update($userInput, $userFields);
				if( $result ) {
					reportSuccess('successUpdate', $userDomain);
				}
				unset($result, $userInput, $userFields);
				
			} elseif( $request->hasData('submitImpersonate') ) {
				$user->impersonate();
				reportSuccess(User::text('successImpersonate', $user));
			}
		} catch( UserException $e ) {
			reportError($e);
		}
		
		$formData = ['user' => $user->all];
		
		includeAdminComponents();
		
		return $this->renderHtml('admin/admin_user_edit', [
			'allowUserUpdate'         => $allowUserUpdate,
			'allowUserPasswordChange' => $allowUserPasswordChange,
			'allowUserDelete'         => $allowUserDelete,
			'allowUserGrant'          => $allowUserGrant,
			'allowImpersonate'        => $allowImpersonate,
			'user'                    => $user,
		]);
	}
	
}
