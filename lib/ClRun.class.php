<?php

class ClRun {

  static function replace($path) {
    if ($path[0] != '/') $path = NGN_ENV_PATH.'/'.$path;
    foreach (['NGN_PATH', 'NGN_ENV_PATH'] as $v) $path = str_replace($v, constant($v), $path);
    return $path;
  }

  function run(array $args) {
    if (empty($args[0])) {
      $this->help();
      return;
    }
    Arr::checkEmpty($args, 0);
    $probableInitPath = dirname($args[0]).'/init.php';
    $probableLibFolder = dirname($args[0]);
    $include = false;
    if (file_exists(self::replace($probableInitPath))) $include = $probableInitPath;
    elseif (file_exists(self::replace($probableLibFolder))) $include = $probableLibFolder;
    if (!empty($args[1])) {
      if ($this->isOptionsArg($args[1])) {
        require_once NGN_PATH.'/more/lib/common/NgnCl.class.php';
        R::set('options', NgnCl::strParamsToArray($args[1]));
      }
    }
    if ($include) {
      $include = self::replace($include);
      if (Misc::hasSuffix('.php', $include)) require_once $include;
      else Lib::addFolder($include);
      Lib::cache($include);
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
      output2($cmd);
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
