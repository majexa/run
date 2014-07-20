<?php

class TestCliCommon {

  /**
   * Удаляет ошибки
   */
  function clear() {
    chdir(NGN_ENV_PATH.'/run');
    Cli::shell('php run.php "(new AllErrors)->clear()"');
  }

  /**
   * Отображает все, существующие в среде тесты
   */
  function lst() {
    print O::get('CliColors')->getColoredString('tst c proj:', 'yellow')."\n";
    foreach ((new TestRunnerProject('dummy'))->_g() as $class) print ClassCore::classToName('Test', $class)."\n";
    print O::get('CliColors')->getColoredString('tst ngn run:', 'yellow')."\n";
    foreach ((new TestRunnerNgn)->_getClasses() as $class) print ClassCore::classToName('Test', $class)."\n";
  }

  /**
   * Запускает глобальные проектные тесты для проекта "test"
   */
  function proj($filterNames = null) {
    $filterNames = $filterNames ? ' '.$filterNames : '';
    $server = require NGN_ENV_PATH.'/config/server.php';
    $domain = 'test.'.$server['baseDomain'];
    print `pm localServer createProject test $domain common`;
    print `tst proj g test$filterNames`;
    print `pm localProject delete test`;
  }

}