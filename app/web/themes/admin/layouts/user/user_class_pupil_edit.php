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
 */

use App\Entity\ClassPupil;
use App\Entity\Person;
use App\Entity\SchoolClass;
use Orpheus\Form\FormToken;
use Orpheus\InputController\HTTPController\HTTPController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPRoute;
use Orpheus\Rendering\HTMLRendering;

global $formData;

$formData['person'] = $person->all;

$rendering->useLayout('layout.full-width');

?>
<div class="row">
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
