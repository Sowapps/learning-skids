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
 * @var string $title
 * @var string $dateText
 */

$rendering->useLayout('pdf-skeleton');

?>
	<script>
	function refresh() {
		var vars = {};
		var x = document.location.search.substring(1).split('&');
		for( var i in x ) {
			var z = x[i].split('=', 2);
			vars[z[0]] = unescape(z[1]);
		}
		x = ['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection'];
		for( var i in x ) {
			var y = document.getElementsByClassName('meta-' + x[i]);
			for( var j = 0; j < y.length; ++j ) {
				y[j].textContent = vars[x[i]];
			}
		}
	}
	</script>
	<footer>
		<span class="footer-note">Exploits de <?php echo $person; ?> pour sa classe de <?php echo $dateText; ?></span>
		<div class="page-number">Page <span class="meta-page"></span> / <span class="meta-topage"></span></div>
	</footer>
<?php

$rendering->endCurrentLayout([
	'onLoad' => 'refresh()',
]);
