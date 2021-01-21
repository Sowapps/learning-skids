<?php

use App\Entity\User;
use Orpheus\Email\Email;
use Orpheus\EntityDescriptor\PermanentEntity;
use Orpheus\Publisher\Fixture\FixtureRepository;

/**
 * PHP File for the website sources
 * It's your app's library.
 *
 * Author: Your name.
 */

defifn('DOMAIN_SETUP', 'setup');

// Entities
PermanentEntity::registerEntity('App\Entity\ClassPupil');
PermanentEntity::registerEntity('App\Entity\LearningCategory');
PermanentEntity::registerEntity('App\Entity\LearningSheet');
PermanentEntity::registerEntity('App\Entity\LearningSheetClass');
PermanentEntity::registerEntity('App\Entity\LearningSkill');
PermanentEntity::registerEntity('App\Entity\Person');
PermanentEntity::registerEntity('App\Entity\PupilSkill');
PermanentEntity::registerEntity('App\Entity\SchoolClass');
PermanentEntity::registerEntity('App\Entity\User');

FixtureRepository::register('App\Entity\User');

User::setUserClass();

// Hooks

function getModuleAccess($module = null) {
	if( $module === null ) {
		$module = &$GLOBALS['Module'];
	}
	global $ACCESS;
	return !empty($ACCESS) && isset($ACCESS->$module) ? $ACCESS->$module : -2;
}

/**
 * @param User $user
 */
function sendAdminRegistrationEmail($user) {
	$SITENAME = t('app_name');
	$SITEURL = DEFAULTLINK;
	$e = new Email('Orpheus - Registration of ' . $user->fullname);
	$e->setText(<<<BODY
Hi master !

A new dude just registered on <a href="{$SITEURL}">{$SITENAME}</a>, he is named {$user} ({$user->name}) with email {$user->email}.

Your humble servant, {$SITENAME}.
BODY
	);
	
	return $e->send(ADMINEMAIL);
}


function includeHTMLAdminFeatures() {
	require_once ORPHEUSPATH . 'src/admin-form.php';
}

/**
 * Get current user home route name
 *
 * @return string
 */
function getHomeRoute() {
	$user = User::getLoggedUser();
	if( !$user ) {
		return DEFAULT_ROUTE;
	}
	
	return USER_DEFAULT_ROUTE;
}

require_once 'setup.php';
