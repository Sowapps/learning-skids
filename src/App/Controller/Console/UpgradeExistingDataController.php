<?php

namespace App\Controller\Console;

use App\Entity\PupilSkillValue;
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
		
		// Update existing pu
		$pupilSkillValues = PupilSkillValue::select()
			->where('date', '0000-00-00');
		/** @var PupilSkillValue $pupilSkillValue */
		$migrated = 0;
		foreach( $pupilSkillValues as $pupilSkillValue ) {
			$pupilSkillValue->date = $pupilSkillValue->create_date;
			$pupilSkillValue->save();
			$migrated++;
		}
		
		return new CLIResponse(0, sprintf('Migrated %d pupil skills.', $migrated));
	}
}
