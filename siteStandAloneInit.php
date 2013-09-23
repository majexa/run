<?php

$curFile = basename(__FILE__).': ';

$domain = $_SERVER['argv'][1];
if (empty($domain)) die($curFile.'Project folder $domain not defined');

define('NGN_ENV_PATH', dirname(__DIR__));
define('NGN_PATH', NGN_ENV_PATH.'/ngn');
define('WEBROOT_PATH', NGN_ENV_PATH.'/projects/'.$domain);
define('RUN_PATH', __DIR__);

if (!file_exists(WEBROOT_PATH)) die($curFile."Webroot folder '".WEBROOT_PATH."' does not exists.");

define('SITE_PATH', WEBROOT_PATH.'/site');

require_once NGN_PATH.'/init/core.php';
Lib::addFolder(__DIR__.'/lib');

R::set('plainText', true);

require_once SITE_PATH.'/config/constants/core.php';
require_once SITE_PATH.'/config/constants/more.php';
require_once SITE_PATH.'/config/constants/site.php';

require_once NGN_PATH.'/init/more.php';
require_once NGN_PATH.'/init/cli.php';
require_once NGN_PATH.'/init/site.php';

require_once __DIR__.'/lib/ClRun.class.php';
if (file_exists(SITE_PATH.'/init.php')) require_once SITE_PATH.'/init.php';
NgnCache::clean();
