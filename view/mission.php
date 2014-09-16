<?php

include '../inc/config.php';
$pilot = new pilot();
$spob = new spob($pilot->pilot->spob);
?>


<div class="center wide"><h1>Cargo Missions on
<?php echo $spob->spob->name; ?></h1>

<?php 
$missions = new misn();
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


