<?php 
include 'adminHeader.php';
$misn = new misn();
if(isset($_GET['action']) && ($_GET['action'] == 'generateMisn')) {
  $misn->generateMisn(100);
  $generate = 'Generated 100 missions. Generate more?<br>';
  $generate.= '<a href="admin/mission" query="action=generateMisn"';
  $generate.= 'class="load">Generate Missions</a>';
} else {
  $generate = '<a href="admin/mission" query="action=generateMisn"';
  $generate.= 'class="load">Generate Missions</a>';
}

?>

<div class="center wide">
<h1>Mission Statistics</h1>
<?php $stats = $misn->getMissionStats();
echo tableHeader(array('Commodity','Total Value', 'Total Tons', 'Real Value'),
  'misn-stats');
foreach ($stats as $stat) {
    echo "<tr class='".$stat->class."'>";
    echo tableCell($stat->commodity);
    echo tableCell($stat->totalvalue." ".icon('certificate','credits'));
    echo tableCell($stat->totaltons." tons");
    echo tableCell($stat->realvalue." ".icon('certificate','credits'));
    echo "</tr>";
  }
  echo tableFooter(); ?>
</div>

<div class="center wide">
<h1>Active Missions <?php echo $generate; ?></h1>
<?php

$missions = $misn->getMissionList(); 
//var_dump($missions);
echo tableHeader(array('Commodity','Pickup',
  'Deliver','Tons','Reward','Value','Ratio'));
$i = 0;
$c = 0;
foreach ($missions as $mission) {
  if($mission->ratio <= 100) {
    echo "<tr class='zebra'>";
    $i++;
  } else {
    echo "<tr>";
  }
  echo tableCell($mission->commodity);
  echo tableCell($mission->pickup);
  echo tableCell($mission->delivery);
  echo tableCell($mission->tons." tons");
  echo tableCell($mission->reward." ".icon('certificate','credits'));
  echo tableCell($mission->value." ".icon('certificate','credits'));
  echo tableCell($mission->ratio);
  echo "</tr>";
  $c++;
}
echo tableFooter();
echo "<hr>";
echo "Only $i missions are more valuable if completed successfully.<br>";
echo "That's only " . $i/$c * 100 . "% of all missions";
?>
</div>

