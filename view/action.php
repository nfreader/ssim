<?php
include '../inc/config.php';
$user  = new user();
if ($user->isLoggedIn()) {
  
  $action = $_GET['action'];
    switch ($action) {
    //Pilot actions
    case 'newPilot':
      $pilot = new pilot();
      $msg = $pilot->newPilot($_POST['firstname'], $_POST['lastname']);
      break;
  
    case 'renameVessel':
      $pilot = new pilot();
      $msg = $pilot->renameVessel($_GET['vesselName']);
      break;
  
    //end pilot actions
    //Spob actions
    case 'refuel':
      $pilot = new pilot();
      $msg = $pilot->refuel();
      break;
    //End spob actions
    
    //Navigation actions
    case 'liftoff':
      $pilot = new pilot();
      $msg = $pilot->liftoff();
      break;
    
    case 'land':
      $pilot = new pilot();
      $msg = $pilot->land($_GET['spob']);
      break;
    
    case 'jump':
      $pilot = new pilot();
      $msg = $pilot->jump($_GET['target']);
      break;
    
    case 'jumpcomplete':
      //Hack because we're not clicking a button here...
      $pilot = new pilot();
      echo "<script>jumpComplete('".$pilot->jumpComplete()."');</script>";
      break;
    //End navigation actions
    
    //Beacon space actions
    case 'distressBeacon':
      $beacon = new beacon();
      $msg = $beacon->newDistressBeacon();
      break;
    //End space actions
  
    //Commodity actions
    case 'buyCommod':
      $commod = new commod();
      $msg = $commod->buyCommod($_GET['commod'],floor($_POST['amount']),JSON_NUMERIC_CHECK);
      break;
    case 'sellCommod':
      $commod = new commod();
      $msg = $commod->sellCommod($_GET['commod'],floor($_POST['amount']),JSON_NUMERIC_CHECK);
      break;
    //End commodity actions
  
    //Message actions 
    case 'sendMsg':
      $message = new message();
      $msg = $message->newPilotMessage($_GET['to'], $_POST['message']);
      break;
    case 'deleteMessage':
      $message = new message();
      $msg = $message->deleteMessage($_GET['msgid']);
      break;
    case 'deleteThread':
      $message = new message();
      $msg = $message->deleteMessageThread($_GET['from']);
      break;
    //End message actions
  
    //Begin mission actions
  
    case 'acceptMission':
      $misn = new misn();
      $msg = $misn->acceptMission($_GET['UID']);
      break;
  
    case 'deliverMission':
      $misn = new misn();
      $msg = $misn->deliverMission($_GET['UID']);
      break;
  
    case 'pirateMission':
      $misn = new misn();
      $msg = $misn->pirateMission($_GET['UID']);
      break;
  
    //End mission actions
  
    //Begin logout action
    case 'logout':
      $msg = $user->logOut();
      break;
    //End logout action
  }

} else {
 $msg = array(
  "message"=>"You must be logged in! This incident has been reported!",
  "level"=>"emergency"
  );
}

if(is_array($msg)) {
  echo json_encode($msg);
} else {
  // $msg = array(
  //   "message"=>$msg,
  //   "level"=>"normal"
  // );
  $message['message'] = $msg ." (this needs to be an array! Update please!)";
  $message['level'] = 'normal';
  echo "[".json_encode($message, JSON_NUMERIC_CHECK)."]";
}
