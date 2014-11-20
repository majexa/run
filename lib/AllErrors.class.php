<?php

class AllErrors extends Errors {

  protected function getFiles() {
    $files[] = [
      'name' => 'run',
      'file' => NGN_ENV_PATH.'/run/logs/r_errors.log'
    ];
    $files[] = [
      'name' => 'pm',
      'file' => NGN_ENV_PATH.'/pm/logs/r_errors.log'
    ];
    $files[] = [
      'name'   => 'agi-bin',
      'file' => '/usr/share/asterisk/agi-bin/logs/r_errors.log'
    ];
    foreach (glob(NGN_ENV_PATH.'/projects/*') as $v) if (is_dir($v)) $files[] = [
      'name' => basename($v),
      'file' => "$v/site/logs/r_errors.log"
    ];
    return array_filter($files, function($v) {
      return file_exists($v['file']);
    });
  }

  public $limit = 100;

  function get() {
    $n = 0;
    foreach ($this->getFiles() as $v) {
      foreach (LogReader::_get($v['file']) as $item) {
        $item['name'] = $v['name'];
        yield $item;
        $n++;
        if ($n == $this->limit) break 2;
      }
    }
  }

  /**
   * Очищает все логи с ошибками на сервере
   * @manual logs
   * @cmd run
   */
  function clear() {
    foreach ($this->getFiles() as $v) file_put_contents($v['file'], '');
  }

}