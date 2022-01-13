<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 *
 * @var HTMLRendering $rendering
 * @var HttpController $controller
 * @var HttpRequest $request
 * @var HttpRoute $route
 * @var string $content
 * @var FormToken $formToken
 * @var User $currentUser
 * @var SchoolClass $class
 * @var bool $isNewTeacher
 */

use App\Entity\ClassPupil;
use App\Entity\SchoolClass;
use App\Entity\User;
use Orpheus\Form\FormToken;
use Orpheus\InputController\HttpController\HttpController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpRoute;
use Orpheus\Rendering\HTMLRendering;


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
				Pour chaque élève, vous pouvez télécharger sa fiche d'évaluation (positive 🙂)<br>
				<br>
				<b>Compétences des élèves</b><br>
				C'est ici que vous notez les élèves, un tableau récapitulant toutes les compétences acquises par tous les élèves<br>
				<b>La fiche d'apprentissage</b><br>
				C'est ici que vous définissez les domaines et les compétences utilisés par votre classe pour noter vos élèves<br>
			</div>
		</div>
	</div>
	<?php
}
?>
<div class="row">
	<div class="col-12 col-xl-6">
		
		<form method="post">
		<?php $rendering->useLayout('panel-default'); ?>
		
		<?php $this->display('reports-bootstrap3'); ?>
		
		<?php $rendering->display('user/class.form', ['class' => $class]); ?>
		
		<?php $rendering->startNewBlock('footer'); ?>
		
		<div class="mb-2 d-none" data-form-change-not="d-none">
			<div class="alert alert-success d-flex flex-column flex-md-row justify-content-between align-items-center m-0" role="alert">
				Pensez à sauvegarder vos modifications !
			</div>
		</div>
		<div class="d-flex align-items-center justify-content-end gap-1">
			<?php if( $class->hasLearningSheet() ) { ?>
				<a class="btn btn-outline-secondary" href="<?php echo u('user_class_pupils_sheet', ['classId' => $class->id()]); ?>">
					<i class="fas fa-tasks"></i>
					<?php _t('user_class_pupils_sheet'); ?>
				</a>
				<a class="btn btn-outline-secondary" href="<?php
				echo u('user_class_learning_sheet_edit', ['classId' => $class->id(), 'learningSheetId' => $class->getLearningSheet()->id()]);
				?>">
					<i class="far fa-list-alt"></i>
					<?php _t('learningSheet', DOMAIN_CLASS); ?>
				</a>
			<?php } ?>
			<button class="btn btn-primary" type="submit" name="submitUpdate" data-form-change="blink">
				<i class="fas fa-save"></i>
				<?php _t('save'); ?>
			</button>
		</div>
		<?php $rendering->endCurrentLayout(['title' => $class->getLabel()]); ?>
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
								<i class="fas fa-user-edit"></i>
							</a>
							<a class="btn btn-secondary" href="<?php echo u('user_class_pupil_export', ['pupilId' => $pupil->id()]); ?>"
							   title="Télécharger sa fiche d'apprentissage">
								<i class="fas fa-download"></i>
							</a>
							<button type="button" class="btn btn-secondary" title="Enlever cet élève de cette classe"
									data-confirm_title="<?php echo t('removePupil_title', DOMAIN_CLASS); ?>"
									data-confirm_message="<?php echo t('removePupil_message', DOMAIN_CLASS, ['name' => $person->getLabel()]); ?>"
									data-confirm_submit_name="submitRemovePupil" data-confirm_submit_value="<?php echo $pupil->id(); ?>">
								<i class="fas fa-times"></i>
							</button>
						</div>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
		<?php $rendering->startNewBlock('footer'); ?>
		<button class="btn btn-outline-primary" type="button"
				data-toggle="modal" data-target="#DialogLearningSheetImport">
			<i class="fas fa-upload mr-1"></i>
			<?php echo t('import'); ?>
		</button>
		<button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#DialogClassPupilAdd"><?php _t('add'); ?></button>
		<?php
		$rendering->endCurrentLayout(['title' => t('user_class_pupil_list', DOMAIN_CLASS)]);
		?>
	</div>

</div>

<div id="DialogClassPupilAdd" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
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
									<i class="fas fa-times"></i>
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
<div id="DialogLearningSheetImport" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
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
/*

<div id="DialogClassPupilEdit" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form method="post">
				<input type="hidden" name="pupilId" class="pupil_id">
				
				<div class="modal-header">
					<h2 class="modal-title text-center w-100">Éditer <span class="pupil_label"></span></h2>
					<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo t('close'); ?>">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label><?php echo t('firstname', DOMAIN_PERSON); ?></label>
						<input name="person[firstname]" type="text" placeholder="<?php echo t('firstname', DOMAIN_PERSON); ?>" class="form-control">
					</div>
					<div class="form-group">
						<label><?php echo t('lastname', DOMAIN_PERSON); ?></label>
						<input name="person[lastname]" type="text" placeholder="<?php echo t('lastname', DOMAIN_PERSON); ?>" class="form-control">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal"><?php echo t('cancel'); ?></button>
					<button type="submit" class="btn btn-primary" name="submitUpdatePupil"><?php echo t('save'); ?></button>
				</div>
			</form>
		</div>
	</div>
</div>
 */ ?>
