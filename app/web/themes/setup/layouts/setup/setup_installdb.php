<?php

use Orpheus\EntityDescriptor\PermanentEntity;
use Orpheus\Form\FormToken;
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
 * @var string $resultingSQL
 * @var FormToken $FORM_TOKEN
 */

$rendering->useLayout('page_skeleton');
?>
<div class="row">
	<?php
	if( !empty($resultingSQL) ) {
		if( !empty($requireEntityValidation) ) {
			?>
			<div class="col-lg-6">
				<?php $rendering->useLayout('panel-default'); ?>
				<div class="sql_query"><?php echo $resultingSQL; ?></div>
				<form method="POST"><?php echo $FORM_TOKEN; ?>
					<?php
					foreach( POST('entities') as $entityClass => $on ) {
						echo htmlHidden('entities/' . $entityClass);
					}
					if( !empty($unknownTables) ) {
						?>
						<h3><?php _t('removeUnknownTables', DOMAIN_SETUP); ?></h3>
						<ul class="list-group">
						<?php
						foreach( $unknownTables as $table => $on ) {
							echo '
					<li class="list-group-item">
						<label class="wf">
							<input class="entitycb" type="checkbox" name="removeTable[' . $table . ']"/> ' . $table . '
						</label>
					</li>';
						}
						?></ul><?php
					}
					?>
					<button type="submit" class="btn btn-primary" name="submitGenerateSQL[<?php echo OUTPUT_APPLY; ?>]"><?php _t('apply'); ?></button>
				</form>
				<?php
				$rendering->endCurrentLayout(['title' => t('generated_sqlqueries', DOMAIN_SETUP)]);
				?>
			</div>
			<?php
		}
	}
	?>
	
	<div class="col-lg-6">
		<form method="POST" role="form" class="form-horizontal"><?php echo $FORM_TOKEN; ?>
		<?php $rendering->useLayout('panel-default'); ?>
		<button class="btn btn-info btn-sm" type="button" onclick="$('.entitycb').prop('checked', true);"><i
					class="fa-regular fa-fw fa-check-square"></i> <?php _t('checkall'); ?></button>
		<button class="btn btn-info btn-sm" type="button" onclick="$('.entitycb').prop('checked', false);"><i
					class="fa-regular fa-fw fa-square"></i> <?php _t('uncheckall'); ?></button>
		
		<ul class="list-group mt-2 mb-2">
			<?php
			foreach( PermanentEntity::listKnownEntities() as $entityClass ) {
				echo '
			<li class="list-group-item">
				<label class="wf mb-0">
					<input class="entitycb" type="checkbox" name="entities[' . $entityClass . ']"' . (!isPOST() || isPOST('entities/' . $entityClass) ? ' checked' : '')
					. ' title="' . $entityClass . '"/> ' . $entityClass . '
				</label>
			</li>';
				}
				?>
			</ul>
			
			<p>
				<button title="DO IT ! JUST DO IT !" type="submit" class="btn btn-lg <?php echo !$allowContinue && empty($resultingSQL) ? 'btn-primary' : 'btn-outline-secondary'; ?>"
						name="submitGenerateSQL[<?php echo OUTPUT_DISPLAY; ?>]">
					<?php _t('check_database', DOMAIN_SETUP); ?>
				</button>
				<?php
				if( $allowContinue ) {
					?>
					<a class="btn btn-lg btn-primary" href="<?php _u('setup_installfixtures'); ?>" role="button"><?php _t('continue', DOMAIN_SETUP); ?></a>
					<?php
				}
				?>
			</p>
			
			<?php $rendering->endCurrentLayout(['title' => 'Toutes les entités']); ?>
		</form>
	</div>

</div>
<style>
.sql_query {
	font-family: Menlo, Monaco, Consolas, "Courier New", monospace;
	font-size: 90%;
	padding: 10px 20px;
	background-color: #f7f7f9;
	border-radius: 4px;
	margin-bottom: 20px;
}
</style>
