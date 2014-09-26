<?php
include '../inc/config.php';
$user  = new user();
if ($user->isLoggedIn()) {
  
  $action = $_GET['action'];
    switch ($action) {
    //Pilot actions
    case 'newPilot':
      $pilot = new pilot();
      echo $pilot->newPilot($_POST['firstname'], $_POST['lastname']);
      break;
  
    case 'renameVessel':
      $pilot = new pilot();
      echo $pilot->renameVessel($_GET['vesselName']);
      break;
  
    //end pilot actions
    //Spob actions
    case 'refuel':
      $pilot = new pilot();
      echo $pilot->refuel();
      break;
    //End spob actions
    
    //Navigation actions
    case 'liftoff':
      $pilot = new pilot();
      echo $pilot->liftoff();
      break;
    
    case 'land':
      $pilot = new pilot();
      echo $pilot->land($_GET['spob']);
      break;
    
    case 'jump':
      $pilot = new pilot();
      echo $pilot->jump($_GET['target']);
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
      echo $beacon->newDistressBeacon();
      break;
    //End space actions
  
    //Commodity actions
    case 'buyCommod':
      $commod = new commod();
      echo $commod->buyCommod($_GET['commod'],floor($_POST['amount']));
      break;
    case 'sellCommod':
      $commod = new commod();
      echo $commod->sellCommod($_GET['commod'],floor($_POST['amount']));
      break;
    //End commodity actions
  
    //Message actions 
    case 'sendMsg':
      $message = new message();
      echo $message->newPilotMessage($_GET['to'], $_POST['message']);
      break;
    case 'deleteMessage':
      $message = new message();
      echo $message->deleteMessage($_GET['msgid']);
      break;
    case 'deleteThread':
      $message = new message();
      echo $message->deleteMessageThread($_GET['from']);
      break;
    //End message actions
  
    //Begin mission actions
  
    case 'acceptMission':
      $misn = new misn();
      echo $misn->acceptMission($_GET['UID']);
      break;
  
    case 'deliverMission':
      $misn = new misn();
      echo $misn->deliverMission($_GET['UID']);
      break;
  
    case 'pirateMission':
      $misn = new misn();
      echo $misn->pirateMission($_GET['UID']);
      break;
  
    //End mission actions
  
    //Begin logout action
    case 'logout':
      echo $user->logOut();
      break;
    //End logout action
  }

} else {
  echo "You must be logged in! This incident has been reported!";
}
