<?php

namespace App\Controller\Console;

use App\Entity\PupilSkillValue;
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
		
		return new CliResponse(0, sprintf('Migrated %d pupil skills.', $migrated));
	}
}
