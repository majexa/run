<?php

class NgnClDaemonQueue extends NgnClDaemon {

  /**
   * @var NgnQueueRunner
   */
  protected $oQR;

  protected function defineOptions() {
    return [
      'iterTime' => 1
    ];
  }

  protected function init() {
    $this->oQR = new NgnQueueRunner();
  }

  protected function iteration() {
    $this->oQR->run();
  }

}