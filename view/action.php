<?php
include '../inc/config.php';
$user  = new user();
if ($user->isLoggedIn()) {
  
  $action = $_GET['action'];
    switch ($action) {
    //Pilot actions
    case 'newPilot':
      $pilot = new pilot();
      echo json_encode($$pilot->newPilot($_POST['firstname'], $_POST['lastname']), JSON_NUMERIC_CHECK);
      break;
  
    case 'renameVessel':
      $pilot = new pilot();
      echo json_encode($$pilot->renameVessel($_GET['vesselName']), JSON_NUMERIC_CHECK);
      break;
  
    //end pilot actions
    //Spob actions
    case 'refuel':
      $pilot = new pilot();
      echo json_encode($$pilot->refuel(), JSON_NUMERIC_CHECK);
      break;
    //End spob actions
    
    //Navigation actions
    case 'liftoff':
      $pilot = new pilot();
      echo json_encode($pilot->liftoff(), JSON_NUMERIC_CHECK);
      break;
    
    case 'land':
      $pilot = new pilot();
      echo json_encode($pilot->land($_GET['spob']), JSON_NUMERIC_CHECK);
      break;
    
    case 'jump':
      $pilot = new pilot();
      echo json_encode($pilot->jump($_GET['target']), JSON_NUMERIC_CHECK);
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
      echo json_encode($beacon->newDistressBeacon(),JSON_NUMERIC_CHECK);
      break;
    //End space actions
  
    //Commodity actions
    case 'buyCommod':
      $commod = new commod();
      echo json_encode($commod->buyCommod($_GET['commod'],floor($_POST['amount'])),JSON_NUMERIC_CHECK);
      break;
    case 'sellCommod':
      $commod = new commod();
      echo json_encode($commod->sellCommod($_GET['commod'],floor($_POST['amount'])),JSON_NUMERIC_CHECK);
      break;
    //End commodity actions
  
    //Message actions 
    case 'sendMsg':
      $message = new message();
      echo json_encode($message->newPilotMessage($_GET['to'], $_POST['message']), JSON_NUMERIC_CHECK);
      break;
    case 'deleteMessage':
      $message = new message();
      echo json_encode($message->deleteMessage($_GET['msgid']), JSON_NUMERIC_CHECK);
      break;
    case 'deleteThread':
      $message = new message();
      echo json_encode($message->deleteMessageThread($_GET['from']), JSON_NUMERIC_CHECK);
      break;
    //End message actions
  
    //Begin mission actions
  
    case 'acceptMission':
      $misn = new misn();
      echo json_encode($misn->acceptMission($_GET['UID']), JSON_NUMERIC_CHECK);
      break;
  
    case 'deliverMission':
      $misn = new misn();
      echo json_encode($misn->deliverMission($_GET['UID']), JSON_NUMERIC_CHECK);
      break;
  
    case 'pirateMission':
      $misn = new misn();
      echo json_encode($misn->pirateMission($_GET['UID']));
      break;
  
    //End mission actions
  
    //Begin logout action
    case 'logout':
      echo json_encode($user->logOut(), JSON_NUMERIC_CHECK);
      break;
    //End logout action
  }

} else {
  echo "You must be logged in! This incident has been reported!";
}
