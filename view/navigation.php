<?php 

include '../inc/config.php';

$user = new user();
if ($user->isLoggedIn()){
  $pilot = new pilot();
  

   
  if(isset($_GET['action'])) {
    $action = $_GET['action'];
  
    if($action == 'newPilot') {
      returnMsg($pilot->newPilot($_GET['firstname'],$_GET['lastname']));
    }
  
    if($action === 'refuel') {
      returnMsg($pilot->refuel());
    }
  }
    include 'viewHeader.php';
  
    echo "<div class='center'>"; 
  
    include 'navigation.php';
  
    echo "</div>";
  
    if (!$pilot) {
  
      echo "No pilots found!";
      include 'html/newPilot.php';
    } else {
  
    }
} else {
  directLoad('view/loginerror.php');
}
?>

<script>
  loadContent('ping', '.footer', '.footerbar');
</script>
