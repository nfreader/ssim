<?php 
include 'adminHeader.php';

//Galaxy editor
$syst = new syst();

if(isset($_GET['action']) && ($_GET['action'] == 'addSyst')) {
    if ($newSyst = $syst->addSyst($_GET['name'],$_GET['coordx'],$_GET['coordy'])) {
      echo "Added system".$_GET['name'];
    } else {
      echo "Unable to add system";
    }
}

$spob = new spob();
$spobs = $spob->getSpobs();

?>

<div class="rightbar">
  <div class="form-group">
    <h2 class='form-title'>Add new System</h2>
    <form class="vertical local-form" action='addSyst' dest='admin/galaxy'>
      <input type='text' name='name' placeholder='System Name' />
      <input type='number' name='coordx' placeholder='X Coordinate' />
      <input type='number' name='coordy' placeholder='Y Coordinate' />
      <button>Add</button>
    </form>
  </div>
</div>

<div class='center'>
<h1>Galaxy Map</h1>
<?php
$systems = $syst->getSyst();
if ($systems == array()) {
  echo "No galaxy found!";
} else {
  echo "<ul class='options'>";
  foreach($systems as $system) {
    echo "<li><a href='admin/system' query='syst=".$system->id."' class='load'>".$system->name;
    echo " (".$system->coord_x.",".$system->coord_y.")";
    echo "</a></li>";
    echo "<ul class='options'>";
    foreach ($spobs as $spob) {
      if ($spob->parent === $system->id) {
        echo "<li><a href='admin/planet' query='spob=".$spob->id."' class='load'>".spobType($spob->type)." ".$spob->name."</a></li>";
      }
    }
    echo "</ul>";
  }
  echo "</ul>";
}

?>
</div>