<?php
include '../inc/config.php';
$user  = new user();
if ($user->isLoggedIn()) {
  
  $action = $_GET['action'];
  //Pilot actions
  $pilot = new pilot();
  if ($action == 'newPilot') {
   echo $pilot->newPilot($_POST['firstname'], $_POST['lastname']);
  }

  if ($action === 'renameVessel') {
    echo $pilot->renameVessel($_GET['vesselName']);
  }

  //end pilot actions
  //Spob actions
  if ($action === 'refuel') {
    echo $pilot->refuel();
  }
  //End spob actions
  
  //Navigation actions
  if ($action === 'liftoff') {
    echo $pilot->liftoff();
  }
  
  if ($action === 'land') {
    echo $pilot->land($_GET['spob']);
  }
  
  if ($action === 'jump'){
    echo $pilot->jump($_GET['target']);
  }
  
  if ($action === 'jumpcomplete'){
    echo $pilot->jumpComplete();
    //Hack because we're not clicking a button here...
    echo "<script>jumpComplete('".$msg."');</script>";
  }
  //End navigation actions
  
  //Beacon space actions
  if ($action === 'distressBeacon') {
    $beacon = new beacon();
    echo $beacon->newDistressBeacon();
  }
  //End space actions

  //Commodity actions
  if ($action === 'buyCommod') {
    $commod = new commod();
    echo $commod->buyCommod($_GET['commod'],floor($_POST['amount']));
  }
  if ($action === 'sellCommod') {
    $commod = new commod();
    echo $commod->sellCommod($_GET['commod'],floor($_POST['amount']));
  }
  //End commodity actions

  //Message actions 
  if ($action === 'sendMsg') {
    $message = new message();
    echo $message->newPilotMessage($_GET['to'], $_POST['message']);
  }
  //End message actions

  //Begin logout action
  if ($action === 'logout') {
    $_SESSION = '';
    session_destroy();
    echo "session.terminate()";
  }
  //End logout action

} else {
  echo "You must be logged in! This incident has been reported!";
}
