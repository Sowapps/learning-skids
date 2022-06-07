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
 * @var SchoolClass $class
 * @var LearningSheet $learningSheet
 * @var Person[] $pupils
 * @var LearningSkill[] $skills
 * @var array $pupilSkills
 */

use App\Entity\LearningSheet;
use App\Entity\LearningSkill;
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

?>
<div class="row">
	
	<div class="col-12<?php echo count($skills) < 2 ? ' col-lg-6' : ''; ?>">
		<?php $rendering->useLayout('panel-default'); ?>
		
		<?php $this->display('reports-bootstrap3'); ?>
		
		<div id="TablePupilSkillListWrapper" class="loading">
			<table id="TablePupilSkillList" class="table table-striped table-bordered invisible">
				<thead>
				<tr>
					<th scope="col" style="max-width: 20rem;"><?php echo t('pupil', DOMAIN_LEARNING_SKILL); ?></th>
					<?php
					foreach( $skills as $skill ) {
						?>
						<th scope="col" class="text-vertical item-skill header-top header-skill" data-skill="<?php asJsonAttribute($skill); ?>">
							<?php echo $skill; ?>
						</th>
						<?php
					}
					?>
				</tr>
				</thead>
				<tbody class="table-valign-middle">
				<?php
				/** @var PupilSkill $pupilSkill */
				foreach( $pupils as $pupilPerson ) {
					?>
					<tr class="item-pupil-person" data-pupil-person="<?php asJsonAttribute($pupilPerson, PermanentObject::OUTPUT_MODEL_MINIMALS); ?>">
						<th scope="row" class="person-name"><?php echo $pupilPerson; ?></th>
						<?php
						foreach( $skills as $skill ) {
							$domId = 'Input_' . $pupilPerson->id() . '_' . $skill->id();
							$pupilSkill = $pupilSkills[$pupilPerson->id()][$skill->id()] ?? null;
							?>
							<td class="item-pupil-skill p-0" data-pupil-skill="<?php if( $pupilSkill ) {
								asJsonAttribute($pupilSkill, OUTPUT_MODEL_EDITION);
							} ?>" data-order="<?php echo sprintf('%s-%s', $pupilSkill ? 1 : 2, $pupilPerson); ?>">
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

<?php $rendering->display('component/pupil-skill-edit.dialog', ['withHistory' => true]); ?>
