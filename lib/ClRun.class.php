<?php

class ClRun {

  static function replace($path) {
    if ($path[0] != '/') $path = NGN_ENV_PATH.'/'.$path;
    foreach (['NGN_PATH', 'NGN_ENV_PATH'] as $v) $path = str_replace($v, constant($v), $path);
    return $path;
  }

  function run(array $args, array $initArgs) {
    if (empty($args[0])) {
      $this->help();
      return;
    }
    Arr::checkEmpty($args, 0);
    $includes = '';
    if (strstr($args[0], '/')) {
      $probableLibFolder = dirname($args[0]);
      if (file_exists(self::replace($probableLibFolder))) $includes .= ($includes ? ';' : '').$probableLibFolder;
      $probableInitPath = dirname($args[0]).'/init.php';
      if (file_exists(self::replace($probableInitPath))) $includes .= ($includes ? ';' : '').$probableInitPath;
    }
    if (!empty($args[1])) {
      if ($this->isOptionsArg($args[1])) {
        require_once NGN_PATH.'/more/lib/common/NgnCl.class.php';
        R::set('options', NgnCl::strParamsToArray($args[1]));
      } else {
        if (isset($probableLibFolder) and $probableLibFolder == $args[1]) throw new Exception('no need to require twice');
        $includes .= ($includes ? ';' : '').$args[1];
      }
    }
    if ($includes) {
      // если есть "/", значит 2-й параметр - инклюды
      foreach (explode(';', $includes) as $libPath) {
        $libPath = self::replace($libPath);
        if (Misc::hasSuffix('.php', $libPath)) require_once $libPath;
        else Lib::addFolder($libPath);
      }
      Lib::cache($includes);
    } else {
      require RUN_PATH.'/defaultInit.php';
    }
    if (file_exists(__DIR__.'/runConfig.php')) require_once __DIR__.'/runConfig.php';
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
    $methods = array_filter((new ReflectionClass($this))->getMethods(ReflectionMethod::IS_PUBLIC), function (ReflectionMethod $m) {
      return $m->getName() != 'run' and !$m->isStatic();
    });
    foreach ($methods as $method) print $this->runner().' '.$method->getName().(O::get('CliColors')->getColoredString(' -- Clears run envirnment cache', 'cyan'))."\n";
    print $this->runner().' cmd/path'."\n";
    print $this->runner().' site projectName cmd/path/ngn'.(O::get('CliColors')->getColoredString(' -- cmd: "new Class()" / path: NGN_ENV_PATH/path/to/libOrFile / ngn: just type it', 'cyan'))."\n";
    print O::get('CliColors')->getColoredString('cmd/path variants:', 'yellow')."\n";
    print "* (new Class('a'))->run()\n* /path/to/file\n* NGN_ENV_PATH/path/to/file\n* NGN_PATH/path/to/file\n";
  }

  protected function isOptionsArg($arg) {
    return (bool)strstr($arg, '=');
  }

  function cc() {
    foreach (glob(DATA_PATH.'/cache/*---*') as $file) unlink($file);
    print "done.\n";
  }

  protected function processPath($path) {
    if (method_exists($this, $path)) {
      $this->$path();
      return;
    }
    if (strstr($path, '(')) { // eval
      $cmd = trim($path);
      if ($cmd[strlen($cmd) - 1] != ';') $cmd = "$cmd;";
      eval($cmd);
    }
    elseif ($path == 'ngn') {
      throw new Exception('depricated');
      // new NgnCli($args, ['runner' => 'run '.implode(' ', array_slice($initArgs, 1, 3))]);
    }
    else {
      $path = self::replace($path.'.php');
      if (!($_path = realpath($path))) throw new Exception("path '$path' not found");
      include $_path;
    }
    Cli::storeCommand(RUN_PATH.'/logs');
  }

}
