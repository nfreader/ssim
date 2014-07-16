<?php 
include 'adminHeader.php';

$syst = new syst();
$syst = $syst->getSyst($_GET['syst']);

if(isset($_GET['action']) && ($_GET['action'] == 'addSpob')) {
  $spob->addSpob(
    $syst->id,
    $_GET['name'],
    $_GET['type'],
    $_GET['techlevel'],
    $_GET['description']
  );
}

$spob = new spob();
$spobs = $spob->getSpobs($syst->id);
$beacon = new beacon();
$beacons = $beacon->getBeacons($syst->id);

?>

<div class='leftbar'>
<h1>Details</h1>
<ul class="dot-leader">
  <li>
    <span class='left'>ID</span>
    <span class='right'><?php echo $syst->id; ?></span>
  </li>
  <li>
    <span class='left'>Coordinate X</span>
    <span class='right'><?php echo $syst->coord_x; ?></span>
  </li>
  <li>
    <span class='left'>Coordinate Y</span>
    <span class='right'><?php echo $syst->coord_y; ?></span>
  </li>
    <li>
    <span>Government</span>
    <span><?php echo $syst->govt; ?></span>
  </li>
</ul>

</div>

<div class="center">
  <h1>System <?php echo $syst->name; ?></h1>


  <?php
    if ($spobs == array()) {
      echo "Uninhabited system";
    } 
    echo "<br>";
    if ($beacons == array()) {
      echo "No beacons";
    } 
  ?>

    <div class="form-group">
      <h2 class='form-title'>Add new destination</h2>
      <!--
        // HAAAACK OMG A TERRIBLE HACK THAT WE SHOULD NOT BE DOING WHY GOD
        // TODO: Remove &syst parameter requirement. That's super dumb.
      --> 
      <form class="vertical local-form" action='addSpob&syst=<?php echo $_GET['syst']; ?>' dest='admin/system'>
        <input type='text' name='name' placeholder='Name' />
        <h3>Type</h3>
        <label class="radio"><input type='radio' name='type' value='S' />
          Station
        </label>
        <label class="radio"><input type='radio' name='type' value='M' />
          Moon
        </label>
        <label class="radio"><input type='radio' name='type' value='P' />
          Planet
        </label>
        <label class="radio"><input type='radio' name='type' value='N' checked="checked" />
          None
        </label>
        <input type='number' name='techlevel' min='0' max='10' placeholder='Techlevel' />
        <textarea name="description" placeholder="Description" rows="5"></textarea>
        <button>Add</button>
      </form>
    </div>  
</div>
