<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */


$this->useLayout('page_skeleton');

$invertedStyle = $controller->getOption('invertedStyle', 1);

?>
<div id="wrapper">
	
	<!-- Sidebar -->
	<nav class="navbar <?php echo $invertedStyle ? 'navbar-inverse' : 'navbar-default'; ?> navbar-fixed-top" role="navigation">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php _u(DEFAULT_ROUTE); ?>"><?php _t($controller->getOption('main_title', 'adminpanel_title')); ?></a>
		</div>
		
		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse navbar-ex1-collapse">
			<?php
			$this->showMenu($controller->getOption('mainmenu', 'adminmenu'), 'menu-sidebar');
			?>
			
			<ul class="nav navbar-nav navbar-right navbar-user">
				<?php
				if( $user ) {
					?>
					<li class="dropdown user-dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $user; ?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="<?php _u(ROUTE_ADM_MYSETTINGS); ?>"><i class="fa fa-gear"></i> <?php _t(ROUTE_ADM_MYSETTINGS); ?></a></li>
							<li><a href="<?php _u(ROUTE_LOGOUT); ?>"><i class="fa fa-power-off"></i> <?php _t(ROUTE_LOGOUT); ?></a></li>
						</ul>
					</li>
					<?php
				}
				?>
			</ul>
		</div>
	</nav>
	
	<div id="page-wrapper">
		
		<div class="container-fluid">
			
			<div class="row">
				<div class="col-lg-12">
					<?php
					if( empty($NoContentTitle) ) {
						?>
						<h1 class="page-header"><?php echo isset($ContentTitle) ? $ContentTitle : t(isset($titleRoute) ? $titleRoute : $routeName); ?>
							<small><?php _t((isset($titleRoute) ? $titleRoute : $routeName) . '_legend'); ?></small></h1>
						<?php
					}
					if( !empty($Breadcrumb) ) {
						?>
						<ol class="breadcrumb">
							<?php
							$bcLast = count($Breadcrumb) - 1;
							foreach( $Breadcrumb as $index => $page ) {
								if( $index >= $bcLast || empty($page->link) ) {
									echo '
						<li class="active">' . $page->label . '</li>';
								} else {
									echo '
						<li><a href="' . $page->link . '">' . $page->label . '</a></li>';
								}
							}
							?>
						</ol>
						<?php
					}
					$this->display('reports-bootstrap3');
					?>
				</div>
			</div>
			
			<?php
			echo $CONTROLLER_OUTPUT;
			echo $content;
			?>
		
		</div>
	</div>

</div>
