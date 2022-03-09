<?php
/**
 * @var HtmlRendering $rendering
 * @var AbstractHttpController $controller
 * @var HttpRequest $request
 * @var HttpRoute $route
 *
 * @var string $CONTROLLER_OUTPUT
 * @var User $currentUser
 * @var string $content
 */

use App\Controller\AbstractHttpController;
use App\Entity\User;
use Orpheus\Controller\Admin\AbstractAdminController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpRoute;
use Orpheus\Rendering\HtmlRendering;


global $APP_LANG;

$routeName = $controller->getRouteName();
$user = User::getLoggedUser();

$pageTitle = $controller->getOption(AbstractAdminController::OPTION_PAGE_TITLE, isset($pageTitle) ? $pageTitle : null);

$libExtension = DEV_VERSION ? '' : '.min';

?>
<!DOCTYPE html>
<html lang="<?php echo $APP_LANG; ?>">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $pageTitle ?? t('app_name'); ?></title>
	<meta name="Description" content=""/>
	<meta name="Author" content="<?php echo AUTHORNAME; ?>"/>
	<meta name="application-name" content="<?php _t('app_name'); ?>"/>
	<meta name="msapplication-starturl" content="<?php echo u(ROUTE_HOME); ?>"/>
	<meta name="Keywords" content="projet"/>
	<meta name="Robots" content="Index, Follow"/>
	<meta name="revisit-after" content="16 days"/>
	<?php /*
	<link rel="icon" type="image/png" href="<?php echo STATIC_ASSETS_URL . '/images/icon.png'; ?>"/>
	 */ ?>
	<?php
	foreach( $this->listMetaProperties() as $property => $content ) {
		echo '
	<meta property="' . $property . '" content="' . $content . '"/>';
	}
	?>
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/css/bootstrap.min.css" media="screen"/>
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" media="screen"/>
	
	<?php /* DataTables with extensions */ ?>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowgroup/1.1.2/css/rowGroup.bootstrap4.min.css">
	<?php /* Not working well and incompatible with rowGroup
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/3.3.2/css/fixedColumns.dataTables.min.css">
 */ ?>
	
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css"
		  media="screen"/>
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" media="screen"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $rendering->getCssUrl(); ?>select2-bootstrap.css" media="screen"/>
	<?php
	
	foreach( $this->listCSSURLs(HtmlRendering::LINK_TYPE_PLUGIN) as $url ) {
		echo '
	<link rel="stylesheet" href="' . $url . '" type="text/css" media="screen" />';
	}
	?>
	
	<link rel="stylesheet" href="<?php echo VENDOR_URL; ?>/sb-admin/sb-admin-6.0.2/css/styles.css" type="text/css" media="screen"/>
	<link rel="stylesheet" href="<?php echo STYLE_URL; ?>/base.css" type="text/css" media="screen"/>
	<link rel="stylesheet" href="<?php echo $rendering->getCssUrl(); ?>theme-fix.css" type="text/css" media="screen"/>
	<link rel="stylesheet" href="<?php echo $rendering->getCssUrl(); ?>style.css" type="text/css" media="screen"/>
	<?php
	foreach( $this->listCSSURLs() as $url ) {
		echo '
	<link rel="stylesheet" href="' . $url . '" type="text/css" media="screen" />';
	}
	?>
	
	<!-- External JS libraries -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body class="sb-nav-fixed">

<?php
echo $content;
?>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/js/bootstrap.min.js"></script>

<script type="text/javascript" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.js"></script>
<?php /* Not working well and incompatible with rowGroup
<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.3.2/js/dataTables.fixedColumns.min.js"></script>
 */ ?>
<script type="text/javascript" src="https://cdn.datatables.net/rowgroup/1.1.2/js/dataTables.rowGroup.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.full.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/i18n/fr.js"></script>

<?php
foreach( $this->listJSURLs(HtmlRendering::LINK_TYPE_PLUGIN) as $url ) {
	echo '
	<script type="text/javascript" src="' . $url . '"></script>';
}
?>

<script type="text/javascript" src="<?php echo VENDOR_URL; ?>/sb-admin/sb-admin-6.0.2/js/scripts.js"></script>
<script src="<?php echo VENDOR_URL; ?>/orpheus/js/orpheus.js"></script>
<script src="<?php echo VENDOR_URL; ?>/orpheus/js/orpheus-confirmdialog.js"></script>
<script src="<?php echo VENDOR_URL; ?>/orpheus/js/dom-service.js"></script>
<script src="<?php echo $rendering->getJsUrl(); ?>orpheus.js"></script>
<script src="<?php echo $rendering->getJsUrl(); ?>script.js"></script>

<?php
foreach( $this->listJSURLs() as $url ) {
	echo '
	<script type="text/javascript" src="' . $url . '"></script>';
}
?>
</body>
</html>
