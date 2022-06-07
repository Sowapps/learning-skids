<?php

use App\Entity\ClassPupil;
use App\Entity\LearningSheet;
use App\Entity\Person;
use App\Entity\SchoolClass;
use Orpheus\Rendering\HtmlRendering;

/**
 * @var HtmlRendering $rendering
 * @var string $content
 *
 * @var string $title
 * @var string $onLoad
 *
 * @var ClassPupil $pupil
 * @var Person $person
 * @var SchoolClass $class
 * @var LearningSheet $learningSheet
 */

if( !isset($onLoad) ) {
	$onLoad = null;
}

$rendering->addThemeCssFile('style.css');

?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	
	<title><?php echo $title; ?></title>
	
	<?php
	foreach( $rendering->listMetaProperties() as $property => $content ) {
		?>
		<meta property="<?php echo $property; ?>" content="<?php echo $content; ?>"/>
		<?php
	}
	
	foreach( $rendering->listCssUrls(HtmlRendering::LINK_TYPE_PLUGIN) as $url ) {
		?>
		<link rel="stylesheet" href="<?php echo $url; ?>" type="text/css" media="screen"/>
		<?php
	}
	
	foreach( $rendering->listCssUrls() as $url ) {
		?>
		<link rel="stylesheet" href="<?php echo $url; ?>" type="text/css" media="screen"/>
		<?php
	}
	?>
</head>
<body<?php echo $onLoad ? ' onload="' . $onLoad . '"' : ''; ?>>

<div class="container container-main">
	<?php echo $content; ?>
</div>

</body>
</html>
