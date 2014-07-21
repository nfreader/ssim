<?php 

include '../inc/config.php'; 
$user = new user();
if ($user->isLoggedIn()){
  $pilot = new pilot();
  $pilots = $pilot->getUserPilots();
  
  if (!$pilots) {
    echo "No pilots found!";
    include 'html/newPilot.php';
  } else {
    include 'viewHeader.php';
  }
} else {
  directLoad('view/loginerror.php');
}
?>

<script>
  loadContent('ping', '.footer', '.footerbar');
</script>
