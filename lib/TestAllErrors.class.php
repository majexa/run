<?php

class TestAllErrors extends NgnTestCase {

  function test() {
    $this->assertFalse(!!(new AllErrors)->get());
  }

}