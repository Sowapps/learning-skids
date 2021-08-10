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

use App\Entity\User;
use Orpheus\InputController\HTTPController\HTTPController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPRoute;
use Orpheus\Rendering\HTMLRendering;

$user = User::getLoggedUser();

$rendering->useLayout('page_skeleton');

$invertedStyle = $controller->getOption('invertedStyle', 1);
?>

<!-- Sidebar -->
<nav class="sb-topnav navbar navbar-expand <?php echo $invertedStyle ? 'navbar-dark bg-dark' : 'navbar-light bg-light'; ?>" role="navigation">
	<a class="navbar-brand" href="<?php _u(DEFAULT_ROUTE); ?>">
		<?php _t($controller->getOption('main_title', 'app_name')); ?>
	</a>
	<button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
	
	<?php
	if( $user ) {
		?>
		<ul class="navbar-nav ml-auto">
			<li class="nav-item dropdown show">
				<a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					<i class="fas fa-user fa-fw"></i>
				</a>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
					<?php /*
					<a class="dropdown-item" href="<?php _u(ROUTE_ADM_MYSETTINGS); ?>"><?php _t(ROUTE_ADM_MYSETTINGS); ?></a>
					<div class="dropdown-divider"></div>
					*/ ?>
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
