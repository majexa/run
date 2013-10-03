<?php

class TestAllErrors extends NgnTestCase {

  function test() {
    $r = (new AllErrors)->get();
    $this->assertFalse(!!$r, implode(', ', Arr::get($r, 'file')));
  }

}