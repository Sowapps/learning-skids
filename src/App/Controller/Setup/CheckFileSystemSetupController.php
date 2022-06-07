<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace App\Controller\Setup;

use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpResponse;

class CheckFileSystemSetupController extends SetupController {
	
	protected static $routeName = 'setup_checkfs';
	
	/**
	 * @param HttpRequest $request The input HTTP request
	 * @return HttpResponse The output HTTP response
	 */
	public function run($request): HttpResponse {
		
		$env = [
			'folders'       => [],
			'allowContinue' => true,
		];
		
		if( is_writable(ACCESSPATH) ) {
			$env['folders']['accesspath'] = (object) [
				'title'       => t('folderWritable_error_title', DOMAIN_SETUP, t('folder_webaccess', DOMAIN_SETUP)),
				'description' => t('folderWritable_error_description', DOMAIN_SETUP, ACCESSPATH),
				'panel'       => PANEL_WARNING,
				'open'        => 1,
			];
		} else {
			$env['folders']['accesspath'] = (object) [
				'title'       => t('folderNotWritable_success_title', DOMAIN_SETUP, t('folder_webaccess', DOMAIN_SETUP)),
				'description' => t('folderNotWritable_success_description', DOMAIN_SETUP, ACCESSPATH),
				'panel'       => PANEL_SUCCESS,
				'open'        => 0,
			];
		}
		if( is_writable(STOREPATH) ) {
			$env['folders']['storepath'] = (object) [
				'title'       => t('folderWritable_success_title', DOMAIN_SETUP, t('folder_store', DOMAIN_SETUP)),
				'description' => t('folderWritable_success_description', DOMAIN_SETUP, STOREPATH),
				'panel'       => PANEL_SUCCESS,
				'open'        => 0,
			];
		} else {
			$env['allowContinue'] = false;
			$env['folders']['storepath'] = (object) [
				'title'       => t('folderNotWritable_error_title', DOMAIN_SETUP, t('folder_store', DOMAIN_SETUP)),
				'description' => t('folderNotWritable_error_description', DOMAIN_SETUP, STOREPATH),
				'panel'       => PANEL_DANGER,
				'open'        => 1,
			];
		}
		if( is_writable(LOGS_PATH) ) {
			$env['folders']['logspath'] = (object) [
				'title'       => t('folderWritable_success_title', DOMAIN_SETUP, t('folder_logs', DOMAIN_SETUP)),
				'description' => t('folderWritable_success_description', DOMAIN_SETUP, LOGS_PATH),
				'panel'       => PANEL_SUCCESS,
				'open'        => 0,
			];
		} else {
			$env['allowContinue'] = false;
			$env['folders']['logspath'] = (object) [
				'title'       => t('folderNotWritable_error_title', DOMAIN_SETUP, t('folder_logs', DOMAIN_SETUP)),
				'description' => t('folderNotWritable_error_description', DOMAIN_SETUP, LOGS_PATH),
				'panel'       => PANEL_DANGER,
				'open'        => 1,
			];
		}
		
		if( $env['allowContinue'] ) {
			$this->validateStep();
		}
		
		return $this->renderHtml('setup/setup_checkfs', $env);
	}
	
}

define('PANEL_SUCCESS', 'panel-success');
define('PANEL_WARNING', 'panel-warning');
define('PANEL_DANGER', 'panel-danger');
