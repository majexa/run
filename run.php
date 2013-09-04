#!/usr/bin/php
<?php

define('NGN_PATH', dirname(__DIR__).'/ngn');
define('NGN_ENV_PATH', dirname(__DIR__));

require_once NGN_PATH.'/init/core.php';
require_once NGN_PATH.'/init/cli.php';
define('PROJECT_KEY', 'run');
define('LOGS_PATH', __DIR__.'/logs');
define('RUN_PATH', __DIR__);

Lib::addFolder(__DIR__.'/lib');

function replace($path) {
  foreach (['NGN_PATH', 'NGN_ENV_PATH'] as $v) $path = str_replace($v, constant($v), $path);
  return $path;
}

if (!empty($_SERVER['argv'][2])) {
  foreach (explode(';', $_SERVER['argv'][2]) as $path) {
    $path = replace($path);
    if (Misc::hasSuffix('.php', $path)) require $path;
    else Lib::addFolder($path);
  }
}

if (empty($_SERVER['argv'][1])) die('Script file $_SERVER[\'argv\'][1] not defined');

if (strstr($_SERVER['argv'][1], '(')) { // eval
  $cmd = trim($_SERVER['argv'][1]);
  if ($cmd[strlen($cmd)-1] != ';') $cmd = "$cmd;";
  eval($cmd);
  return;
}

$path = replace($_SERVER['argv'][1].'.php');
if ($path[0] == '/' or $path[0] == '~');
else $path = __DIR__.'/run/'.$path;

include $path;