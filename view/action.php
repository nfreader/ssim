<?php
include '../inc/config.php';

$user  = new user();
if ($user->isLoggedIn()) {
  
  $pilot = new pilot();
  $action = $_GET['action'];
  //Pilot actions
  if ($action == 'newPilot') {
    returnMsg($pilot->newPilot($_GET['firstname'], $_GET['lastname']));
  }

  if ($action === 'renameVessel') {
    $msg = $pilot->renameVessel($_GET['vesselName']);
  }

  //end pilot actions
  //Spob actions
  if ($action === 'refuel') {
    $msg = $pilot->refuel();
  }
  //End spob actions
  
  //Navigation actions
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
  //End navigation actions
  
  //Space actions
  if ($action === 'distressBeacon') {
    $beacon = new beacon();
    $msg = $beacon->newDistressBeacon();
  }
  //End space actions
}
echo $msg;
