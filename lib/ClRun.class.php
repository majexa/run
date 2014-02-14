<?php

class ClRun {

  function replace($path) {
    foreach (['NGN_PATH', 'NGN_ENV_PATH'] as $v) $path = str_replace($v, constant($v), $path);
    return $path;
  }

  function run(array $args) {
    Arr::checkEmpty($args, 0);
    if (!empty($args[1])) {
      $this->processIncludes($args[1]);
    }
    else {
      require RUN_PATH.'/defaultInit.php';
    }
    $this->processPath($args[0]);
  }

  protected function processIncludes($includes) {
    // указан 2-й параметр
    if (!isset($path) or !preg_match('/\$_SERVER\[[\'"]argv[\'"]\]/', file_get_contents($path))) {
      // если в скрипте нет использования параметров командной строки
      if (strstr($includes, '/')) {
        // если есть "/", значит 2-й параметр - инклюды
        foreach (explode(';', $includes) as $libPath) {
          $libPath = self::replace($libPath);
          if (Misc::hasSuffix('.php', $libPath)) require $libPath;
          else Lib::addFolder($libPath);
        }
      }
      elseif (strstr($includes, '+')) {
        // если есть "+", значит 2-й параметр - опции
        require_once NGN_PATH.'/lib/more/common/NgnCl.class.php';
        R::set('options', NgnCl::strParamsToArray($includes));
      }
    }
  }

  protected function processPath($initPath) {
    if (strstr($initPath, '(')) { // eval
      $cmd = trim($initPath);
      if ($cmd[strlen($cmd) - 1] != ';') $cmd = "$cmd;";
    }
    else {
      $path = self::replace($initPath.'.php');
      //if ($path[0] == '/' or $path[0] == '~');
      //else $path = RUN_PATH.'/run/'.$path;
    }
    if (($_path = realpath($path))) isset($cmd) ? eval($cmd) : include $_path;
    else throw new Exception("path '$path' not found");
    Cli::storeCommand(RUN_PATH.'/logs');
  }

}