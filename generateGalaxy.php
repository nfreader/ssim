<?php
require_once('inc/config.php');
require_once('inc/arrays.php');
require_once('inc/functions.php');

$user = new user();
if(!$user->isAdmin()){
  die("You must be an administrator to view this page.");
}

define('NUMBER_OF_SYSTS_TO_GENERATE',100);
define('GALAXY_MAX_X',100);
define('GALAXY_MAX_Y',100);
define('GOVT_DISTRIBUTION',25);
define('INDIE_GOVT_ID',1);
define('MAX_PORTS_PER_SYSTEM',5);
define('PORT_DISTRIBUTION',65);
define('PLANET_DISTRIBUTION',65);
define('MOON_DISTRIBUTION',17.5);
define('STATION_DISTRIBUTION',17.5);
define('NUMBER_OF_NEIGHBORS',4);

$i = 0;
$systems = array();
$techlevels = array(
  array(10,10,7,5,5),
  array(9,8,7,6,5),
  array(7,6,5,5,4),
  array(5,4,3,3,2),
  array(4,3,2,2,1)
);

$i = 0;
$unpopulatedsystems = 0;
$totalports = 0;
$totalplanets = 0;
$totalmoons = 0;
$totalstations = 0;
$systems = array();
while ($i <= NUMBER_OF_SYSTS_TO_GENERATE) {
  $x = floor(rand(GALAXY_MAX_X*-1,GALAXY_MAX_X));
  $y = floor(rand(GALAXY_MAX_Y*-1,GALAXY_MAX_Y));
  //System is uninhabited
  if(floor(rand(0,100)) > PORT_DISTRIBUTION) {
    switch(floor(rand(0,1))) {
      case 0:
        $prefix = pick($systPrefixes)." ";
        break;
      case 1:
        $prefix = '';
        break;
    }
    $sysname = pick($systNames);
    echo "<strong>$prefix$sysname ($i)</strong> ($x,$y) (Unpopulated)<br>";
    $system = array(
      'id'=>$i,
      'name'=>"$prefix$sysname",
      'x'=>intval($x),
      'y'=>intval($y)
    );
    $systems[] = $system;
    $unpopulatedsystems++;
    consoleDump($system);
  } else {
      //System is inhabited
    $sysname = pick($systNames);
    echo "<strong>$sysname ($i)</strong> ($x,$y)<br>";
    $system = array(
      'name'=>"$sysname",
      'id'=>$i,
      'x'=>intval($x),
      'y'=>intval($y)
    );
    $numports = floor(rand(1,MAX_PORTS_PER_SYSTEM));
    $ports = array();
    $type = '';
    while ($numports >= 1) {
      $techlevel = pick($techlevels[$numports-1]);
      $porttypechance = floor(rand(0,100));
      switch(true) {
        case ($porttypechance <= PLANET_DISTRIBUTION): //Port is a planet
          $portname = pick($systNames);
          $type = 'P';
          $totalplanets++;
          break;

        case ($porttypechance <= PLANET_DISTRIBUTION + MOON_DISTRIBUTION): //Port is a moon
          $portname = pick($systNames);
          $type = 'M';
          $totalmoons++;
          break;

        case ($porttypechance > STATION_DISTRIBUTION - 100): //Port is a station
          $prefix = pick($stationAdjectives);
          $stationname = pick($stationNames);
          switch(floor(rand(0,3))) {
            case 0:
              $suffix = pick($phoneticAlphabet);
              $type = 'N';
              break;

            case 1:
              $suffix = pick($greekAlphabet);
              $type = 'N';
              break;

            case 2:
              $suffix = pick($romanNumerals);
              $type = 'N';
              break;

            case 3:
              $suffix = '';
              $type = 'S';
              break;
          }
        $portname = "$prefix $stationname $suffix";
        $totalstations++;
      }
      echo "<em>$portname</em> ($techlevel)<br>";
      $port = array(
        'name'=>$portname,
        'techlevel'=>$techlevel,
        'type'=>$type
      );
      $totalports++;
      $numports--;
      $ports[] = $port;
    }
    $system['ports'] = $ports;
    $systems[] = $system;
  }
  $i++;
}

foreach ($systems as $sys) {
  $distances = array();
  foreach ($systems as $sub) {
    if ($sub['id'] != $sys['id']) {
      $d = floor(sqrt((($sub['x'] - $sys['x']) ** 2) + (($sub['y'] - $sys['y']) ** 2)));
      $sub['distance'] = $d;
      $distances[] = $sub;
      //echo "Distance from ".$sys['id']." to ".$sub['id'].": $d<br>";
    }
  }
  $dist = array();
  foreach ($distances as $key => $value) {
    $dist[$key] = $value['distance'];
  }
  array_multisort($dist,SORT_ASC,$distances);

  $numNeighbors = floor(rand(1,NUMBER_OF_NEIGHBORS));

  $distances = array_slice($distances,0,$numNeighbors);
  $sys['neighbors'] = $distances;
  consoleDump($sys);
  $galaxy[] = $sys;
}
consoleDump($galaxy);
$syst = new syst();
$spob = new spob();
foreach($galaxy as $system){
  $syst->addSyst($system['name'],$system['x'],$system['y'],NULL,$system['id']);
  foreach($system['neighbors'] as $neighbor) {
    echo "<br>Linking ".$system['name']." to ".$neighbor['name'].": ";
    $syst->linkSyst($system['id'],$neighbor['id']);
    echo "<br>";
  }
  if(isset($system['ports'])){
    foreach($system['ports'] as $port){  consoleDump($spob->addSpob($system['id'],$port['name'],$port['type'],$port['techlevel'],''));
    }
  }
}
?>
<canvas id="demoCanvas" width="513" height="513"></canvas>
<script src="//code.createjs.com/easeljs-0.8.1.min.js"></script>

<script>
    var canvas, stage, exportRoot;
    var canvas = document.getElementById("demoCanvas");
    var context = canvas.getContext("2d");

    var systems = <?php echo json_encode($galaxy,JSON_NUMERIC_CHECK); ?>;
    var stage = new createjs.Stage("demoCanvas");
    var w = 513;
    var h = 513;
    stage.scaleX = window.devicePixelRatio;
    stage.scaleY = window.devicePixelRatio;
    stage.addChild(exportRoot);
    stage.update();
    canvas.width = w * window.devicePixelRatio;
    canvas.height = h * window.devicePixelRatio;
    canvas.style.width = w + "px";
    canvas.style.height = h + "px";
    stage.enableMouseOver();
    var zoom = 2.5;

    var lines = [];

    var g = new createjs.Shape();
    for (var x = 0.5; x < canvas.width; x += 8) {
      g.graphics.setStrokeStyle(1).beginStroke('#eee').mt(x,0).lt(x,canvas.height);
      stage.addChild(g);
    }

    for (var y = 0.5; y < canvas.height ; y += 8) {
      g.graphics.setStrokeStyle(1).beginStroke('#eee').mt(0,y).lt(canvas.width,y);
      stage.addChild(g);
    }
    stage.update();

    //This is the label that we use for hover()
    var output = new createjs.Text("", "14px Arial");
    output.x = output.y = 10;
    stage.addChild(output);

    //This draws system dots
    systems.forEach(function(s){
      var circle = new createjs.Shape();
      circle.data = s;
      var x = parseInt((circle.data.x * zoom) + (canvas.width/4));
      var y = parseInt(((circle.data.y * zoom) - (canvas.height/4)) * -1);
      circle.data.x = x;
      circle.data.y = y;
      circle.graphics.beginFill('#000').drawCircle(x,y,4);
      circle.data.neighbors.forEach(function(i){
        var line = new createjs.Shape();
        var dx = parseInt((i.x * zoom) + (canvas.width/4));
        var dy = parseInt(((i.y * zoom) - (canvas.height/4)) * -1);
        line.graphics.setStrokeStyle(1).beginStroke('#CCC').mt(x,y).lt(dx,dy);
        stage.addChild(line);
        var distance = new createjs.Text(i.distance,"10px Helvetica",'#000');
        var mx = ((x+dx)/2);
        var my = ((y+dy)/2);
        distance.x = mx;
        distance.y = my;
        stage.addChild(distance);
        var connect = {};
        connect.origin = circle.data.id;
        connect.dest = i.id;
        connect.oname = circle.data.name;
        connect.dname = i.name;
        lines.push(connect);
      });
      stage.addChild(circle);
      circle.on("mouseover", hover);
      circle.on("mouseout", unhover);
      circle.on("click", sysclick);
    });

    stage.update();

    stage.on('stagemousedown',function(event){
      if (event.relatedTarget == null) {
        linksys = [];
      }
    });

    function hover(event) {
      output.x = event.target.data.x + 7;
      output.y = event.target.data.y - 10;
      output.text = event.target.data.name;
      stage.update();
    }
    function unhover(event) {
      output.text = '';
      stage.update();
    }

    function sysclick(event) {
      console.log( event.target.data.id);
      if (linksys.length == 2) {

      } else if (linksys[0].id == event.target.data.id){
        console.error("You cannot link a system to itself!");
      } else {
        linksys.push(event.target.data);
      }
    }

    function connectSystems(data) {
      dx = data[0].x;
      dy = data[0].y;
      ox = data[1].x;
      oy = data[1].y;
      if (data[0].id == data[1].id) {
        console.error('You cannot link a system to itself!');
        return false;
      }
      var line = new createjs.Shape();
      line.graphics.setStrokeStyle(1).beginStroke('red').mt(dx,dy).lt(ox,oy);
      stage.addChild(line);
      stage.update();
      var templink = {};
      templink.origin = data[0].id;
      templink.dest = data[1].id;
      templink.type = 'R';
      newlinks.push(templink);
      var templink = {};
      templink.origin = data[1].id;
      templink.dest = data[0].id;
      templink.type = 'R';
      newlinks.push(templink);
      $('.newlinkjson').text(JSON.stringify(newlinks));
      $('#sysid').text('Linked '+data[0].id+ ' to '+data[1].id+'!');
     }
</script>
