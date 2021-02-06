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
<header>
	<span class="document-name"><?php echo $title; ?></span>
</header>
