<?php
/**
 * @var string $CONTROLLER_OUTPUT
 * @var HTMLRendering $rendering
 * @var AbstractHttpController $Controller
 * @var HTTPRequest $Request
 * @var HTTPRoute $Route
 * @var User $currentUser
 * @var string $Content
 */

use App\Controller\AbstractHttpController;
use App\Entity\User;
use Orpheus\Controller\Admin\AbstractAdminController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPRoute;
use Orpheus\Rendering\HTMLRendering;


global $APP_LANG;

$routeName = $Controller->getRouteName();
$user = User::getLoggedUser();

$pageTitle = $Controller->getOption(AbstractAdminController::OPTION_PAGE_TITLE, isset($pageTitle) ? $pageTitle : null);

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
	<link rel="icon" type="image/png" href="<?php echo STATIC_ASSETS_URL . '/images/icon.png'; ?>"/>
	<?php
	foreach( $this->listMetaProperties() as $property => $content ) {
		echo '
	<meta property="' . $property . '" content="' . $content . '"/>';
	}
	?>
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/css/bootstrap<?php echo $libExtension; ?>.css" media="screen"/>
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all<?php echo $libExtension; ?>.css" media="screen"/>
	
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4<?php echo $libExtension; ?>.css" media="screen"/>
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2<?php echo $libExtension; ?>.css" media="screen"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $rendering->getCssUrl(); ?>select2-bootstrap.css" media="screen"/>
	<?php
	
	foreach( $this->listCSSURLs(HTMLRendering::LINK_TYPE_PLUGIN) as $url ) {
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
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery<?php echo $libExtension; ?>.js"></script>
</head>
<body class="sb-nav-fixed">

<?php
echo $CONTROLLER_OUTPUT;
echo $Content;
?>

<!--<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>-->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/js/bootstrap<?php echo $libExtension; ?>.js"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales<?php echo $libExtension; ?>.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4<?php echo $libExtension; ?>.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.full<?php echo $libExtension; ?>.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/i18n/fr.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.28.5/js/jquery.tablesorter.js"></script>

<?php
foreach( $this->listJSURLs(HTMLRendering::LINK_TYPE_PLUGIN) as $url ) {
	echo '
	<script type="text/javascript" src="' . $url . '"></script>';
}
?>

<script type="text/javascript" src="<?php echo VENDOR_URL; ?>/sb-admin/sb-admin-6.0.2/js/scripts.js"></script>
<script src="<?php echo JS_URL; ?>/orpheus/orpheus.js"></script>
<script src="<?php echo JS_URL; ?>/orpheus/orpheus-confirmdialog.js"></script>
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
