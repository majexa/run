#!/usr/bin/php
<?php

// example: ./site.php projectName scriptName

define('IS_DEUBG', true);

require_once __DIR__.'/siteStandAloneInit.php';

if (empty($_SERVER['argv'][2])) die('Script file $_SERVER[\'argv\'][2] not defined');

$file = __DIR__.'/site/'.$_SERVER['argv'][2].'.php';

Cli::storeCommand(__DIR__.'/logs');

if (file_exists($file)) require_once $file;
else throw new Exception("File '$commonFile' and '$siteFile' does not exists");