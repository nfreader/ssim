<?php 

class spob {

  //Get a list of all spobs assigned to a system
  public function getSpobs($syst) {
    $db = new database();
    $db->query("SELECT * FROM ssim_spob WHERE parent = :syst");
    $db->bind(':syst',$syst);
    $db->execute();
    return $db->resultSet();
  }

  //Get a list of all spobs with a planet flag set
  public function getHomeworlds() {
    $db = new database();
    $db->query("SELECT name, id FROM ssim_spob WHERE homeworld = 1");
    $db->execute();
    return $db->resultSet();
  }

  public function addSpob($parent, $name, $type, $techlevel, $description) {
    $db = new database();
    $db->query("INSERT INTO ssim_spob (parent, name, type, techlevel, description) 
    VALUES (:parent, :name, :type, :techlevel, :description)");
    $db->bind(':parent', $parent);    
    $db->bind(':name', $name);
    $db->bind(':type', $type);
    $db->bind(':techlevel', $techlevel);
    $db->bind(':description', $description);
    $db->execute();
  }

}