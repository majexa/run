<?php

class TestAllErrors extends NgnTestCase {

  function test() {
    $caption = '';
    foreach ((new AllErrors)->get() as $v) {
      $caption .= $v['file']."\n".$v['body']."\n".$v['trace']."\n=======\n";
    }
    $this->assertTrue($caption === '', $caption);
  }

}