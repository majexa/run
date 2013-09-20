<?php

class Errors {

  function get() {
    return Arr::append(LogReader::get('warnings'), LogReader::get('errors'));
  }

  protected function _get() {

  }

}