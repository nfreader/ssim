<?php 
  $spobs = new spob();
  $spobs = $spobs->getHomeworlds(); 

  if ($spobs === array()) {
    echo "No homeworlds found!";
    if ($user->isAdmin()) {
      echo "<a href='admin/galaxy' class='load'>Edit Galaxy</a>";
    }
  }
?>