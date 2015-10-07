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


<div class="center wide">

  <h1>Shipyard
    <div class="pull-right">
      <a class="load" href="admin/shipyard" data="addShip">Add new ship</a>
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
        <a class="load" href="admin/ship" data="ship=<?php echo $ship->id;?>">
          <?php echo $ship->shipwright; ?> <?php echo $ship->name;?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>

</div>


