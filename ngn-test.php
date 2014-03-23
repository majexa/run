<?php

$params = implode(' ', array_slice($_SERVER['argv'], 1));
$dir = __DIR__;
if (isset($_SERVER['argv'][1]) and $_SERVER['argv'][1] == 'proj') {
  if (empty($_SERVER['argv'][3])) throw new Exception('projectName param #3 not defined');
  print `php $dir/run.php site {$_SERVER['argv'][3]} "new CliTestRunner('$params')"`;
}
else {
  if (isset($_SERVER['argv'][1]) and $_SERVER['argv'][1] == 'lib') $suffix = ' '.$_SERVER['argv'][3];
  else $suffix = '';
  print `php $dir/run.php "new CliTestRunner('$params')"$suffix`;
}
