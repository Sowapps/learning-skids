<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 *
 * @var HtmlRendering $rendering
 * @var HttpController $controller
 * @var HttpRequest $request
 * @var HttpRoute $route
 * @var string $CONTROLLER_OUTPUT
 * @var string $content
 * @var FormToken $formToken
 * @var SchoolClass[] $classes
 */

use App\Entity\SchoolClass;
use Orpheus\Form\FormToken;
use Orpheus\InputController\HttpController\HttpController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpRoute;
use Orpheus\Rendering\HtmlRendering;

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
				<th scope="col" class="text-nowrap"><?php echo t('year', DOMAIN_CLASS); ?></th>
				<th scope="col" class="text-nowrap"><?php echo t('openDate', DOMAIN_CLASS); ?></th>
				<th scope="col" class="text-nowrap"><?php echo t('pupilsCount', DOMAIN_CLASS); ?></th>
				<th scope="col" class="text-nowrap" data-orderable="false"><?php echo t('actionsColumn'); ?></th>
			</tr>
			</thead>
			<tbody class="table-valign-middle">
			<?php
			foreach( $classes as $class ) {
				?>
				<tr>
					<th scope="row" style="width:1%;"><?php echo $class->id(); ?></th>
					<td>
						<a href="<?php echo u('user_class_edit', ['classId' => $class->id()]); ?>">
							<?php echo $class; ?>
						</a>
					</td>
					<td><?php echo t('level_' . $class->level, DOMAIN_CLASS); ?></td>
					<td><?php echo $class->year; ?></td>
					<td><?php echo d($class->openDate); ?></td>
					<td><?php echo $class->queryPupils()->count(); ?></td>
					<td class="text-right">
						<div class="btn-group btn-group-sm" role="group">
							<a href="<?php echo u('user_class_edit', ['classId' => $class->id()]); ?>" class="btn btn-secondary">
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
		<?php $rendering->startNewBlock('footer'); ?>
		<a class="btn btn-secondary" href="<?php echo u('user_class_new'); ?>"><?php _t('add'); ?></a>
		<?php
		$rendering->endCurrentLayout(['title' => t('user_class_list', DOMAIN_CLASS)]);
		?>
	</div>
</div>

