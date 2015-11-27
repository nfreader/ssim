<?php
include 'adminHeader.php';

if (isset($_GET['genCSS'])){
  $govt = new govt();
  $govt->generateCSS();
}

if(isset($_GET['govtid'])) :
  $govt = new govt($_GET['govtid'],TRUE);
  ?>
  <div class="center wide">
    <h1 class="govt-label pull-center"
      style="background: <?php echo $govt->color1;?>; color: <?php echo $govt->color2;?>;">
      <?php echo $govt->name;?>
    </h1>
    <h2>Stats</h2>
    <table>
      <thead>
        <tr>
          <th>Type</th>
          <th>ISO-3166-1 alpha-2 code</th>
          <th>Members</th>
          <th>Approximate Wealth</th>
          <th>Controlled systems (controlled ports)</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $govt->type;?></td>
          <td><?php echo $govt->isoname;?></td>
          <td><?php echo $govt->totalpilots;?></td>
          <td><?php echo credits($govt->totalmemberbalance);?></td>
          <td><?php echo "$govt->systems ($govt->spobs)";?></td>
        </tr>
      </tbody>
    </table>

    <h2>Relations</h2>
    <?php foreach ($govt->relations as $relation) : ?>
      <?php if($govt->id == $relation->subject) :?>
        <?php echo relationType($relation->relation)['Full']; ?>
          with
          <a href="admin/government" data="govtid=<?php echo $relation->target;?>" class="page govt-label"
            style="background: <?php echo $relation->tgtcolor1;?>; color: <?php echo $relation->tgtcolor2;?>;">
            <?php echo $relation->tgtname;?></a><br>
      <?php else: ?>
        <?php echo relationType($relation->relation)['Full']; ?> with
          <a href="admin/government" data="govtid=<?php echo $relation->subject;?>" class="page govt-label"
            style="background: <?php echo $relation->subjcolor1;?>; color: <?php echo $relation->subjcolor2;?>;">
            <?php echo $relation->subjname;?></a><br>
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
  <a class="btn page block" data="genCSS">Generate CSS</a>
</div>
<?php endif;?>
