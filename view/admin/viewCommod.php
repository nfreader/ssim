<?php
include 'adminHeader.php';
$commod = new commod($_GET['commod']);
//var_dump($commod);
?>

<div class="center">
  <table>
    <thead>
      <tr>
        <th>Spob</th>
        <th>Amount</th>
        <th>Cost/ton</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($commod->commodSpob as $spob): ?>
      <tr>
        <td>
          <a href="admin/spob" class="page" data="spob=<?php echo $spob->spob;?>"><?php echo $spob->name;?></a>
          (<?php echo $spob->techlevel;?>)
        </td>
        <td><?php echo singular($spob->supply,'ton','tons'); ?></td>
        <td><?php echo credits($spob->price);?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="rightbar">
  <h1><?php echo $commod->name;?></h1>
  <span id="fingerprint">
  <div class="pull-left"><?php echo $commod->fullclass;?></div>
    Techlevel <?php echo $commod->techlevel;?>
  </span>
  <ul class="dot-leader">
    <li>
      <span>Avg. Price /ton</span>
      <span><?php echo credits($commod->avgPrice); ?></span>
    </li>
    <li>
      <span>Avg. Supply /spob</span>
      <span><?php echo singular($commod->avgSupply,'ton','tons'); ?></span>
    </li>
  </ul>
  <ul class="options">
  <?php if ('R' == $commod->class) : ?>
    <li>
      <a href="spamCommods&commod=<?php echo $commod->id;?>" class="action" data-dest="admin/commod">
        Spam Commod
      </a>
    </li>
  <?php endif; ?>
  </ul>
</div>
