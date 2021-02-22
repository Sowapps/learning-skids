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
 *
 * @var SchoolClass $class
 * @var LearningSheet $learningSheet
 * @var Person[] $pupils
 * @var array $pupilSkills
 */

use App\Entity\LearningCategory;
use App\Entity\LearningSheet;
use App\Entity\Person;
use App\Entity\PupilSkill;
use App\Entity\SchoolClass;
use Orpheus\Form\FormToken;
use Orpheus\InputController\HTTPController\HTTPController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPRoute;
use Orpheus\Publisher\PermanentObject\PermanentObject;
use Orpheus\Rendering\HTMLRendering;

$rendering->useLayout('layout.full-width');
$rendering->addThemeJsFile('class_pupils_sheet.js');
$rendering->addThemeCssFile('class_pupils_sheet.css');

?>
<div class="row">
	
	<div class="col-12">
		<?php $rendering->useLayout('panel-default'); ?>
		
		<?php $this->display('reports-bootstrap3'); ?>
		
		<div id="TablePupilSkillListWrapper" class="loading" style="max-height: 800px;">
			<table id="TablePupilSkillList" class="table table-striped table-bordered invisible">
				<thead>
				<tr>
					<th scope="col" style="max-width: 400px;"><?php echo t('learningSkill', DOMAIN_LEARNING_SKILL); ?></th>
					<th scope="col" class="d-none"><?php echo t('learningCategory', DOMAIN_LEARNING_SKILL); ?></th>
					<?php
					foreach( $pupils as $pupilPerson ) {
						?>
						<th scope="col" class="text-vertical item-pupil-person" style="max-width: 120px;"
							data-pupil-person="<?php asJsonAttribute($pupilPerson, PermanentObject::OUTPUT_MODEL_MINIMALS); ?>">
							<?php echo $pupilPerson; ?>
						</th>
						<?php
					}
					?>
				</tr>
				</thead>
				<tbody class="table-valign-middle">
				<?php
				/** @var LearningCategory[] $categories */
				$categories = $class->getLearningSheet()->queryCategories();
				foreach( $categories as $category ) {
					foreach( $category->querySkills() as $skill ) {
						?>
						<tr class="item-skill" data-skill="<?php asJsonAttribute($skill); ?>">
							<td class="skill-name" title="Filtrer par cette compétence"><?php echo $skill; ?></td>
							<td class="d-none"><?php echo $category; ?></td>
							<?php
							/** @var PupilSkill $pupilSkill */
							foreach( $pupils as $pupilPerson ) {
								$domId = 'Input_' . $pupilPerson->id() . '_' . $skill->id();
								//									$domName = 'pupilSkill[' . $pupilPerson->id() . '][' . $skill->id() . ']';
								$pupilSkill = $pupilSkills[$pupilPerson->id()][$skill->id()] ?? null;
								?>
								<td class="item-pupil-skill p-0" data-pupil-skill="<?php if( $pupilSkill ) {
									asJsonAttribute($pupilSkill, OUTPUT_MODEL_EDITION);
								} ?>">
									<label class="label-checkbox d-block text-center p-3">
										<input type="checkbox" class="custom-control-input input-skill-accept" <?php echo $pupilSkill ? 'checked' : ''; ?>>
										<i class="fas fa-check-square fa-2x text-success checked"></i>
										<i class="far fa-square fa-2x text-muted unchecked"></i>
										<div class="pupil-skill_value skill-valuable action-value-edit pt-1 font-weight-bold"></div>
									</label>
								</td>
								<?php
							}
							?>
						</tr>
						<?php
					}
				}
				?>
				</tbody>
			</table>
		</div>
		
		<form method="post">
			<div id="FormPupilSkills" class="form"></div>
			<?php $rendering->startNewBlock('footer'); ?>
			<button class="btn btn-primary" type="submit" name="submitUpdateSkills"><?php _t('save'); ?></button>
			<?php $rendering->endCurrentLayout(['title' => t('learningSheet', DOMAIN_CLASS)]); ?>
		</form>
	</div>

</div>

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
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary action-cancel" data-dismiss="modal"><?php echo t('cancel'); ?></button>
				<button type="button" class="btn btn-primary action-accept" id="ButtonPupilSkillSave"><?php echo t('add'); ?></button>
			</div>
		</form>
	</div>
</div>
