<?php

include '../inc/config.php';
$pilot = new pilot();
$spob = new spob($pilot->pilot->spob);
?>


<div class="center wide">

<?php 
$missions = new misn();

$deliveries = $missions->getDeliverableMissions();

if (!$deliveries) {

} else {
  echo "<h1>Deliverable Missions</h1>";
  echo tableHeader(array('Commodity','Destination',
  'Tons','Reward','UID','Deliver'),'misn sort');

  foreach ($deliveries as $deliver) {
    echo "<tr class='$deliver->class'>";
    echo tableCell($deliver->commodity);
    echo tableCell($deliver->destination);
    echo tableCell($deliver->amount);
    echo tableCell($deliver->reward);
    echo tableCell($deliver->uid);
    echo tableCell("<a class='btn local-action'
      action='deliverMission&UID=$deliver->uid' href='mission'>Deliver</a>");
    echo "</tr>";
  }
  echo tableFooter();
}

echo "<h1>Cargo Missions on ".$spob->spob->name."</h1>";
$missions = $missions->getAvailableMissions();
//var_dump($missions);
echo tableHeader(array('Commodity','Destination',
  'Tons','Reward','UID','Accept'),'misn sort');
if (!$missions) {
  echo "<tr><td colspan='6'><div class='pull-center'>";
  echo "&#x0226A; No missions available &#x0226B;</div></td></tr>";
}
foreach ($missions as $mission) {

echo "<tr class='$mission->class'>";
echo tableCell($mission->commodity);
echo tableCell($mission->destination);
echo tableCell($mission->amount);
echo tableCell($mission->reward);
echo tableCell($mission->uid);
echo tableCell("<a class='btn local-action'
  action='acceptMission&UID=$mission->uid' href='mission'>Accept</a>");
echo "</tr>";

}

echo tableFooter();

?>

</div>

<?php include 'rightbar.php'; ?>
<script>
  loadContent('ping', '.footer', '.footerbar');
  $('document').ready(function(){
     $('.sort').tablesorter();
   });
</script>


