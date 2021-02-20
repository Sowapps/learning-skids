#!/usr/bin/env php
<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

# Change working directory to this file's folder
chdir(__DIR__ . '/..');

passthru('/usr/bin/php7.4 app/console/run.php upgrade-database');
