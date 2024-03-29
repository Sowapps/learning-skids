<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 *
 * @var HtmlRendering $rendering
 * @var SchoolClass $class
 * @var User $currentUser
 */

use App\Entity\SchoolClass;
use App\Entity\User;
use Orpheus\Rendering\HtmlRendering;

$openDateDefault = (new DateTime('first monday of september'))->format('c');
$class ??= null;
$isNew = !$class;
$readOnly ??= false;
global $formData;

if( !$isNew ) {
	$formData['class'] = $class->all;
}
?>
	<div class="form-group">
		<label class="form-label" for="InputClassLevel"><?php _t('level', DOMAIN_CLASS); ?></label>
		<select name="class[level]" class="select2" id="InputClassLevel"<?php echo $readOnly ? ' disabled' : ''; ?>>
			<?php echo htmlOptions('class/level', SchoolClass::getAllLevels(), null, OPT_VALUE, 'level_', DOMAIN_CLASS); ?>
		</select>
	</div>
	
	<div class="form-group">
		<label class="form-label" for="InputClassYear"><?php _t('year', DOMAIN_CLASS); ?></label>
		<input type="text" class="form-control" id="InputClassYear" <?php echo formInput('class/year'); ?><?php echo $readOnly ? ' disabled' : ''; ?>>
	</div>
	
	<div class="form-group">
		<label class="form-label" for="InputClassName"><?php _t('name', DOMAIN_CLASS); ?></label>
		<input type="text" class="form-control" id="InputClassName" <?php echo formInput('class/name'); ?><?php echo $readOnly ? ' disabled' : ''; ?>>
	</div>
	
	<div class="form-group">
		<label class="form-label" for="InputClassOpenDate"><?php _t('openDate', DOMAIN_CLASS); ?></label>
		<div class="input-group datepicker" data-target-input="nearest" id="InputClassOpenDateWrapper" data-default-date="<?php echo $openDateDefault; ?>">
			<input type="text" class="form-control datetimepicker-input" id="InputClassOpenDate"
				   data-target="#InputClassOpenDateWrapper" <?php echo formInput('class/openDate'); ?><?php echo $readOnly ? ' disabled' : ''; ?>>
			<div class="input-group-append" data-target="#InputClassOpenDateWrapper" data-toggle="datetimepicker"<?php echo $readOnly ? ' disabled' : ''; ?>>
				<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<label class="form-label" for="InputClassLearningSheet"><?php _t('learningSheet', DOMAIN_CLASS); ?></label>
		<select name="class[learning_sheet_id]" class="select2" id="InputClassLearningSheet"<?php echo $readOnly ? ' disabled' : ''; ?>>
			<?php echo htmlOptions('class/learning_sheet_id', $currentUser->queryLearningSheets(null, true), null, OPT_PERMANENTOBJECT); ?>
			<option value="new"<?php echo $isNew || !$class->learning_sheet_id ? ' selected' : ''; ?>>Nouvelle fiche</option>
		</select>
	</div>

<?php

if( !$isNew ) {
	$teacher = $class->getTeacher();
	?>
	<div class="form-group">
		<label class="form-label" for="InputClassTeacher"><?php _t('teacher', DOMAIN_CLASS); ?></label>
		<input type="text" class="form-control-plaintext" disabled id="InputClassTeacher"<?php echo $readOnly ? ' disabled' : ''; ?>
			   value="<?php echo $class->getTeacher() . ($teacher->equals($currentUser->getPerson()) ? ' (vous)' : ''); ?>">
	</div>
	<?php
}

