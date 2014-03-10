<?php

class ClRun {

  static function replace($path) {
    foreach (['NGN_PATH', 'NGN_ENV_PATH'] as $v) $path = str_replace($v, constant($v), $path);
    return $path;
  }

  function run(array $args) {
    if (empty($args[0])) {
      $this->help();
      return;
    }
    Arr::checkEmpty($args, 0);
    if (!empty($args[1])) {
      $this->processIncludes($args[1]);
    }
    else {
      require RUN_PATH.'/defaultInit.php';
    }
    $this->processPath($args[0]);
  }

  protected function runner() {
    return O::get('CliColors')->getColoredString('run', 'brown');
  }

  protected function caption($text) {
    return O::get('CliColors')->getColoredString('-- '.$text, 'cyan');
  }

  protected function help() {
    print O::get('CliColors')->getColoredString('Supported commands:', 'yellow')."\n";
    print $this->runner().' cmd/path'."\n";
    print $this->runner().' site projectName cmd/path'."\n";
    print O::get('CliColors')->getColoredString('cmd/path variants:', 'yellow')."\n";
    print "* (new Class('a'))->run()\n* /path/to/file\n* NGN_ENV_PATH/path/to/file\n* NGN_PATH/path/to/file\n";
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
        Lib::cache($includes);
      }
      elseif (strstr($includes, '+')) {
        // если есть "+", значит 2-й параметр - опции
        require_once NGN_PATH.'/lib/more/common/NgnCl.class.php';
        R::set('options', NgnCl::strParamsToArray($includes));
      }
    }
    //die2($includes);
  }

  protected function processPath($initPath) {
    if (strstr($initPath, '(')) { // eval
      $cmd = trim($initPath);
      if ($cmd[strlen($cmd) - 1] != ';') $cmd = "$cmd;";
      eval($cmd);
    }
    else {
      $path = self::replace($initPath.'.php');
      if (!($path = realpath($path))) throw new Exception("path '$initPath' not found");
      include $path;
    }
    Cli::storeCommand(RUN_PATH.'/logs');
  }

}
