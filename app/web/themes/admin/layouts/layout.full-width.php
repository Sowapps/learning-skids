<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 *
 * @var HTMLRendering $rendering
 * @var HTTPController $controller
 * @var HTTPRequest $request
 * @var HTTPRoute $route
 * @var string $CONTROLLER_OUTPUT
 * @var string $content
 */

use Orpheus\Controller\Admin\AbstractAdminController;
use Orpheus\InputController\HTTPController\HTTPController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPRoute;
use Orpheus\Rendering\HTMLRendering;

$routeName = $controller->getRouteName();
$contentTitle = $controller->getOption(AbstractAdminController::OPTION_CONTENT_TITLE, isset($contentTitle) ? $contentTitle : null);
$contentLegend = $controller->getOption(AbstractAdminController::OPTION_CONTENT_LEGEND);

$rendering->useLayout('layout.admin');


$this->showMenu($controller->getOption('mainmenu', 'adminmenu'), 'menu-sidebar');
?>

<div id="layoutSidenav_content">
	<main>
		<div class="container-fluid">
			
			<div class="row">
				<div class="col-lg-12">
					<?php
					if( $contentTitle !== false ) {
						if( $contentTitle === null ) {
							$contentTitle = t($titleRoute ?? $routeName);
						}
						if( $contentLegend === null ) {
							$contentLegend = t(($titleRoute ?? $routeName) . '_legend');
						}
						?>
						<h1 class="page-header mt-4">
							<?php echo $contentTitle; ?>
							<small><?php echo $contentLegend; ?></small>
						</h1>
						<?php
					}
					if( !empty($breadcrumb) ) {
						?>
						<ol class="breadcrumb mb-4">
							<?php
							$bcLast = count($breadcrumb) - 1;
							foreach( $breadcrumb as $index => $page ) {
								if( $index >= $bcLast || empty($page->link) ) {
									?>
									<li class="breadcrumb-item active">
										<?php echo $page->label; ?>
									</li>
									<?php
								} else {
									?>
									<li class="breadcrumb-item">
										<a href="<?php echo $page->link; ?>"><?php echo $page->label; ?></a>
									</li>
									<?php
								}
							}
							?>
						</ol>
						<?php
					}
					$rendering->display('reports-bootstrap3');
					?>
				</div>
			</div>
			
			<?php
			echo $content;
			?>
		
		</div>
	</main>
	
	<?php
	if( $controller->hasNotification() ) {
		?>
		<div class="py-3 px-2" style="position: fixed; top: 56px; right: 0;">
			<?php
			foreach( $controller->getNotifications() as $notification ) {
				$style = (object) [
					'header' => null,
					'date'   => 'text-muted',
				];
				if( $notification->type === 'warning' ) {
					$style->header = 'bg-warning text-white';
					$style->date = 'text-white';
				}
				?>
				<div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
					<div class="toast-header <?php echo $style->header; ?>">
						<i class="far fa-bell mr-1"></i>
						<strong class="mr-auto"><?php echo $notification->title; ?></strong>
						<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="toast-body">
						<?php echo nl2br($notification->text); ?>
					</div>
				</div>
				<?php
			}
			?>
		</div>
		<?php
	}
	?>
</div>
