<?php


?>

<div id="DialogLearningSheetCreate" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form method="post" action="<?php echo u('user_learning_sheet_new'); ?>">
				<div class="modal-header">
					<h2 class="modal-title text-center w-100"><?php echo t('createLearningSkill_title', DOMAIN_CLASS); ?></h2>
					<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo t('close'); ?>">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p>
						Créez une nouvelle fiche de compétence pour votre classe.<br>
						Une fois créée, elle est affectée à cette classe, suite à quoi, vous pourrez définir les compétences acquises par tel ou tel élève.
					</p>
					
					<div class="form-group">
						<label class="form-label" for="InputLearningSheetName"><?php _t('name', DOMAIN_CLASS); ?></label>
						<input type="text" class="form-control" id="InputLearningSheetName" <?php echo formInput('learningSheet/name'); ?>
							   placeholder="Donnez un nom à votre nouvelle fiche">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal"><?php echo t('cancel'); ?></button>
					<button type="submit" class="btn btn-primary" name="submitLearningSheetCreate"><?php echo t('create'); ?></button>
				</div>
			</form>
		</div>
	</div>
</div>
