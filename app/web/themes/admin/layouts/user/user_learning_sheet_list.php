<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 *
 * @var HTMLRendering $rendering
 * @var HTTPController $controller
 * @var HTTPRequest $request
 * @var HTTPRoute $route
 * @var string $CONTROLLER_OUTPUT
 * @var string $content
 * @var FormToken $formToken
 * @var LearningSheet[] $learningSheets
 */

use App\Entity\LearningSheet;
use App\Entity\SchoolClass;
use Orpheus\Form\FormToken;
use Orpheus\InputController\HTTPController\HTTPController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPRoute;
use Orpheus\Rendering\HTMLRendering;

$rendering->useLayout('layout.full-width');
?>
<div class="row">
	<div class="col-12 col-xl-6">
		
		<?php $rendering->useLayout('panel-default'); ?>
		
		<table id="TableClassList" class="table table-striped table-bordered">
			<thead>
			<tr>
				<th scope="col" style="width:1%;"><?php echo t('idColumn'); ?></th>
				<th scope="col" class="text-nowrap"><?php echo t('name', DOMAIN_CLASS); ?></th>
				<th scope="col" class="text-nowrap"><?php echo t('level', DOMAIN_CLASS); ?></th>
				<th scope="col" class="text-nowrap" data-orderable="false"><?php echo t('actionsColumn'); ?></th>
			</tr>
			</thead>
			<tbody class="table-valign-middle">
			<?php
			foreach( $learningSheets as $learningSheet ) {
				?>
				<tr>
					<th scope="row" style="width:1%;"><?php echo $learningSheet->id(); ?></th>
					<td>
						<a href="<?php echo u('user_learning_sheet_edit', ['learningSheetId' => $learningSheet->id()]); ?>">
							<?php echo $learningSheet; ?>
						</a>
					</td>
					<td><?php echo t('level_' . $learningSheet->level, DOMAIN_CLASS); ?></td>
					<td class="text-right">
						<div class="btn-group btn-group-sm" role="group">
							<a href="<?php echo u('user_learning_sheet_edit', ['learningSheetId' => $learningSheet->id()]); ?>" class="btn btn-secondary">
								<i class="fas fa-edit"></i>
							</a>
						</div>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
		<?php $rendering->startNewBlock('footer'); ?>
		<button class="btn btn-outline-primary" type="button" data-toggle="modal" data-target="#DialogLearningSheetCreate"><?php _t('add'); ?></button>
		<?php
		$rendering->endCurrentLayout(['title' => t('user_learning_sheet_list')]);
		?>
	</div>
</div>

<div id="DialogLearningSheetCreate" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<form class="modal-content" method="post">
			<div class="modal-header">
				<h2 class="modal-title text-center w-100">Nouvelle fiche d'apprentissage</h2>
				<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo t('close'); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="form-label" for="InputLearningSheetLevel"><?php _t('level', DOMAIN_CLASS); ?></label>
					<select name="learningSheet[level]" class="select2 sheet_level" id="InputLearningSheetLevel">
						<?php echo htmlOptions('class/level', SchoolClass::getAllLevels(), null, OPT_VALUE, 'level_', DOMAIN_CLASS); ?>
					</select>
				</div>
				<div class="form-group">
					<label class="form-label" for="InputLearningSheetName"><?php _t('name', DOMAIN_CLASS); ?></label>
					<input type="text" class="form-control modal-focus sheet_name" id="InputLearningSheetName" name="learningSheet[name]"
						   placeholder="Le nom ne peut être vide" data-enter="click" data-target="#ButtonLearningSheetSave">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary" data-dismiss="modal"><?php echo t('cancel'); ?></button>
				<button type="submit" class="btn btn-primary" name="submitCreate"><?php echo t('save'); ?></button>
			</div>
		</form>
	</div>
</div>
