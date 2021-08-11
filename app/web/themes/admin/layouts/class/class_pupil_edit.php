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
$rendering->addThemeCssFile('class_pupil_edit.css');
$rendering->addThemeJsFile('class_pupil_edit.js');

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
								asJsonAttribute($pupilSkills[$skill->id()], OUTPUT_MODEL_EDITION);
							} ?>">
							<th class="status-bg" scope="row" style="width:1%;"><?php echo $skill->id(); ?></th>
							<td><?php echo $skill; ?></td>
							<td>
								<button class="btn action-value-edit font-weight-bold pupil-skill-value skill-valuable" type="button" title="Cliquez pour modifier la valeur et voir l'historique"
										style="display: none;"></button>
								<div class="skill-not-valuable text-center" style="display: none;">-</div>
							</td>
							<td class="text-right">
								<div class="form"></div>
								<button class="btn action-skill-reject status-accepted" type="button" style="display: none;">
									<i class="fas fa-check-square text-success fa-2x"></i>
								</button>
								<button class="btn action-skill-accept status-not-accepted" type="button" style="display: none;">
									<i class="far fa-square text-muted fa-2x"></i>
								</button>
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

<?php $rendering->display('component/pupil-skill-edit.dialog', ['withHistory' => true]); ?>
