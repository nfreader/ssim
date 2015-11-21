<?php
require_once('../config.php');
header('Content-Type: application/json');

if (isset($_POST['text'])) {
  $search = $_POST['text'];
} elseif (isset($_GET['spob'])) {
  $search = $_GET['spob'];
} else {
  return;
}

$spob = new spob(NULL);
$spob = $spob->getSpobByName($search);
$commod = new commod();
$commods = $commod->getSpobCommods($spob);
  //Sending a message to a slack channel

  $data = "Commodity stats for *$search* \n";
  $fields = array();
  foreach($commods as $commod) {
    $data.="$commod->name : ".singular($commod->supply,'ton','tons')." at ".singular($commod->price,'credit','credits'." per ton\n");
  }

  $payload = array(
    'text'=>$data,
  );

  $payload = json_encode($payload);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, SLACK_ROOM);
  curl_setopt($ch, CURLOPT_POST, sizeof($payload));
  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
  curl_exec($ch);
  curl_close($ch);
