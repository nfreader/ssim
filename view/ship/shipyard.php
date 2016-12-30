<?php
include '../../inc/config.php';
$pilot = new pilot();
$ships = new ship();
$classList = $ships->getShipClasses();
$ships = $ships->getShipyard(); ?>

<div id="left">
  <ul class="options">
    <li><a class="page" href="home">Back</a></li>
  </ul>
</div>

<div id="center">
  <h1><?php echo $pilot->spobname; ?> Shipyard</h1>
  <ul class="options">
    <?php foreach($ships as $ship) : ?>
      <li>
        <a class="page" href="ship/buyShip" data="ship=<?php echo $ship->id;?>">
          <?php echo $ship->shipwright; ?> <?php echo $ship->name;?><br>
          <small><?php echo $classList[$ship->class]; ?> | <?php echo credits($ship->cost);?></small>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>

<?php require_once('../rightbar.php'); ?>
