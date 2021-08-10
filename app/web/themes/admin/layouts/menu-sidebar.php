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
use Orpheus\Controller\Admin\AbstractAdminController;
use Orpheus\InputController\HTTPController\HTTPController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPRoute;
use Orpheus\Rendering\HTMLRendering;
use Orpheus\Rendering\Menu\MenuItem;

/**
 * @var HTMLRendering $rendering
 * @var AbstractAdminController $controller
 * @var HTTPRequest $request
 * @var HTTPRoute $route
 *
 * @var string $menu
 * @var MenuItem[] $items
 */

$invertedStyle = $controller->getOption('invertedStyle', 1);
$user = User::getLoggedUser();

?>
<div id="layoutSidenav_nav">
	<nav class="sb-sidenav accordion <?php echo $invertedStyle ? 'sb-sidenav-dark' : 'sb-sidenav-light'; ?>" id="sidenavAccordion">
		<div class="sb-sidenav-menu">
			
			<div class="nav menu <?php echo $menu; ?>">
				<div class="sb-sidenav-menu-heading"><?php echo t($menu); ?></div>
				<?php
				foreach( $items as $item ) {
					?>
					<a class="nav-link menu-item<?php echo (isset($item->route) ? ' ' . $item->route : '') . (!empty($item->current) ? ' active' : ''); ?>" href="<?php echo $item->link; ?>">
						<?php echo $item->label; ?>
					</a>
					<?php
				}
				?>
			</div>
		
		</div>
		<div class="sb-sidenav-footer">
			<?php
			if( $user ) {
				?>
				<div class="small">Connecté en tant que:</div>
				<?php echo $user; ?> (#<?php echo $user->id(); ?>)
				<div class="small">Adresse IP:</div>
				<?php echo clientIP();
			}
			?>
		</div>
	</nav>
</div>
