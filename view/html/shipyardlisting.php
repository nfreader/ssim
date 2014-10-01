<div class="box">

<?php

echo "<h3>$ship->name</h3>";
echo optionlist(array(
  'Class' => shipClass($ship->class)['class'],
  'Shipwright' => $ship->shipwright,
  'Shields' => $ship->shields,
  'Armor' => $ship->armor,
  'Cargobay' => $ship->cargobay,
  'Fueltank' => $ship->fueltank,
  'Cost' => credits($ship->cost)
));

echo "<a class='local-action btn btn-block'";
echo "action='buyShip&ship=$ship->id' href='home'>Purchase</a>"
?>

</div>