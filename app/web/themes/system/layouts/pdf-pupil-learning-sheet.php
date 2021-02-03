<?php

use App\Entity\ClassPupil;
use App\Entity\LearningSheet;
use App\Entity\Person;
use App\Entity\SchoolClass;
use Orpheus\Rendering\HTMLRendering;

/**
 * @var HTMLRendering $rendering
 * @var string $title
 *
 * @var ClassPupil $pupil
 * @var Person $person
 * @var SchoolClass $class
 * @var LearningSheet $learningSheet
 * @var string $dateText
 */

$rendering->useLayout('pdf-skeleton');

?>

<h1 class="text-center">
	Mes exploits<br>
	<small><?php echo $person; ?> - <?php echo $dateText; ?></small>
</h1>

<h2 style="margin-top: 28px;">Compétences acquises</h2>

<ul class="list-group">
	<?php
	$pupilSkills = $person->getPupilSkills($learningSheet);
	$categories = $learningSheet->queryCategories();
	
	foreach( $categories as $category ) {
		HTMLRendering::captureOutput();
		foreach( $category->querySkills() as $skill ) {
			if( !isset($pupilSkills[$skill->id()]) ) {
				continue;
			}
			?>
			<li class="list-group-item item-skill no-page-break"><?php echo $skill->formatName($pupilSkills[$skill->id()]); ?></li>
			<?php
		}
		$skillRows = HTMLRendering::endCapture();
		if( $skillRows ) {
			?>
			<li class="list-group-item item-category no-page-break bg-info text-white"><?php echo $category; ?></li>
			<?php
			echo $skillRows;
		}
	}
	
	unset($pupilSkills, $categories);
	?>
</ul>
