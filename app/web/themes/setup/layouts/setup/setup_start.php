<?php
/* @var Orpheus\Rendering\HtmlRendering $this */

$rendering->useLayout('page_skeleton');

?>
<form method="POST">
	<div class="row">
		
		<div class="col-lg-10 offset-lg-1">
			
			<div class="jumbotron">
				<h1><?php _t('start_title', DOMAIN_SETUP, t('app_name')); ?></h1>
				<p class="lead"><?php echo text2HTML(t('start_description', DOMAIN_SETUP, ['APP_NAME' => t('app_name')])); ?></p>
				
				<?php
				$this->display('reports');
				?>
				<p>
					<a class="btn btn-lg btn-primary" href="<?php _u('setup_checkfs'); ?>" role="button">
						<?php _t('start_install', DOMAIN_SETUP); ?>
						<i class="fa fa-chevron-right"></i>
					</a>
				</p>
			</div>
		
		</div>
	
	</div>
</form>
