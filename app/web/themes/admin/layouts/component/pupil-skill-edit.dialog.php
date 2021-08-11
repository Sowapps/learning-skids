<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 *
 * @var bool $withHistory
 */

$withHistory = $withHistory ?? false;

?>

<div id="DialogPupilSkillEdit" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<form class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title text-center w-100 skill_label"></h2>
				<button type="button" class="close action-cancel" data-dismiss="modal" aria-label="<?php echo t('close'); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p>
					Une valeur est attendue pour cette compétence, au moment de générer un extrait des compétences de cet élève,
					cette valeur remplacera tout caractère # présent dans le nom de la compétence.<br>
					Par exemple: "Sait compter jusqu'à #" (valeur = 35) devient "Sait compter jusqu'à 35"<br>
					Attention donc à entrer quelque chose de cohérent avec ce qui est attendu.<br>
					Pour continuer, veuillez entrer cette valeur et pressez la touche <b>Entrée</b>.
				</p>
				<div class="form-group">
					<label class="form-label" for="InputPupilSkillValue"><?php _t('value', DOMAIN_LEARNING_SKILL); ?></label>
					<input type="text" class="form-control modal-focus" id="InputPupilSkillValue" name="pupilSkill[value]"
						   placeholder="Nouvelle valeur" data-enter="click" data-target="#ButtonPupilSkillSave">
				</div>
				<?php
				if( $withHistory ) {
					?>
					<div class="pupil-skill-history">
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
				<button type="button" class="btn btn-primary action-accept" id="ButtonPupilSkillSave"><?php echo t('add'); ?></button>
			</div>
		</form>
	</div>
</div>
