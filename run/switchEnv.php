<?php

Lib::addFolder(NGN_ENV_PATH.'/pm/lib');

// 4 важные константы
function outputCurrentConfig() {
  $d = [
    'core' => ['IS_DEBUG'],
    'more' => ['FORCE_STATIC_FILES_CACHE', 'SITE_DOMAIN'],
    'site' => ['ALLOW_SEND']
  ];
  print "Current projects configuration:\n";
  foreach (glob(NGN_ENV_PATH.'/projects/*') as $folder) {
    $project = basename($folder);
    $folder = "$folder/site/config/constants";
    if (!file_exists($folder)) continue;
    print "$project\n";
    foreach ($d as $name => $vv) {
      $file = "$folder/$name.php";
      foreach ($vv as $k) {
        if (Config::constantExists($file, $k)) print "  $k: ".Arr::formatValue(Config::getConstant($file, $k, true))."\n";
      }
    }
  }
}
if (!empty($_SERVER['argv'][2])) {
  $server = require NGN_ENV_PATH.'/config/server.php';
  $type = $_SERVER['argv'][2];
  if ($type == $server['sType']) {
    output("Server is already $type");
    return;
  }
  Config::updateSubVar(NGN_ENV_PATH.'/config/server.php', 'sType', $type);
  $projects = require NGN_ENV_PATH.'/config/projects.php';
  $method = (strstr($type, 'test') ? 'test' : 'prod').'Domain';
  foreach ($projects as &$v) $v['domain'] = PmCore::$method($v['domain'], $type);
  Config::updateVar(NGN_ENV_PATH.'/config/projects.php', $projects);
  system('php '.NGN_ENV_PATH.'/pm/pm.php localProjects updateConfig');
  system('php '.NGN_ENV_PATH.'/pm/pm.php localProjects updateIndex');
  system('php '.NGN_ENV_PATH.'/pm/pm.php localServer updateHosts');
}
outputCurrentConfig();