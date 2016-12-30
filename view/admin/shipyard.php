<?php require_once('adminHeader.php'); ?>
<?php $ship = new ship();
$classes = $ship->getShipClasses();
$options = "<select name='class'>";
$options.= "<option></option>";
foreach ($classes as $short => $name)  {
  $options.="<option value='$short'>$name</option>";
}
$options.="</select>";

?>

<script src="assets/js/lib/chart.js"></script>


<div id="center" class="wide">

  <h1>Shipyard
    <div class="right">
      <a class="page" href="admin/shipyard" data="addShip">Add new ship</a>
    </div>
  </h1>
  
<?php 
if (isset($_GET['addShip'])) :
  require_once('html/newship.php');
else :
  $ships = $ship->getShipyard(); ?>
  <ul class="options">
    <?php foreach($ships as $ship) : ?>
      <li>
        <a class="page" href="admin/ship" data="ship=<?php echo $ship->id;?>">
          <?php echo $ship->shipwright; ?> <?php echo $ship->name;?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>

</div>


