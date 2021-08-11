<?php

use App\Entity\User;
use Orpheus\Controller\Admin\AbstractAdminController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPRoute;
use Orpheus\Rendering\HTMLRendering;

/**
 * @var HTMLRendering $rendering
 * @var AbstractAdminController $controller
 * @var HTTPRequest $request
 * @var HTTPRoute $route
 *
 * @var boolean $allowUserUpdate
 * @var boolean $allowUserPasswordChange
 * @var boolean $allowUserDelete
 * @var boolean $allowUserGrant
 * @var boolean $allowImpersonate
 * @var User $user
 */

// TODO Replace readonly content by bs readonly plaintext input

$rendering->useLayout('page_skeleton');
$rendering->addJSFile('jquery.fileDownload.js');

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
	
	<div class="col-lg-6">
		<?php $rendering->useLayout('panel-default'); ?>
		
		<ul class="list-group">
			<li class="list-group-item">
				<?php
				if( $user->allow_contact_phone === null ) {
					?><i class="fa fa-phone text-info"></i> L'utilisateur n'a pas défini s'il accepte d'être contacté par téléphone pour un démarchage commercial.<?php
				} elseif( $user->allow_contact_phone ) {
					?><i class="fa fa-phone text-success"></i> L'utilisateur accepte d'être contacté par téléphone pour un démarchage commercial.<?php
				} else {
					?><i class="fa fa-phone text-danger"></i> L'utilisateur s'oppose au démarchage commercial par téléphone.<?php
				}
				?>
			</li>
			<li class="list-group-item">
				<?php
				if( $user->allow_contact_email === null ) {
					?><i class="fa fa-at text-info"></i> L'utilisateur n'a pas défini s'il accepte d'être contacté par email pour un démarchage commercial.<?php
				} elseif( $user->allow_contact_email ) {
					?><i class="fa fa-at text-success"></i> L'utilisateur accepte d'être contacté par email pour un démarchage commercial.<?php
				} else {
					?><i class="fa fa-at text-danger"></i> L'utilisateur s'oppose au démarchage commercial par email.<?php
				}
				?>
			</li>
		</ul>
		<?php $rendering->startNewBlock('footer'); ?>
		
		<a type="button" class="btn btn-secondary bind-download" href="<?php _u('user_record_download', ['userId' => $user->id()]); ?>">
			<i class="fas fa-file-pdf mr-1"></i> Fichier
		</a>
		<a type="button" class="btn btn-secondary bind-download" href="<?php _u('user_export_download', ['userId' => $user->id()]); ?>">
			<i class="fas fa-file-archive mr-1"></i> Export
		</a>
		
		<?php
		$rendering->endCurrentLayout([
			'title' => t('user_privacy'),
		]);
		?>
	</div>

</div>

<script type="text/javascript">
$(function () {
	$('select.agency').change(function () {
		if( $(this).val() > 0 ) {
			$(".hasagency").show();
		} else {
			$(".hasagency").hide();
		}
	});
});
</script>

