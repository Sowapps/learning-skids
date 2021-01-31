<?php

namespace App\Entity;

use DateTime;
use Orpheus\EntityDescriptor\User\AbstractUser;
use Orpheus\Exception\NotFoundException;
use Orpheus\Exception\UserException;
use Orpheus\Publisher\Fixture\FixtureInterface;
use Orpheus\SQLAdapter\SQLAdapter;
use Orpheus\SQLRequest\SQLSelectRequest;

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
	 * @param string|string[],null $role
	 * @return SQLSelectRequest
	 */
	public function queryLearningSheets($role = null): SQLSelectRequest {
		$query = LearningSheet::get()
			->join(LearningSheetUser::class, $userAlias, null, 'learning_sheet_id', true);
		if( $role ) {
			$query->where($userAlias . '.role', $role);
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
		
		return $this->canDo('class_manage') || ($learningSheetUser && $learningSheetUser->canAdminLearningSheet());
	}
	
	public function getLabel() {
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
	
	public static function getByEmail($email) {
		if( !is_email($email) ) {
			static::throwException('invalidEmail');
		}
		
		return static::get([
			'where'  => 'email LIKE ' . static::fv($email),
			'output' => SQLAdapter::OBJECT,
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
		
		return User::createAndGet($input, ['email', 'password', 'fullname', 'person_id', 'activation_code', 'published']);
	}
	
	public static function checkUserInput($uInputData, $fields = null, $ref = null, &$errCount = 0, $ignoreRequired = false) {
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
