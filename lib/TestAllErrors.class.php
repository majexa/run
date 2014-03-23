<?php

class TestAllErrors extends NgnTestCase {

  function test() {
    $r = (new AllErrors)->get();
    $caption = '';
    if ($r) {
      foreach ($r as $v) {
        $caption .= $v['file']."\n".$v['body']."\n".$v['trace']."\n=======\n";
      }
    }
    $this->assertFalse(!!$r, $caption);
  }

}