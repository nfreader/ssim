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

<div id="right">
<h1>Details</h1>
<ul class="dot-leader">
  <li>
    <span class='left'>ID</span>
    <span class='right'><?php echo $spob->id; ?></span>
  </li>
  <li>
    <span class='left'>System</span>
    <span class='right'><?php echo "<a href='admin/system'
    data='syst=".$spob->parent->id."' class='page'>
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
    <span><?php echo ($spob->homeworld == 0 ? '<a href="admin/spob" data="action=makeHomeworld&spob='.$spob->id.'" class="page">No</a>'
      :'<a href="admin/spob" data="action=revokeHomeworld&spob='.$spob->id.'" class="page">Yes</a>');
    ?>
    </span>
  </li>
</ul>
</div>

<div id="center">
  <h1><?php echo $spob->fullname; ?></h1>
  <p><?php echo $spob->description; ?></p>
  <h2>Commodities</h2>
  <table class="table" >
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Type</th>
        <th>Price/ton</th>
        <th>Supply</th>
        <th>Link</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($spob->commods as $commod): ?>
      <?php
      switch($commod->class) {
        case 'R':
          $type = 'Regular';
        break;
        case 'S':
          $type = 'Special';
        $spawnbtn = '';
        break;
        case 'M':
          $type = 'Mission';
        $spawnbtn = '';
        break;
      }?>
      <tr class="commod commod-<?php echo $commod->class;?>">
        <td><?php echo $commod->id;?></td>
        <td><?php echo $commod->name;?></td>
        <td><?php echo $type;?></td>
        <td><?php echo credits($commod->price);?></td>
        <td><?php echo singular($commod->supply,'ton','tons');?></td>
        <td>
          <a href="admin/viewCommod" data="commod=<?php echo $commod->id;?>" class="page">View</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
