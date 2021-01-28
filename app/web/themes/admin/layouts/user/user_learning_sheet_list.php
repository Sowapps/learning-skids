<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 *
 * @var HTMLRendering $rendering
 * @var HTTPController $Controller
 * @var HTTPRequest $Request
 * @var HTTPRoute $Route
 * @var string $CONTROLLER_OUTPUT
 * @var string $Content
 * @var FormToken $formToken
 * @var LearningSheet[] $learningSheets
 */

use App\Entity\LearningSheet;
use Orpheus\Form\FormToken;
use Orpheus\InputController\HTTPController\HTTPController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPRoute;
use Orpheus\Rendering\HTMLRendering;

$rendering->useLayout('layout.full-width');
?>
<div class="row">
	<div class="col-12 col-xl-6">
		
		<?php $rendering->useLayout('panel-default'); ?>
		
		<table id="TableClassList" class="table table-striped table-bordered">
			<thead>
			<tr>
				<th scope="col" style="width:1%;"><?php echo t('idColumn'); ?></th>
				<th scope="col" class="text-nowrap"><?php echo t('name', DOMAIN_CLASS); ?></th>
				<th scope="col" class="text-nowrap"><?php echo t('level', DOMAIN_CLASS); ?></th>
				<th scope="col" class="text-nowrap" data-orderable="false"><?php echo t('actionsColumn'); ?></th>
			</tr>
			</thead>
			<tbody class="table-valign-middle">
			<?php
			foreach( $learningSheets as $learningSheet ) {
				?>
				<tr>
					<th scope="row" style="width:1%;"><?php echo $learningSheet->id(); ?></th>
					<td>
						<a href="<?php echo u('user_learning_sheet_edit', ['learningSheetId' => $learningSheet->id()]); ?>">
							<?php echo $learningSheet; ?>
						</a>
					</td>
					<td><?php echo t('level_' . $learningSheet->level, DOMAIN_CLASS); ?></td>
					<td class="text-right">
						<div class="btn-group btn-group-sm" role="group">
							<a href="<?php echo u('user_learning_sheet_edit', ['learningSheetId' => $learningSheet->id()]); ?>" class="btn btn-secondary">
								<i class="fas fa-edit"></i>
							</a>
						</div>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
		<?php
		$rendering->endCurrentLayout(['title' => t('user_learning_sheet_list')]);
		?>
	</div>
</div>

