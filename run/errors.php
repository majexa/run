<?php

while (1) {
  if (($r = (new AllErrors)->get())) {
    print_r($r);
    die();
  }
  sleep(10);
}
