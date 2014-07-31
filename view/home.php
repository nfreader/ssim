<?php

include '../inc/config.php';
$user  = new user();
$pilot = new pilot();
if (!$pilot) {
	echo "No pilots found!";
	include 'html/newPilot.php';
} else {
	($pilot->pilot->status === 'L' ? $spob = new spob($pilot->pilot->spob):'');
	$syst = new syst($pilot->pilot->syst);

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
</script>


