<?php 
include 'adminHeader.php';
$misn = new misn();
if(isset($_GET['action']) && ($_GET['action'] == 'generateMisn')) {
  $misn->generateMisn(100);
  $generate = 'Generated 100 missions. Generate more?<br>';
  $generate.= '<a href="admin/mission" data="action=generateMisn"';
  $generate.= 'class="page">Generate Missions</a>';
} else {
  $generate = '<a href="admin/mission" data="action=generateMisn"';
  $generate.= 'class="page">Generate Missions</a>';
}

?>

<div id="center" class="wide">
<h1>Mission Statistics</h1>
<?php $stats = $misn->getMissionStats();
echo tableHeader(array('Commodity','Total Value', 'Total Tons', 'Real Value'),
  'misn sort');
foreach ($stats as $stat) {
    echo "<tr class='commod-$stat->class'>";
    echo tableCell($stat->commodity);
    echo tableCell($stat->totalvalue." ".icon('certificate','credits'));
    echo tableCell($stat->totaltons." tons");
    echo tableCell($stat->realvalue." ".icon('certificate','credits'));
    echo "</tr>";
  }
  echo tableFooter(); ?>

<h1>Active Missions <?php echo $generate; ?></h1>
<?php

$missions = $misn->getMissionList(); 
//var_dump($missions);
echo tableHeader(array('Commodity','Pickup',
  'Deliver','Tons','Reward','Value','Ratio'),'misn sort');
foreach ($missions as $mission) {
  echo "<tr class='commod-$mission->class'>";
  echo tableCell($mission->commodity);
  echo tableCell($mission->pickup);
  echo tableCell($mission->delivery);
  echo tableCell($mission->tons." tons");
  echo tableCell($mission->reward." ".icon('certificate','credits'));
  echo tableCell($mission->value." ".icon('certificate','credits'));
  echo tableCell($mission->ratio);
  echo "</tr>";
}
echo tableFooter();
?>
</div>

<script>
  $('document').ready(function(){
     $('.sort').tablesorter();
   });
</script>

