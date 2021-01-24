<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 *
 * @var HTMLRendering $rendering
 * @var HTTPController $Controller
 * @var HTTPRequest $Request
 * @var HTTPRoute $Route
 * @var string $CONTROLLER_OUTPUT
 * @var string $Content
 * @var FormToken $formToken
 * @var User $currentUser
 * @var SchoolClass $class
 */

use App\Entity\ClassPupil;
use App\Entity\SchoolClass;
use App\Entity\User;
use Orpheus\Form\FormToken;
use Orpheus\InputController\HTTPController\HTTPController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPRoute;
use Orpheus\Rendering\HTMLRendering;

global $formData;

$formData['class'] = $class->all;

$rendering->addThemeJsFile('user_class_edit.js');
$rendering->useLayout('layout.full-width');

$teacher = $class->getTeacher();
?>
<div class="row">
	<div class="col-12 col-xl-6">
		
		<form method="post">
			<?php $rendering->useLayout('panel-default'); ?>
			
			<?php $this->display('reports-bootstrap3'); ?>
			
			<?php $rendering->display('user/class.form'); ?>
			
			<div class="form-group">
				<label class="form-label" for="InputClassTeacher"><?php _t('teacher', DOMAIN_CLASS); ?></label>
				<input type="text" class="form-control-plaintext" disabled id="InputClassTeacher"
					   value="<?php echo $class->getTeacher() . ($teacher->equals($currentUser->getPerson()) ? ' (vous)' : ''); ?>">
			</div>
			
			<?php $rendering->startNewBlock('footer'); ?>
			<button class="btn btn-primary" type="submit" name="submitUpdate"><?php _t('save'); ?></button>
			<?php $rendering->endCurrentLayout(['title' => $class->getLabel()]); ?>
		</form>
	
	</div>
	
	<div class="col-12 col-xl-6">
		<?php $rendering->useLayout('panel-default'); ?>
		
		<?php $this->display('reports-bootstrap3', ['stream' => 'pupilList']); ?>
		
		<table id="TableClassList" class="table table-striped table-bordered">
			<thead>
			<tr>
				<th scope="col" style="width:1%;"><?php echo t('idColumn'); ?></th>
				<th scope="col" class="text-nowrap"><?php echo t('firstname', DOMAIN_PERSON); ?></th>
				<th scope="col" class="text-nowrap"><?php echo t('lastname', DOMAIN_PERSON); ?></th>
				<th scope="col" class="text-nowrap" data-orderable="false"><?php echo t('actionsColumn'); ?></th>
			</tr>
			</thead>
			<tbody class="table-valign-middle">
			<?php
			/** @var ClassPupil[] $pupils */
			$pupils = $class->queryPupils()->run();
			foreach( $pupils as $pupil ) {
				$person = $pupil->getPerson();
				?>
				<tr class="item-pupil" data-item="<?php asJsonAttribute($pupil); ?>">
					<th scope="row" style="width:1%;" title="Pupil #<?php echo $pupil->id(); ?>"><?php echo $person->id(); ?></th>
					<td><?php echo $person->firstname; ?></td>
					<td><?php echo $person->lastname; ?></td>
					<td class="text-right">
						<div class="btn-group btn-group-sm" role="group">
							<a class="btn btn-secondary" href="<?php echo u('user_class_pupil_edit', ['classId' => $class->id(), 'pupilId' => $pupil->id()]); ?>">
								<i class="fas fa-edit"></i>
							</a>
							<?php /*
							<button type="button" class="btn btn-secondary action-edit-pupil">
								<i class="fas fa-edit"></i>
							</button>
							*/ ?>
							<button type="button" class="btn btn-secondary" data-confirm_title="<?php echo t('removePupil_title', DOMAIN_CLASS); ?>"
									data-confirm_message="<?php echo t('removePupil_message', DOMAIN_CLASS, ['name' => $person->getLabel()]); ?>"
									data-confirm_submit_name="submitRemovePupil" data-confirm_submit_value="<?php echo $pupil->id(); ?>">
								<i class="fas fa-times"></i>
							</button>
						</div>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
		<?php $rendering->startNewBlock('footer'); ?>
		<button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#DialogClassPupilAdd"><?php _t('add'); ?></button>
		<?php
		$rendering->endCurrentLayout(['title' => t('user_class_pupil_list', DOMAIN_CLASS)]);
		?>
	</div>

</div>

<div id="DialogClassPupilAdd" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form method="post">
				<div class="modal-header">
					<h2 class="modal-title text-center w-100"><?php echo t('addPupil_title', DOMAIN_CLASS); ?></h2>
					<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo t('close'); ?>">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p>
						Ajoutez un ou plusieurs élèves, une entrée vide est ignorée.<br>
						Vous pouvez ajouter de nouveaux élèves au fûr et à mesure.
					</p>
					<ul class="list-unstyled list-new-class-pupil">
						<li class="template item-new-class-pupil mb-1">
							<div class="row">
								<div class="form-group col mb-0">
									<label class="sr-only"><?php echo t('firstname', DOMAIN_PERSON); ?></label>
									<input name="pupil[{{ index }}][firstname]" type="text" placeholder="<?php echo t('firstname', DOMAIN_PERSON); ?>"
										   class="form-control form-control-sm person_firstname">
								</div>
								<div class="form-group col mb-0">
									<label class="sr-only"><?php echo t('lastname', DOMAIN_PERSON); ?></label>
									<input name="pupil[{{ index }}][lastname]" type="text" placeholder="<?php echo t('lastname', DOMAIN_PERSON); ?>"
										   class="form-control form-control-sm person_lastname">
								</div>
								<div class="col-auto ml-auto">
									<button class="btn btn-outline-warning btn-sm action-item-remove" type="button">
										<i class="fas fa-times"></i>
									</button>
								</div>
							</div>
						</li>
					</ul>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal"><?php echo t('cancel'); ?></button>
					<button type="submit" class="btn btn-primary" name="submitAddMultiple"><?php echo t('add'); ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php /*

<div id="DialogClassPupilEdit" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form method="post">
				<input type="hidden" name="pupilId" class="pupil_id">
				
				<div class="modal-header">
					<h2 class="modal-title text-center w-100">Éditer <span class="pupil_label"></span></h2>
					<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo t('close'); ?>">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label><?php echo t('firstname', DOMAIN_PERSON); ?></label>
						<input name="person[firstname]" type="text" placeholder="<?php echo t('firstname', DOMAIN_PERSON); ?>" class="form-control">
					</div>
					<div class="form-group">
						<label><?php echo t('lastname', DOMAIN_PERSON); ?></label>
						<input name="person[lastname]" type="text" placeholder="<?php echo t('lastname', DOMAIN_PERSON); ?>" class="form-control">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal"><?php echo t('cancel'); ?></button>
					<button type="submit" class="btn btn-primary" name="submitUpdatePupil"><?php echo t('save'); ?></button>
				</div>
			</form>
		</div>
	</div>
</div>
 */ ?>