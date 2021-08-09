<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 *
 * @var HTMLRendering $rendering
 * @var HTTPController $controller
 * @var HTTPRequest $Request
 * @var HTTPRoute $Route
 * @var string $content
 * @var FormToken $formToken
 * @var User $currentUser
 * @var LearningSheet $learningSheet
 * @var SchoolClass $class
 * @var bool $allowLearningSheetUpdate
 * @var bool $allowLearningSheetRemove
 * @var bool $allowLearningSheetArchive
 * @var string[] $removeDisallowReasons
 * @var string[] $archiveDisallowReasons
 */

use App\Entity\LearningSheet;
use App\Entity\SchoolClass;
use App\Entity\User;
use Orpheus\Form\FormToken;
use Orpheus\InputController\HTTPController\HTTPController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPRoute;
use Orpheus\Rendering\HTMLRendering;

$rendering->addThemeCssFile('user_learning_sheet_edit.css');
$rendering->addThemeJsFile('user_learning_sheet_edit.js');
$rendering->useLayout('layout.full-width');

?>

<form method="POST">
	
	<?php $this->display('reports-bootstrap3'); ?>
	
	<div class="row notify-learning-sheet-save justify-content-center" style="display: none;">
		<div class="col col-lg-4">
			<div class="alert alert-success mt-3 d-flex flex-column flex-md-row justify-content-between align-items-center" role="alert">
				<span class="mb-2 mb-md-0">Pensez à sauvegarder vos modifications !</span>
				<button class="btn btn-success d-none d-md-inline-block" type="submit" name="submitSave"><?php echo t('save'); ?></button>
				<button class="btn btn-success btn-block d-md-none" type="submit" name="submitSave"><?php echo t('save'); ?></button>
			</div>
		</div>
	</div>
	
	<div class="row d-none d-lg-flex mb-1">
		<div class="col-4">
			<div class="card bg-primary text-white">
				<div class="card-body">
					<h6 class="card-title m-0"><?php echo t('learningSheet', DOMAIN_LEARNING_SKILL); ?></h6>
				</div>
			</div>
		</div>
		<div class="col-4">
			<div class="card bg-primary text-white">
				<div class="card-body">
					<h6 class="card-title m-0"><?php echo t('learningCategory', DOMAIN_LEARNING_SKILL); ?></h6>
				</div>
			</div>
		</div>
		<div class="col-4">
			<div class="card bg-primary text-white">
				<div class="card-body">
					<h6 class="card-title m-0"><?php echo t('learningSkill', DOMAIN_LEARNING_SKILL); ?></h6>
				</div>
			</div>
		</div>
	</div>
	
	<div id="LearningSheet" data-sheet-edit-dialog="#DialogLearningSheetEdit" data-category-edit-dialog="#DialogLearningCategoryEdit">
		
		<div class="row mb-1 item-category list-hide template">
			<div class="col-12 col-lg-4 mb-1 column-sheet read-only">
				<div class="form"></div>
				<div class="card">
					<div class="card-body">
						<h6 class="card-title m-0 d-flex justify-content-between align-items-center">
						<span>
							<span class="sheet_label"></span>
							(<span class="sheet_level_label"></span>)
						</span>
							<button class="btn btn-outline-primary btn-sm" type="button" data-toggle-class="read-only" data-toggle-target=".column-sheet">
								<i class="fas fa-edit show-if-read-only"></i>
								<i class="fas fa-eye show-if-not-read-only"></i>
							</button>
						</h6>
					</div>
					<div class="card-footer">
						<div class="row">
							<div class="col">
								<button class="btn btn-outline-primary btn-block btn-sm" type="button"
										data-toggle="modal" data-target="#DialogLearningSheetImport">
									<i class="fas fa-upload mr-1"></i>
									<?php echo t('import'); ?>
								</button>
							</div>
							<div class="col">
								<button class="btn btn-outline-primary btn-block btn-sm action-sheet-update" type="button">
									<i class="fas fa-edit mr-1"></i>
									<?php echo t('edit'); ?>
								</button>
							</div>
						</div>
						<div class="row mt-2">
							<div class="col">
								<?php
								if( $learningSheet->enabled ) {
									?>
									<button class="btn btn-outline-warning btn-block btn-sm" type="button" data-toggle="confirm"
										<?php
										if( $allowLearningSheetArchive ) {
											?>
											data-confirm_title="<?php echo t('archiveLearningSheet_title', DOMAIN_LEARNING_SKILL); ?>"
											data-confirm_message="<?php echo t('archiveLearningSheet_message', DOMAIN_LEARNING_SKILL); ?>"
											data-confirm_submit_name="submitArchive"
											<?php
										} else {
											echo sprintf('disabled title="%s"', implode('; ', $archiveDisallowReasons));
										}
										?>>
										<i class="fas fa-archive mr-1"></i>
										<?php echo t('archive'); ?>
									</button>
									<?php
								} else {
									?>
									<button class="btn btn-success btn-block btn-sm" type="button" data-toggle="confirm"
											data-confirm_title="<?php echo t('enableLearningSheet_title', DOMAIN_LEARNING_SKILL); ?>"
											data-confirm_message="<?php echo t('enableLearningSheet_message', DOMAIN_LEARNING_SKILL); ?>"
											data-confirm_submit_name="submitEnable">
										<i class="far fa-check-circle mr-1"></i>
										<?php echo t('unarchive'); ?>
									</button>
									<?php
								}
								?>
							</div>
							<div class="col">
								<button class="btn btn-outline-danger btn-block btn-sm" type="button" data-toggle="confirm"
									<?php
									if( $allowLearningSheetRemove ) {
										?>
										data-confirm_title="<?php echo t('removeLearningSheet_title', DOMAIN_LEARNING_SKILL); ?>"
										data-confirm_message="<?php echo t('removeLearningSheet_message', DOMAIN_LEARNING_SKILL); ?>"
										data-confirm_submit_name="submitRemove"
										<?php
									} else {
										echo sprintf('disabled title="%s"', implode('; ', $removeDisallowReasons));
									}
									?>>
									<i class="fas fa-times-circle mr-1"></i>
									<?php echo t('delete'); ?>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-12 col-md-6 col-lg-4 column-category" data-class-no-sheet="offset-lg-4">
				<div class="form"></div>
				<div class="card">
					<div class="card-body toggle-list">
						<div class="category-details">
							<h6 class="card-title m-0 d-flex justify-content-between align-items-center">
								<span class="category_label"></span>
								<i class="icon-collapse fas fa-chevron-right fa-fw"></i>
							</h6>
						</div>
						<button class="btn btn-outline-primary btn-block btn-sm action-category-add" type="button">
							<i class="fas fa-plus mr-1"></i>
							<?php echo t('add'); ?>
						</button>
					</div>
				</div>
			</div>
			
			<div class="col-12 col-md-6 col-lg-4 column-skill">
				<div class="card">
					<div class="card-body p-2">
						<ul class="list-group mb-1 list-skill">
							<li class="list-group-item item-skill template">
								<div class="form"></div>
								<span class="skill_label"></span>
							</li>
						</ul>
						<button class="btn btn-outline-primary btn-sm btn-block action-skill-add" type="button">
							<i class="fas fa-plus mr-1"></i>
							<?php echo t('add'); ?>
						</button>
					</div>
				</div>
			</div>
		</div>
	
	</div>
	
	<div class="row notify-learning-sheet-save justify-content-center" style="display: none;">
		<div class="col col-lg-4">
			<div class="alert alert-success mt-3 d-flex flex-column flex-md-row justify-content-between align-items-center" role="alert">
				<span class="mb-2 mb-md-0">Pensez à sauvegarder vos modifications !</span>
				<button class="btn btn-success d-none d-md-inline-block" type="submit" name="submitSave"><?php echo t('save'); ?></button>
				<button class="btn btn-success btn-block d-md-none" type="submit" name="submitSave"><?php echo t('save'); ?></button>
			</div>
		</div>
	</div>

</form>

<script>
$(function () {
	provideTranslations(<?php echo json_encode([
		'level_kid_low'    => t('level_kid_low', DOMAIN_CLASS),
		'level_kid_middle' => t('level_kid_middle', DOMAIN_CLASS),
		'level_kid_high'   => t('level_kid_high', DOMAIN_CLASS),
	]); ?>);
});
$(window).on('load', function () {
	$('#LearningSheet').data('learningSheet').load(<?php echo json_encode($learningSheet->getTree()); ?>);
});
</script>

<div id="DialogLearningSheetEdit" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<form class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title text-center w-100 sheet_label"></h2>
				<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo t('close'); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="form-label" for="InputLearningSheetName"><?php _t('name', DOMAIN_CLASS); ?></label>
					<input type="text" class="form-control modal-focus sheet_name" id="InputLearningSheetName" name="learningSheet[name]"
						   placeholder="Le nom ne peut être vide" data-enter="click" data-target="#ButtonLearningSheetSave">
				</div>
				<div class="form-group">
					<label class="form-label" for="InputLearningSheetLevel"><?php _t('level', DOMAIN_CLASS); ?></label>
					<select name="learningSheet[level]" class="select2 sheet_level" id="InputLearningSheetLevel">
						<?php echo htmlOptions('class/level', SchoolClass::getAllLevels(), null, OPT_VALUE, 'level_', DOMAIN_CLASS); ?>
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary" data-dismiss="modal"><?php echo t('cancel'); ?></button>
				<button type="button" class="btn btn-primary action-save" id="ButtonLearningSheetSave"><?php echo t('edit'); ?></button>
			</div>
		</form>
	</div>
</div>

<div id="DialogLearningCategoryEdit" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<form class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title text-center w-100"
					data-text-new="<?php echo t('learningCategory_new', DOMAIN_LEARNING_SKILL); ?>"
					data-text-update="<?php echo t('learningCategory_update', DOMAIN_LEARNING_SKILL); ?>"
				></h2>
				<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo t('close'); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p>
					Vous pouvez ajouter facilement de nouveaux domaines de compétences sans utiliser la souris.<br>
					Pour cela, entrez son nom et pressez la touche <b>Entrée</b>.
				</p>
				<div class="form-group">
					<label class="form-label" for="InputLearningCategoryName"><?php _t('name', DOMAIN_CLASS); ?></label>
					<input type="text" class="form-control modal-focus" id="InputLearningCategoryName" <?php echo formInput('category/name'); ?>
						   placeholder="Nom du nouveau domaine" data-enter="click" data-target="#ButtonLearningCategorySave">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary" data-dismiss="modal"><?php echo t('cancel'); ?></button>
				<button type="button" class="btn btn-primary action-save" id="ButtonLearningCategorySave"><?php echo t('add'); ?></button>
			</div>
		</form>
	</div>
</div>

<div id="DialogLearningSheetImport" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<form method="post" enctype="multipart/form-data" class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title text-center w-100">Import de la fiche d'apprentissage</h2>
				<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo t('close'); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="alert alert-danger notify-learning-sheet-save" role="alert" style="display: none;">
					Attention ! Veuillez sauvegarder vos modifications avant d'importer de nouvelles données sinon elles seront perdues !
				</div>
				<p>
					L'import ne supporte que le format CSV (Excel, séparateur <b>;</b>) et il ne peut qu'ajouter de nouvelles compétences et de nouveaux domaines.<br>
					Vous pouvez cependant y utiliser un domaine existant pour y ajouter de nouvelles compétences.<br>
					N'hésitez pas à modifier un export pour envoyer un fichier au bon format.
				</p>
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="InputImportFile" name="file" required>
					<label class="custom-file-label" for="InputImportFile">Parcourir</label>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary" data-dismiss="modal"><?php echo t('cancel'); ?></button>
				<button type="submit" class="btn btn-primary" name="submitImport"><?php echo t('import'); ?></button>
			</div>
		</form>
	</div>
</div>
