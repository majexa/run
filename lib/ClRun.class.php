<?php

class ClRun {

  protected $site;

  function __construct($site = false) {
    $this->site = $site;
  }

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
    $_SERVER['argv'] = array_merge([''], $args);
    Arr::checkEmpty($args, 0);
    Err::setEntryCmd('run '.($this->site ? 'site '.$this->site.' ' : '').implode(' ',$args));
    $includes = false;
    $directFile = null;
    $quietly = false;
    if (!$this->isCode($args[0]) and file_exists($args[0])) {
      // Если первый параметр - путь, смотрим вероятные файлы/папки для инициализации относительно него
      $directFile = $args[0];
      $probableInitPath = dirname($args[0]).'/init.php';
      $probableLibFolder = dirname($args[0]);
      if (file_exists(self::replace($probableInitPath))) {
        output("Probable init path exists: $probableInitPath");
        $includes = $probableInitPath;
      }
      elseif (file_exists(self::replace($probableLibFolder))) {
        output("Probable lib folder exists: $probableLibFolder");
        $includes = $probableLibFolder;
      }
    }
    if (!empty($args[1])) {
      if ($args[1] == 'quietly') {
        $quietly = true;
      } else {
        if ($this->isOptionsArg($args[1])) {
          require_once CORE_PATH.'/lib/cli/Cli.class.php';
          R::set('options', Cli::strParamsToArray($args[1]));
        }
        else {
          $includes = $args[1];
        }
      }
    }
    if (!empty($args[2]) and $args[2] == 'quietly') $quietly = true;
    if ($includes) {
      foreach (explode(',', $includes) as $include) {
        $include = self::replace($include);
        $probableInitPath = $include.'/init.php';
        Err::$errorExtra['argv'] = getPrr($_SERVER['argv']);
        if (!Misc::hasSuffix('.php', $include)) Lib::addFolder($include);
        if (Misc::hasSuffix('.php', $include)) require_once $include;
        if (file_exists($probableInitPath)) require $probableInitPath;
      }
      Lib::enableCache($includes);
    }
    else {
      require RUN_PATH.'/defaultInit.php';
      Lib::enableCache($includes);
    }
    if (file_exists(__DIR__.'/runConfig.php')) require_once __DIR__.'/runConfig.php';
    if ($directFile) {
      include $directFile;
    } else {
      $this->processFirstArg($args[0], $quietly);
    }
    Cli::storeCommand(RUN_PATH.'/logs');
  }

  protected function runner() {
    return CliColors::colored('run', 'brown');
  }

  protected function caption($text) {
    return CliColors::colored('-- '.$text, 'cyan');
  }

  protected function help() {
    Lib::enableCache();
    print CliColors::colored('Универсальная точка запуска:', 'green')."\n";
    if (!CliAccess::$disableDescription) print CliColors::colored('Supported commands:', 'yellow')."\n";
    $methods = array_filter((new ReflectionClass($this))->getMethods(ReflectionMethod::IS_PUBLIC), function (ReflectionMethod $m) {
      if ($m->isConstructor()) return false;
      return $m->getName() != 'run' and !$m->isStatic();
    });
    foreach ($methods as $method) print $this->runner().' '.$method->getName().(CliColors::colored(' -- Clears run envirnment cache', 'cyan'))."\n";
    print $this->runner().' path/cmd/code {options/lib}'."\n";
    print $this->runner().' site projectName path/cmd/code'."\n";
    if (!CliAccess::$disableDescription) {
      print CliColors::colored('path/cmd/code variants:', 'yellow')."\n";
      print //
        "  /path/to/file\n". //
        "  ci/1 ci\n". //
        "  NGN_ENV_PATH/path/to/file\n". //
        "  NGN_PATH/path/to/file\n". //
        "  someFileInCmdFolder\n". //
        "  \"(new Class('a'))->run()\"\n". //
        "  \"(new CiClass)->method()\" ci\n"; //
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

  protected function processFirstArg($firstArg, $quietly) {
    if (method_exists($this, $firstArg)) {
      $this->$firstArg();
    }
    else {
      if ($this->isCode($firstArg)) {
        // code eval
        $code = trim($firstArg);
        if (($class = $this->parseClass($code))) {
          if (!class_exists($class)) throw new Exception('Class "'.$class.'" not exists in code: '.$code);
        }
        if ($code[strlen($code) - 1] != ';') $code = "$code;";
        eval($code);
      }
      else {
        $path = $firstArg.'.php';
        $found = false;
        if ($path[0] == '/') {
          include $path;
          $found = true;
        } elseif (file_exists(NGN_ENV_PATH.'/'.$path)) {
          include NGN_ENV_PATH.'/'.$path;
          $found = true;
        } else {
          foreach (Ngn::$basePaths as $basePath) {
            if (file_exists("$basePath/cmd/$path")) {
              include "$basePath/cmd/$path";
              $found = true;
              break;
            }
          }
        }
        if (!$found and !$quietly) throw new Exception("path '$firstArg' not found");
      }
    }
  }

}
