<?php

$params = implode(' ', array_slice($_SERVER['argv'], 1));
$dir = __DIR__;
if (isset($_SERVER['argv'][1]) and ($_SERVER['argv'][1] == 'proj' or $_SERVER['argv'][1] == 'plib')) {
  if (empty($_SERVER['argv'][3])) throw new Exception('projectName param #3 not defined');
  if ($_SERVER['argv'][1] == 'proj' and $_SERVER['argv'][2] == 'g') {
    print `pm localServer createTestProject common`;
  }
  $offset = $_SERVER['argv'][1] == 'plib' ? -1 : 0;
  if ($_SERVER['argv'][1] == 'plib') $includes = ' '.$_SERVER['argv'][4 + $offset];
  else $includes = '';
  $cmd = "php $dir/run.php site {$_SERVER['argv'][3 + $offset]} \"new CliTestRunner('$params')\"$includes";
  print `$cmd`;
}
else {
  if (isset($_SERVER['argv'][1]) and $_SERVER['argv'][1] == 'lib') $includes = ' '.$_SERVER['argv'][2];
  else $includes = '';
  print `php $dir/run.php "new CliTestRunner('$params')"$includes`;
}
