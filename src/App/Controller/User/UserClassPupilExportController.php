<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\User;

use App\Entity\ClassPupil;
use App\Entity\User;
use DateTime;
use Orpheus\Exception\ForbiddenException;
use Orpheus\Exception\UserException;
use Orpheus\File\Generator\WkHtmlToPdfGenerator;
use Orpheus\InputController\HttpController\HtmlHttpResponse;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpResponse;
use Orpheus\InputController\HttpController\LocalFileHttpResponse;
use Orpheus\Publisher\SlugGenerator;
use Orpheus\Rendering\HtmlRendering;

class UserClassPupilExportController extends AbstractUserController {
	
	/**
	 * @param HttpRequest $request The input HTTP request
	 * @return HttpResponse The output HTTP response
	 */
	public function run($request): HttpResponse {
		$pupil = ClassPupil::load($request->getPathValue('pupilId'), false);
		$class = $pupil->getSchoolClass();
		$person = $pupil->getPerson();
		$learningSheet = $class->getLearningSheet();
		
		$debug = $request->getParameter('debug', false);
		$download = $request->getParameter('dl', true);
		
		if( !User::getLoggedUser()->canClassManage($class) ) {
			throw new ForbiddenException();
		}
		
		$render = new HtmlRendering();
		$render->setTheme('system');
		$render->setRemote($debug);
		
		$date = new DateTime('previous month');
		$dateText = sprintf('%s %s', formatDateMonth($date), $date->format('Y'));
		
		$data = [
			'pupil'         => $pupil,
			'person'        => $person,
			'class'         => $class,
			'learningSheet' => $learningSheet,
			'dateText'      => $dateText,
			'title'         => sprintf("Exploits de %s pour %s", $person, $class->year),
		];
		
		$bodyHtml = $render->render('pdf-pupil-learning-sheet', $data);
		//		$headerHtml = $render->render('pdf-header', $data);
		$footerHtml = $render->render('pdf-footer', $data);
		
		// Set downloaded cookie for js usage.
		setcookie('fileDownload', 'true', 0, '/');
		
		if( $debug ) {
			//			if( $debug === 'header' ) {
			//				return new HtmlHttpResponse($headerHtml);
			//			}
			if( $debug === 'footer' ) {
				return new HtmlHttpResponse($footerHtml);
			}
			
			return new HtmlHttpResponse($bodyHtml);
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
		
		return new LocalFileHttpResponse(
			$generator->getOutputPath(),
			// fiche-d-apprentissage-annee-nom-prenom.pdf
			sprintf('fiche-d-apprentissage-%s-%s-%s-%s.pdf', $class->year, $slugGenerator->format($person->lastname), $slugGenerator->format($person->firstname), $date->format('Ym')),
			$download
		);
	}
	
}
