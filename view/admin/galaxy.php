<?php 
include 'adminHeader.php';

//Galaxy editor
$syst = new syst();

if(isset($_GET['action']) && ($_GET['action'] == 'addSyst')) {
  if(
      (!empty($_GET['name']))
      &&
      (!empty($_GET['coordx']))
      &&
      (!empty($_GET['coordy']))
    ) {
    $newSyst = $syst->addSyst($_GET['name'],$_GET['coordx'],$_GET['coordy']);
  }
} ?>

<div class="leftbar">
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


<?php
echo "<div class='center'>";
echo "<h1>Systems</h1>";
$systems = $syst->getSyst();
if ($systems == array()) {
  echo "No galaxy found!";
} else {
  echo "<ul class='options'>";
  foreach($systems as $system) {
    echo "<li><a href='admin/system' query='syst=".$system->id."' class='load'>".$system->name;
    echo " (".$system->coord_x.",".$system->coord_y.")";
    echo "</a></li>";
  }
  echo "</ul>";
}
echo "</div>";

?>
