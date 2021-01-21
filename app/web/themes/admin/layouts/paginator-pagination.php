<?php

use Orpheus\Publisher\Paginator;

/**
 * @var Paginator $paginator
 */

$currentPage = $paginator->getPage();
$firstPage = 0;
$lastPage = $paginator->getLastPage();

if( $lastPage < 1 ) {
	// If only one page, we don't display the paginator
	return;
}

define('STATE_NONE', 0);
define('STATE_DISABLED', 1);
define('STATE_ACTIVE', 2);

if( !function_exists('showPageItem') ) {
	function showPageItem($paginator, $page, $state, $label = null, $icon = null) {
		$class = '';
		if( $state === STATE_DISABLED ) {
			$class = ' disabled';
		} elseif( $state === STATE_ACTIVE ) {
			$class = ' active';
		}
		if( $label === null ) {
			$label = $page + 1;
		}
		?>
		<li class="page-item<?php echo $class; ?>" title="<?php echo $label; ?>">
			<?php
			if( $state !== STATE_DISABLED ) {
				$attr = $label && $icon ? ' aria-label="' . $label . '"' : '';
				echo <<<TAG
		<a class="page-link" href="{$paginator->getPageLink($page)}"{$attr}>
TAG;
			} else {
				echo <<<TAG
		<span class="page-link">
TAG;
			}
			if( $icon ) {
				?>
				<span aria-hidden="true"><i class="fa fa-<?php echo $icon; ?>"></i></span>
				<span class="sr-only"><?php echo $label; ?></span>
				<?php
			} else {
				echo $label;
			}
			if( $state !== STATE_DISABLED ) {
				echo '</a>';
			} else {
				echo '</span>';
			}
			?>
		</li>
		<?php
	}
}

?>
<nav aria-label="Naviguer à travers les résultats" class="text-center">
	<ul class="pagination justify-content-center">
		<?php
		showPageItem($paginator, $firstPage, $currentPage === $firstPage ? STATE_DISABLED : STATE_NONE, 'Première', 'angle-double-left');
		showPageItem($paginator, $currentPage - 1, $currentPage === $firstPage ? STATE_DISABLED : STATE_NONE, 'Précédente', 'angle-left');
		if( $paginator->getMinorPage() > $firstPage ) {
			showPageItem($paginator, null, STATE_DISABLED, '…');
		}
		for( $page = $paginator->getMinorPage(); $page <= $paginator->getMajorPage(); $page++ ) {
			showPageItem($paginator, $page, $currentPage === $page ? STATE_ACTIVE : STATE_NONE);
		}
		if( $paginator->getMajorPage() < $lastPage ) {
			showPageItem($paginator, null, STATE_DISABLED, '…');
		}
		showPageItem($paginator, $currentPage + 1, $currentPage === $lastPage ? STATE_DISABLED : STATE_NONE, 'Suivante', 'angle-right');
		showPageItem($paginator, $lastPage, $currentPage === $lastPage ? STATE_DISABLED : STATE_NONE, 'Dernière', 'angle-double-right');
		?>
	</ul>
</nav>

