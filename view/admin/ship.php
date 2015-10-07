<?php require_once('adminHeader.php');
$ship = new ship($_GET['ship']); ?>

<div class="center">

  <h1><?php echo $ship->name;?>
    <small class="pull-right">by <?php echo $ship->shipwright; ?></small>
  </h1>

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