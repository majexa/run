<?php

class ClRun {

  function replace($path) {
    foreach (['NGN_PATH', 'NGN_ENV_PATH'] as $v) $path = str_replace($v, constant($v), $path);
    return $path;
  }

  function run(array $args) {
    Arr::checkEmpty($args, 0);
    if (strstr($args[0], '(')) { // eval
      $cmd = trim($args[0]);
      if ($cmd[strlen($cmd)-1] != ';') $cmd = "$cmd;";
    } else {
      $path = self::replace($args[0].'.php');
      if ($path[0] == '/' or $path[0] == '~');
      else $path = __DIR__.'/run/'.$path;
    }
    if (!empty($args[1])) {
      // указан 2-й параметр
      if (!isset($path) or !preg_match('/\$_SERVER\[[\'"]argv[\'"]\]/', file_get_contents($path))) {
        // если в скрипте нет использования параметров командной строки
        if (strstr($args[1], '/')) {
          // если есть "/", значит 2-й параметр - инклюды
          foreach (explode(';', $args[1]) as $libPath) {
            $libPath = self::replace($libPath);
            if (Misc::hasSuffix('.php', $libPath)) require $libPath;
            else Lib::addFolder($libPath);
          }
        } elseif (strstr($args[1], '+')) {
          // если есть "+", значит 2-й параметр - опции
          require_once NGN_PATH.'/lib/more/common/NgnCl.class.php';
          R::set('options', NgnCl::strParamsToArray($args[1]));
        }
      }
    }
    isset($cmd) ? eval($cmd) : include $path;
    Cli::storeCommand(RUN_PATH.'/logs');
  }

}