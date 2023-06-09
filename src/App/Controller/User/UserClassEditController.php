<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use App\Entity\ClassPupil;
use App\Entity\LearningSkill;
use App\Entity\SchoolClass;
use App\Entity\User;
use App\Importer\ClassPupilListImporter;
use DateTime;
use Orpheus\Exception\ForbiddenException;
use Orpheus\Exception\UserException;
use Orpheus\File\UploadedFile;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpResponse;
use Orpheus\InputController\HttpController\RedirectHttpResponse;

class UserClassEditController extends AbstractUserController {
	
	/**
	 * @param HttpRequest $request The input HTTP request
	 * @return HttpResponse The output HTTP response
	 */
	public function run($request): HttpResponse {
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
		
		$now = new DateTime();
		$readOnly = $class->isArchived();
		$allowClose = !$readOnly && $class->getEstimatedEndDate() && $class->getEstimatedEndDate() < $now && !$class->hasNextClass();
		$allowArchive = !$readOnly;
		$allowUnarchive = $readOnly;
		try {
			//			if( $request->isPOST() ) {
			//				// TODO REMOVE IF BECAUSE COMMIT
			//				// TODO Compétences non copiées !
			//				$currentLearningSheet = $class->getLearningSheet();
			//				$newLearningSheet = $currentLearningSheet->clone(true);
			//				var_dump($newLearningSheet);
			//			} else // Test
			if( $request->hasData('submitUpdate') ) {
				if( $readOnly ) {
					throw new ForbiddenException();
				}
				$classInput = $request->getData('class');
				//				if( !empty($classInput['name']) && !empty($classInput['level']) && !empty($classInput['learning_sheet_id']) && $classInput['learning_sheet_id'] === 'new' ) {
				//					$classInput['learning_sheet_id'] = LearningSheet::make($classInput);
				//				}
				$class->update($classInput, ['name', 'year', 'level', 'open_date', 'close_estimated_date']);
				
				$this->storeSuccess('classEdit', 'successClassEdit', ['name' => $class->getLabel()], DOMAIN_CLASS);
				
				return $this->redirectToClass($class);
				
			} elseif( $request->hasData('submitAddMultiplePupils') ) {
				if( $readOnly ) {
					throw new ForbiddenException();
				}
				
				return $this->redirectToPupilAddValidator($class, $request->getData('pupil'));
				
			} elseif( $request->hasData('submitImport') ) {
				if( $readOnly ) {
					throw new ForbiddenException();
				}
				
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
				
			} else {
				if( $request->hasData('submitRemovePupil') ) {
					if( $readOnly ) {
						throw new ForbiddenException();
					}
					
					startReportStream('pupilList');
					$pupil = ClassPupil::load($request->getData('submitRemovePupil'), false);
					if( !$pupil->getSchoolClass()->equals($class) ) {
						throw new ForbiddenException();
					}
					$pupil->remove();
					endReportStream();
					
				} else {
					if( $request->hasData('submitClose') ) {
						//				if( !$allowClose ) {
						//					throw new ForbiddenException();
						//				}
						$currentLearningSheet = $class->getLearningSheet();
						$currentClassInput = $request->getData('current_class');
						$thenInput = $request->getData('then');
						$currentClassInput['enabled'] = false;
						$class->update($currentClassInput, ['enabled', 'close_date']);
						$currentLearningSheet->enabled = false;
						$currentLearningSheet->save();
						if( $thenInput === 'duplicate' ) {
							// TODO Test cloning
							$newLearningSheet = $currentLearningSheet->clone(true);
							$newClassInput = $request->getData('next_class');
							//						try {
							$newClass = SchoolClass::createAndGet([
								'name'                 => sprintf('%s - S2', $class->name),
								'teacher_id'           => $class->teacher_id,
								'previous_class_id'    => $class->id(),
								'learning_sheet_id'    => $newLearningSheet->id(),
								'open_date'            => $newClassInput['open_date'] ?? null,
								'close_estimated_date' => $newClassInput['close_estimated_date'] ?? null,
								'level'                => $class->level,
								'year'                 => $class->year,
							]);
							//						} catch(Exception $exception) {
							//							$newLearningSheet->remove();
							//						}
							$class->linkNextClass($newClass);
							$class->save();
							foreach( $class->queryPupils() as $pupil ) {
								/** @var ClassPupil $pupil */
								ClassPupil::create([
									'class_id' => $newClass->id(),
									'pupil_id' => $pupil->pupil_id,
								]);
							}
							$newClass->save();// Previous class should be already set
						}
						$this->storeSuccess('classEdit', 'successClassClose', ['name' => $class->getLabel()], DOMAIN_CLASS);
						
						return $this->redirectToClass($class);
						
					} else {
						if( $request->hasData('submitArchive') ) {
							if( !$allowArchive ) {
								throw new ForbiddenException();
							}
							$class->update(['enabled' => false, 'close_date' => new DateTime()], ['enabled', 'close_date']);
							$this->storeSuccess('classEdit', 'successClassArchive', ['name' => $class->getLabel()], DOMAIN_CLASS);
							
							return $this->redirectToClass($class);
							
						} else {
							if( $request->hasData('submitUnarchive') ) {
								if( !$allowUnarchive ) {
									throw new ForbiddenException();
								}
								$class->update(['enabled' => true, 'close_date' => null], ['enabled', 'close_date']);
								$this->storeSuccess('classEdit', 'successClassUnarchive', ['name' => $class->getLabel()], DOMAIN_CLASS);
								
								return $this->redirectToClass($class);
							}
						}
					}
				}
			}
		} catch( UserException $e ) {
			reportError($e);
		}
		$user = User::getLoggedUser();
		$isNewTeacher = $user->create_date > new DateTime('-1 month');
		
		
		//		$allowArchive = $class->enabled;
		//		$allowUnarchive = !$class->enabled;
		//		$allowClose = $allowArchive && ($class->openDate < new DateTime('-11 months
		return $this->renderHtml('class/class_edit', [
			'readOnly'       => $readOnly,
			'class'          => $class,
			'isNewTeacher'   => $isNewTeacher,
			'allowArchive'   => $allowArchive,
			'allowUnarchive' => $allowUnarchive,
			'allowClose'     => $allowClose,
		]);
	}
	
	protected function redirectToClass(SchoolClass $class): RedirectHttpResponse {
		return new RedirectHttpResponse(u('user_class_edit', ['classId' => $class->id()]));
	}
	
	protected function redirectToPupilAddValidator(SchoolClass $class, array $pupils): RedirectHttpResponse {
		$token = generateRandomString(8);
		if( !isset($_SESSION['class_add_pupils']) ) {
			$_SESSION['class_add_pupils'] = [];
		}
		$_SESSION['class_add_pupils'][$token] = (object) [
			'createDate' => new DateTime(),
			'classId'    => $class->id(),
			'pupils'     => $pupils,
		];
		
		return new RedirectHttpResponse(u('user_class_add_pupils', [
			'classId' => $class->id(),
			'token'   => $token,
		]));
	}
	
}
