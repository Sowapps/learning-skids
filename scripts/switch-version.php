#!/usr/bin/env php
<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

# Change working directory to this file's folder

chdir(__DIR__ . '/..');

define('APPLICATION_PATH', getcwd() . '/');
define('TERMINAL', 1);

function writeError($text, $die = true) {
	fwrite(STDERR, $text . PHP_EOL);
	$die && die();
}

if( !$argc ) {
	writeError('Only working with command line');
}

$currentUser = isset($_SERVER['USER']) ? $_SERVER['USER'] : null;

if( $currentUser !== 'root' ) {
	writeError("Only root can run mysql commands with default config !");
	exit(1);
}

function showUsage() {
	global $scriptName;
	echo sprintf('%s [VERSION_FROM] VERSION_TO', $scriptName);
}

function writeUsageError($text) {
	writeError($text, false);
	echo "\n";
	showUsage();
	die();
}

// Parse input
if( empty($argv[1]) ) {
	writeUsageError('Missing "VERSION_TO" parameter');
}

class Version {
	
	public $label;
	public $code;
	
	public function __construct($label) {
		$this->label = $label;
		$this->code = substr($label, 1);
	}
	
	public function __toString() {
		return $this->label;
	}
	
	public function isGreaterOrEqualTo(Version $otherVersion) {
		return $this->compareTo($otherVersion) > -1;
	}
	
	public function compareTo(Version $otherVersion) {
		return version_compare($this->code, $otherVersion->code);
	}
	
	public function isLesserOrEqualTo(Version $otherVersion) {
		return $this->compareTo($otherVersion) < 1;
	}
	
}

function checkFile($file, &$outputPath) {
	if( is_readable($file) ) {
		$outputPath = $file;
		
		return true;
	}
	
	return false;
}

class Upgrade {
	
	public static $folderPath = APPLICATION_PATH . 'scripts/upgrade';
	
	public $path;
	public $from;
	public $to;
	
	public function __construct($path) {
		$basename = basename($path);
		$versions = explode('-', $basename);
		if( count($versions) !== 2 ) {
			throw new Exception('Invalid upgrade file');
		}
		$this->path = $path;
		$this->from = new Version($versions[0]);
		$this->to = new Version($versions[1]);
	}
	
	public function getScript($isUpgrade) {
		return $isUpgrade ? $this->getUpgradeScript() : $this->getDowngradeScript();
	}
	
	public function getUpgradeScript() {
		checkFile($this->path . '/upgrade.php', $path) ||
		checkFile($this->path . '/upgrade.sh', $path);
		
		return $path;
	}
	
	public function getDowngradeScript() {
		checkFile($this->path . '/downgrade.php', $path) ||
		checkFile($this->path . '/downgrade.sh', $path);
		
		return $path;
	}
	
	/**
	 * @return Upgrade[]
	 */
	public static function getAllAvailableUpgrades() {
		$upgrades = [];
		foreach( scandir(self::$folderPath) as $upgradeFolder ) {
			if( $upgradeFolder[0] === '.' ) {
				continue;
			}
			try {
				$upgrades[] = new Upgrade(self::$folderPath . '/' . $upgradeFolder);
			} catch( Exception $e ) {
				// Ignore invalid files
				var_dump($e->getMessage());
			}
		}
		
		return $upgrades;
	}
	
}

//define('ENVIRONMENT_FILE', APPLICATION_PATH . 'configs/environment.json');
//require_once APPLICATION_PATH . 'libs/src/Sowapps/Environment/Environment.php';
//require_once APPLICATION_PATH . 'libs/src/Sowapps/Environment/EnvironmentProject.php';
//function getApplicationVersion() {
//	$environment = Environment::get();
//	return $environment->getProject()->getVersion();
//}
define('STORE_PATH', APPLICATION_PATH . 'store');
define('UPGRADE_FILE', STORE_PATH . '/upgrade.json');
function getUpgradeVersion() {
	if( !is_readable(UPGRADE_FILE) ) {
		return 'v0.0';
	}
	$version = file_get_contents(UPGRADE_FILE);
	if( !$version ) {
		return 'v0.0';
	}
	
	return $version;
}

/**
 * @param Version $version
 * @return false|int|string
 */
function setUpgradeVersion($version) {
	$version = file_put_contents(UPGRADE_FILE, $version->label);
	if( !$version ) {
		return 'v0.0';
	}
	
	return $version;
}

$from = new Version(isset($argv[2]) ? $argv[1] : getUpgradeVersion());
$to = new Version(isset($argv[2]) ? $argv[2] : $argv[1]);

$comparison = $from->compareTo($to);
if( !$comparison ) {
	writeUsageError('"VERSION_FROM" and "VERSION_TO" are same versions.');
}

// Upgrade or downgrade ?
$isUpgrade = $comparison < 0;

$upgrades = Upgrade::getAllAvailableUpgrades();

// Look for upgrades between both version
// In downgrade, we are looking for downgrade script that are in upgrade folders, so we invert from and to to get it
// To run an upgrade, it must have a FROM equals or greater than requested FROM and a TO equals or lesser than requested TO
// Also, it must provide the upgrade.sh or downgrade.sh we need

$upgradeFrom = $isUpgrade ? $from : $to;
$upgradeTo = $isUpgrade ? $to : $from;

$requiredUpgrades = [];
foreach( $upgrades as $upgrade ) {
	if(
		$upgrade->from->isGreaterOrEqualTo($upgradeFrom) &&
		$upgrade->to->isLesserOrEqualTo($upgradeTo) &&
		$upgrade->getScript($isUpgrade)
	) {
		$requiredUpgrades[] = $upgrade;
	}
}
unset($upgrades);

// Order upgrade / downgrades

usort($requiredUpgrades, function ($a, $b) {
	/** @var Upgrade $a */
	/** @var Upgrade $b */
	$compare = $a->to->compareTo($b->to);
	if( !$compare ) {
		$compare = $a->from->compareTo($b->from);
	}
	
	return $compare;
});
if( !$isUpgrade ) {
	$requiredUpgrades = array_reverse($requiredUpgrades);
}

// Run upgrades / downgrades
foreach( $requiredUpgrades as $upgrade ) {
	$command = $upgrade->getScript($isUpgrade);
	echo sprintf("Run %s script from %s to %s :\n  %s\n",
		$isUpgrade ? 'upgrade' : 'downgrade',
		$isUpgrade ? $upgrade->from : $upgrade->to,
		$isUpgrade ? $upgrade->to : $upgrade->from,
		$command);
	`$command`;
}

setUpgradeVersion($to);
