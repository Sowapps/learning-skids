<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use App\Entity\ClassPupil;
use App\Entity\User;
use Orpheus\Exception\ForbiddenException;
use Orpheus\Exception\UserException;
use Orpheus\File\Generator\WkHtmlToPdfGenerator;
use Orpheus\InputController\HTTPController\HTMLHTTPResponse;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPResponse;
use Orpheus\InputController\HTTPController\LocalFileHTTPResponse;
use Orpheus\Publisher\SlugGenerator;
use Orpheus\Rendering\HTMLRendering;

class UserClassPupilExportController extends AbstractUserController {
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 */
	public function run($request) {
		$pupil = ClassPupil::load($request->getPathValue('pupilId'), false);
		$class = $pupil->getSchoolClass();
		$person = $pupil->getPerson();
		$learningSheet = $class->getLearningSheet();
		
		$debug = $request->getParameter('debug');
		$download = $request->getParameter('dl', true);
		
		if( !User::getLoggedUser()->canClassManage($class) ) {
			throw new ForbiddenException();
		}
		
		$render = new HTMLRendering();
		$render->setTheme('system');
		$render->setRemote($debug);
		
		$data = [
			'pupil'         => $pupil,
			'person'        => $person,
			'class'         => $class,
			'learningSheet' => $learningSheet,
			'title'         => sprintf("Fiche d'apprentissage de %s pour %s", $person, $class->year),
		];
		
		$bodyHtml = $render->render('pdf-pupil-learning-sheet', $data);
		//		$headerHtml = $render->render('pdf-header', $data);
		$footerHtml = $render->render('pdf-footer', $data);
		
		// Set downloaded cookie for js usage.
		setcookie('fileDownload', 'true', 0, '/');
		
		if( $debug ) {
			//			if( $debug === 'header' ) {
			//				return new HTMLHTTPResponse($headerHtml);
			//			}
			if( $debug === 'footer' ) {
				return new HTMLHTTPResponse($footerHtml);
			}
			
			return new HTMLHTTPResponse($bodyHtml);
		}
		
		$generator = new WkHtmlToPdfGenerator($bodyHtml);
		
		//		$generator->setHeaderHtml($headerHtml);
		$generator->setFooterHtml($footerHtml);
		
		if( !$generator->generate() ) {
			// Error while generating
			throw new UserException('Error while generating pupil record');
		}
		
		$slugGenerator = new SlugGenerator();
		$slugGenerator->setCaseProcessing(SlugGenerator::CASE_LOWER);
		
		return new LocalFileHTTPResponse(
			$generator->getOutputPath(),
			// fiche-d-apprentissage-annee-nom-prenom.pdf
			sprintf('fiche-d-apprentissage-%s-%s-%s.pdf', $class->year, $slugGenerator->format($person->lastname), $slugGenerator->format($person->firstname)),
			$download
		);
	}
	
}
