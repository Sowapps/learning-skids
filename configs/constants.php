<?php
/** \file
 * All web site constants.
 *
 * @page constants Constants
 *
 * This file contains all the main constants, you will often work with it and you need to define your own.
 * You will find here constants like AUTHORNAME and SITENAME, and also path constants.\n
 * Configure others carefully and only if it's really necessary, libraries may require some.\n
 *
 * Set ERROR_LEVEL to put your website in production (with no error reports to the user).
 * This is compatible with multi-instance architecture, so you can set a dev version and
 * a production version using the same sources on you own server.
 * Official ERROR_LEVEL values are DEV_LEVEL (all errors) and PROD_LEVEL (no errors) and
 * ERROR_LEVEL is set depending on DEV_VERSION value (if set).
 */

defifn('ERROR_LEVEL', DEV_VERSION && !defined('FORCE_ERRORS') ? DEV_LEVEL : PROD_LEVEL);

defifn('DEV_TOOLS', DEV_VERSION && (defined('TERMINAL') || !empty($_SERVER['PHP_AUTH_USER'])));

// Theme
defifn('LAYOUT_MENU', 'menu-bootstrap3');

// LIB Initernationalization
defifn('LANG_FOLDER', '/languages');
defifn('DEFAULT_LOCALE', 'fr_FR');

// Static medias
defifn('THEMES_URL', WEB_ROOT . THEMES_FOLDER);
defifn('STATIC_ASSETS_URL', WEB_ROOT . '/static');
defifn('STYLE_URL', STATIC_ASSETS_URL . '/style');
defifn('VENDOR_URL', STATIC_ASSETS_URL . '/vendor');
defifn('IMAGES_URL', STATIC_ASSETS_URL . '/images');
defifn('JS_URL', STATIC_ASSETS_URL . '/js');

// Contact
defifn('ADMIN_EMAIL', 'f.hazard@sowapps.com');
defifn('DEV_EMAIL', ADMIN_EMAIL);
defifn('AUTHORNAME', 'Sowapps');
defifn('SITENAME', 'Learning Skids');// See also translation app_name
defifn('ADMINEMAIL', ADMIN_EMAIL);
defifn('DEVEMAIL', DEV_EMAIL);

// Object output asArray
define('OUTPUT_MODEL_USAGE', 'usage');
define('OUTPUT_MODEL_EDITION', 'edition');

// Domains
define('DOMAIN_CLASS', 'class');
define('DOMAIN_LEARNING_SKILL', 'learning-skill');
define('DOMAIN_PERSON', 'person');
define('DOMAIN_TRANSLATIONS', 'translations');

// Users
define('USER_SALT', 'Y*Ck5D=H');
defifn('DEFAULT_TIMEZONE', 'Europe/Paris');
defifn('DATE_SQL_DATETIME', 'Y-m-d H:i:s');
defifn('DATE_SQL_DATE', 'Y-m-d');

define('CRAC_CONTEXT_APPLICATION', 1);
define('CRAC_CONTEXT_AGENCY', 2);
define('CRAC_CONTEXT_RESOURCE', 3);

define('FILE_USAGE_USER_PICTURE', 'user_picture');
define('FILE_USAGE_INVOICE', 'invoice');

function listFileUsages() {
	return [
		FILE_USAGE_USER_PICTURE => ['type' => 'image'],
	];
}

define('FILE_SOURCETYPE_UPLOAD', 'upload');
define('FILE_SOURCETYPE_UPLOAD_CONVERTED', 'upload_converted');
// define('FILE_SOURCETYPE_DATAURI',			'datauri');
define('FILE_SOURCETYPE_PHPQRCODE', 'qrcode');
define('FILE_SOURCETYPE_LOCALDEMO', 'demo');
define('FILE_SOURCETYPE_WKPDF', 'wkpdf');
define('FILE_SOURCETYPE_FACEBOOK', 'fb');

function listFileSourceTypes() {
	return [FILE_SOURCETYPE_UPLOAD, FILE_SOURCETYPE_UPLOAD_CONVERTED, FILE_SOURCETYPE_PHPQRCODE, FILE_SOURCETYPE_WKPDF, FILE_SOURCETYPE_LOCALDEMO, FILE_SOURCETYPE_FACEBOOK];
}


defifn('TRANSLATIONS_PATH', STORE_PATH . 'translations/');

/* WKHTMLTOPDF */
defifn('WKHTMLTOPDF_EXEC', '/usr/local/bin/wkhtmltopdf');
