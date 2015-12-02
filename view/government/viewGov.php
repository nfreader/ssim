<div class="center wide">
  <h1>
    <span class="label"
    style="background: <?php echo $govt->color1;?>; color: <?php echo $govt->color2;?>;">
      <?php echo $govt->name;?>
    </span>
    <div class="pull-right">
      <?php if ($govt->id == $pilot->govt && 'R' == $govt->type) :?>
        <a href="leaveGovt" class="action" dest="home">Leave government</a>
      <?php elseif('R' == $govt->type) :?>
        <a href="joinGovt&govt=<?php echo $govt->id;?>" class="action" data-dest="home">Apply to join</a>
      <?php endif; ?>
    </div>
  </h1>
  <?php if ('P' == $govt->type) :?>
    <span id="fingerprint">You cannot leave or join the pirate government</span>
  <?php endif; if('I' == $govt->type):?>
    <span id="fingerprint">Non-affiliated pilots are automatically marked as independent pilots.</span>
  <?php endif;?>

  <h2>Relations</h2>
  <?php foreach ($govt->relations as $relation) : ?>
    <?php if($govt->id == $relation->subject) :?>
      <?php echo relationType($relation->relation)['Full']; ?>
        with
        <a href="government/govt" data="govtid=<?php echo $relation->target;?>" class="page label inline"
          style="background: <?php echo $relation->tgtcolor1;?>; color: <?php echo $relation->tgtcolor2;?>;">
          <?php echo $relation->tgtname;?></a><br>
    <?php else: ?>
      <?php echo relationType($relation->relation)['Full']; ?> with
        <a href="government/govt" data="govtid=<?php echo $relation->subject;?>" class="page label inline"
          style="background: <?php echo $relation->subjcolor1;?>; color: <?php echo $relation->subjcolor2;?>;">
          <?php echo $relation->subjname;?></a><br>
    <?php endif;?>
  <?php endforeach;?>
</div>
