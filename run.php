#!/usr/bin/php
<?php

define('NGN_PATH', dirname(__DIR__).'/ngn');
define('NGN_ENV_PATH', dirname(__DIR__));

require_once NGN_PATH.'/init/core.php';
require_once NGN_PATH.'/init/cli.php';
define('LOGS_PATH', __DIR__.'/logs');
define('RUN_PATH', __DIR__);

Lib::addFolder(__DIR__.'/lib');
Lib::addFolder(NGN_ENV_PATH.'/scripts/web/site/lib');

if (empty($_SERVER['argv'][1])) die('Scriptr file $_SERVER[\'argv\'][1] not defined');

$path = $_SERVER['argv'][1].'.php';
foreach (['NGN_PATH', 'NGN_ENV_PATH'] as $v) $path = str_replace($v, constant($v), $path);
if ($path[0] == '/' or $path[0] == '~');
else $path = __DIR__.'/run/'.$path;

require_once $path;

