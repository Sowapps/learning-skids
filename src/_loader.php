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
PermanentEntity::registerEntity('App\Entity\LearningSheetUser');
PermanentEntity::registerEntity('App\Entity\LearningSkill');
PermanentEntity::registerEntity('App\Entity\Person');
PermanentEntity::registerEntity('App\Entity\PupilSkill');
PermanentEntity::registerEntity('App\Entity\PupilSkillValue');
PermanentEntity::registerEntity('App\Entity\SchoolClass');
PermanentEntity::registerEntity('App\Entity\User');
PermanentEntity::registerEntity('App\Entity\User');

FixtureRepository::register('App\Entity\User');

User::setUserClass();

function asJsonAttribute(PermanentEntity $object, $model = OUTPUT_MODEL_USAGE) {
	echo htmlFormATtr($object->asArray($model));
}

function listAsArray(array $list, $model = OUTPUT_MODEL_USAGE): array {
	return array_map(function (PermanentEntity $entity) use ($model) {
		return $entity->asArray($model);
	}, $list);
}

/**
 * @param User $user
 * @return bool
 */
function sendAdminRegistrationEmail(User $user): bool {
	$appName = t('app_name');
	$appUrl = u(DEFAULT_ROUTE);
	$email = new Email($appName . ' - Inscription du professeur ' . $user->getLabel());
	$email->setHTMLBody(nl2br(<<<BODY
Yo !

Un nouveau professeur s'est inscrit sur <a href="{$appUrl}">{$appName}</a>, il s'appelle {$user} avec l'adresse email {$user->email}.

Votre humble serviteur, {$appName}.
BODY
	));
	
	return $email->send(ADMIN_EMAIL);
}

/**
 * @param User $user
 * @return bool
 */
function sendUserActivationEmail(User $user): bool {
	$appName = t('app_name');
	$appUrl = u(DEFAULT_ROUTE);
	$email = new Email($appName . ' - Activation de votre compte');
	$activationLink = u('login') . sprintf('?u=%s&ac=%s', $user->id(), $user->activation_code);
	$email->setHTMLBody(nl2br(<<<BODY
Bonjour,

Bienvenue sur {$appName}, notre site a été conçu pour vous accompagner dans l'évaluation de vos élèves de maternelle.
Votre compte a bien été enregistré mais il n'est pas encore activé.
Une fois que votre compte sera activé, vous serez connecté et vous pourrez créer et gérer votre classe.

<a href="{$activationLink}">Cliquez ici pour activer votre compte !</a>

<a href="{$appUrl}">{$appName}</a>
BODY
	));
	
	return $email->send($user->email);
}

function includeAdminComponents() {
	require_once pathOf(SRC_PATH . '/admin-form.php');
}

/**
 * Get current user home route name
 *
 * @return string
 */
function getHomeRoute(): string {
	$user = User::getLoggedUser();
	if( !$user ) {
		return DEFAULT_ROUTE;
	}
	
	return USER_DEFAULT_ROUTE;
}

function formatDateMonth(DateTime $dateTime): string {
	$month = strtolower($dateTime->format('F'));
	
	return t('month.' . $month);
}

/**
 * @param User|null $user
 */
function renderUserLink(?User $user) {
	if( $user ) {
		?>
		<a href="<?php echo $user->getAdminLink(); ?>"><?php echo $user; ?></a>
		<?php
	}
}

require_once 'setup.php';
