<?php

$params = implode(' ', array_slice($_SERVER['argv'], 1));
$dir = __DIR__;
if (isset($_SERVER['argv'][1]) and ($_SERVER['argv'][1] == 'proj' or $_SERVER['argv'][1] == 'plib')) {
  if (empty($_SERVER['argv'][3])) throw new Exception('projectName param #3 not defined');
  if ($_SERVER['argv'][1] == 'plib') $includes = ' '.$_SERVER['argv'][4];
  else $includes = '';
  print `php $dir/run.php site {$_SERVER['argv'][3]} "new CliTestRunner('$params')"$includes`;
}
else {
  if (isset($_SERVER['argv'][1]) and $_SERVER['argv'][1] == 'lib') $includes = ' '.$_SERVER['argv'][3];
  else $includes = '';
  print `php $dir/run.php "new CliTestRunner('$params')"$includes`;
}
