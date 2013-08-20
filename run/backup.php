<?php

Misc::checkEmpty($_SERVER['argv'][2]);
sys("rsync -avz --delete /home/user/ngn-env/projects/{$_SERVER['argv'][2]} user@62.76.47.176:/home/user/backup");