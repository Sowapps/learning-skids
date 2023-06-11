#!/usr/bin/env php
<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

# Change working directory to this file's folder
chdir(__DIR__ . '/..');

passthru('php app/console/run.php upgrade-database');
