<?php
header('Content-Type: application/json');
require_once('../../inc/config.php');

$pilot = new pilot(FALSE);
$ping = $pilot->ping($_SESSION['pilotuid']);
if (!$ping){

} else {
  echo json_encode($ping);
}
?>
