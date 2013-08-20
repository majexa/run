<?php

$o = new LibStorage();
$o->addLib('core/init.php');
$dir = dirname(__DIR__).'/standAlone';
$name = $_SERVER['argv'][2];
$o->addFile("$dir/$name.php");
file_put_contents("$dir/result/$name.php", $o->get());
