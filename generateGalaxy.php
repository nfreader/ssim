<?php 

$i = 0;
$rawsyst = array();
while($i < 10) {
  $i++;
  $rawsyst[] = array(
    'x'=>floor(rand(-100,100)),
    'y'=>floor(rand(-100,100)),
    'name'=>"Sys-$i"
  );
}
$systems = array();
foreach ($rawsyst as $sys) {
  $distances = array();
  foreach ($rawsyst as $sub) {
    if ($sub['name'] != $sys['name']) {
      $d = floor(sqrt((($sub['x'] - $sys['x']) ** 2) + (($sub['y'] - $sys['y']) ** 2)));
      $distances[] = array(
        'name'=>$sub['name'],
        'distance'=>$d,
        'x'=>$sub['x'],
        'y'=>$sub['y']
      );
      //echo "Distance from ".$sys['name']." to ".$sub['name'].": $d<br>";
    }
  }
  $dist = array();
  $name = array();
  $x = array();
  $y = array();
  foreach ($distances as $key => $value) {
    $dist[$key] = $value['distance'];
    $name[$key] = $value['name'];
    $x[$key] = $value['x'];
    $y[$key] = $value['y'];
  }
  array_multisort($dist,SORT_ASC,$distances);

  $distances = array_slice($distances,0,3);
  //echo "<strong>System ".$sys['name']."'s nearest neighbors:</strong><br>";
  //foreach ($distances as $key => $value) {
  //  echo $value['name']."<br>";
  //}
  $sys['neighbors'] = $distances;
  $systems[] = $sys;
}
//var_dump($systems);
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
    var zoom = 2;

    var gridline = new createjs.Shape();
    for (var x = 0.5; x < canvas.width; x += 8) {
      gridline.graphics.setStrokeStyle(1).beginStroke('#eee').mt(x,0).lt(x,canvas.height);
      stage.addChild(gridline);
    }
    
    for (var y = 0.5; y < canvas.height ; y += 8) {
      gridline.graphics.setStrokeStyle(1).beginStroke('#eee').mt(0,y).lt(canvas.width,y);
      stage.addChild(gridline);
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
      $('#sysname').text('Linked '+data[0].name+ ' to '+data[1].name+'!');
     }
</script>