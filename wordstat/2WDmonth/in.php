<?php
   if (!isset($_REQUEST['s'])) die();
   if (isset($_REQUEST['slash'])) $_REQUEST['s'] = str_replace('\\', '', $_REQUEST['s']);
   echo  md5($_REQUEST['s']);
?>
