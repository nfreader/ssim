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

<div class="fiftyfifty">
<h1>Active Missions</h1>
<?php var_dump($misn->getMissionList()); ?>
</div>

<div class="fiftyfifty">
  <h1>Generate Missions</h1>
  <?php echo $generate; ?>
</div>
