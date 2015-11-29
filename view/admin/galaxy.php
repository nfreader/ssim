<?php 
include 'adminHeader.php';

$syst = new syst();
if(isset($_GET['action']) && ($_GET['action'] == 'addSyst')) {
    if ($newSyst = $syst->addSyst($_GET['name'],$_GET['coordx'],$_GET['coordy'])) {
      echo "Added system".$_GET['name'];
    } else {
      echo "Unable to add system";
    }
}
$spob = new spob();
$spobs = $spob->getSpobs();

?>

<div class="rightbar">
  <div class="form-group">
    <h2 class='form-title'>Add new System</h2>
    <form class="vertical async" action='addSyst' data-dest='admin/galaxy'>
      <input type='text' name='name' placeholder='System Name' />
      <input type='number' name='coordx' placeholder='X Coordinate' />
      <input type='number' name='coordy' placeholder='Y Coordinate' />
      <button>Add</button>
    </form>
  </div>
</div>

<div class='center'>
<h1>Galaxy Map</h1>
<?php
$systems = $syst->listSysts();
$spobs = $spob->getSpobs();
if ($systems == array()): ?>

<h1>Error 404: Galaxy not found</h1>
<p>Add a few systems on the left!</p>
  
<?php else: ?>
  <ul class="options">
  <?php foreach($systems as $system): ?>
    <li>
      <a href="admin/system" class="page" data="syst=<?php echo $system->id;?>">
        <?php echo "$system->name ($system->coord_x,$system->coord_y)";?>
      </a>
    </li>
    <ul class="options">
    <?php foreach($spobs as $spob) :?>
      <?php if ($system->id === $spob->parent): ?>
        <li>
          <a href="admin/spob" class="page" data="spob=<?php echo $spob->id;?>">
            <?php echo spobType($spob->type,'icon')." ".$spob->name;?>
            <?php echo TRUE == $spob->homeworld ? "<span class='pull-right'>".icon('home')."</span>" : ''; ?>
          </a>
        </li>
      <?php endif;?>
    <?php endforeach; ?>
    </ul>
  <?php endforeach;?>

<?php endif; ?>
</div>