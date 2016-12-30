<?php consoleDump($govt);?>
<div id="center" class="wide">
  <h1 style="<?php echo $govt->css;?>">
      <?php echo $govt->name;?>
    <div class="right">
      <?php if ($govt->id == $pilot->govt && 'R' == $govt->type) :?>
        <a href="leaveGovt" class="action" dest="home">Leave government</a>
      <?php elseif('R' == $govt->type) :?>
        <a href="joinGovt&govt=<?php echo $govt->id;?>" class="action" data-dest="home">Apply to join</a>
      <?php endif; ?>
    </div>
  </h1>
  <?php if ('P' == $govt->type) :?>
    <small>You cannot leave or join the pirate government</small>
  <?php endif; if('I' == $govt->type):?>
    <small>Non-affiliated pilots are automatically marked as independent pilots.</small>
  <?php endif;?>

  <h2>Relations</h2>
  <ul class="options">
  <?php foreach ($govt->relations as $relation) : ?>
    <li>
      <a href="government/govt" data="govtid=<?php echo $relation->id;?>"
      style="<?php echo $relation->css;?>" class="page">
        <?php echo "$relation->relationName with $relation->name";?>
      </a>
  <?php endforeach;?>
  </ul>
</div>

