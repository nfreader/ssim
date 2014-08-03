<?php 
include 'adminHeader.php';

$spob = new spob($_GET['spob']);

if(isset($_GET['action']) && ($_GET['action'] == 'makeHomeworld')) {
  if($spob->makeHomeworld($_GET['spob'])) {
    echo $spob->spob->name." has been declared a homeworld.";
  }
} else {

}

?>

<div class='rightbar'>
<h1>Details</h1>
<ul class="dot-leader">
  <li>
    <span class='left'>ID</span>
    <span class='right'><?php echo $spob->spob->id; ?></span>
  </li>
  <li>
    <span class='left'>System</span>
    <span class='right'><?php echo "<a href='admin/system' query='syst=".$spob->spob->parent."' class='load'>". $spob->spob->system."</a>"; ?></span>
  </li>
  <li>
    <span class='left'>Type</span>
    <span class='right'><?php echo spobType($spob->spob->type); ?></span>
  </li>
    <li>
    <span>Government</span>
    <span><?php echo $spob->spob->government; ?></span>
  </li>
  <li>
    <span>Tech Level</span>
    <span><?php echo $spob->spob->techlevel; ?></span>
  </li>
  <li>
    <span>Fuel Cost</span>
    <span><?php echo $spob->fuelcost; ?>cr./unit</span>
  </li>
  <li>
    <span>Homeworld?</span>
    <span><?php echo ($spob->spob->homeworld == 0 ? 'No <a href="admin/planet" query="action=makeHomeworld&spob='.$spob->spob->id.'" class="load">Change</a>'
      :'Yes'); 
    ?>

    </span>
  </li>
</ul>
</div>

<div class="center">
  <h1><?php echo spobType($spob->spob->type)." ".$spob->spob->name;?></h1>
  <p><?php echo $spob->spob->description; ?></p>
  <div class="technical">
    <p><strong>Bluespace Transmission Node:</strong><br>
    <?php echo $spob->nodeid; ?></p>
  </div>
</div>

