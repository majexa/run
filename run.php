#!/usr/bin/php
<?php

// примеры:
// php run.php '(new StandardClass)->method()'
// php run.php '(new SpecificClass)->method()' NGN_ENV_PATH/specific/lib
// php run.php NGN_ENV_PATH/path/to/script.php NGN_ENV_PATH/specific/init.php

define('NGN_PATH', dirname(__DIR__).'/ngn');
define('NGN_ENV_PATH', dirname(__DIR__));

require_once NGN_PATH.'/init/core.php';
require_once NGN_PATH.'/init/cli.php';
define('PROJECT_KEY', 'run');
define('LOGS_PATH', __DIR__.'/logs');
define('RUN_PATH', __DIR__);
require_once __DIR__.'/lib/ClRun.class.php';
Lib::addFolder(__DIR__.'/lib');

(new ClRun)->run(array_slice($_SERVER['argv'], 1));