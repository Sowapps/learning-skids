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
 * @var SchoolClass $class
 * @var ClassPupil $pupil
 * @var Person $person
 * @var PupilSkill[] $pupilSkills
 */

use App\Entity\ClassPupil;
use App\Entity\LearningCategory;
use App\Entity\Person;
use App\Entity\PupilSkill;
use App\Entity\SchoolClass;
use Orpheus\Form\FormToken;
use Orpheus\InputController\HTTPController\HTTPController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPRoute;
use Orpheus\Rendering\HTMLRendering;

global $formData;

$formData['person'] = $person->all;

$rendering->useLayout('layout.full-width');
$rendering->addThemeCssFile('user_class_pupil_edit.css');
$rendering->addThemeJsFile('user_class_pupil_edit.js');

?>
<div class="row">
	
	<div class="col-12 col-xl-6">
		<form method="post">
			<?php $rendering->useLayout('panel-default'); ?>
			
			<?php $this->display('reports-bootstrap3', ['stream' => 'pupilSkillsUpdate']); ?>
			
			<table id="TablePupilSkillList" class="table table-striped table-bordered">
				<thead>
				<tr>
					<th scope="col" style="width:1%;"><?php echo t('idColumn'); ?></th>
					<th scope="col" class="text-nowrap"><?php echo t('name', DOMAIN_LEARNING_SKILL); ?></th>
					<th scope="col" class="text-nowrap"><?php echo t('value', DOMAIN_LEARNING_SKILL); ?></th>
					<th scope="col" class="text-nowrap" data-orderable="false"><?php echo t('actionsColumn'); ?></th>
				</tr>
				</thead>
				<tbody class="table-valign-middle">
				<?php
				/** @var LearningCategory[] $categories */
				$categories = $class->getLearningSheet()->queryCategories();
				foreach( $categories as $category ) {
					?>
					<tr class="item-category">
						<th scope="row" class="bg-info text-white" colspan="99"><?php echo $category; ?></th>
					</tr>
					<?php
					foreach( $category->querySkills() as $skill ) {
						?>
						<tr class="item-skill" data-skill="<?php asJsonAttribute($skill); ?>"
							data-pupil-skill="<?php if( isset($pupilSkills[$skill->id()]) ) {
								asJsonAttribute($pupilSkills[$skill->id()]);
							} ?>">
							<th class="status-bg" scope="row" style="width:1%;"><?php echo $skill->id(); ?></th>
							<td><?php echo $skill; ?></td>
							<td>
								<span class="font-weight-bold pupil-skill_value skill-valuable"></span>
								<span class="skill-not-valuable" style="display: none;">-</span>
							</td>
							<td class="text-right">
								<div class="form"></div>
								<div class="btn-group btn-group-sm status-accepted" role="group" style="display: none;">
									<button class="btn btn-secondary action-value-edit" type="button">
										<i class="fas fa-edit fa-fw"></i>
									</button>
									<button class="btn btn-warning action-skill-reject" type="button">
										<i class="fas fa-times fa-fw"></i>
									</button>
								</div>
								<div class="btn-group btn-group-sm status-not-accepted" role="group" style="display: none;">
									<button class="btn btn-success action-skill-accept" type="button">
										<i class="fas fa-check fa-fw"></i>
									</button>
								</div>
							</td>
						</tr>
						<?php
					}
				}
				?>
				</tbody>
			</table>
			
			<?php $rendering->startNewBlock('footer'); ?>
			<button class="btn btn-primary" type="submit" name="submitUpdateSkills"><?php _t('save'); ?></button>
			<?php $rendering->endCurrentLayout(['title' => t('learningSheet', DOMAIN_CLASS)]); ?>
		</form>
	</div>
	
	<div class="col-12 col-xl-6">
		<form method="post">
			<?php $rendering->useLayout('panel-default'); ?>
			
			<?php $this->display('reports-bootstrap3'); ?>
			
			<div class="form-group">
				<label class="form-label"><?php echo t('firstname', DOMAIN_PERSON); ?></label>
				<input n<?php echo formInput('person/firstname'); ?> type="text" class="form-control person_firstname">
			</div>
			<div class="form-group">
				<label class="form-label"><?php echo t('lastname', DOMAIN_PERSON); ?></label>
				<input <?php echo formInput('person/lastname'); ?> type="text" class="form-control person_lastname">
			</div>
			
			<?php $rendering->startNewBlock('footer'); ?>
			<button class="btn btn-primary" type="submit" name="submitUpdate"><?php _t('save'); ?></button>
			<?php $rendering->endCurrentLayout(['title' => $person->getLabel()]); ?>
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
