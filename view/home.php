<?php
include '../inc/config.php';

//Active session checks 
$user  = new user();
if(!$user->isLoggedIn()) {
  directLoad('view/login.php');
  die();
}
//Badmin settings
if((isset($_SESSION['sudo_mode'])) && (true === $_SESSION['sudo_mode'])) {
  $game = new game();
  $game->logEvent('SD','Sudo mode disengaged');
  $_SESSION['sudo_mode'] = false;
}?>

<?php

if(isset($_SESSION['pilotuid'])) :
  $pilot = new pilot();
  switch($pilot->status) {
    default:
    case 'B':
      include('freshPilot/freshPilot.php');
    break;
  }

elseif(isset($_GET['activatePilot'])) :
  $pilot = new pilot();
  $pilot = $pilot->activatePilot($_GET['activatePilot']); ?>
  <div class="center wide">
  <h1><?php echo $pilot->name;?> has been activated</h1>
  <h2><a href='home' class='load'>Continue</a></h2>
  </div>
  <?php
else :
  include 'pilotSelect.php';
endif; ?>


<script>
  loadContent('.footerbar','footer');
  $('body').removeClass('admin');
</script>


