<?php
include '../inc/config.php';
require_once('rightbar.php');
?>

<div class="leftbar">
<h1>About Spacesim</h1>  
  <ul class="options">
    <li><a href='home' class='page'>Back</a></li>
  </ul>

</div>
<?php

$changes[] = array(
  'class'=>'olive',
  'content'=>'Added a changelog!'
);

$changes[] = array(
  'class'=>'olive',
  'content'=>'Restored messaging functionality'
);

?>

<div class="center"><h1>Changelog</h1>
  <ul class="options changelog">
    <?php foreach ($changes as $change) {
      echo "<li class='color ".$change['class']."'> ".$change['content']."</li>";
    } ?>
  </ul>
</div>

