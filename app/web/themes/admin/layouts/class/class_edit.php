<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 *
 * @var HtmlRendering $rendering
 * @var HttpController $controller
 * @var HttpRequest $request
 * @var HttpRoute $route
 * @var string $content
 * @var FormToken $formToken
 * @var User $currentUser
 * @var SchoolClass $class
 * @var bool $isNewTeacher
 * @var bool $allowArchive
 * @var bool $allowUnarchive
 * @var bool $allowClose
 * @var bool $readOnly
 */

use App\Entity\ClassPupil;
use App\Entity\SchoolClass;
use App\Entity\User;
use Orpheus\Form\FormToken;
use Orpheus\InputController\HttpController\HttpController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpRoute;
use Orpheus\Rendering\HtmlRendering;

function getNextClassDetails(SchoolClass $currentClass): array {
	$closeDateDefault = new DateTime();
	$nextClassOpenDateDefault = clone $closeDateDefault;
	$nextClassOpenDateDefault->modify('+1 day');
	$firstSemester = $currentClass->isFirstSemester();
	$nextClassCloseEstimatedDateDefault = new DateTime(sprintf('first monday of %s %d', $firstSemester ? 'june' : 'september', $currentClass->year + 1));
	
	return [$closeDateDefault->format('c'), $nextClassOpenDateDefault->format('c'), $nextClassCloseEstimatedDateDefault->format('c')];
}

$rendering->addThemeJsFile('class_edit.js');
$rendering->useLayout('layout.full-width');

if( $isNewTeacher ) {
	?>
	<div class="row justify-content-center">
		<div class="col-12 col-xl-10">
			<div class="alert alert-info">
				Vous venez d'arriver sur LearningSkids et vous avez besoin d'un peu d'aide ?!<br>
				Vous trouverez en dessous les informations sur votre classe, vous pouvez les modifier à tout moment !<br>
				Avec cela, la liste des élèves de votre classe, vous pouvez les éditer manuellement ou les importer depuis un fichier CSV (extrait d'une feuille
				Excel).<br>
				Pour chaque élève, vous pouvez télécharger sa fiche d'évaluation (positive 🙂).<br>
				<br>
				<b>Compétences des élèves</b><br>
				C'est ici que vous notez les élèves, un tableau récapitulant toutes les compétences acquises par tous les élèves.<br>
				<b>La fiche d'apprentissage</b><br>
				C'est ici que vous définissez les domaines et les compétences utilisés par votre classe pour noter vos élèves.<br>
			</div>
		</div>
	</div>
	<?php
}
?>
	<div class="row">
		<div class="col-12 col-xl-6">
			
			<form method="post">
			<?php
			$rendering->useLayout('panel-default');
			$this->display('reports-bootstrap3');
			
			if( $allowClose ) {
				?>
				<div class="alert alert-info">
					Cette classe ne devrait-elle pas être fermée ? Si c'est le cas, veuillez cliquer sur « Fermer la classe » svp, cela archivera également les fiches de
					ses élèves.
				</div>
				<?php
			} else {
				if( $readOnly ) {
					?>
					<div class="alert alert-info">
						La classe est fermée, il vous est impossible de la modifier.
					</div>
					<?php
				}
			}
			
			$rendering->display('user/class.form', ['class' => $class]);
			$rendering->startNewBlock('footer');
			?>
			
			<div class="mb-2 d-none" data-form-change-not="d-none">
				<div class="alert alert-success d-flex flex-column flex-md-row justify-content-between align-items-center m-0" role="alert">
					Pensez à sauvegarder vos modifications !
				</div>
			</div>
			<div class="d-flex align-items-center justify-content-between gap-1">
				<div class="d-flex gap-1">
					<?php
					if( $class->previous_class_id ) {
						?>
						<a class="btn btn-outline-secondary" href="<?php echo u('user_class_edit', ['classId' => $class->previous_class_id]); ?>">
							<i class="fa-solid fa-angle-left me-1"></i>
							<?php echo SchoolClass::text('semester_previous'); ?>
						</a>
						<?php
					}
					if( $class->next_class_id ) {
						?>
						<a class="btn btn-outline-primary" href="<?php echo u('user_class_edit', ['classId' => $class->next_class_id]); ?>">
							<?php echo SchoolClass::text('semester_next'); ?>
							<i class="fa-solid fa-angle-right ms-1"></i>
						</a>
						<?php
					}
					?>
				</div>
				<div class="d-flex flex-wrap gap-1 justify-content-end">
					<?php
					if( $allowArchive && !$allowClose ) {
						?>
						<button class="btn btn-outline-secondary" type="submit" name="submitArchive">
							<i class="fa-solid fa-lock me-1"></i>
							<?php _t('archive'); ?>
						</button>
						<?php
					}
					if( $allowUnarchive ) {
						?>
						<button class="btn btn-outline-secondary" type="submit" name="submitUnarchive">
							<i class="fa-solid fa-lock-open me-1"></i>
							<?php _t('unarchive'); ?>
						</button>
						<?php
					}
					if( $class->hasLearningSheet() ) {
						?>
						<a class="btn btn-outline-secondary" href="<?php echo u('user_class_pupils_sheet', ['classId' => $class->id()]); ?>">
							<i class="fa-solid fa-tasks me-1"></i>
							<?php _t('user_class_pupils_sheet'); ?>
						</a>
						<a class="btn btn-outline-secondary" href="<?php
						echo u('user_class_learning_sheet_edit', ['classId' => $class->id(), 'learningSheetId' => $class->getLearningSheet()->id()]);
						?>">
							<i class="fa-regular fa-list-alt me-1"></i>
							<?php _t('learningSheet', DOMAIN_CLASS); ?>
						</a>
						<?php
					}
					if( $allowClose ) {
						?>
						<button class="btn btn-outline-primary" type="button" data-toggle="modal" data-target="#DialogClassClose">
							<i class="fa-solid fa-lock me-1"></i>
							<?php _t('closeClass_label', DOMAIN_CLASS); ?>
						</button>
						<?php
					}
					if( !$readOnly ) {
						?>
						<button class="btn btn-primary" type="submit" name="submitUpdate" data-form-change="blink">
							<i class="fa-solid fa-save me-1"></i>
							<?php _t('save'); ?>
						</button>
						<?php
					}
					?>
				</div>
			</div>
			<?php $rendering->endCurrentLayout(['title' => t('my_class_details_title', DOMAIN_CLASS, [$class->getLabel()])]); ?>
			</form>
		
		</div>
		
		<div class="col-12 col-xl-6">
			<?php $rendering->useLayout('panel-default'); ?>
			
			<?php $this->display('reports-bootstrap3', ['stream' => 'pupilList']); ?>
			
			<table class="table table-striped table-bordered">
				<thead>
				<tr>
					<th scope="col" style="width:1%;"><?php echo t('idColumn'); ?></th>
					<th scope="col" class="text-nowrap"><?php echo t('firstname', DOMAIN_PERSON); ?></th>
					<th scope="col" class="text-nowrap"><?php echo t('lastname', DOMAIN_PERSON); ?></th>
					<th scope="col" class="text-nowrap" data-orderable="false"><?php echo t('actionsColumn'); ?></th>
				</tr>
				</thead>
				<tbody class="table-valign-middle">
				<?php
				/** @var ClassPupil[] $pupils */
				$pupils = $class->queryPupils()->run();
				foreach( $pupils as $pupil ) {
					$person = $pupil->getPerson();
					?>
					<tr class="item-pupil" data-item="<?php asJsonAttribute($pupil); ?>">
						<th scope="row" style="width:1%;" title="Pupil #<?php echo $pupil->id(); ?>"><?php echo $person->id(); ?></th>
						<td><?php echo $person->firstname; ?></td>
						<td><?php echo $person->lastname; ?></td>
						<td class="text-right">
							<div class="btn-group btn-group-sm" role="group">
								<a class="btn btn-secondary"
								   href="<?php echo u('user_class_pupil_edit', ['classId' => $class->id(), 'pupilId' => $pupil->id()]); ?>"
								   title="Voir sa fiche personnelle">
									<i class="fa-solid fa-user-edit"></i>
								</a>
								<a class="btn btn-secondary" href="<?php echo u('user_class_pupil_export', ['pupilId' => $pupil->id()]); ?>"
								   title="Télécharger sa fiche d'apprentissage">
									<i class="fa-solid fa-download"></i>
								</a>
								<?php
								if( !$readOnly ) {
									?>
									<button type="button" class="btn btn-secondary" title="Enlever cet élève de cette classe"
											data-confirm_title="<?php echo t('removePupil_title', DOMAIN_CLASS); ?>"
											data-confirm_message="<?php echo t('removePupil_message', DOMAIN_CLASS, ['name' => $person->getLabel()]); ?>"
											data-confirm_submit_name="submitRemovePupil" data-confirm_submit_value="<?php echo $pupil->id(); ?>">
										<i class="fa-solid fa-times"></i>
									</button>
									<?php
								}
								?>
							</div>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
			<?php
			$rendering->startNewBlock('footer');
			
			if( !$readOnly ) {
				?>
				<button class="btn btn-outline-primary" type="button"
						data-toggle="modal" data-target="#DialogLearningSheetImport">
					<i class="fa-solid fa-upload me-1"></i>
					<?php echo t('import'); ?>
				</button>
				<button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#DialogClassPupilAdd"><?php _t('add'); ?></button>
				<?php
			}
			$rendering->endCurrentLayout(['title' => t('user_class_pupil_list', DOMAIN_CLASS)]);
			?>
		</div>
	
	</div>

<?php
if( !$readOnly ) {
	?>
	<div id="DialogClassPupilAdd" class="modal" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="post">
				<div class="modal-header">
					<h2 class="modal-title text-center w-100"><?php echo t('addPupil_title', DOMAIN_CLASS); ?></h2>
					<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo t('close'); ?>">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p>
						Ajoutez un ou plusieurs élèves, une entrée vide est ignorée.<br>
						Vous pouvez ajouter de nouveaux élèves au fûr et à mesure.
					</p>
					<ul class="list-unstyled list-new-class-pupil">
						<li class="template item-new-class-pupil mb-1">
							<div class="row">
								<div class="form-group col mb-0">
									<label class="sr-only"><?php echo t('firstname', DOMAIN_PERSON); ?></label>
									<input name="pupil[{{ index }}][firstname]" type="text" placeholder="<?php echo t('firstname', DOMAIN_PERSON); ?>"
										   class="form-control form-control-sm person_firstname">
								</div>
								<div class="form-group col mb-0">
									<label class="sr-only"><?php echo t('lastname', DOMAIN_PERSON); ?></label>
									<input name="pupil[{{ index }}][lastname]" type="text" placeholder="<?php echo t('lastname', DOMAIN_PERSON); ?>"
										   class="form-control form-control-sm person_lastname">
								</div>
								<div class="col-auto ml-auto">
									<button class="btn btn-outline-warning btn-sm action-item-remove" type="button">
										<i class="fa-solid fa-times"></i>
									</button>
								</div>
							</div>
						</li>
					</ul>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal"><?php echo t('cancel'); ?></button>
					<button type="submit" class="btn btn-primary" name="submitAddMultiplePupils"><?php echo t('add'); ?></button>
				</div>
				</form>
			</div>
		</div>
	</div>
	<div id="DialogLearningSheetImport" class="modal" tabindex="-1">
		<div class="modal-dialog">
			<form method="post" enctype="multipart/form-data" class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title text-center w-100">Import d'une liste d'élève</h2>
				<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo t('close'); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="alert alert-danger notify-learning-sheet-save" role="alert" style="display: none;">
					Attention ! Veuillez sauvegarder vos modifications avant d'importer de nouvelles données sinon elles seront perdues !
				</div>
				<p>
					L'import ne supporte que le format CSV (Excel, séparateur <b>;</b>) et il ne peut qu'ajouter de nouveaux élèves ou ré-affecter d'anciens
					élèves.<br>
					Les entêtes <b>ELEVE_PRENOM</b> et <b>ELEVE_NOM</b> sont requises.
				</p>
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="InputImportFile" name="file" required>
					<label class="custom-file-label" for="InputImportFile">Parcourir</label>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary" data-dismiss="modal"><?php echo t('cancel'); ?></button>
				<button type="submit" class="btn btn-primary" name="submitImport"><?php echo t('import'); ?></button>
			</div>
			</form>
		</div>
	</div>
	<?php
}

if( $allowClose ) {
	// [$closeDateDefault->format('c'), $nextClassOpenDateDefault->format('c'), $nextClassCloseEstimatedDateDefault->format('c')];
	[$closeDateDefault, $nextClassOpenDateDefault, $nextClassCloseEstimatedDateDefault] = getNextClassDetails($class);
	
	//	$classTerminateLimit = new DateTime($class->getEstimatedEndDate()->format('Y').'-05-01');
	//	$showClassTerminate = $class->getEstimatedEndDate() > $classTerminateLimit;
	$showClassDuplicate = $class->isFirstSemester();
	$showClassTerminate = !$showClassDuplicate;// Termine while second semestre
	?>
	<div id="DialogClassClose" class="modal fade" tabindex="-1">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<form method="post">
				<div class="modal-header">
					<h2 class="modal-title text-center w-100"><?php echo t('closeClass_title', DOMAIN_CLASS); ?></h2>
					<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo t('close'); ?>">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p>
						Fermer la classe provoque son archivage, vous ne pourrez ensuite plus éditer ses informations, sa liste d'élèves et leurs compétences.<br>
						Vous pourrez toujours télécharger les fiches d'apprentissage des élèves. Pensez à bien les compléter avant de fermer la classe !
					</p>
					
					<h6>Pour la classe actuelle</h6>
					
					<div class="form-group">
						<label class="form-label" for="InputCloseClassCloseDate"><?php _t('close_date', DOMAIN_CLASS); ?></label>
						<div class="input-group datepicker" data-target-input="nearest" id="InputCloseClassCloseDateWrapper"
							 data-default-date="<?php echo $closeDateDefault; ?>">
							<input type="text" class="form-control datetimepicker-input" id="InputCloseClassCloseDate"
								   data-target="#InputCloseClassCloseDateWrapper" <?php echo formInput('current_class/close_date'); ?>>
							<div class="input-group-append" data-target="#InputCloseClassCloseDateWrapper" data-toggle="datetimepicker">
								<div class="input-group-text"><i class="fa-solid fa-calendar-alt"></i></div>
							</div>
						</div>
					</div>
					
					<h6>Pour la suite...</h6>
					
					<div class="accordion" id="AccordionEndSelect">
						
						<div class="card">
							<div class="card-header" id="AccordionEndSelectTerminateHeading">
								<label class="mb-0">
									<input name="then" type="radio" data-toggle="collapse" data-target="#SectionClassTerminate"
										   aria-expanded="<?php echo b($showClassTerminate); ?>"
										   aria-controls="SectionClassTerminate" hidden
										<?php echo $showClassTerminate ? ' checked' : ''; ?>/>
									Terminer l'année
								</label>
								<?php /*
									<button class="btn btn-link btn-block text-left <?php echo $showClassTerminate ? '' : 'collapsed'; ?>" type="button" data-toggle="collapse"
											data-target="#SectionClassTerminate" aria-expanded="<?php echo b($showClassTerminate); ?>" aria-controls="SectionClassTerminate">
										Terminer l'année
									</button>
									 */ ?>
							</div>
							<div id="SectionClassTerminate" class="collapse <?php echo $showClassTerminate ? 'show' : ''; ?>"
								 aria-labelledby="AccordionEndSelectTerminateHeading"
								 data-parent="#AccordionEndSelect">
								<div class="card-body">
									L'année est terminée, cette classe est fermée.
								</div>
							</div>
						</div>
						
						<div class="card overflow-visible"><?php /* Overflow visible to allow datepicker to get out of the box */ ?>
							<div class="card-header" id="AccordionEndSelectDuplicateHeading">
								<label class="mb-0">
									<input name="then" value="duplicate" type="radio" data-toggle="collapse" data-target="#SectionClassDuplicate"
										   aria-expanded="<?php echo b($showClassTerminate); ?>" aria-controls="SectionClassDuplicate" hidden
										<?php echo $showClassDuplicate ? ' checked' : ''; ?>/>
									Nouveau semestre
								</label>
								<?php /*
									<button class="btn btn-link btn-block text-left <?php echo $showClassDuplicate ? '' : 'collapsed'; ?>" type="button" data-toggle="collapse"
											data-target="#SectionClassDuplicate" aria-expanded="<?php echo b($showClassDuplicate); ?>" aria-controls="SectionClassDuplicate">
										Nouveau semestre
									</button>
									 */ ?>
							</div>
							<div id="SectionClassDuplicate" class="collapse <?php echo $showClassDuplicate ? 'show' : ''; ?>"
								 aria-labelledby="AccordionEndSelectDuplicateHeading"
								 data-parent="#AccordionEndSelect">
								<div class="card-body">
									<p>
										Pour créer un nouveau semestre, une nouvelle classe va être créée et sera liée à la précédente.<br>
										La fiche d'apprentissage sera copiée, ainsi que toutes les fiches de compétences des élèves.
									</p>
									
									<div class="form-group">
										<label class="form-label" for="InputCloseClassOpenDate"><?php _t('open_date', DOMAIN_CLASS); ?></label>
										<div class="input-group datepicker" data-target-input="nearest" id="InputCloseClassOpenDateWrapper"
											 data-default-date="<?php echo $nextClassOpenDateDefault; ?>">
											<input type="text" class="form-control datetimepicker-input" id="InputCloseClassOpenDate"
												   data-target="#InputCloseClassOpenDateWrapper" <?php echo formInput('next_class/open_date'); ?>>
											<div class="input-group-append" data-target="#InputCloseClassOpenDateWrapper" data-toggle="datetimepicker">
												<div class="input-group-text"><i class="fa-solid fa-calendar-alt"></i></div>
											</div>
										</div>
									</div>
									
									<div class="form-group">
										<label class="form-label" for="InputCloseClassCloseEstimatedDate"><?php _t('close_estimated_date', DOMAIN_CLASS); ?></label>
										<div class="input-group datepicker" data-target-input="nearest" id="InputCloseClassCloseEstimatedDateWrapper"
											 data-default-date="<?php echo $nextClassCloseEstimatedDateDefault; ?>">
											<input type="text" class="form-control datetimepicker-input" id="InputCloseClassCloseEstimatedDate"
												   data-target="#InputCloseClassCloseEstimatedDateWrapper" <?php echo formInput('next_class/close_estimated_date'); ?>>
											<div class="input-group-append" data-target="#InputCloseClassCloseEstimatedDateWrapper" data-toggle="datetimepicker">
												<div class="input-group-text"><i class="fa-solid fa-calendar-alt"></i></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					
					</div>
					
					<!--						<ul class="list-unstyled list-new-class-pupil">-->
					<!--							<li class="template item-new-class-pupil mb-1">-->
					<!--								<div class="row">-->
					<!--									<div class="form-group col mb-0">-->
					<!--										<label class="sr-only">--><?php //echo t('firstname', DOMAIN_PERSON); ?><!--</label>-->
					<!--										<input name="pupil[{{ index }}][firstname]" type="text" placeholder="-->
					<?php //echo t('firstname', DOMAIN_PERSON); ?><!--"-->
					<!--											   class="form-control form-control-sm person_firstname">-->
					<!--									</div>-->
					<!--									<div class="form-group col mb-0">-->
					<!--										<label class="sr-only">--><?php //echo t('lastname', DOMAIN_PERSON); ?><!--</label>-->
					<!--										<input name="pupil[{{ index }}][lastname]" type="text" placeholder="-->
					<?php //echo t('lastname', DOMAIN_PERSON); ?><!--"-->
					<!--											   class="form-control form-control-sm person_lastname">-->
					<!--									</div>-->
					<!--									<div class="col-auto ml-auto">-->
					<!--										<button class="btn btn-outline-warning btn-sm action-item-remove" type="button">-->
					<!--											<i class="fa-solid fa-times"></i>-->
					<!--										</button>-->
					<!--									</div>-->
					<!--								</div>-->
					<!--							</li>-->
					<!--						</ul>-->
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal"><?php echo t('cancel'); ?></button>
					<button type="submit" class="btn btn-primary" name="submitClose"><?php echo t('closeClass_label', DOMAIN_CLASS); ?></button>
				</div>
				</form>
			</div>
		</div>
	</div>
	<?php
}
