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
 */

$rendering->useLayout('pdf-skeleton');

?>

<h1 class="text-center">
	Mes exploits<br>
	<small><?php echo $person; ?> - <?php echo $class->year ?></small>
</h1>

<h2 style="margin-top: 28px;">Compétences acquises</h2>

<ul class="list-group text-center">
	<?php
	$pupilSkills = $person->getPupilSkills($learningSheet);
	$categories = $learningSheet->queryCategories();
	
	foreach( $categories as $category ) {
		foreach( $category->querySkills() as $skill ) {
			if( !isset($pupilSkills[$skill->id()]) ) {
				continue;
			}
			?>
			<li class="list-group-item no-page-break"><?php echo $skill->formatName($pupilSkills[$skill->id()]); ?></li>
			<?php
		}
	}
	
	unset($pupilSkills, $categories);
	?>
</ul>
