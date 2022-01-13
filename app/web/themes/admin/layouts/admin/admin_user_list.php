<?php

use App\Entity\User;
use Orpheus\Controller\Admin\AbstractAdminController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpRoute;
use Orpheus\Rendering\HTMLRendering;
use Orpheus\SqlRequest\SqlSelectRequest;

/**
 * @var HTMLRendering $rendering
 * @var AbstractAdminController $controller
 * @var HttpRequest $Request
 * @var HttpRoute $Route
 *
 * @var boolean $allowCreate
 * @var boolean $allowUpdate
 * @var SqlSelectRequest $users
 */

$rendering->useLayout('layout.full-width');
?>
	<form method="POST">
		
		<div class="row">
			<div class="col-lg-12">
				<?php $rendering->useLayout('panel-default'); ?>
				
				<?php
				if( $allowCreate ) {
					?>
					<div class="btn-group mb-3" role="group" aria-label="<?php _t('actionsColumn'); ?>">
						<button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#AddUserDialog">
							<i class="fa fa-plus"></i> <?php _t('new'); ?>
						</button>
					</div>
					<?php
				}
				?>
				<table class="table table-bordered table-hover">
					<thead>
					<tr>
						<th scope="col"><?php _t('idColumn'); ?></th>
						<th scope="col"><?php User::_text('name'); ?></th>
						<th scope="col"><?php User::_text('email'); ?></th>
						<th scope="col"><?php User::_text('role'); ?></th>
						<th scope="col"><?php _t('actionsColumn'); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php
					/* @ar $user User */
					foreach( $users as $user ) {
						?>
						<tr>
							<th scope="row"><?php echo $user->id(); ?></th>
							<td><?php renderUserLink($user); ?></td>
							<td><?php echo $user->email; ?></td>
							<td><?php echo $user->getRoleText(); ?></td>
							<td><?php
								if( $allowUpdate ) {
									?>
									<div class="btn-group btn-group-sm" role="group" aria-label="<?php echo t('actionsColumn'); ?>">
										<a href="<?php echo $user->getAdminLink(); ?>" class="btn btn-success btn-sm">
											<i class="fa fa-edit"></i>
										</a>
									</div>
									<?php
								}
								?>
							</td>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
				
				<?php $rendering->endCurrentLayout(); ?>
			</div>
		</div>
	</form>

<?php
if( $allowCreate ) {
	?>
	<div id="AddUserDialog" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="POST">
					
					<div class="modal-header">
						<h4 class="modal-title"><?php User::_text('addUser'); ?></h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="<?php _t('close'); ?>"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<p class="help-block"><?php User::_text('addUser_lead'); ?></p>
						<div class="form-group">
							<label><?php User::_text('name'); ?></label>
							<input class="form-control" type="text" name="user[fullname]" <?php echo htmlValue('fullname'); ?>/>
						</div>
						<div class="form-group">
							<label><?php User::_text('email'); ?></label>
							<input class="form-control" type="text" name="user[email]" <?php echo htmlValue('email'); ?> autocomplete="off">
						</div>
						<div class="form-group">
							<label><?php User::_text('password'); ?></label>
							<input class="form-control" type="password" name="user[password]" autocomplete="off">
						</div>
						<div class="form-group">
							<label><?php User::_text('confirmPassword'); ?></label>
							<input class="form-control" type="password" name="user[password_conf]">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-outline-secondary" data-dismiss="modal"><?php _t('cancel'); ?></button>
						<button name="submitCreate" type="submit" class="btn btn-primary" data-submittext="<?php _t('saving'); ?>"><?php _t('add'); ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php
}

