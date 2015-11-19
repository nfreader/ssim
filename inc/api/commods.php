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
var_dump($spob);
$commods = new commod();
echo json_encode($commods->getSpobCommods($spob));
