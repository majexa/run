<?php

class ClRun {

  static function replace($path) {
    foreach (['NGN_PATH', 'NGN_ENV_PATH'] as $v) $path = str_replace($v, constant($v), $path);
    return $path;
  }

  function run(array $args, array $initArgs) {
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
    $this->processPath($args, $initArgs);
  }

  protected function runner() {
    return O::get('CliColors')->getColoredString('run', 'brown');
  }

  protected function caption($text) {
    return O::get('CliColors')->getColoredString('-- '.$text, 'cyan');
  }

  protected function help() {
    print O::get('CliColors')->getColoredString('Supported commands:', 'yellow')."\n";
    $methods = array_filter((new ReflectionClass($this))->getMethods(ReflectionMethod::IS_PUBLIC), function (ReflectionMethod $m) {
      return $m->getName() != 'run' and !$m->isStatic();
    });
    foreach ($methods as $method) print $this->runner().' '.$method->getName().(O::get('CliColors')->getColoredString(' -- Clears run envirnment cache', 'cyan'))."\n";
    print $this->runner().' cmd/path'."\n";
    print $this->runner().' site projectName cmd/path/ngn'.(O::get('CliColors')->getColoredString(' -- cmd: "new Class()" / path: NGN_ENV_PATH/path/to/libOrFile / ngn: just type it', 'cyan'))."\n";
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
  }

  function cc() {
    FileCache::clean();
    print "done.\n";
  }

  protected function processPath(array $args, array $initArgs) {
    $initPath = $args[0];
    if (method_exists($this, $initPath)) {
      $this->$initPath();
      return;
    }
    if (strstr($initPath, '(')) { // eval
      $cmd = trim($initPath);
      if ($cmd[strlen($cmd) - 1] != ';') $cmd = "$cmd;";
      eval($cmd);
    }
    elseif ($initPath == 'ngn') {
      new NgnCli($args, ['runner' => 'run '.implode(' ', array_slice($initArgs, 1, 3))]);
    }
    else {
      $path = self::replace($initPath.'.php');
      if (!($path = realpath($path))) throw new Exception("path '$initPath' not found");
      include $path;
    }
    Cli::storeCommand(RUN_PATH.'/logs');
  }

}
