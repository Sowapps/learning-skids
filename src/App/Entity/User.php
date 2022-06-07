<?php

namespace App\Entity;

use Agency;
use DateTime;
use Exception;
use Orpheus\EntityDescriptor\User\AbstractUser;
use Orpheus\Exception\NotFoundException;
use Orpheus\Exception\UserException;
use Orpheus\Publisher\Fixture\FixtureInterface;
use Orpheus\SqlAdapter\SqlAdapter;
use Orpheus\SqlRequest\SqlSelectRequest;

/**
 * Class User
 *
 * @package App\Entity
 *
 * @property DateTime $create_date
 * @property string $create_ip
 * @property DateTime $login_date
 * @property string $login_ip
 * @property string $login_agent
 * @property DateTime $activity_date
 * @property string $activity_ip
 * @property int $accesslevel
 * @property string $recovery_code
 * @property string $activation_code
 * @property DateTime $activation_date
 * @property string $email
 * @property int $person_id
 */
class User extends AbstractUser implements FixtureInterface {
	
	protected ?Person $person = null;
	
	/**
	 * @param string|null $role
	 * @param boolean|null $enabled
	 * @return SqlSelectRequest
	 */
	public function queryLearningSheets($role = null, $enabled = null): SqlSelectRequest {
		$query = LearningSheet::get()
			->alias('learningSheet')
			->join(LearningSheetUser::class, $userAlias, null, 'learning_sheet_id', true);
		if( $role ) {
			$query->where($userAlias . '.role', $role);
		}
		if( $enabled !== null ) {
			$query->where('learningSheet.enabled', $enabled);
		}
		$query->where($userAlias . '.user_id', $this);
		
		return $query;
	}
	
	/**
	 * @return SchoolClass|null
	 */
	public function getLastActiveClass() {
		return $this->getPerson()
			->queryClasses(true)
			->orderby('id DESC')
			->number(1)
			->asObject()
			->run();
	}
	
	/**
	 * @return Person
	 * @throws NotFoundException
	 * @throws UserException
	 */
	public function getPerson(): Person {
		if( !isset($this->person) ) {
			$this->person = Person::load($this->person_id);
		}
		
		return $this->person;
	}
	
	public function canClassManage(SchoolClass $class) {
		return $this->canDo('class_manage') || $class->getTeacher()->equals($this->getPerson());
	}
	
	public function canLearningSheetAccess(LearningSheet $learningSheet, ?LearningSheetUser &$learningSheetUser) {
		$learningSheetUser = LearningSheetUser::get()
			->where('user_id', $this)
			->where('learning_sheet_id', $learningSheet)
			->asObject()->run();
		if( !$learningSheetUser ) {
			return false;
		}
		
		return $this->canDo('class_manage') || ($learningSheetUser && $learningSheetUser->canAdminLearningSheet());
	}
	
	public function getLabel(): string {
		return $this->fullname;
	}
	
	/**
	 * @return bool
	 */
	public function isAdminUser() {
		return $this->checkPerm(1);
	}
	
	public function activate() {
		$this->published = 1;
		$this->logEvent('activation');
		$this->activation_code = null;
	}
	
	public function getActivationLink() {
		return u(ROUTE_LOGIN) . '?ac=' . $this->activation_code . '&u=' . $this->id();
	}
	
	public function getAdminLink($ref = 0) {
		return u('adm_user', ['userID' => $this->id()]);
	}
	
	public function getLink() {
		return u('profile', ['userId' => $this->id()]);
	}
	
	/**
	 * @param int $context
	 * @param User $contextResource
	 * @return boolean
	 */
	public function canUserCreate($context = CRAC_CONTEXT_APPLICATION, $contextResource = null) {
		return $this->canDo('user_edit');// Only App admins can do it.
	}
	
	public function canUserView($context = CRAC_CONTEXT_APPLICATION, $contextResource = null) {
		return $this->canUserUpdate($context, $contextResource);
	}
	
	/**
	 * @param int $context
	 * @param User $contextResource
	 * @return boolean
	 */
	public function canUserUpdate($context = CRAC_CONTEXT_APPLICATION, $contextResource = null): bool {
		return $this->canDo('user_edit');// Only App admins can do it.
	}
	
	public function canSeeDevelopers($context = CRAC_CONTEXT_APPLICATION, $contextResource = null): bool {
		return $this->canDo('user_seedev');// Only App admins can do it.
	}
	
	public function canUserStatus($context = CRAC_CONTEXT_APPLICATION, $contextResource = null): bool {
		return $this->canDo('user_status');// Only App admins can do it.
	}
	
	public function canUserDelete($context = CRAC_CONTEXT_APPLICATION, $contextResource = null): bool {
		return $this->canDo('user_delete');// Only App admins can do it.
	}
	
	public function canUserGrant($context = CRAC_CONTEXT_APPLICATION, $contextResource = null): bool {
		return $this->canDo('user_grant');// Only App admins can do it.
	}
	
	public function canUserImpersonate($context = CRAC_CONTEXT_APPLICATION, $contextResource = null): bool {
		// Only App dev can do it or an user with more permission (inclusive)
		return $this->canDo('user_impersonate') && (!$contextResource || $contextResource->accesslevel <= $this->accesslevel);
	}
	
	public function canUserPassword($context = CRAC_CONTEXT_APPLICATION, $contextResource = null): bool {
		return $this->canDo('user_password');// Only App admins can do it.
	}
	
	public function getRoleText() {
		$status = array_flip($this->getAvailRoles());
		
		return isset($status[$this->accesslevel]) ? t('role_' . $status[$this->accesslevel], static::getDomain()) : t('role_unknown', static::getDomain(), $this->accesslevel);
	}
	
	public function getAvailRoles() {
		$permStatus = static::getAppRoles();
		foreach( $permStatus as $status => $accesslevel ) {
			if( !$this->checkPerm($accesslevel) ) {
				unset($permStatus[$status]);
			}
		}
		
		return $permStatus;
	}
	
	public static function getByEmail($email) {
		if( !is_email($email) ) {
			static::throwException('invalidEmail');
		}
		
		return static::get([
			'where'  => 'email LIKE ' . static::fv($email),
			'output' => SqlAdapter::OBJECT,
		]);
	}
	
	public static function make(array $input, string $role) {
		User::testUserInput($input, ['email', 'password'], null, $errors, true);
		if( $errors ) {
			User::throwException('errorCreateChecking');
		}
		$input['role'] = $role;
		$person = Person::createAndGet($input, ['firstname', 'lastname', 'role']);
		$input['activation_code'] = generateRandomString(30);
		$input['published'] = false;
		$input['person_id'] = $person->id();
		$input['fullname'] = $person->getLabel();
		
		try {
			return User::createAndGet($input, ['email', 'password', 'fullname', 'person_id', 'activation_code', 'published']);
		} catch( Exception $exception ) {
			$person->remove();
			throw $exception;
		}
	}
	
	public static function checkUserInput($uInputData, $fields = null, $ref = null, &$errCount = 0, $ignoreRequired = false): array {
		$data = parent::checkUserInput($uInputData, $fields, $ref, $errCount, $ignoreRequired);
		if( !empty($uInputData['password']) ) {
			$data['real_password'] = $uInputData['password'];
		}
		
		return $data;
	}
	
	public static function loadFixtures() {
		static::create([
			'email'         => 'contact@sowapps.com',
			'fullname'      => 'Administrateur',
			'password'      => 'admin',
			'password_conf' => 'admin',
			'accesslevel'   => 300,
			'published'     => 1,
			'timezone'      => 'Europe/Paris',
		]);
	}
	
}

User::init();
