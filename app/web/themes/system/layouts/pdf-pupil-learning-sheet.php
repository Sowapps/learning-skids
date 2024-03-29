<?php

use App\Entity\ClassPupil;
use App\Entity\LearningSheet;
use App\Entity\Person;
use App\Entity\SchoolClass;
use Orpheus\Rendering\HtmlRendering;

/**
 * @var HtmlRendering $rendering
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
		HtmlRendering::captureOutput();
		foreach( $category->querySkills() as $skill ) {
			if( !isset($pupilSkills[$skill->id()]) ) {
				continue;
			}
			$pupilSkill = $pupilSkills[$skill->id()];
			$activeValue = $pupilSkill->getActiveValue();
			?>
			<li class="list-group-item item-skill no-page-break">
				<div class="pull-right"><?php echo d($activeValue ? $activeValue->date : $pupilSkill->date); ?></div>
				<div><?php echo $skill->formatName($pupilSkill); ?></div>
			</li>
			<?php
		}
		$skillRows = HtmlRendering::endCapture();
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
