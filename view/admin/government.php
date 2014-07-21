<?php 
include 'adminHeader.php';

$govt = new govt();
$governments = $govt->listGovt();

?>

<div class='leftbar'>

</div>

<div class="center">
  <h1>Governments</h1>
  <ul class="options">
  <?php foreach ($governments as $govt) {
    echo "<li><a href='' style='background:".$govt->color2."; color:".$govt->color."'>".$govt->name."</a></li>";
  } ?>
  </ul>
</div>