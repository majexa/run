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

  function get() {
    $r = [];
    foreach ($this->getFiles() as $v) {
      if (!($items = LogReader::_get($v['file']))) continue;
      foreach ($items as &$item) $item['name'] = $v['name'];
      $r = Arr::append($r, $items);
    }
    return $r;
  }

  function clear() {
    foreach ($this->getFiles() as $v) file_put_contents($v['file'], '');
  }

}