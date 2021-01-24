<?php
/*
 * @var string $reportStream
 * @var string $stream Alias of $reportStream
 */
if( !isset($reportStream) ) {
	$reportStream = isset($stream) ? $stream : 'global';
}

foreach( getReports($reportStream) as $type => $typeReports ) {
	$type = ($type === 'error') ? 'danger' : $type;
	foreach( $typeReports as $report ) {
		$rType = $type;
		if( $rType === 'danger' && !$report['severity'] ) {
			$rType = 'warning';
		}
		echo '
<div class="alert alert-' . $rType . ' ' . $report['domain'] . ' alert-dismissable">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	' . $report['report'] . '
</div>';
	}
}

$reportStream = null;
