<?php

$params = implode(' ', array_slice($_SERVER['argv'], 1));
$dir = __DIR__;
if (isset($_SERVER['argv'][1]) and ($_SERVER['argv'][1] == 'proj' or $_SERVER['argv'][1] == 'plib')) {
  if (empty($_SERVER['argv'][3])) throw new Exception('projectName param #3 not defined');
  $offset = $_SERVER['argv'][1] == 'plib' ? -1 : 0;
  if ($_SERVER['argv'][1] == 'plib') $includes = ' '.$_SERVER['argv'][4 + $offset];
  else $includes = '';
  $projectName = $_SERVER['argv'][2];
  //if (isset($_SERVER['argv'][3])) {
    // creating project
    //$projectType = $_SERVER['argv'][3];
    //$r = `pm localServer replaceProjectOnDiff $projectName default $projectType`;
    //if (strstr($r, 'created')) print `pm localProject replaceConstant $projectName core IS_DEBUG true`;
    //print `pm localProject cc $projectName`;
  //}
  $cmd = "php $dir/run.php site {$_SERVER['argv'][3 + $offset]} \"new CliTestRunner('$params')\"$includes";
  print `$cmd`;
}
else {
  if (isset($_SERVER['argv'][1]) and $_SERVER['argv'][1] == 'lib') $includes = ' '.$_SERVER['argv'][2];
  else $includes = '';
  print `php $dir/run.php "new CliTestRunner('$params')"$includes`;
}
