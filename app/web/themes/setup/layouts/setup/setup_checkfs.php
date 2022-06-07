<?php

use Orpheus\InputController\HttpController\HttpController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpRoute;
use Orpheus\Rendering\HtmlRendering;

/**
 * @var string $CONTROLLER_OUTPUT
 * @var HtmlRendering $rendering
 * @var HttpController $controller
 * @var HttpRequest $request
 * @var HttpRoute $route
 *
 * @var bool $allowContinue
 * @var array $folders
 */

$rendering->useLayout('page_skeleton');

$panelsConfig = [
	PANEL_SUCCESS => (object) [
		'cardClass'   => 'border-success',
		'headerClass' => 'bg-success text-white',
		'icon'        => 'fa-check',
	],
	PANEL_WARNING => (object) [
		'cardClass'   => 'border-danger',
		'headerClass' => 'bg-danger text-white',
		'icon'        => 'fa-times',
	],
	PANEL_DANGER  => (object) [
		'cardClass'   => 'border-warning',
		'headerClass' => 'bg-warning text-white',
		'icon'        => 'fa-times',
	],
];

function collapsiblePanelHTML($id, $title, $description, $panel, $open = 0) {
	?>
	<div class="card border-success <?php echo $panel->cardClass; ?> mb-2">
		<div class="card-header <?php echo $panel->headerClass; ?>" id="<?php echo $id; ?>Head">
			<h5 class="mb-0">
				<button class="btn btn-link wf text-left text-white" data-toggle="collapse" data-target="#<?php echo $id; ?>" aria-expanded="true" aria-controls="<?php echo $id; ?>" type="button">
					<i class="fa fa-fw <?php echo $panel->icon; ?>"></i> <?php echo $title; ?>
				</button>
			</h5>
		</div>
		<div id="<?php echo $id; ?>" class="collapse<?php echo $open ? ' show' : ''; ?>" aria-labelledby="<?php echo $id; ?>Head" data-parent="#CheckFSAccordion">
			<div class="card-body">
				<?php echo text2HTML($description); ?>
			</div>
		</div>
	</div>
	<?php
}

?>
<form method="POST">
	<div class="row">
		
		<div class="col-lg-8 offset-lg-2">
			
			<h1><?php _t('checkfs_title', DOMAIN_SETUP, t('app_name')); ?></h1>
			<p class="lead"><?php echo text2HTML(t('checkfs_description', DOMAIN_SETUP, ['APP_NAME' => t('app_name')])); ?></p>
			
			<?php renderReadonlyInputHtml(t('pathToFolder', DOMAIN_SETUP, t('folder_webaccess', DOMAIN_SETUP)), INSTANCEPATH); ?>
			<?php renderReadonlyInputHtml(t('pathToFolder', DOMAIN_SETUP, t('folder_application', DOMAIN_SETUP)), ACCESSPATH); ?>
			
			<div class="panel-group" id="CheckFSAccordion" role="tablist" aria-multiselectable="true">
				<?php
				foreach( $folders as $folder => $fi ) {
					collapsiblePanelHTML($folder, $fi->title, $fi->description, $panelsConfig[$fi->panel], $fi->open);
				}
				?>
			</div>
			
			<?php
			if( $allowContinue ) {
				?>
				<p><a class="btn btn-lg btn-primary" href="<?php _u('setup_checkdb'); ?>" role="button"><?php _t('continue', DOMAIN_SETUP); ?></a></p>
				<?php
			}
			?>
		
		</div>
	
	</div>
</form>
