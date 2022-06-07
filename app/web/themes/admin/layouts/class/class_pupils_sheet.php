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
 * @var string $pageUrl
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
use Orpheus\InputController\HttpController\HttpController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpRoute;
use Orpheus\Publisher\PermanentObject\PermanentObject;
use Orpheus\Rendering\HtmlRendering;

$rendering->useLayout('layout.full-width');
$rendering->addThemeJsFile('class_pupils_sheet.js');
$rendering->addThemeCssFile('class_pupils_sheet.css');

/** @var LearningCategory[] $categories */
$categories = $class->getLearningSheet()->queryCategories();
?>
<div class="row">
	
	<div class="col-12">
		<?php $rendering->useLayout('panel-default'); ?>
		
		<?php $this->display('reports-bootstrap3'); ?>
		
		<div id="TablePupilSkillListWrapper" class="loading">
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
				foreach( $categories as $category ) {
					foreach( $category->querySkills() as $skill ) {
						?>
						<tr class="item-skill" data-skill="<?php asJsonAttribute($skill); ?>" title="<?php echo $skill->getLabel(); ?>">
							<th scope="row"><a class="btn d-block text-left filter-skill" href="<?php echo sprintf('%s?skills[]=%d', $pageUrl, $skill->id()); ?>"
											   title="Filtrer par cette compétence"><?php echo $skill; ?></a></th>
							<td class="d-none"><?php echo $category; ?></td>
							<?php
							/** @var PupilSkill $pupilSkill */
							foreach( $pupils as $pupilPerson ) {
								$domId = 'Input_' . $pupilPerson->id() . '_' . $skill->id();
								$pupilSkill = $pupilSkills[$pupilPerson->id()][$skill->id()] ?? null;
								?>
								<td class="item-pupil-skill p-0" data-pupil-skill="<?php if( $pupilSkill ) {
									asJsonAttribute($pupilSkill, OUTPUT_MODEL_EDITION);
								} ?>">
									<div class="text-center p-3">
										<label class="label-checkbox action-context-edit">
											<input type="checkbox" class="custom-control-input input-skill-accept" <?php echo $pupilSkill ? 'checked' : ''; ?>>
											<i class="fas fa-check-square fa-2x text-success checked"></i>
											<i class="far fa-square fa-2x text-muted unchecked"></i>
										</label>
										<div class="pupil-skill-value skill-valuable action-value-edit pt-1 font-weight-bold"></div>
									</div>
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

<?php $rendering->display('component/pupil-skill-edit.dialog'); ?>
