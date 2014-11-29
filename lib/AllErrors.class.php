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
    $r = [];
    foreach ($this->getFiles() as $v) {
      foreach (LogReader::_get($v['file']) as $item) {
        $item['name'] = $v['name'];
        $r[] = $item;
        // generators realization
        //yield $item;
        $n++;
        if ($n == $this->limit) break 2;
      }
    }
    return Arr::sortByOrderKey($r, 'time', SORT_DESC);
  }

  function output() {
    throw new Exception('asd');
    foreach ($this->get() as $v) {
      print O::get('CliColors')->getColoredString(str_pad($v['name'], 13), 'green').date('d.m.Y H:i:s', $v['time'])."     ".O::get('CliColors')->getColoredString($v['body'], 'red')."\n";
      if (isset($v['url'])) print O::get('CliColors')->getColoredString('URL: '.$v['url'], 'cyan')."\n";
      print O::get('CliColors')->getColoredString($v['trace'], 'brown')."\n";
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