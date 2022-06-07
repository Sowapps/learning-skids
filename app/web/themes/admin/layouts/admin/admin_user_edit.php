<?php

use App\Entity\User;
use Orpheus\Controller\Admin\AbstractAdminController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpRoute;
use Orpheus\Rendering\HtmlRendering;

/**
 * @var HtmlRendering $rendering
 * @var AbstractAdminController $controller
 * @var HttpRequest $request
 * @var HttpRoute $route
 *
 * @var boolean $allowUserUpdate
 * @var boolean $allowUserPasswordChange
 * @var boolean $allowUserDelete
 * @var boolean $allowUserGrant
 * @var boolean $allowImpersonate
 * @var User $user
 */

// TODO Replace readonly content by bs readonly plaintext input

$rendering->useLayout('layout.full-width');
//$rendering->addJSFile('jquery.fileDownload.js');

?>

<div class="row">
	
	<div class="col-lg-6">
		<form method="POST">
		
		<div style="display: none;">
			<input type="text" autocomplete="new-password"/>
				<input type="password" autocomplete="new-password"/>
			</div>
			<?php $rendering->useLayout('panel-default'); ?>
			
			<div class="form-group">
				<label><?php User::_text('name'); ?></label>
				<?php
				if( $allowUserUpdate ) {
					_adm_htmlTextInput('user/fullname', 'form-control');
				} else {
					?><p class="form-control-static"><?php echo $user; ?></p><?php
				}
				?>
			</div>
			<div class="form-group">
				<label><?php User::_text('email'); ?></label>
				<?php
				if( $allowUserUpdate ) {
					_adm_htmlTextInput('user/email', 'form-control', 'autocomplete="new-password"');
				} else {
					?><p class="form-control-static"><?php echo $user->email; ?></p><?php
				}
				?>
			</div>
			<div class="form-group">
				<label><?php User::_text('password'); ?></label>
				<?php
				if( $allowUserPasswordChange ) {
					_adm_htmlPassword('user/password', '', 'autocomplete="new-password" placeholder="' . User::text('fillToUpdate') . '"');
				} else {
					?><p class="form-control-static">Contactez un administrateur pour changer le mot de passe de cet utilisateur.</p><?php
				}
				?>
			</div>
			<div class="form-group">
				<label>Dernière activité</label>
				<p class="form-control-static"><?php echo dt($user->activity_date); ?></p>
			</div>
			<?php
			
			$rendering->startNewBlock('footer');
			
			if( $allowImpersonate ) {
				?>
				<button class="btn btn-secondary" type="submit" name="submitImpersonate">
					<i class="fas fa-user-secret mr-1"></i> <?php echo User::text('impersonate'); ?>
				</button>
				<?php
			}
			?>
			<button class="btn btn-primary" type="submit" name="submitUpdate">
				<i class="fas fa-save mr-1"></i> <?php echo t('save'); ?>
			</button>
			<?php
			
			$rendering->endCurrentLayout([
				'title' => User::text('editUser'),
			]);
			?>
		
		</form>
	</div>

</div>
