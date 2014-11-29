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
    Err::setEntryCmd('run '.implode($args));
    $include = false;
    if (!$this->isCode($args[0]) and file_exists($args[0])) {
      // Если первый параметр - путь, смотрим вероятные файлы/папки для инициализации относительно него
      $probableInitPath = dirname($args[0]).'/init.php';
      $probableLibFolder = dirname($args[0]);
      if (file_exists(self::replace($probableInitPath))) {
        output("Probable init path exists: $probableInitPath");
        $include = $probableInitPath;
      }
      elseif (file_exists(self::replace($probableLibFolder))) {
        output("Probable lib folder exists: $probableLibFolder");
        $include = $probableLibFolder;
      }
    }
    if (!empty($args[1])) {
      if ($this->isOptionsArg($args[1])) {
        require_once CORE_PATH.'/lib/cli/Cli.class.php';
        R::set('options', Cli::strParamsToArray($args[1]));
      }
      else {
        $include = $args[1];
      }
    }
    if ($include) {
      $include = self::replace($include);
      $probableInitPath = $include.'/init.php';
      if (!Misc::hasSuffix('.php', $include)) Lib::addFolder($include);
      Lib::enableCache($include);
      if (Misc::hasSuffix('.php', $include)) require_once $include;
      if (file_exists($probableInitPath)) require $probableInitPath;
    }
    else {
      require RUN_PATH.'/defaultInit.php';
      Lib::enableCache($include);
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
    Lib::enableCache();
    print O::get('CliColors')->getColoredString('Универсальная точка запуска:', 'green')."\n";
    if (!CliAccess::$disableDescription) print O::get('CliColors')->getColoredString('Supported commands:', 'yellow')."\n";
    $methods = array_filter((new ReflectionClass($this))->getMethods(ReflectionMethod::IS_PUBLIC), function (ReflectionMethod $m) {
      return $m->getName() != 'run' and !$m->isStatic();
    });
    foreach ($methods as $method) print $this->runner().' '.$method->getName().(O::get('CliColors')->getColoredString(' -- Clears run envirnment cache', 'cyan'))."\n";
    print $this->runner().' cmd/path/code {ngnEnvLib}'."\n";
    print $this->runner().' site projectName cmd/path/ngn'.(O::get('CliColors')->getColoredString(' -- cmd: "new Class()" / path: NGN_ENV_PATH/path/to/libOrFile / ngn: just type it', 'cyan'))."\n";
    if (!CliAccess::$disableDescription) {
      print O::get('CliColors')->getColoredString('cmd/path variants:', 'yellow')."\n";
      print "* (new Class('a'))->run()\n".
      "* /path/to/file\n".
      "* ci/1 ci\n".
      "* '(new CiClass)->method()' ci\n".
      "* NGN_ENV_PATH/path/to/file\n".
      "* NGN_PATH/path/to/file\n";
    }
  }

  protected function isOptionsArg($arg) {
    return (bool)strstr($arg, '=');
  }

  function cc() {
    foreach (glob(DATA_PATH.'/cache/*---*') as $file) unlink($file);
    print "done.\n";
  }

  protected function isCode($param) {
    return strstr($param, '(');
  }

  protected function parseClass($s) {
    $classRe = '[A-Z][a-zA-Z0-9_]*';
    if (preg_match("/new ($classRe)/", $s, $m)) return $m[1];
    if (preg_match("/($classRe)::/", $s, $m)) return $m[1];
    return false;
  }

  protected function processPath($path) {
    if (method_exists($this, $path)) {
      $this->$path();
      return;
    }
    if ($this->isCode($path)) { // eval
      $code = trim($path);
      if (($class = $this->parseClass($code))) {
        if (!class_exists($class)) throw new Exception('Class "'.$class.'" not exists in code: '.$code);
      }
      if ($code[strlen($code) - 1] != ';') $code = "$code;";
      eval($code);
    }
    else {
      $path = self::replace($path.'.php');
      if (!($_path = realpath($path))) throw new Exception("path '$path' not found");
      include $_path;
    }
    Cli::storeCommand(RUN_PATH.'/logs');
  }

}
