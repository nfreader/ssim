<?php 
$type = beaconTypes($beacon->type); ?>

<div class="beacon <?php echo $type['class']; ?>">
  <?php echo $type['header'].$beacon->content;?>
</div>