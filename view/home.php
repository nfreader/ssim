<?php

include '../inc/config.php';
$user  = new user();
// if ($user->status === 0) {
//   echo "<div class='leftbar'></div><div class='center'>";
//   echo "Your account is awaiting activation. Please wait.";
//   echo "</div><div class='rightbar'></div>";
// }
$pilot = new pilot();
$pilotcheck = $pilot->userHasPilot($_SESSION['userid']);
if (!$pilotcheck) {
	echo "No pilots found!";
	include 'html/newPilot.php';
} else {
	($pilot->pilot->status === 'L' ? $spob = new spob($pilot->pilot->spob):'');
	$syst = new syst($pilot->pilot->syst);
  ($pilot->pilot->status === 'S' ? $syst->addNewSyst($pilot->pilot->syst):'');

	if ($user->isLoggedIn()) {

		if (isset($_GET['action'])) {
		  require_once 'action.php';
    }
		
    if ($pilot->pilot->status === 'L') {
      include 'landed.php';
    } elseif ($pilot->pilot->status === 'S') {
      include 'space.php';
    } elseif ($pilot->pilot->status === 'J') {
      include 'bluespace.php';
    }
    
    include 'rightbar.php';
  }
}
?>
<script>
  loadContent('ping', '.footer', '.footerbar');
    $('body').removeClass('admin');
</script>


