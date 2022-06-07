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
 * @var User $currentUser
 */

use App\Entity\User;
use Orpheus\Form\FormToken;
use Orpheus\InputController\HttpController\HttpController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpRoute;
use Orpheus\Rendering\HtmlRendering;

$rendering->useLayout('layout.full-width');
?>
<div class="row">
	<div class="col-12 col-xl-6">
		<div class="alert alert-info">
			Bienvenue sur Learning Skids,<br>
			Pour chacune de vos classes de maternelle, vous pouvez gérer vos élèves et les compétences qu'ils ont acquis.<br>
			L'objectif de Learning Skids est de vous permettre de faire le suivi de vos élèves et d'obtenir chaque semestre un rendu de leur évaluation
			positive.<br>
			Cette humble application a été développée par <a href="https://sowapps.com" target="_blank">Florent HAZARD</a>.
		</div>
	</div>
</div>

<div class="row">
	
	<?php
	$activeClass = $currentUser->getLastActiveClass();
	if( $activeClass ) {
		?>
		<div class="col-md-6 col-xl-3">
			<?php $rendering->useLayout('panel-default'); ?>
			
			<?php echo 'Ma classe ' . $activeClass->getLabel(); ?>
			
			<?php $rendering->startNewBlock('footer'); ?>
			<a class="small text-white stretched-link" href="<?php echo u('user_class_edit', ['classId' => $activeClass->id()]); ?>">
				<?php _t('manage'); ?>
			</a>
			<div class="small text-white">
				<i class="fas fa-chevron-right"></i>
			</div>
			<?php
			$rendering->endCurrentLayout([
				'panelClass'  => 'bg-primary text-white',
				'footerClass' => 'd-flex align-items-center justify-content-between',
			]);
			?>
		</div>
		<?php
	} else {
		?>
		<div class="col-md-6 col-xl-3">
			<?php $rendering->useLayout('panel-default'); ?>
			
			Créez votre première classe !
			
			<?php $rendering->startNewBlock('footer'); ?>
			<a class="small text-white stretched-link" href="<?php echo u('user_class_new'); ?>">
				<?php _t('create'); ?>
			</a>
			<div class="small text-white">
				<i class="fas fa-chevron-right"></i>
			</div>
			<?php
			$rendering->endCurrentLayout([
				'panelClass'  => 'bg-success text-white',
				'footerClass' => 'd-flex align-items-center justify-content-between',
			]);
			?>
		</div>
		<?php
	}
	?>

</div>
