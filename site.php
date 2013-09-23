#!/usr/bin/php
<?php

// примеры:
// php site.php projectName '(new StandardClass)->method()'

define('IS_DEUBG', true);
require_once __DIR__.'/siteStandAloneInit.php';

//if (!class_exists('ClRun')) die2(Lib::$firstCallBacktrace);
(new ClRun)->run(array_slice($_SERVER['argv'], 2));