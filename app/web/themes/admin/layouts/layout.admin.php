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
 */

use App\Entity\User;
use Orpheus\InputController\HttpController\HttpController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpRoute;
use Orpheus\Rendering\HtmlRendering;

$user = User::getLoggedUser();

$rendering->useLayout('page_skeleton');

$invertedStyle = $controller->getOption('invertedStyle', 1);
?>

<!-- Sidebar -->
<nav class="sb-topnav navbar navbar-expand <?php echo $invertedStyle ? 'navbar-dark bg-dark' : 'navbar-light bg-light'; ?>" role="navigation">
	<a class="navbar-brand" href="<?php _u(DEFAULT_ROUTE); ?>">
		<?php _t($controller->getOption('main_title', 'app_name')); ?>
	</a>
	<button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" type="button">
		<i class="fa-solid fa-bars"></i>
	</button>
	
	<?php
	if( $user ) {
		?>
		<ul class="navbar-nav ml-auto">
			<li class="nav-item dropdown show">
				<a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					<i class="fa-solid fa-user fa-fw"></i>
				</a>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
					<?php /*
					<a class="dropdown-item" href="<?php _u(ROUTE_ADM_MYSETTINGS); ?>"><?php _t(ROUTE_ADM_MYSETTINGS); ?></a>
					<div class="dropdown-divider"></div>
					*/
					if( User::isImpersonating() ) {
						?>
						<a class="dropdown-item" href="<?php _u('user_terminate_impersonate'); ?>"><?php echo User::text('terminateImpersonate'); ?></a>
						<?php
					}
					?>
					<a class="dropdown-item" href="<?php _u(ROUTE_LOGOUT); ?>"><?php _t(ROUTE_LOGOUT); ?></a>
				</div>
			</li>
		</ul>
		<?php
	}
	?>
</nav>

<div id="layoutSidenav">
	<?php echo $content; ?>
</div>
<?php
if( $controller->hasNotification() ) {
	?>
	
	<script>
	$(function () {
		$('.toast').toast({
			autohide: false
		}).toast('show');
	});
	</script>
	<?php
}
?>
