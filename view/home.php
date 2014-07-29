<?php

include '../inc/config.php';
$user  = new user();
$pilot = new pilot();
if (!$pilot) {
	echo "No pilots found!";
	include 'html/newPilot.php';
} else {
	$spob = new spob($pilot->pilot->spob);
	$syst = new syst($pilot->pilot->syst);

	if ($user->isLoggedIn()) {

		if (isset($_GET['action'])) {
		  require_once 'action.php';
    }
		?>

		<?php if ($pilot->pilot->status === 'L') {

//LANDED sidebar
			?>
<div class="leftbar">
  <div class="location-box">
    <h1><?php echo spobType($spob->spob->type)." ".$spob->spob->name;?></h1>
    <img src="assets/img/planets/earth.png"
    alt="Earth" height="128" width="128" class="planet" />
    <p><?php echo $spob->spob->description;?></p>
    <small>Bluespace node: <?php echo $spob->nodeid;?></small>
    <ul class="options">
			<?php
			echo ($pilot->pilot->fuelmeter > 1?"<li><a disabled='true'>Refuel</a></li>":"<li><a class='local-action' action='refuel' href='home'>Refuel</a></li>");
			?>
			<li><a>Missions</a></li>
      <li><a>Commodity Center</a></li>
      <li><a>Spaceport Bar</a></li>
      <li><a>Shipyard</a></li>
      <li><a>Outfitter</a></li>
    </ul>
  </div>
</div>

			<?php }?>
		<div class="center">
		<?php
		if ($pilot->pilot->status == 'L') {
			echo '<h1>Departure Clearance';
			echo '<span class="green pull-right">GRANTED</span>';
			echo '</h1>';
			echo "<a class='btn btn-block'>".landVerb($spob->spob->type, 'then')." ".$spob->spob->name."</a>";
			//include 'galaxyMap.php';
		}
		?>
		</div>

		<?php include 'rightbar.php';

	} else {
    $msg = urlencode('Incorrect identification details.');
    directLoad('view/login.php?msg='.$msg);
	}
}
?>
<script>
  loadContent('ping', '.footer', '.footerbar');
</script>


