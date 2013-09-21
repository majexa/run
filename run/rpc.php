<?php

print json_encode(eval('return '.$_SERVER['argv'][2].';'));

