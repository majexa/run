<?php

$curFile = basename(__FILE__).': ';
$projectName = defined('PROJECT') ? PROJECT : $_SERVER['argv'][2];
if (empty($projectName)) die($curFile.'Project folder $domain not defined');

define('NGN_ENV_PATH', dirname(__DIR__));
define('NGN_PATH', NGN_ENV_PATH.'/ngn');
define('WEBROOT_PATH', NGN_ENV_PATH.'/projects/'.$projectName);
define('RUN_PATH', __DIR__);
//define('IS_DEBUG', true);
if (!file_exists(WEBROOT_PATH)) die($curFile."Webroot folder '".WEBROOT_PATH."' does not exists.\n");
define('PROJECT_PATH', WEBROOT_PATH.'/site');

require_once NGN_PATH.'/init/core.php';
Lib::addFolder(__DIR__.'/lib');

R::set('plainText', true);

require_once NGN_PATH.'/init/project-cli.php';

require_once __DIR__.'/lib/ClRun.class.php';
if (file_exists(PROJECT_PATH.'/init.php')) require_once PROJECT_PATH.'/init.php';
