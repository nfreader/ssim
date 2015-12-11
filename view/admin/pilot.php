<?php require_once('adminHeader.php');

if (isset($_GET['pilot'])):
  $pilot = new pilot($_GET['pilot'],FALSE);?>

<div class="fiftyfifty">
  <h1><?php echo $pilot->name;?></h1>
  <span id='fingerprint'>
    Fingerprint <?php echo $pilot->fingerprint;?>
  <div class='pull-right'><?php echo $pilot->uid;?></div></span>

  <a href="admin/government" data="govtid=<?php echo $pilot->govt->id;?>" class="page label"
style="background: <?php echo $pilot->govt->color1;?>; color: <?php echo $pilot->govt->color2;?>;">
  <?php echo $pilot->govt->name;?>
  </a>

  <ul class="dot-leader">
    <li>
      <span>Status</span>
      <span><?php echo $pilot->fullstatus;?></span>
    </li>
    <li>
      <span>Credits</span>
      <span>
        <a id="credits" class="editable">
          <?php echo $pilot->credits;?>
        </a>
        <i class="fa fa-certificate credits"></i>
      </span>
    </li>
    <li>
      <span>Legal</span>
      <span>
        <a id="legal" class="editable">
          <?php echo $pilot->legal;?>
        </a>
        <i class="fa fa-flag"></i>
    </span>
    </li>
    <li>
      <span><a href="ship/viewShip" data="ship=<?php echo $pilot->vessel->id;?>"
      class="page">Ship</a>
      </span>
      <span><?php echo $pilot->vessel->name;?></span>
    </li>
  </ul>

</div>

<div class="fiftyfifty">

  <h1><?php echo $pilot->vessel->name;?></h1>
  <span class="fingerprint">Registration number <?php echo $pilot->vessel->registration;?></span>

  <ul class="meters">
    <li><?php echo $pilot->vessel->fuelGauge; ?></li>
    <li><?php echo $pilot->vessel->shieldGauge; ?></li>
    <li><?php echo $pilot->vessel->armorGauge; ?></li>
    <li><?php echo meter("Cargo (".$pilot->cargo->cargo." / ".$pilot->cargo->cargobay.")",0,$pilot->cargo->cargometer);?></li>
  </ul>

  <h2>Outfits</h2>
  <?php foreach ($pilot->vessel->outfits as $outfit) : ?>
    <?php echo outfitFormatter($outfit);?>
  <?php endforeach; ?>

  <h2>Commodity cargo</h2>
  <?php if (empty($pilot->cargo->commods)) :?>
    <div class="pull-center">&#x0226A; No commodity cargo &#x0226B;</div>
  <?php endif; ?>
  <?php foreach ($pilot->cargo->commods as $commod): ?>
    <div class="commodity jettison">
      <?php if (!$pilot->flags->isLanded) :?>
      <form class="async" data-dest="ship/viewShip"
      action="jettisonCommod&commod=<?php echo $commod->id;?>">
        <input type="number" min="1" max="<?php echo $commod->amount; ?>"
        name="amount" placeholder="Amount"
        data-supply="<?php echo $commod->amount;?>" />
        <button disabled>Enter Amount</button>
      </form>
    <?php endif;?>
      <h3>
        <?php echo $commod->name;?>
        <small>
          <?php echo singular($commod->amount,'ton','tons');?> in hold
        </small>
      </h3>
    </div>
  <?php endforeach; ?>

</div>

<?php else:
  $pilot = new pilot(FALSE);
  $pilots = $pilot->getPilotList(TRUE);
?>
<div class="center wide">
  <h1>Pilots</h1>
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>UID</th>
        <th>View</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($pilots as $pilot):?>
        <tr>
          <td><?php echo $pilot->name;?></td>
          <td><?php echo $pilot->uid;?></td>
          <td>
            <a href="admin/pilot" data="pilot=<?php echo $pilot->uid;?>"
             class="page">View</a>
          </td>
        </tr>
      <?php endforeach;?>
    </tbody>
  </table>
</div>
<?php endif; ?>
<script>
$('#credits').editable({
    type: 'text',
    pk: '<?php echo $pilot->uid;?>',
    url: 'view/admin/action.php?action=changeCredits',
    title: 'Change user credits'
});
$('#legal').editable({
    type: 'text',
    pk: '<?php echo $pilot->uid;?>',
    url: 'view/admin/action.php?action=changeLegal',
    title: 'Change user legal rating'
});
</script>
