<?php 
include 'adminHeader.php';
$commod = new commod();
$commods = $commod->getCommods(TRUE);

?>
<div class="center wide">
<h1>All commodities</h1>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Techlevel</th>
        <th>Type</th>
        <th>Base Price/unit</th>
        <th>Avg. Price</th>
        <th>Spobs</th>
        <th>Total Supply</th>
        <th>Avg Supply</th>
        <th>Link</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($commods as $commod): ?>
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
        <td><?php echo $commod->techlevel;?></td>
        <td><?php echo $type;?></td>
        <td><?php echo credits($commod->baseprice);?></td>
        <td><?php echo credits($commod->price);?></td>
        <td><?php echo $commod->spobs;?></td>
        <td><?php echo singular($commod->totalsupply,'ton','tons');?></td>
        <td><?php echo singular($commod->avgsupply,'ton','tons');?></td>
        <td>
          <a href="admin/viewCommod" data="commod=<?php echo $commod->id;?>" class="load">View</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <h2>Add a new base commodity</h2>
  <form class="vertical async"
  action='addBaseCommod'
  data-dest='admin/commod'>
    <input type='text' name='name' placeholder='Name' />
    <input type='number' name='techlevel' min='2' max='10' placeholder='Techlevel' />
    <input type='number' name='price' placeholder='Baseprice' />
    <button class="color olive">Add</button>
  </form>
  <h2>Options</h2>
  <ul class="options">
    <li>
      <a href="spamAllCommods" class="action" data-dest="admin/commod">
        Spam Commods
      </a>
    </li>
  </ul>
</div>