<?php 

class spob {

  public $spob;
  public $fuelcost;
  public $nodeid;

  public function __construct($id=null) {
    if (isset($id)) {
      $this->spob = $this->getSpob($id);
      switch ($this->spob->type) {
        case 'S':
        case 'N':
        $modifier = .5;
        break;

        case 'M':
        $modifier = 1.5;
        break;

        default:
        $modifier = 1;
        break;
      }
      $this->fuelcost = floor(FUEL_BASE / $this->spob->techlevel) * $modifier;
      $this->nodeid = hexPrint($this->spob->name.$this->spob->system);
    }
  }

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
    return $db->single();    
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
      || $techlevel > 1) {
      return false;
    } else {
      $db->bind(':parent', $parent);    
      $db->bind(':name', $name);
      $db->bind(':type', $type);
      $db->bind(':techlevel', floor($techlevel));
      $db->bind(':description', $description);
      if ($db->execute()) {
        return $name. " was added.";
      } else {
        return "Something went wrong. Spob not added";
      }
      
    }
  }

  public function generatePlanets($count) {
    global $systNames;
    $i=0;
    $planets = [];
    while ($i<$count) {
      $planet = [];
      $planet['name'] = $systNames[array_rand($systNames)];
      $planet['desc'] = 'TEST';
      $planet['techlevel'] = floor(rand(1,10));
      $planets[] = $planet;
      $i++;
    }
    return $planets;
  }

  public function generateStation($count) {
    global $companyNames;
    global $stationNames;
    global $stationAdjectives;
    global $phoneticAlphabet;
    global $greekAlphabet;
    global $romanNumerals;
    $i=0;
    $stations = [];
    while ($i<$count) {
    $isCompany = (bool)rand(0,1);
    if ($isCompany === true) { //This station needs a company prefix and desc.
      $company = $companyNames[array_rand($companyNames)];
      $stationName = $stationNames[array_rand($stationNames)];
      //TODO: Description generators
      /*
      
      $name is widely regarded as the crown jewel of $company. 
      
      $company spent 10 years and 20 billion credits to build $name. Today it lies almost completely dormant, a monument to excess.
      
      The $company's security forces glare at you, uncertain of your intentions on $name. They don't get many visitors here.
  
      "Welcome to $name" blares the automated attendant. "Thenk you for choosing $company, we hope you enjoy your stay!"

      

      */
    } else { //Just a regular station.
      $company = $stationAdjectives[array_rand($stationAdjectives)];
      $stationName = $stationNames[array_rand($stationNames)];
      //TODO: Description generators
      /*
        
      Seven hundred and sixty one armless and legless corpses float inconspicuously around the inside of hangar $name

      The bar on $name is widely known for its 

      */
    }
    $suffix = floor(rand(1,4));
    if ($suffix == 1) {
      $sfx = $phoneticAlphabet[array_rand($phoneticAlphabet)];
    } elseif ($suffix == 2) {
      $sfx = $greekAlphabet[array_rand($greekAlphabet)];
    } elseif ($suffix == 3) {
      $sfx = $romanNumerals[array_rand($romanNumerals)];
    } else {
      $sfx = '';
    }
    $i++;
    $station = [];
    $station['name'] = $company." ".$stationName." ".$sfx;
    $station['desc'] = 'TEST';
    $station['techlevel'] = floor(rand(1,10));
    $stations[] = $station;
    }
    return $stations;
  }

}