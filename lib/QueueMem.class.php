<?php

class QueueMem extends Mem {
  
  static $keyPrefix = '';
  static protected $projectKey;
  
  const MEM_KEY = 'queue';
  
  static protected function init() {
    if (defined(static::$projectKey))
      throw new NgnException(__CLASS__.'::$projectKey not defined. Use '.__CLASS__.'::setProjectKey()');
  }
  
  static function setProjectKey($key) {
    self::$projectKey = $key;
    static::$keyPrefix = self::MEM_KEY.$key;
  }
  
}