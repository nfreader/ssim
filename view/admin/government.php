<?php
include 'adminHeader.php';


if(isset($_GET['govtid'])) :
  $govt = new govt($_GET['govtid']);
  ?>
  <div class="center wide">
    <h1><?php echo $govt->name;?></h1>
    <span class="fingerprint"><?php echo $govt->type;?> Government</span>
    <h2>Relations</h2>
    <?php foreach ($govt->relations as $relation) : ?>
      <?php if($govt->id == $relation->subject) :?>
        <?php echo $relation->relation;?> with <?php echo $relation->tgtname;?><br>
      <?php else: ?>
      <?php echo $relation->relation;?> with <?php echo $relation->subjname;?><br>
    <?php endif;?>
    <?php endforeach;?>
</div>
  <?php
else:
  $govt = new govt();
  $governments = $govt->getGovts();
?>

<div class="center wide">
  <h1>Governments</h1>
  <ul class="options">
    <?php foreach ($governments as $govt) : ?>
      <li>
        <a href="admin/government" data="govtid=<?php echo $govt->id;?>" class="page"
          style="background: <?php echo $govt->color1;?>; color: <?php echo $govt->color2;?>;">
          <?php echo $govt->name;?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif;?>
