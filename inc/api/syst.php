<?php
require_once '../config.php';
$syst = new syst();
header('Content-Type: application/json');

if(isset($_GET['data'])) {
  $data = $_GET['data'];
  if($data === 'systjson') {
    echo $syst->getSyst(null, true); 
  }
}
?>

