<?php

class NgnCli extends CliHelpDirectClasses {

  function prefix() {
    return false;
  }

  function getClasses() {
    return [
      [
        'class' => 'DdStructureCli',
        'name' => 'structure'
      ],
      [
        'class' => 'DdFieldCli',
        'name' => 'field'
      ],
      [
        'class' => 'ConfigCli',
        'name' => 'config'
      ],
      [
        'class' => 'PageCli',
        'name' => 'page'
      ],
      [
        'class' => 'PageOpCli',
        'name' => 'pageOp'
      ],
   ];
  }

  protected function _runner() {
    if (isset($this->options['runner'])) return $this->options['runner'];
    return 'ngn';
  }

}