<?php
include '../../inc/config.php';
$pilot = new pilot();
$ship = new ship($_GET['ship']); ?>

<?php require_once('../rightbar.php'); ?>

<div id="center">

  <h1><?php echo $ship->name;?>
    <small class="right">by <?php echo $ship->shipwright; ?></small>
  </h1>
  <div class="ship-img">
    <?php echo $pilot->govt->shipcss; ?>
    <?php echo file_get_contents("../../assets/img/ships/".$ship->image.".svg"); ?>
  </div>

  <p><?php echo $ship->description; ?></p>

  <ul class="dot-leader">
    <li>
      <span>Class</span>
      <span><?php echo $ship->classname; ?></span>
    </li>
    <li>
      <span>Cost</span>
      <span><?php echo credits($ship->cost); ?></span>
    </li>
    <li>
      <span>Fueltank</span>
      <span><?php echo singular($ship->fueltank,'jump','jumps');?></span>
    </li>
    <hr>
    <li>
      <span>Shields</span>
      <span><?php echo icon('magnet');?> <?php echo $ship->shields; ?></span>
    </li>
    <li>
      <span>Armor</span>
      <span><?php echo icon('th');?> <?php echo $ship->armor; ?></span>
    </li>
    <hr>
    <li>
      <span>Mass</span>
      <span><?php echo singular($ship->mass,'ton','tons');?></span>
    </li>
    <li>
      <span>Acceleration</span>
      <span><?php echo $ship->accel;?> m/s</span>
    </li>
    <li>
      <span>Turn Speed</span>
      <span><?php echo $ship->turn;?> m/s</span>
    </li>
    <li>
      <span>Acceleration</span>
      <span><?php echo $ship->accel;?> m/s</span>
    </li>
    <hr>
    <li>
      <span>Cargo Bay</span>
      <span><?php echo singular($ship->cargobay,'ton','tons');?></span>
    </li>
    <li>
      <span>Expansion Space</span>
      <span><?php echo singular($ship->expansion,'ton','tons');?></span>
    </li>
  </ul>
</div>

<div id="left">

<h1>Purchase order</h1>

<p>Before your purchase can be completed, we need to finalize some details.</p>

<form class="async" action="purchaseShip&ship=<?php echo $ship->id;?>" data-dest="home">
<input type="text" name="vesselName" placeholder="Vessel Name" />

<p>If you would prefer to register your vessel with a custom registration number, please enter it below. Custom registration numbers cost an additional <?php echo credits(50); ?>. If this field is left blank, or if the data is in an incorrect format, you will be assigned a random registration number. Registration numbers can only consist of letters A-Z and digitis 0-9</p>

<input type="text" name="regNumber" placeholder="XXXXXXXXX" />

<button class="btn-block">Complete purchase</button>

</form>
</div>
