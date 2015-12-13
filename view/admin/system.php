<?php include 'adminHeader.php'; ?>
<?php $syst = new syst($_GET['syst']); ?>

<?php if(isset($_GET['action']) && ($_GET['action'] == 'addSpob')) {
  $spob = new spob();
  if ($spob->addSpob(
    $syst->id,
    $_GET['name'],
    $_GET['type'],
    $_GET['techlevel'],
    $_GET['description']
  )) {
    echo "Added ".$_GET['name'];
  } else {
    echo "Unable to add new destination";
  }
}
?>

<div class='rightbar'>
<h1>Details</h1>
<ul class="dot-leader">
  <li>
    <span class='left'>ID</span>
    <span class='right'><?php echo $syst->id; ?></span>
  </li>
  <li>
    <span class='left'>Coordinates</span>
    <span class='right'><?php echo $syst->coords; ?></span>
  </li>
  <li>
    <span class='left'>Node</span>
    <span class='right'><?php echo $syst->fingerprint; ?></span>
  </li>
    <li>
    <span>Government</span>
    <span><?php echo $syst->govt->name; ?></span>
  </li>
</ul>

<h1>Connections</h1>
<ul class="options">
<?php foreach ($syst->connections as $connection) :?>
<li>
  <a href="admin/system" class="page" data="syst=<?php echo $connection->id;?>">
    <?php echo $connection->name;?>
  </a>
</li>
<?php endforeach;?>
</ul>

<h1>Pilots</h1>
<ul class="options">
<?php foreach ($syst->pilots as $pilot) :?>
<li>
  <a href="admin/pilot" class="page" data="pilot=<?php echo $pilot->uid;?>">
    <?php echo $pilot->name;?>
  </a>
</li>
<?php endforeach;?>
</ul>

</div>

<div class="center">
<h1><?php echo $syst->name;?> attractions</h1>
  <ul class="options">
  <?php foreach($syst->spobs as $spob) :?>
    <li>
      <a href="admin/spob" class="page" data="spob=<?php echo $spob->id;?>">
        <?php echo spobType($spob->type,'icon')." ".$spob->name;?>
      </a>
    </li>
  <?php endforeach; ?>
  </ul>
    <div class="form-group">
      <h2 class='form-title'>Add new destination</h2>
      <form class="vertical async-form"
      action='view/admin/action.php?action=addSpob&syst=<?php echo $_GET['syst']; ?>'
      page='admin/galaxy'>
        <input type='text' name='name' placeholder='Name' />
        <h3>Type</h3>
        <label class="radio"><input type='radio' name='type' value='S' />
          Station
        </label>
        <label class="radio"><input type='radio' name='type' value='M' />
          Moon
        </label>
        <label class="radio"><input type='radio' name='type' value='P' />
          Planet
        </label>
        <label class="radio"><input type='radio' name='type' value='N' checked="checked" />
          None
        </label>
        <input type='number' name='techlevel' min='0' max='10' placeholder='Techlevel' />
        <textarea name="description" placeholder="Description" rows="5"></textarea>
        <button>Add</button>
      </form>
    </div>
    <h1>Beacons</h1>
  <?php foreach($syst->beacons as $beacon): ?>
    <?php $type = beaconTypes($beacon->type); ?>
    <div class="beacon <?php echo $beacon->class; ?>">
      <?php echo $beacon->header; ?>
      <p id="beacon" data-type="textarea" data-pk="<?php echo $beacon->id;?>" data-url="view/admin/action.php?action=editBeacon" data-title="Edit beacon" class="editable">
        <?php echo $beacon->content;?>
      </p>
      <?php echo $beacon->footer; ?>
    </div>
  <?php endforeach;?>
</div>

<script>
$(document).ready(function(){
  $('.editable').editable();
});
</script>
