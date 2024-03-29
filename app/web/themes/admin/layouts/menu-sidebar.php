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
use Orpheus\Controller\Admin\AbstractAdminController;
use Orpheus\InputController\HttpController\HttpController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpRoute;
use Orpheus\Rendering\HtmlRendering;
use Orpheus\Rendering\Menu\MenuItem;

/**
 * @var HtmlRendering $rendering
 * @var AbstractAdminController $controller
 * @var HttpRequest $request
 * @var HttpRoute $route
 *
 * @var string $menu
 * @var MenuItem[] $items
 */

$invertedStyle = $controller->getOption('invertedStyle', 1);
$user = User::getLoggedUser();

$allowNavigationToUser = $menu !== 'user' && true;
$allowNavigationToAdmin = $menu !== 'admin' && $user->isAdminUser();
$allowNavigationToDeveloper = $menu !== 'developer' && $user->checkPerm(201);

?>
<div id="layoutSidenav_nav">
	<nav class="sb-sidenav accordion <?php echo $invertedStyle ? 'sb-sidenav-dark' : 'sb-sidenav-light'; ?>" id="sidenavAccordion">
		<div class="sb-sidenav-menu">
			
			<div class="nav menu <?php echo $menu; ?>">
				<div class="sb-sidenav-menu-heading"><?php echo t('menu_' . $menu); ?></div>
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
			
			<?php
			if( $allowNavigationToUser || $allowNavigationToAdmin || $allowNavigationToDeveloper ) {
				?>
				<div class="nav menu navigation">
					<div class="sb-sidenav-menu-heading"><?php echo t('menu_navigation'); ?></div>
					<?php
					if( $allowNavigationToUser ) {
						?>
						<a class="nav-link menu-item user_home" href="<?php echo u('user_home'); ?>">
							<?php echo t('user_home'); ?>
						</a>
						<?php
					}
					if( $allowNavigationToAdmin ) {
						?>
						<a class="nav-link menu-item admin_home" href="<?php echo u('admin_home'); ?>">
							<?php echo t('admin_home'); ?>
						</a>
						<?php
					}
					if( $allowNavigationToDeveloper ) {
						?>
						<a class="nav-link menu-item dev_home" href="<?php echo u('dev_home'); ?>">
							<?php echo t('dev_home'); ?>
						</a>
						<?php
					}
					?>
				</div>
				<?php
			}
			?>
		
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
