<?php

namespace App\Controller\Console;

use App\Entity\PupilSkill;
use Exception;
use Orpheus\InputController\CLIController\CLIController;
use Orpheus\InputController\CLIController\CLIRequest;
use Orpheus\InputController\CLIController\CLIResponse;

/**
 * Class UpgradeExistingDataController
 * Controller to upgrade database's data using cli
 */
class UpgradeExistingDataController extends CLIController {
	
	/**
	 * @param CLIRequest $request The input CLI request
	 * @return CLIResponse
	 * @throws Exception
	 */
	public function run($request): CLIResponse {
		if( $request->isVerbose() && $request->isDryRun() ) {
			$this->printLine('Running as dry run, we are not applying any change.');
		}
		
		$pupilSkills = PupilSkill::select()
			->where('value', 'IS NOT', 'NULL', false);
		
		/** @var PupilSkill $pupilSkill */
		$migrated = 0;
		foreach( $pupilSkills as $pupilSkill ) {
			if( !$pupilSkill->value ) {
				continue;
			}
			$value = $pupilSkill->value;
			$date = $pupilSkill->create_date;
			if( $request->isDebugVerbose() ) {
				$this->printLine(sprintf('Migrating value "%s" (date %s) from pupil skill #%d', $value, $date, $pupilSkill->id()));
			}
			if( !$request->isDryRun() ) {
				$pupilSkill->value = null;
				$result = $pupilSkill->addValue($value, $date);
				// All changes are saved
				if( !$result && $request->isDebugVerbose() ) {
					$this->printLine('But value was already known as the active one');
				}
			}
			$migrated++;
		}
		
		return new CLIResponse(0, sprintf('Migrated %d pupil skills.', $migrated));
	}
	
	
}
