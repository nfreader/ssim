<?php 
include 'adminHeader.php';

$spob = new spob();

if(isset($_GET['action']) && ($_GET['action'] == 'makeHomeworld')) {
  if($spob->makeHomeworld($_GET['spob'])) {
    $spob = $spob->getSpob($_GET['spob']);
    echo $spob->name." has been declared a homeworld.";
  }
} else {
  $spob = $spob->getSpob($_GET['spob']);
}

?>

<div class='leftbar'>
<h1>Details</h1>
<ul class="dot-leader">
  <li>
    <span class='left'>ID</span>
    <span class='right'><?php echo $spob->id; ?></span>
  </li>
  <li>
    <span class='left'>System</span>
    <span class='right'><?php echo "<a href='admin/system' query='syst=".$spob->parent."' class='load'>". $spob->system."</a>"; ?></span>
  </li>
  <li>
    <span class='left'>Type</span>
    <span class='right'><?php echo spobType($spob->type); ?></span>
  </li>
    <li>
    <span>Government</span>
    <span><?php echo $spob->government; ?></span>
  </li>
  <li>
    <span>Tech Level</span>
    <span><?php echo $spob->techlevel; ?></span>
  </li>
  <li>
    <span>Homeworld?</span>
    <span><?php echo ($spob->homeworld == 0 ? 'No <a href="admin/planet" query="action=makeHomeworld&spob='.$spob->id.'" class="load">Change</a>'
      :'Yes'); 

    ?>

    </span>
  </li>
</ul>
</div>

<div class="center">
  <h1><?php echo spobType($spob->type)." ".$spob->name;?></h1>
  <p><?php echo $spob->description; ?></p>
  <div class="technical">
    <p><strong>Bluespace Transmission Node:</strong><br>
    <?php echo hexPrint($spob->name.$spob->system); ?></p>
  </div>
</div>