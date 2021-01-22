<?php
/**
 * All web site defaults.
 * 
 * TODO: Write documentation :-P
 */


// defifn('CHECK_MODULE_ACCESS',	!DEV_VERSION);
defifn('ENTITY_CLASS_CHECK',	false);
// defifn('ENTITY_ALWAYS_RELOAD',	true);


// Routes
define('ROUTE_HOME', 'home');
define('ROUTE_LOGIN', 'login');
define('ROUTE_LOGOUT', 'logout');
define('ROUTE_FILE_DOWNLOAD', 'file_download');
define('ROUTE_DOWNLOAD_LATEST', 'download_latest');
define('ROUTE_DOWNLOAD_RELEASES', 'download_releases');

define('ROUTE_USER_HOME', 'user_home');

define('ROUTE_ADM_HOME', 'admin_home');
define('ROUTE_ADM_USER_LIST', 'adm_user_list');
define('ROUTE_ADM_USER', 'adm_user');
define('ROUTE_ADM_MYSETTINGS', 'adm_mysettings');

define('ROUTE_DEV_HOME', 'dev_home');
define('ROUTE_DEV_CONFIG', 'dev_config');
define('ROUTE_DEV_SYSTEM', 'dev_system');
define('ROUTE_DEV_COMPOSER', 'dev_composer');
define('ROUTE_DEV_ENTITIES', 'dev_entities');
define('ROUTE_DEV_LOGS', 'dev_loglist');
define('ROUTE_DEV_LOG_VIEW', 'dev_log_view');
define('ROUTE_DEV_APPTRANSLATE', 'dev_app_translate');

// Route's defaults
define('DEFAULT_ROUTE', ROUTE_LOGIN);
define('USER_DEFAULT_ROUTE', ROUTE_USER_HOME);
define('DEFAULT_HOST', 'yourdomain.com');
define('DEFAULT_PATH', '');

