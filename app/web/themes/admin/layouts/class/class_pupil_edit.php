<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 *
 * @var HtmlRendering $rendering
 * @var HttpController $controller
 * @var HttpRequest $request
 * @var HttpRoute $route
 * @var string $CONTROLLER_OUTPUT
 * @var string $content
 * @var FormToken $formToken
 *
 * @var bool $readOnly
 * @var SchoolClass $class
 * @var ClassPupil $pupil
 * @var Person $person
 * @var PupilSkill[] $pupilSkills
 * @var ClassPupil[] $classPupils
 */

use App\Entity\ClassPupil;
use App\Entity\LearningCategory;
use App\Entity\Person;
use App\Entity\PupilSkill;
use App\Entity\SchoolClass;
use Orpheus\Form\FormToken;
use Orpheus\InputController\HttpController\HttpController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpRoute;
use Orpheus\Rendering\HtmlRendering;

global $formData;

$formData['person'] = $person->all;

$rendering->useLayout('layout.full-width');
$rendering->addThemeCssFile('class_pupil_edit.css');
$rendering->addThemeJsFile('class_pupil_edit.js');

?>
<div class="row">
	
	<div class="col-12 col-xl-6">
		
		<form method="post">
		<?php
		$rendering->useLayout('panel-default');
		$this->display('reports-bootstrap3', ['stream' => 'pupilSkillsUpdate']);
		if( $readOnly ) {
			?>
			<div class="alert alert-info">
				Vous regardez actuellement une fiche de l'historique de l'élève, il ne vous est pas possible de l'éditer.
			</div>
			<?php
		}
		?>
		<div class="table-responsive" style="height: 600px;">
			<table id="TablePupilSkillList" class="table table-striped table-bordered" data-readonly="<?php echo $readOnly ? 'true' : 'false'; ?>">
				<thead>
				<tr>
					<th scope="col" style="width:1%;"><?php echo t('idColumn'); ?></th>
					<th scope="col" class="text-nowrap"><?php echo t('name', DOMAIN_LEARNING_SKILL); ?></th>
					<th scope="col" class="text-nowrap"><?php echo t('date', DOMAIN_LEARNING_SKILL); ?></th>
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
								<button class="btn action-value-edit font-weight-bold pupil-skill-date status-accepted" type="button"
										title="Cliquez pour modifier la date"></button>
								<div class="status-not-accepted text-center">-</div>
							</td>
							<td>
								<button class="btn action-value-edit font-weight-bold pupil-skill-value skill-valuable" type="button"
										title="Cliquez pour modifier la valeur et voir l'historique"
								></button>
								<div class="skill-not-valuable text-center">-</div>
							</td>
							<td class="item-actions text-right">
								<div class="form"></div>
								<button class="btn action-skill-reject status-accepted" type="button">
									<i class="fa-solid fa-check-square text-success fa-2x"></i>
								</button>
								<button class="btn action-skill-accept status-not-accepted" type="button">
									<i class="fa-regular fa-square text-muted fa-2x"></i>
								</button>
							</td>
						</tr>
						<?php
					}
				}
				?>
				</tbody>
			</table>
		</div>
		<?php
		$rendering->startNewBlock('footer');
		?>
		<a class="btn btn-secondary" href="<?php echo u('user_class_pupil_export', ['pupilId' => $pupil->id()]); ?>"
		   title="Télécharger sa fiche d'apprentissage">
			<i class="fa-solid fa-download"></i> <?php _t('download'); ?>
		</a>
		<?php
		if( !$readOnly ) {
			?>
			<button class="btn btn-primary" type="submit" name="submitUpdateSkills"><?php _t('save'); ?></button>
			<?php
		}
		$rendering->endCurrentLayout(['title' => t('learningSheet', DOMAIN_CLASS)]); ?>
		</form>
		
		<?php $rendering->useLayout('panel-default'); ?>
		<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
				<tr>
					<th scope="col" style="width:1%;"><?php echo t('idColumn'); ?></th>
					<th scope="col" class="text-nowrap"><?php echo t('name', DOMAIN_CLASS); ?></th>
					<th scope="col" class="text-nowrap"><?php echo t('year', DOMAIN_CLASS); ?></th>
					<th scope="col" class="text-nowrap"><?php echo t('teacher', DOMAIN_CLASS); ?></th>
					<th scope="col" class="text-nowrap"><?php echo t('actionsColumn'); ?></th>
				</tr>
				</thead>
				<tbody class="table-valign-middle">
				<?php
				foreach( $classPupils as $classPupil ) {
					$schoolClass = $classPupil->getSchoolClass();
					?>
					<tr>
						<th scope="row"><?php echo $schoolClass->id(); ?></th>
						<td><?php echo $schoolClass; ?></td>
						<td><?php echo $schoolClass->year; ?></td>
						<td><?php echo $schoolClass->getTeacher(); ?></td>
						<td class="text-right">
							<a class="btn btn-sm btn-outline-secondary"
							   href="<?php echo u('user_class_pupil_view', ['classId' => $schoolClass->id(), 'pupilId' => $classPupil->id()]); ?>">
								<i class="fa-solid fa-eye"></i>
							</a>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</div>
		<?php $rendering->endCurrentLayout(['title' => t('pupilClasses', DOMAIN_CLASS)]); ?>
	
	</div>
	
	<div class="col-12 col-xl-6">
		<form method="post">
		
		<?php
		// Pupil's Details
		$rendering->useLayout('panel-default');
		$this->display('reports-bootstrap3'); ?>
		<div class="form-group">
			<label class="form-label"><?php echo t('firstname', DOMAIN_PERSON); ?></label>
			<input <?php echo formInput('person/firstname'); ?> type="text" class="form-control person_firstname"<?php echo $readOnly ? ' disabled' : ''; ?>>
		</div>
		<div class="form-group">
			<label class="form-label"><?php echo t('lastname', DOMAIN_PERSON); ?></label>
			<input <?php echo formInput('person/lastname'); ?> type="text" class="form-control person_lastname"<?php echo $readOnly ? ' disabled' : ''; ?>>
		</div>
		<?php
		if( !$readOnly ) {
			$rendering->startNewBlock('footer');
			?>
			<button class="btn btn-primary" type="submit" name="submitUpdate"><?php _t('save'); ?></button>
			<?php
		}
		$rendering->endCurrentLayout(['title' => t('pupil_label', DOMAIN_CLASS, $person->getLabel())]);
		
		// Pupil's Class
		$rendering->useLayout('panel-default');
		$rendering->display('user/class.form', ['class' => $class, 'readOnly' => true]);
		$rendering->endCurrentLayout(['title' => t('class_label', DOMAIN_CLASS, $class->getLabel())]);
		
		// Pupil's Note to parent
		$rendering->useLayout('panel-default');
		$this->display('reports-bootstrap3'); ?>
		<div class="form-group">
			<label for="InputPupilNotePublic" class="form-label"><?php echo t('notePublic_label', DOMAIN_CLASS); ?></label>
			<textarea id="InputPupilNotePublic" name="pupil[note_public]" class="form-control textarea-auto-height" data-min-height="100"
					  rows="3"<?php echo $readOnly ? ' disabled' : ''; ?>><?php echo $pupil->note_public; ?></textarea>
		</div>
		<?php
		if( !$readOnly ) {
			$rendering->startNewBlock('footer');
			?>
			<button class="btn btn-primary" type="submit" name="submitNotePublicSave"><?php _t('save'); ?></button>
			<?php
		}
		$rendering->endCurrentLayout(['title' => t('notePublic_label', DOMAIN_CLASS)]);
		
		// Pupil's Internal Note
		$rendering->useLayout('panel-default');
		$this->display('reports-bootstrap3'); ?>
		<div class="form-group">
			<label for="InputPupilNotePrivate" class="form-label"><?php echo t('notePrivate_label', DOMAIN_CLASS); ?></label>
			<textarea id="InputPupilNotePrivate" name="pupil[note_private]" class="form-control textarea-auto-height" data-min-height="100"
					  rows="3"<?php echo $readOnly ? ' disabled' : ''; ?>><?php echo $pupil->note_private; ?></textarea>
		</div>
		<?php
		if( !$readOnly ) {
			$rendering->startNewBlock('footer');
			?>
			<button class="btn btn-primary" type="submit" name="submitNotePrivateSave"><?php _t('save'); ?></button>
			<?php
		}
		$rendering->endCurrentLayout(['title' => t('notePrivate_label', DOMAIN_CLASS)]);
		
		?>
		
		</form>
	</div>

</div>

<?php $rendering->display('component/pupil-skill-edit.dialog', ['withHistory' => true]); ?>
