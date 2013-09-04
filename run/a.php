<?php

$r = [];
(new MailMimeParser)->Decode(['File' => glob('/home/bot/Maildir/new/*')[0]], $r);
die2($r);