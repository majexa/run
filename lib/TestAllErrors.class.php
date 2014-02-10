<?php

class TestAllErrors extends NgnTestCase {

  function test() {
    $r = (new AllErrors)->get();
    $this->assertFalse(!!$r, implode(', ', array_unique(Arr::get($r, 'file'))));
  }

}