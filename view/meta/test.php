<?php

require_once('../../inc/config.php');

$pilot = new pilot(FALSE);
$ping = $pilot->ping($_SESSION['pilotuid']);
if (!$ping){
  return;
} else {
  var_dump($ping);
}
?>
