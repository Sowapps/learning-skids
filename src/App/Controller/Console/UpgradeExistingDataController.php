<?php

namespace App\Controller\Console;

use App\Entity\PupilSkillValue;
use App\Entity\SchoolClass;
use Exception;
use Orpheus\InputController\CliController\CliController;
use Orpheus\InputController\CliController\CliRequest;
use Orpheus\InputController\CliController\CliResponse;

/**
 * Class UpgradeExistingDataController
 * Controller to upgrade database's data using cli
 */
class UpgradeExistingDataController extends CliController {
	
	/**
	 * @param CliRequest $request The input CLI request
	 * @return CliResponse
	 * @throws Exception
	 */
	public function run($request): CliResponse {
		// Try --dry-run and --vvvv
		if( $request->isVerbose() && $request->isDryRun() ) {
			$this->printLine('Running as dry run, we are not applying any change.');
		}
		
		//		$sqlAdapter = PupilSkillValue::getSqlAdapter();
		//		return new CliResponse(0, 'ENDING TEST');
		
		//		$sqlAdapter->startTransaction();
		$migrated = [];
		
		// Update existing PupilSkillValue
		$pupilSkillValues = PupilSkillValue::select()
			->where('date', '0000-00-00');
		/** @var PupilSkillValue $pupilSkillValue */
		$migrated['PupilSkillValue'] = 0;
		foreach( $pupilSkillValues as $pupilSkillValue ) {
			$pupilSkillValue->date = $pupilSkillValue->create_date;
			if( $request->isVeryVerbose() ) {
				$this->printLine(sprintf('Migrate %s', $pupilSkillValue->getReference()));
			}
			if( $request->isDryRun() ) {
				$pupilSkillValue->revert();
			} else {
				$pupilSkillValue->save();
			}
			$migrated['PupilSkillValue']++;
		}
		
		// Update existing SchoolClass
		$schoolClasses = SchoolClass::select()
			->where('open_date', '0000-00-00');
		/** @var SchoolClass $schoolClass */
		$migrated['SchoolClass'] = 0;
		foreach( $schoolClasses as $schoolClass ) {
			$schoolClass->open_date = $schoolClass->openDate;
			if( $request->isVeryVerbose() ) {
				$this->printLine(sprintf('Migrate %s', $schoolClass->getReference()));
			}
			if( $request->isDryRun() ) {
				$schoolClass->revert();
			} else {
				$schoolClass->save();
			}
			$migrated['SchoolClass']++;
		}
		
		// Require support of transaction (InnoDB)
		//		if(!$request->isDryRun()) {
		//			$this->printLine('Commit changes');
		//			$sqlAdapter->endTransaction();
		//		} else {
		//			$this->printLine('Revert changes');
		//			$sqlAdapter->revertTransaction();
		//		}
		
		return new CliResponse(0, sprintf(<<<TEXT
Migrated %d pupil skill values.
Migrated %d school classes.
TEXT, $migrated['PupilSkillValue'], $migrated['SchoolClass']));
	}
}
