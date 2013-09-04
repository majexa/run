<?php

// direct call
$_SERVER['argv'][2] = ucfirst($_SERVER['argv'][2]);
(new $_SERVER['argv'][2])->{$_SERVER['argv'][3]}();