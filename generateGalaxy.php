<?php 
require_once('inc/config.php');
require_once('inc/arrays.php');
require_once('inc/functions.php');
$i = 0;
$rawsyst = array();
while($i < 100) {
  $i++;
  $rawsyst[] = array(
    'x'=>floor(rand(-100,100)),
    'y'=>floor(rand(-100,100)),
    'id'=>$i,
    'name'=>pick($systNames)
  );
}
$systems = array();
foreach ($rawsyst as $sys) {
  $distances = array();
  foreach ($rawsyst as $sub) {
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

  $distances = array_slice($distances,0,1);
  $sys['neighbors'] = $distances;
  $systems[] = $sys;
}
//var_dump($systems);
$syst = new syst();
foreach ($systems as $new) {
  $syst->addSyst($new['name'],$new['x'],$new['y']);
  foreach($new['neighbors'] as $neighbor) {
    $syst->linkSyst($new['id'],$neighbor['id']);
  }
}
?>
<canvas id="demoCanvas" width="513" height="513"></canvas>
<script src="//code.createjs.com/easeljs-0.8.1.min.js"></script>

<script>
    var canvas, stage, exportRoot;
    var canvas = document.getElementById("demoCanvas");
    var context = canvas.getContext("2d");

    var systems = <?php echo json_encode($systems,JSON_NUMERIC_CHECK); ?>;
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

    console.table(lines);

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