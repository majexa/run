<?php

$envPath = dirname(NGN_PATH);
$server = include $envPath.'/config/server.php';
$prefix = ($server['type'] == 'dev' ? 'dev.' : '');
$domain = $prefix.'showcase.masted.ru';
$siteCompilations = sfYaml::load(file_get_contents(RUN_PATH.'/siteCompilations.txt'));
foreach (array_keys($siteCompilations) as $n) {
  sys("php $envPath/pm/pm.php server local createProject domain=$domain+noPages=1", true);
  sys("php $envPath/run/site.php $domain siteCompilation n=$n", true);
  sys("php $envPath/pm/pm.php project local $domain delete", true);
}
Dir::copy(
  RUN_PATH.'/siteCompilations',
  $envPath.'/projects/'.$prefix.'/myninja.ru/u/siteCompilations'
);