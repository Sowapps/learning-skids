<?php

namespace Orpheus\Controller\Developer;

use Orpheus\EntityDescriptor\EntityDescriptor;
use Orpheus\EntityDescriptor\LangGenerator;
use Orpheus\EntityDescriptor\PermanentEntity;
use Orpheus\EntityDescriptor\SqlGenerator\SqlGeneratorMySQL;
use Orpheus\Exception\UserException;
use Orpheus\Form\FormToken;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpResponse;
use Orpheus\Publisher\Exception\InvalidFieldException;
use Orpheus\SqlAdapter\Exception\SqlException;
use Orpheus\SqlAdapter\SqlAdapter;
use PDO;
use PDOStatement;

class DevEntitiesController extends DevController {
	
	/**
	 * @param HttpRequest $request The input HTTP request
	 * @return HttpResponse The output HTTP response
	 */
	public function run($request): HttpResponse {
		
		$this->addThisToBreadcrumb();
		
		$FORM_TOKEN = new FormToken();
		$env = [
			'FORM_TOKEN' => $FORM_TOKEN,
		];
		// TODO: Check and suggest to delete unknown tables in DB
		try {
			if( is_array($request->getData('entities')) ) {
				$output = null;
				if( $request->hasDataKey('submitGenerateSQL', $output) ) {
					$output = $output == OUTPUT_APPLY ? OUTPUT_APPLY : OUTPUT_DISPLAY;
					if( $output == OUTPUT_APPLY ) {
						$FORM_TOKEN->validateForm($request);
					}
					$generator = new SqlGeneratorMySQL();
					$result = [];
					foreach( $request->getArrayData('entities') as $entityClass => $on ) {
						/** @var PermanentEntity $entityClass */
						$query = $generator->matchEntity($entityClass::getValidator());
						if( $query ) {
							$result[$entityClass] = $query;
						}
					}
					
					$env['unknownTables'] = [];
					/* @var PDOStatement $statement */
					$statement = pdo_query('SHOW TABLES', PDOSTMT);
					$knownTables = [];
					foreach( PermanentEntity::listKnownEntities() as $entityClass ) {
						/** @var PermanentEntity $entityClass */
						$knownTables[$entityClass::getTable()] = 1;
					}
					while( $tableFetch = $statement->fetch(PDO::FETCH_NUM) ) {
						$table = $tableFetch[0];
						if( isset($knownTables[$table]) ) {
							continue;
						}
						$env['unknownTables'][$table] = 1;
					}
					
					if( empty($result) ) {
						throw new UserException('No changes');
					}
					$env['resultingSQL'] = implode('', $result);
					if( $output == OUTPUT_DISPLAY ) {
						$env['requireEntityValidation'] = 1;
					} elseif( $output == OUTPUT_APPLY ) {
						foreach( $result as $query ) {
							pdo_query(strip_tags($query), PDOEXEC);
						}
						$tablesToRemove = $request->getData('removeTable');
						foreach( $env['unknownTables'] as $table => $on ) {
							if( empty($tablesToRemove[$table]) ) {
								// Not selected
								continue;
							}
							try {
								pdo_query(sprintf('DROP TABLE `%s`', SqlAdapter::getInstance()->escapeIdentifier($table)), PDOEXEC);
							} catch( SqlException $e ) {
								reportError('Unable to drop table ' . $table . ', cause: ' . $e->getMessage());
							}
						}
						reportSuccess('successSqlApply', DOMAIN_SETUP);
					}
					
				} elseif( $request->hasData('submitGenerateVE') ) {
					$output = $request->getData('ve_output') == OUTPUT_DLRAW ? OUTPUT_DLRAW : OUTPUT_DISPLAY;
					$generator = new LangGenerator();
					$result = '';
					foreach( $request->getArrayData('entities') as $entityClass => $on ) {
						/** @var PermanentEntity $entityClass */
						$entityName = $entityClass::getTable();
						$result .= "\n\n\t$entityName.ini\n";
						foreach( $generator->getRows(EntityDescriptor::load($entityName)) as $k => $exc ) {
							/* @var $exc InvalidFieldException */
							$exc->setDomain('entity_model');
							$exc->removeArgs();//Does not replace arguments
							// Tab size is 4 (as my editor's config)
							$result .= $k . str_repeat("\t", 11 - floor(strlen($k) / 4)) . '= "' . $exc->getText() . "\"\n";
						}
					}
					if( $output === OUTPUT_APPLY ) {
						reportError('Output not implemented !');
					} else {
						echo '<pre style="tab-size: 4; -moz-tab-size: 4;">' . $result . '</pre>';
					}
				}
			}
			
		} catch( UserException $e ) {
			if( $e->getMessage() === 'errorNoChanges' ) {
				reportWarning($e);
			} else {
				reportError($e);
			}
		}
		
		return $this->renderHtml('developer/dev_entities', $env);
	}
	
}

define('OUTPUT_APPLY', 1);
define('OUTPUT_DISPLAY', 2);
define('OUTPUT_DLRAW', 3);
define('OUTPUT_DLZIP', 4);
