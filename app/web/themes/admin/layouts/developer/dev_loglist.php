<?php
/**
 * @var HtmlRendering $rendering
 * @var HttpRequest $request
 * @var HttpRoute $route
 * @var HttpController $controller
 */

use Orpheus\InputController\HttpController\HttpController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpRoute;
use Orpheus\Rendering\HtmlRendering;

$rendering->useLayout('layout.full-width');

?>
<div class="panel-group">
	<?php
	
	foreach( $logs as $logID => $log ) {
		?>
		<div class="panel panel-default">
			<div class="panel-heading"><?php echo $log->label; ?></div>
			<div class="list-group">
				<?php
				$c = 0;
				foreach( $controller->listLogsOfFile($log->file) as $file ) {
					$filePath = (object) pathinfo($file);
					?>
					<a class="list-group-item log_file" href="<?php echo u(ROUTE_DEV_LOG_VIEW) . '?file=' . $filePath->basename; ?>">
						<i class="fa fa-fw log_nothover"></i>
						<i class="fa fa-fw fa-eye log_hover"></i>
						<?php _t('logItemSummary', DOMAIN_LOGS, '<strong>' . $filePath->basename . '</strong>', formatInt(filesize($file)), dt(filemtime($file))); ?>
					</a>
					<?php
					$c++;
				}
				if( !$c ) {
					?>
					<div class="list-group-item"><?php _t('no_logs', DOMAIN_LOGS); ?></div>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}
?>
</div>
<style>
.log_hover {
	display: none;
}
.log_file:hover .log_hover {
	display: inline-block;
}
.log_file:hover .log_nothover {
	display: none;
}
-->
</style>
