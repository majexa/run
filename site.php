#!/usr/bin/php
<?php

print_r($_SERVER['argv']); die('...');

// примеры:
// php site.php projectName '(new StandardClass)->method()'

require_once __DIR__.'/siteStandAloneInit.php';

(new ClRun)->run(array_slice($_SERVER['argv'], 2));