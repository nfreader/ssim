<?php
require_once '../config.php';
$pilot = new pilot();
header('Content-Type: application/json');

if(isset($_GET['data'])) {
  $data = $_GET['data'];
  if($data === 'pilot') {
    echo json_encode($pilot->pilot, JSON_NUMERIC_CHECK); 
  }
}
?>

