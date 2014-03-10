#!/usr/bin/php
<?php

if (isset($_SERVER['argv'][1]) and $_SERVER['argv'][1] == 'site') {
  require_once __DIR__.'/siteStandAloneInit.php';
  (new ClRun)->run(array_slice($_SERVER['argv'], 3));
}
else {
  define('NGN_PATH', dirname(__DIR__).'/ngn');
  define('NGN_ENV_PATH', dirname(__DIR__));
  require_once NGN_PATH.'/init/core.php';
  require_once NGN_PATH.'/init/cli.php';
  define('PROJECT_KEY', 'run');
  define('LOGS_PATH', __DIR__.'/logs');
  define('DATA_PATH', __DIR__.'/data');
  define('RUN_PATH', __DIR__);
  require_once __DIR__.'/lib/ClRun.class.php';
  Lib::addFolder(__DIR__.'/lib');
  (new ClRun)->run(array_slice($_SERVER['argv'], 1));
}
