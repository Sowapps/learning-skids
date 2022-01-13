<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 *
 * @var HTMLRendering $rendering
 * @var HttpController $controller
 * @var HttpRequest $request
 * @var HttpRoute $route
 * @var string $CONTROLLER_OUTPUT
 * @var string $content
 * @var FormToken $formToken
 */

use Orpheus\Form\FormToken;
use Orpheus\InputController\HttpController\HttpController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpRoute;
use Orpheus\Rendering\HTMLRendering;

$rendering->useLayout('layout.full-width');

?>
<div class="row">
	<div class="col-12 col-xl-6">
		
		<form method="post">
		<?php $rendering->useLayout('panel-default'); ?>
			
			<?php $rendering->display('user/class.form'); ?>
			
			<?php $rendering->startNewBlock('footer'); ?>
			<button class="btn btn-primary" type="submit" name="submitCreate"><?php _t('add'); ?></button>
			<?php $rendering->endCurrentLayout(['title' => t('user_class_create', DOMAIN_CLASS)]); ?>
		</form>
	
	</div>
</div>
