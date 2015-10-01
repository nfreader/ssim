<?php 
include 'adminHeader.php';

$spob = new spob($_GET['spob']);

if(isset($_GET['action']) && ($_GET['action'] == 'makeHomeworld')) {
  if($spob->makeHomeworld($_GET['spob'])) {
    echo $spob->name." has been declared a homeworld.";
    $spob->homeworld = 1;
  }
}
if(isset($_GET['action']) && ($_GET['action'] == 'revokeHomeworld')) {
  if($spob->revokeHomeworld($_GET['spob'])) {
    echo "$spob->name is no longer a homeworld.";
    $spob->homeworld = 0;
  }
}
?>

<div class='rightbar'>
<h1>Details</h1>
<ul class="dot-leader">
  <li>
    <span class='left'>ID</span>
    <span class='right'><?php echo $spob->id; ?></span>
  </li>
  <li>
    <span class='left'>System</span>
    <span class='right'><?php echo "<a href='admin/system'
    query='syst=".$spob->parent->id."' class='load'>
    ". $spob->parent->name."</a>"; ?></span>
  </li>
  <li>
    <span class='left'>Type</span>
    <span class='right'><?php echo spobType($spob->type); ?></span>
  </li>
    <li>
    <span>Government</span>
    <span><?php echo $spob->govt->name; ?></span>
  </li>
  <li>
    <span>Tech Level</span>
    <span><?php echo $spob->techlevel; ?></span>
  </li>
  <li>
    <span>Fuel Cost</span>
    <span><?php echo credits($spob->fuelcost); ?> / unit
    </span>
  </li>
  <li>
    <span>Node</span>
    <span><?php echo $spob->nodeid; ?></span>
  </li>
  <li>
    <span>Homeworld?</span>
    <span><?php echo ($spob->homeworld == 0 ? '<a href="admin/spob" query="action=makeHomeworld&spob='.$spob->id.'" class="load">No</a>'
      :'<a href="admin/spob" query="action=revokeHomeworld&spob='.$spob->id.'" class="load">Yes</a>'); 
    ?>
    </span>
  </li>
</ul>
</div>

<div class="center">
  <h1><?php echo $spob->fullname; ?></h1>
  <p><?php echo $spob->description; ?></p>
</div>

