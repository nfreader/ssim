<?php
include '../inc/config.php';

$user  = new user();
$pilot = new pilot();

$spob = new spob($pilot->pilot->spob);
$syst = new syst($pilot->pilot->syst);

$action = $_GET['action'];

if ($action == 'newPilot') {
  returnMsg($pilot->newPilot($_GET['firstname'], $_GET['lastname']));
}

if ($action === 'refuel') {
  $msg = $pilot->refuel();
}

if ($action === 'liftoff') {
  $msg = $pilot->liftoff();
}

if ($action === 'land') {
  $msg = $pilot->land($_GET['spob']);
}

if ($action === 'jump'){
  $msg = $pilot->jump($_GET['target']);
}

if ($action === 'jumpcomplete'){
  $msg = $pilot->jumpComplete();
  //Hack because we're not clicking a button here...
  echo "<script>jumpComplete('".$msg."');</script>";
}

if ($action === 'distressBeacon') {
  $beacon = new beacon();
  $msg = $beacon->newDistressBeacon();
}

echo $msg;
