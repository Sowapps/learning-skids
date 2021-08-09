<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use App\Entity\ClassPupil;
use App\Entity\LearningSheet;
use App\Entity\LearningSkill;
use App\Entity\SchoolClass;
use App\Entity\User;
use App\Importer\ClassPupilListImporter;
use DateTime;
use Orpheus\Exception\ForbiddenException;
use Orpheus\Exception\UserException;
use Orpheus\File\UploadedFile;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPResponse;
use Orpheus\InputController\HTTPController\RedirectHTTPResponse;

class UserClassEditController extends AbstractUserController {
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 */
	public function run($request) {
		$class = SchoolClass::load($request->getPathValue('classId'), false);
		
		if( !User::getLoggedUser()->canClassManage($class) ) {
			throw new ForbiddenException();
		}
		
		$this->addRouteToBreadcrumb('user_class_list');
		$this->addRouteToBreadcrumb('user_class_edit', t('class_label', DOMAIN_CLASS, $class->getLabel()), ['classId' => $class->id()]);
		
		$this->consumeSuccess('classEdit');
		$this->consumeSuccess('pupilList', 'pupilList');
		$this->setPageTitle(t('class_label', DOMAIN_CLASS, $class->getLabel()));
		$this->setContentTitle($class);
		
		$requirePupilValidation = false;
		$outputPupilList = null;
		try {
			if( $request->hasData('submitUpdate') ) {
				$classInput = $request->getData('class');
				if( !empty($classInput['name']) && !empty($classInput['level']) && !empty($classInput['learning_sheet_id']) && $classInput['learning_sheet_id'] === 'new' ) {
					$classInput['learning_sheet_id'] = LearningSheet::make($classInput);
				}
				$class->update($classInput, ['name', 'year', 'level', 'openDate', 'learning_sheet_id']);
				
				$this->storeSuccess('classEdit', 'successClassEdit', ['name' => $class->getLabel()], DOMAIN_CLASS);
				
				return new RedirectHTTPResponse(u('user_class_edit', ['classId' => $class->id()]));
				
			} elseif( $request->hasData('submitAddMultiplePupils') ) {
				
				return $this->redirectToPupilAddValidator($class, $request->getData('pupil'));
				//				startReportStream('pupilList');
				//				$pupilListInput = $request->getData('pupil');
				//				$requirePupilValidation = $class->checkPupilList($pupilListInput, $outputPupilList);
				//				if( !$requirePupilValidation ) {
				//					$class->addPupilList($outputPupilList);
				//				}
				//				endReportStream();
				
			} elseif( $request->hasData('submitImport') ) {
				
				$uploadedFile = UploadedFile::load('file');
				if( !$uploadedFile ) {
					LearningSkill::throwException('importFileRequired');
				}
				$uploadedFile->allowedExtensions = ['csv'];
				$uploadedFile->validate();
				
				$importer = new ClassPupilListImporter();
				try {
					$importer->import($uploadedFile);
				} finally {
					$importErrors = $importer->getErrors();
				}
				foreach( $importErrors as $importError ) {
					reportError(sprintf('%s : ligne #%d', $importError->exception, $importError->row));
				}
				if( !$importErrors && $importer->didAnyChanges() ) {
					return $this->redirectToPupilAddValidator($class, $importer->getPupils());
				}
				
			} elseif( $request->hasData('submitRemovePupil') ) {
				startReportStream('pupilList');
				$pupil = ClassPupil::load($request->getData('submitRemovePupil'), false);
				if( !$pupil->getSchoolClass()->equals($class) ) {
					throw new ForbiddenException();
				}
				$pupil->remove();
				endReportStream();
				
				//			} elseif( $request->hasData('submitUpdatePupil') ) {
				//				startReportStream('pupilList');
				//				$pupil = ClassPupil::load($request->getData('pupilId'), false);
				//				if( !$pupil->getSchoolClass()->equals($class) ) {
				//					throw new ForbiddenException();
				//				}
				//				$person = $pupil->getPerson();
				//				$person->update($request->getArrayData('person'), ['firstname', 'lastname']);
				//				endReportStream();
				//
				//				$this->storeSuccess('pupilList', 'successClassPupilEdit', ['name' => $class->getLabel()], DOMAIN_CLASS);
				//				return new RedirectHTTPResponse(u('user_class_edit', ['classId' => $class->id()]));
			}
		} catch( UserException $e ) {
			reportError($e);
		}
		
		return $this->renderHTML('class/class_edit', [
			'class' => $class,
		]);
	}
	
	protected function redirectToPupilAddValidator(SchoolClass $class, array $pupils): RedirectHTTPResponse {
		$token = generateRandomString(8);
		if( !isset($_SESSION['class_add_pupils']) ) {
			$_SESSION['class_add_pupils'] = [];
		}
		$_SESSION['class_add_pupils'][$token] = (object) [
			'createDate' => new DateTime(),
			'classId'    => $class->id(),
			'pupils'     => $pupils,
		];
		
		return new RedirectHTTPResponse(u('user_class_add_pupils', [
			'classId' => $class->id(),
			'token'   => $token,
		]));
	}
	
}
