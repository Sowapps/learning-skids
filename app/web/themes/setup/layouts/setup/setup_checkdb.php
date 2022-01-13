<?php

use Orpheus\InputController\HttpController\HttpController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpRoute;
use Orpheus\Rendering\HTMLRendering;

/**
 * @var string $CONTROLLER_OUTPUT
 * @var HTMLRendering $rendering
 * @var HttpController $controller
 * @var HttpRequest $request
 * @var HttpRoute $route
 *
 * @var object $DB_SETTINGS
 */

$rendering->useLayout('page_skeleton');

?>
<form method="POST">
	<div class="row">
		
		<div class="col-lg-10 offset-lg-1">
			
			<h1><?php _t('checkdb_title', DOMAIN_SETUP, t('app_name')); ?></h1>
			<p class="lead"><?php echo text2HTML(t('checkdb_description', DOMAIN_SETUP, ['APP_NAME' => t('app_name')])); ?></p>
			
			<?php renderReadonlyInputHtml(t('db_host', DOMAIN_SETUP), $DB_SETTINGS->host); ?>
			<?php renderReadonlyInputHtml(t('db_driver', DOMAIN_SETUP), $DB_SETTINGS->driver); ?>
			<?php renderReadonlyInputHtml(t('db_database', DOMAIN_SETUP), $DB_SETTINGS->dbname); ?>
			<?php renderReadonlyInputHtml(t('db_user', DOMAIN_SETUP), $DB_SETTINGS->user); ?>
			<?php renderReadonlyInputHtml(t('db_password', DOMAIN_SETUP), '*********'); ?>
			<?php
			unset($DB_SETTINGS);
			
			$this->display('reports');
			
			if( $allowContinue ) {
				?>
				<p><a class="btn btn-lg btn-primary" href="<?php _u('setup_installdb'); ?>" role="button"><?php _t('continue', DOMAIN_SETUP); ?></a></p>
				<?php
			}
			?>
		
		</div>
	
	</div>
</form>
