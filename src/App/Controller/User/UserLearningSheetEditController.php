<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use App\Entity\LearningCategory;
use App\Entity\LearningSheet;
use App\Entity\LearningSheetUser;
use App\Entity\LearningSkill;
use App\Entity\PupilSkill;
use App\Entity\SchoolClass;
use App\Entity\User;
use App\Exporter\CsvExporter;
use App\Importer\LearningSheetImporter;
use Orpheus\Exception\ForbiddenException;
use Orpheus\Exception\UserException;
use Orpheus\File\UploadedFile;
use Orpheus\InputController\HttpController\FileHttpResponse;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpResponse;
use Orpheus\InputController\HttpController\RedirectHttpResponse;

class UserLearningSheetEditController extends AbstractUserController {
	
	/**
	 * @param HttpRequest $request The input HTTP request
	 * @return HttpResponse The output HTTP response
	 */
	public function run($request): HttpResponse {
		// Parameters
		$learningSheet = LearningSheet::load($request->getPathValue('learningSheetId'), false);
		$class = $request->hasPathValue('classId') ? SchoolClass::load($request->getPathValue('classId'), false) : null;
		
		$currentUser = User::getLoggedUser();
		/** @var LearningSheetUser $learningSheetUser */
		if( !$currentUser->canLearningSheetAccess($learningSheet, $learningSheetUser) ) {
			throw new ForbiddenException();
		}
		
		// Breadcrumb
		$parentRoute = 'user_learning_sheet_list';
		if( $class ) {
			if( !$currentUser->canClassManage($class) ) {
				throw new ForbiddenException();
			}
			$this->addRouteToBreadcrumb('user_class_list');
			$this->addRouteToBreadcrumb('user_class_edit', t('class_label', DOMAIN_CLASS, $class->getLabel()), ['classId' => $class->id()]);
			$this->addRouteToBreadcrumb('user_class_learning_sheet_edit',
				t('learningSheet_label', DOMAIN_LEARNING_SKILL, $learningSheet->getLabel()),
				['classId' => $class->id(), 'learningSheetId' => $learningSheet->id()]);
			$parentRoute = 'user_class_list';
		} else {
			$this->addRouteToBreadcrumb('user_learning_sheet_list');
			$this->addRouteToBreadcrumb('user_learning_sheet_edit', t('learningSheet_label', DOMAIN_LEARNING_SKILL, $learningSheet->getLabel()), ['learningSheetId' => $learningSheet->id()]);
		}
		
		// Permissions
		$allowLearningSheetUpdate = $learningSheetUser->canAdminLearningSheet();
		
		$removeDisallowReasons = $this->checkLearningSheetIsRemovable($learningSheet);
		$allowLearningSheetRemove = !$removeDisallowReasons;
		
		$archiveDisallowReasons = $this->checkLearningSheetIsArchivable($learningSheet);
		$allowLearningSheetArchive = !$archiveDisallowReasons;
		
		// Template data
		$this->consumeSuccess('learningSheetEdit');
		$this->setPageTitle(t('learningSheet_label', DOMAIN_LEARNING_SKILL, $learningSheet->getLabel()));
		$this->setContentTitle($learningSheet);
		
		// Forms
		try {
			if( $request->hasData('submitExport') ) {
				$exporter = new CsvExporter();
				$exporter
					->addHeader('DOM_NOM', function (LearningSkill $item, ?LearningSkill $previous) {
						// First row of new category
						$category = $item->getLearningCategory();
						
						return !$previous || !$previous->getLearningCategory()->equals($category) ? $category->name : '';
					})
					->addHeader('COMP_NOM', 'name')
					->addHeader('COMP_VALEUR', function (LearningSkill $item) {
						return $item->valuable ? 'x' : '';
					});
				$exporter->writeHeaders();
				foreach( $learningSheet->queryCategories() as $category ) {
					foreach( $category->querySkills() as $skill ) {
						$written = $exporter->addRow($skill);
					}
				}
				
				return new FileHttpResponse($exporter->terminate()->getStream(), sprintf('%s.csv', LearningCategory::slugName($learningSheet->name)), true);
				
			} elseif( $request->hasData('submitImport') ) {
				$uploadedFile = UploadedFile::load('file');
				if( !$uploadedFile ) {
					LearningSkill::throwException('importFileRequired');
				}
				$uploadedFile->allowedExtensions = ['csv'];
				$uploadedFile->validate();
				
				$importer = new LearningSheetImporter();
				$importer->initialize($learningSheet);
				try {
					$importer->import($uploadedFile);
				} finally {
					$importErrors = $importer->getErrors();
				}
				if( $importer->didAnyChanges() ) {
					reportSuccess(t('importSuccess', DOMAIN_LEARNING_SKILL, [
						'categories' => $importer->getCategoryChanges(),
						'skills'     => $importer->getSkillChanges(),
						'errors'     => count($importErrors),
						'rows'       => $importer->getRowCount(),
					]));
				} else {
					reportSuccess(t('importNoChanges', DOMAIN_LEARNING_SKILL, [
						'categories' => $importer->getCategoryChanges(),
						'skills'     => $importer->getSkillChanges(),
						'errors'     => count($importErrors),
						'rows'       => $importer->getRowCount(),
					]));
				}
				foreach( $importErrors as $importError ) {
					reportError(sprintf('%s : ligne #%d pour domaine "%s" et compétence "%s"', $importError->exception, $importError->row, $importError->category, $importError->skill));
				}
				
			} elseif( $request->hasData('submitSave') ) {
				$sheetInput = $request->getArrayData('sheet');
				if( $sheetInput ) {
					$learningSheet->update($sheetInput, ['name', 'level']);
					reportSuccess(t('learningSheetSaved', DOMAIN_LEARNING_SKILL, $learningSheet));
				}
				
			} elseif( $request->hasData('submitRemove') ) {
				
				$learningSheet->remove();
				
				return new RedirectHttpResponse(u($parentRoute));
				
			} elseif( $request->hasData('submitArchive') ) {
				if( !$learningSheet->enabled ) {
					throw new ForbiddenException();
				}
				$learningSheet->setEnabled(false);
				$learningSheet->save();
				reportSuccess(t('learningSheetArchived', DOMAIN_LEARNING_SKILL, $learningSheet));
				
			} elseif( $request->hasData('submitEnable') ) {
				if( $learningSheet->enabled ) {
					throw new ForbiddenException();
				}
				$learningSheet->setEnabled(true);
				$learningSheet->save();
				reportSuccess(t('learningSheetEnabled', DOMAIN_LEARNING_SKILL, $learningSheet));
				
			} elseif( $request->hasData('submitSkill') ) {
				$skill = LearningSkill::load($request->getData('submitSkill'), false);
				if( !$skill->getLearningCategory()->getLearningSheet()->equals($learningSheet) ) {
					// Skill from another learning sheet, hack attempt ?
					throw new ForbiddenException();
				}
				$skill->update($request->getData('skill'), ['name', 'valuable']);
				reportSuccess(t('learningSkill.update.success', DOMAIN_LEARNING_SKILL, $learningSheet));
			}
		} catch( UserException $e ) {
			reportError($e);
		}
		
		return $this->renderHtml('user/user_learning_sheet_edit', [
			'learningSheet'             => $learningSheet,
			'class'                     => $class,
			'allowLearningSheetUpdate'  => $allowLearningSheetUpdate,
			'allowLearningSheetRemove'  => $allowLearningSheetRemove,
			'removeDisallowReasons'     => $removeDisallowReasons,
			'allowLearningSheetArchive' => $allowLearningSheetArchive,
			'archiveDisallowReasons'    => $archiveDisallowReasons,
		]);
	}
	
	public function checkLearningSheetIsRemovable(LearningSheet $learningSheet) {
		$disallowReasons = [];
		$classCount = SchoolClass::get()
			->where('learning_sheet_id', $learningSheet)
			->count();
		if( $classCount ) {
			$disallowReasons[] = t('learningSheetIsUsedByClass', DOMAIN_LEARNING_SKILL, $classCount);
		}
		$pupilSkillCount = PupilSkill::get()
			->where('learning_sheet_id', $learningSheet)
			->count();
		if( $pupilSkillCount ) {
			$disallowReasons[] = t('learningSheetIsUsedByPupil', DOMAIN_LEARNING_SKILL, $pupilSkillCount);
		}
		
		return $disallowReasons;
	}
	
	public function checkLearningSheetIsArchivable(LearningSheet $learningSheet) {
		$disallowReasons = [];
		$classCount = SchoolClass::get()
			->where('learning_sheet_id', $learningSheet)
			->where('enabled')
			->count();
		if( $classCount ) {
			$disallowReasons[] = t('learningSheetIsUsedByActiveClass', DOMAIN_LEARNING_SKILL, $classCount);
		}
		
		return $disallowReasons;
	}
	
}
