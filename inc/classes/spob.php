<?php 

class spob {

  //Get a list of all spobs assigned to a system
  public function getSpobs($syst=null) {
    if ($syst === null) {
      $db = new database();
      $db->query("SELECT * FROM ssim_spob");
      $db->execute();
      return $db->resultSet();  
    } else {
      $db = new database();
      $db->query("SELECT * FROM ssim_spob WHERE parent = :syst");
      $db->bind(':syst',$syst);
      $db->execute();
      return $db->resultSet();        
    }

  }

  public function getSpob($spob) {
    $db = new database();
    $db->query("SELECT ssim_spob.*,
      ssim_syst.name AS system,
      ssim_syst.govt,
      ssim_govt.name AS government,
      ssim_govt.isoname,
      ssim_govt.color,
      ssim_govt.color2,
      ssim_govt.id AS govid
      FROM ssim_spob
      LEFT JOIN ssim_syst ON ssim_spob.parent = ssim_syst.id
      LEFT JOIN ssim_govt ON ssim_syst.govt = ssim_govt.id
      WHERE ssim_spob.id = :spob");
    $db->bind(':spob',$spob);
    $db->execute(); 
    return $db->single();
  }

  //Get a list of all spobs with a planet flag set
  public function getHomeworlds() {
    $db = new database();
    $db->query("SELECT name, id FROM ssim_spob WHERE homeworld = 1");
    $db->execute();
    return $db->resultSet();
  }

  //Returns one random homeworld spob (id and name)
  public function getRandHomeworld() {
    $db = new database();
    $db->query("SELECT name, id, parent
      FROM ssim_spob
      WHERE homeworld = 1
      ORDER BY RAND()
      LIMIT 0,1");
    $db->execute();
    return $db->single());    
  }

  public function makeHomeworld($spob) {
    $db = new database();
    $db->query("UPDATE ssim_spob SET homeworld = 1 WHERE id = :id");
    $db->bind(":id",$spob);
    $db->execute();
    return $db->rowCount();
    $game = new game();
    $game->logEvent('MH','Made '.$spob.' a homeworld');
  }

  public function addSpob($parent, $name, $type, $techlevel, $description) {
    $db = new database();
    $db->query("INSERT INTO ssim_spob (parent, name, type, techlevel, description) 
    VALUES (:parent, :name, :type, :techlevel, :description)");
    if (empty($parent)
      || empty($name)
      || empty($techlevel)
      || $techlevel < 10
      || $techlevel > 0) {
      return false;
    } else {
      $db->bind(':parent', $parent);    
      $db->bind(':name', $name);
      $db->bind(':type', $type);
      $db->bind(':techlevel', $techlevel);
      $db->bind(':description', $description);
      $db->execute();
      return true;
    }
  }

}