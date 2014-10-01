<?php

include '../inc/config.php';
$pilot = new pilot();

$ships = new ship();
?>

<div class="leftbar">
  <h1>Your Vessel</h1>

<?php

$ship = $ships->getShip($pilot->pilot->ship);

echo optionlist(array(
  'Name' => $pilot->pilot->vessel,
  'Make' => $ship->name,
  'Class' => shipClass($ship->class)['class'],
  'Shipwright' => $ship->shipwright,
  'Shields' => $ship->shields,
  'Armor' => $ship->armor,
  'Cargobay' => $ship->cargobay,
  'Fueltank' => $ship->fueltank,
  'Trade In Value' => credits(shipValue($pilot->pilot->ship, $pilot->getPilotErrata('shipbuy'), $ship->cost))
));

?>
</div>

<div class="center">
  <h1><?php echo $pilot->pilot->planet; ?> Shipyard</h1>

<?php
$ships = $ships->getShipyard();
foreach ($ships as $ship) {
  if($ship->id != $pilot->pilot->ship){
    include('html/shipyardlisting.php');
  }
}
?>

</div>

<?php include 'rightbar.php'; ?>
<script>
  loadContent('ping', '.footer', '.footerbar');
</script>


