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
 */

use Orpheus\Form\FormToken;
use Orpheus\InputController\HTTPController\HTTPController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPRoute;
use Orpheus\Rendering\HTMLRendering;

$rendering->useLayout('layout.full-width');
?>
<div class="row">
	<div class="col-12 col-xl-6">
		<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			Bienvenue sur Learning Skids,<br>
			Pour chacune de vos classes de maternel, vous pouvez gérer ses élèves et leurs compétences.<br>
			Cette humble application a été développée par <a href="https://sowapps.com" target="_blank">Florent HAZARD</a>.
		</div>
	</div>
</div>

