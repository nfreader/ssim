<div class="contact">

<?php 
echo "<h3>".$target->name."</h3>";
?>

  <?php 
  echo "<span class='fingerprint'>".hexPrint($target->name.$target->timestamp)."</span>";
  ?>
  <ul class="dot-leader">
    <li id='govt'>
      <span class="left">Government</span>
      <span class="right"><?php echo $target->government;?></span>
    </li>
    <li class="legal">
      <span class="left">Legal</span>
      <span class="right"><?php echo $target->legal.icon('flag');?></span >
    </li>
    <li id='ship'>
      <span class="left">Ship</span>
      <span class="right"><?php echo $target->vessel; ?></span>
    </li>
    <li id='make'>
      <span class="left">Make</span>
      <span class="right"><?php echo $target->shipname; ?></span>
    </li>
    <li id='class'>
      <span class="left">Class</span>
      <span class="right"><?php echo shipClass($target->class)['class'];  ?></span>
    </li>
  </ul>

  <ul class="meters">
  <?php 
    echo "<li>".icon('magnet')."".shieldMeter($target->shields)."</li>";
    echo "<li>".icon('wrench')."".armorMeter($target->armor)."</li>";
  ?>
  </ul>
  <?php echo "<a class='btn btn-block page'";
  echo "href=messages data='convo=".$target->id."'";
  echo ">Send Message</a>";
  ?>
  <a class='btn btn-block' disabled='true'>ENGAGE</a>
</div>