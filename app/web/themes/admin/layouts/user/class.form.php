<?php

use App\Entity\SchoolClass;

$openDateDefault = (new DateTime('first monday of september'))->format('c');
?>
<div class="form-group">
	<label class="form-label" for="InputClassLevel"><?php _t('level', DOMAIN_CLASS); ?></label>
	<select name="class[level]" class="select2" id="InputClassLevel">
		<?php echo htmlOptions('class/level', SchoolClass::getAllLevels(), null, OPT_VALUE, 'level_', DOMAIN_CLASS); ?>
	</select>
</div>

<div class="form-group">
	<label class="form-label" for="InputClassYear"><?php _t('year', DOMAIN_CLASS); ?></label>
	<input type="text" class="form-control" id="InputClassYear" <?php echo formInput('class/year'); ?>>
</div>

<div class="form-group">
	<label class="form-label" for="InputClassName"><?php _t('name', DOMAIN_CLASS); ?></label>
	<input type="text" class="form-control" id="InputClassName" <?php echo formInput('class/name'); ?>>
</div>

<div class="form-group">
	<label class="form-label" for="InputClassOpenDate"><?php _t('openDate', DOMAIN_CLASS); ?></label>
	<div class="input-group datepicker" data-target-input="nearest" id="InputClassOpenDateWrapper" data-default-date="<?php echo $openDateDefault; ?>">
		<input type="text" class="form-control datetimepicker-input" id="InputClassOpenDate" data-target="#InputClassOpenDateWrapper" <?php echo formInput('class/openDate'); ?>>
		<div class="input-group-append" data-target="#InputClassOpenDateWrapper" data-toggle="datetimepicker">
			<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
		</div>
	</div>
</div>

