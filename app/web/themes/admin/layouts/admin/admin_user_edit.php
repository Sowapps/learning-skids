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

?>

<div class="row">
	
	<div class="col-lg-6">
		<form method="POST">
		
		<?php
		$rendering->useLayout('panel-default');
		$rendering->display('reports-bootstrap3', ['stream' => 'userEdit']);
		?>
		
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
					_adm_htmlTextInput('user/email', 'form-control');
				} else {
					?><p class="form-control-static"><?php echo $user->email; ?></p><?php
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
					<i class="fa-solid fa-user-secret me-1"></i> <?php echo User::text('impersonate'); ?>
				</button>
				<?php
			}
			?>
		<button class="btn btn-primary" type="submit" name="submitUpdate">
			<i class="fa-solid fa-save me-1"></i> <?php echo t('save'); ?>
		</button>
		<?php
		
		$rendering->endCurrentLayout([
			'icon'  => 'fa-solid fa-user-pen',
			'title' => User::text('editUser'),
		]);
		?>
		
		</form>
	</div>
	
	<div class="col-lg-6">
		<form method="POST">
		
		<div style="display: none;">
			<input type="text" autocomplete="new-password"/>
			<input type="password" autocomplete="new-password"/>
		</div>
		
		<?php
		$rendering->useLayout('panel-default');
		$rendering->display('reports-bootstrap3', ['stream' => 'userEditPassword']);
		?>
		
		<div class="form-group">
			<label><?php User::_text('loginKey'); ?></label>
			<?php
			_adm_htmlTextInput('user/email', '', 'readonly');
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
		<?php
		
		$rendering->startNewBlock('footer');
		
		?>
		<button class="btn btn-primary" type="submit" name="submitUpdatePassword">
			<i class="fa-solid fa-save me-1"></i> <?php echo t('save'); ?>
		</button>
		<?php
		
		$rendering->endCurrentLayout([
			'icon'  => 'fa-solid fa-user-shield',
			'title' => User::text('editUser'),
		]);
		?>
		
		</form>
	</div>

</div>
