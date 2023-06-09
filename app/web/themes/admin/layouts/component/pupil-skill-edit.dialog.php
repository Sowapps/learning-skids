<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 *
 * @var bool $withHistory
 */

$withHistory = $withHistory ?? false;

?>

<div id="DialogPupilSkillEdit" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-center w-100 skill_label"></h5>
				<button type="button" class="close action-cancel" data-dismiss="modal" aria-label="<?php echo t('close'); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				
				<div class="mb-3">
					<label for="InputPupilSkillDate" class="form-label"><?php _t('pupil_skill_date', DOMAIN_LEARNING_SKILL); ?></label>
					<input type="text" class="form-control" id="InputPupilSkillDate" placeholder="JJ/MM/AAAA" name="pupilSkill[date]" required>
				</div>
				
				<div class="mb-3 show-if-valuable">
					<div class="form-group">
						<label class="form-label" for="InputPupilSkillValue"><?php _t('value', DOMAIN_LEARNING_SKILL); ?></label>
						<input type="text" class="form-control modal-focus" id="InputPupilSkillValue" name="pupilSkill[value]"
							   placeholder="Nouvelle valeur" data-enter="click" data-target="#ButtonPupilSkillSave" required>
						<div id="emailHelp" class="form-text fs-8">
							Une valeur est attendue pour cette compétence, au moment de générer un extrait des compétences de cet élève,
							cette valeur remplacera tout caractère # présent dans le nom de la compétence.<br>
							Par exemple : "Sait compter jusqu'à #" (valeur = 35) devient "Sait compter jusqu'à 35"<br>
							Attention donc à entrer quelque chose de cohérent avec ce qui est attendu.<br>
						</div>
					</div>
				</div>
				<?php
				if( $withHistory ) {
					?>
					<div class="pupil-skill-history show-if-valuable">
						<button class="btn btn-outline-primary btn-sm" type="button" data-toggle="collapse" data-target="#CollapseValueHistory" aria-expanded="false"
								aria-controls="CollapseValueHistory">
							Afficher l'historique
						</button>
						<div id="CollapseValueHistory" class="collapse py-2">
							<div class="table-responsive">
								<table class="table table-striped table-bordered mb-0">
									<tbody class="table-valign-middle pupil-skill-history-body">
									</tbody>
								</table>
							</div>
							<template id="TemplateValueRow">
								<tr>
									<td>{ value }</td>
									<td>{ date }</td>
								</tr>
							</template>
						</div>
					</div>
					<?php
				}
				?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary action-cancel" data-dismiss="modal"><?php echo t('cancel'); ?></button>
				<button type="button" class="btn btn-primary action-accept" id="ButtonPupilSkillSave">Valider cette compétence</button>
			</div>
		</form>
	</div>
</div>
