#!/usr/bin/php
<?php

if (isset($_SERVER['argv'][1]) and $_SERVER['argv'][1] == 'site') {
  require_once __DIR__.'/projectStandAloneInit.php';
  (new ClRun($_SERVER['argv'][2]))->run(array_slice($_SERVER['argv'], 3));
}
else {
  define('NGN_PATH', dirname(__DIR__).'/ngn');
  define('NGN_ENV_PATH', dirname(__DIR__));
  define('PROJECT_KEY', 'run');
  define('LOGS_PATH', __DIR__.'/logs');
  define('LOG_OUTPUT', false);
  define('DATA_PATH', __DIR__.'/data');
  define('RUN_PATH', __DIR__);
  define('PROJECT_PATH', RUN_PATH);
  require_once NGN_PATH.'/init/more.php';
  require_once NGN_PATH.'/init/cli.php';
  require_once __DIR__.'/lib/ClRun.class.php';
  Lib::addFolder(__DIR__.'/lib');
  (new ClRun)->run(array_slice($_SERVER['argv'], 1));
}
