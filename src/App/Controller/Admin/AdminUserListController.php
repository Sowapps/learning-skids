<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\Admin;

use App\Entity\User;
use Orpheus\Config\IniConfig;
use Orpheus\Controller\Admin\AbstractAdminController;
use Orpheus\Exception\UserException;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPResponse;

class AdminUserListController extends AbstractAdminController {
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 */
	public function run($request): HttpResponse {
		
		$user = User::getLoggedUser();
		
		$this->addThisToBreadcrumb();
		
		$allowCreate = !CHECK_MODULE_ACCESS || $user->canUserCreate();
		$allowUpdate = !CHECK_MODULE_ACCESS || $user->canUserUpdate();
		$allowDevSee = !CHECK_MODULE_ACCESS || $user->canSeeDevelopers();
		
		try {
			if( $request->hasData('submitCreate') ) {
				if( !$allowCreate ) {
					throw new UserException('forbiddenOperation');
				}
				$newUser = User::create($request->getArrayData('user'));
				reportSuccess(User::text('successCreate', $newUser));
			}
		} catch( UserException $e ) {
			reportError($e);
		}
		
		$query = User::get()
			->orderby('fullname ASC');
		if( !$allowDevSee ) {
			$query->where('accesslevel', '<=', IniConfig::get('user_roles/administrator'));
		}
		
		return $this->renderHTML('admin/admin_user_list', [
			'allowCreate' => $allowCreate,
			'allowUpdate' => $allowUpdate,
			'users'       => $query,
		]);
	}
	
}
