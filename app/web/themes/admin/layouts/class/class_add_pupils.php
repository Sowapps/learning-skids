<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 *
 * @var HtmlRendering $rendering
 * @var HttpController $controller
 * @var HttpRequest $Request
 * @var HttpRoute $Route
 * @var string $content
 * @var FormToken $formToken
 * @var User $currentUser
 * @var SchoolClass $class
 * @var array $pupils
 */

use App\Entity\Person;
use App\Entity\SchoolClass;
use App\Entity\User;
use Orpheus\Form\FormToken;
use Orpheus\InputController\HttpController\HttpController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpRoute;
use Orpheus\Rendering\HtmlRendering;

$rendering->useLayout('layout.full-width');

?>
<div class="row">
	<div class="col-12 col-xl-6">
		
		<form method="post">
		<?php $rendering->useLayout('panel-default'); ?>
			
			<?php $this->display('reports-bootstrap3'); ?>
			
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<thead>
					<tr>
						<th scope="col" class="text-nowrap"><?php echo t('pupil', DOMAIN_CLASS); ?></th>
						<th scope="col" class="text-nowrap"><?php echo t('statusColumn'); ?></th>
						<th scope="col" class="text-nowrap" data-orderable="false">Valider l'ajout ?</th>
					</tr>
					</thead>
					<tbody class="table-valign-middle">
					<?php
					$submittablePupils = 0;
					// @see SchoolClass::checkPupilList
					foreach( $pupils as $pupilIndex => $pupilData ) {
						$status = $pupilData['status'];
						$person = $pupilData['person'] ?? null;
						$isSubmittable = false;
						?>
						<tr class="item-pupil">
							<td><?php echo $person ?: t('itemLabel', DOMAIN_PERSON, $pupilData); ?></td>
							<td>
								<?php
								if( $status === 'existing' ) {
									if( !empty($pupilData['persons']) && count($pupilData['persons']) > 1 ) {
										?>
										Plusieurs correspondances ont été trouvées
										<?php
									} else {
										?>
										Une correspondance a été trouvée
										<?php
									}
								} elseif( $status === 'new' ) {
									?>
									Nouvel(le) élève
									<?php
								} elseif( $status === 'added' ) {
									?>
									<span class="text-success">Élève ajouté à la classe</span>
									<?php
								} elseif( $status === 'missing' ) {
									?>
									<span class="text-danger">L'élève n'a pas été retrouvé</span>
									<?php
								} elseif( $status === 'assigned' ) {
									?>
									<span class="text-warning">L'élève est déjà assigné à une classe pour cette année</span>
									<?php
								} elseif( $status === 'assignedSelf' ) {
									?>
									<span class="text-warning">L'élève est déjà assigné à cette classe</span>
									<?php
								} elseif( $status === 'error' ) {
									?>
									<span class="text-danger">Une erreur est survenue lors de son ajout à la classe</span>
									<?php
								} elseif( $status === 'cancel' ) {
									?>
									<span class="text-muted">Ajout annulé</span>
									<?php
								}
								?>
							</td>
							<td>
								<input type="hidden" name="pupil[<?php echo $pupilIndex; ?>][firstname]" value="<?php echo $pupilData['firstname']; ?>"/>
								<input type="hidden" name="pupil[<?php echo $pupilIndex; ?>][lastname]" value="<?php echo $pupilData['lastname']; ?>"/>
								<?php
								if( $status === 'existing' && !empty($pupilData['persons']) ) {
									$isSubmittable = true;
									?>
									<select name="pupil[<?php echo $pupilIndex; ?>][personId]" class="select2" data-width="100%">
										<option value="">Annuler</option>
										<option value="new">Nouveau</option>
										<?php
										/** @var Person $person */
										$hasSelected = false;
										foreach( $pupilData['persons'] as $personIndex => $personPupil ) {
											[$person, $class, $enabled] = $personPupil;
											$selected = !$hasSelected && $enabled;
											if( $selected ) {
												$hasSelected = true;
											}
											?>
											<option value="<?php echo $person->id(); ?>"<?php echo $selected ? ' selected' : ''; ?><?php echo !$enabled ? ' disabled' : ''; ?>>
												<?php echo $person . ($class ? sprintf(' (%s)', $class) : ''); ?>
											</option>
											<?php
										}
										if( $hasSelected ) {
											$isSubmittable = true;
										}
										?>
									</select>
									<?php
								} elseif( $status === 'new' ) {
									$isSubmittable = true;
									?>
									<label class="label-checkbox d-block text-center m-0 p-1 fs20">
										<input type="checkbox" class="custom-control-input" name="pupil[<?php echo $pupilIndex; ?>][personId]" value="new" checked>
										<i class="fa-solid fa-check-square fa-1x text-success checked"></i>
										<i class="fa-regular fa-square fa-1x text-muted unchecked"></i>
									</label>
									<?php
								}
								?>
							</td>
						</tr>
						<?php
						if( $isSubmittable ) {
							$submittablePupils++;
						}
					}
					?>
					</tbody>
				</table>
			</div>
			
			<?php $rendering->startNewBlock('footer'); ?>
			
			<a class="btn btn-outline-secondary" href="<?php echo u('user_class_edit', ['classId' => $class->id()]); ?>"><?php _t($isSubmittable ? 'cancel' : 'backToClass'); ?></a>
			<?php
			if( $isSubmittable ) {
				?>
				<button class="btn btn-primary" type="submit" name="submitValidate"><?php _t('validate'); ?></button>
				<?php
			}
			?>
			
			<?php $rendering->endCurrentLayout(['title' => t('user_class_pupil_list', DOMAIN_CLASS)]); ?>
		</form>
	
	</div>

</div>
