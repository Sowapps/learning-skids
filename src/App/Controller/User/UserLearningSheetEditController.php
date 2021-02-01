<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use App\Entity\LearningSheet;
use App\Entity\LearningSheetUser;
use App\Entity\LearningSkill;
use App\Entity\SchoolClass;
use App\Entity\User;
use App\Importer\LearningSheetImporter;
use Orpheus\Exception\ForbiddenException;
use Orpheus\Exception\UserException;
use Orpheus\File\UploadedFile;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPResponse;

class UserLearningSheetEditController extends AbstractUserController {
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 */
	public function run($request) {
		$learningSheet = LearningSheet::load($request->getPathValue('learningSheetId'), false);
		$class = $request->hasPathValue('classId') ? SchoolClass::load($request->getPathValue('classId'), false) : null;
		
		$currentUser = User::getLoggedUser();
		/** @var LearningSheetUser $learningSheetUser */
		if( !$currentUser->canLearningSheetAccess($learningSheet, $learningSheetUser) ) {
			throw new ForbiddenException();
		}
		
		if( $class ) {
			if( !$currentUser->canClassManage($class) ) {
				throw new ForbiddenException();
			}
			$this->addRouteToBreadcrumb('user_class_list');
			$this->addRouteToBreadcrumb('user_class_edit', t('class_label', DOMAIN_CLASS, $class->getLabel()), ['classId' => $class->id()]);
			$this->addRouteToBreadcrumb('user_class_learning_sheet_edit',
				t('learningSheet_label', DOMAIN_LEARNING_SKILL, $learningSheet->getLabel()),
				['classId' => $class->id(), 'learningSheetId' => $learningSheet->id()]);
		} else {
			$this->addRouteToBreadcrumb('user_learning_sheet_list');
			$this->addRouteToBreadcrumb('user_learning_sheet_edit', t('learningSheet_label', DOMAIN_LEARNING_SKILL, $learningSheet->getLabel()), ['learningSheetId' => $learningSheet->id()]);
		}
		
		$allowLearningSheetUpdate = $learningSheetUser->canAdminLearningSheet();
		
		$this->consumeSuccess('learningSheetEdit');
		$this->setPageTitle(t('learningSheet_label', DOMAIN_LEARNING_SKILL, $learningSheet->getLabel()));
		$this->setContentTitle($learningSheet);
		
		try {
			if( $request->hasData('submitImport') ) {
				
				$uploadedFile = UploadedFile::load('file');
				if( !$uploadedFile ) {
					LearningSkill::throwException('importFileRequired');
				}
				$uploadedFile->allowedExtensions = ['csv'];
				$uploadedFile->validate();
				
				$importer = new LearningSheetImporter();
				try {
					$importer->import($learningSheet, $uploadedFile);
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
			}
		} catch( UserException $e ) {
			reportError($e);
		}
		
		return $this->renderHTML('user/user_learning_sheet_edit', [
			'learningSheet'            => $learningSheet,
			'class'                    => $class,
			'allowLearningSheetUpdate' => $allowLearningSheetUpdate,
		]);
	}
	
}
