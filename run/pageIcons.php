<?php

include_once LIB_PATH.'/more/config_core.php';
$smFolderClosedIcon = STATIC_PATH.'/img/icons/sm-folder-closed.png';
$smFolderOpenedIcon = STATIC_PATH.'/img/icons/sm-folder-opened.png';
$smHomeIcon = STATIC_PATH.'/img/icons/sm-home.png';
//die2(CORE_PAGE_MODULES_PATH.'/*');
foreach (glob(CORE_PAGE_MODULES_PATH.'/*') as $dir) {
  $pageIcon = $dir.'/sm-page.png';
  if (!file_exists($pageIcon)) continue;  
  $folderClosedIcon = $dir.'/sm-folder-closed.png';
  $folderOpenedIcon = $dir.'/sm-folder-opened.png';
  $homeIcon = $dir.'/sm-home.png';
  File::delete($folderClosedIcon);
  File::delete($folderOpenedIcon);
  File::delete($homeIcon);
  output("process $dir");
  sys("convert $pageIcon -page +6+5 $smFolderClosedIcon -page 0x0 -layers merge $folderClosedIcon");
  sys("convert $pageIcon -page +6+5 $smFolderOpenedIcon -page 0x0 -layers merge $folderOpenedIcon");
  sys("convert $pageIcon -page +6+4 $smHomeIcon -page 0x0 -layers merge $homeIcon");
}
