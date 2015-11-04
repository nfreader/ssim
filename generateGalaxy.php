<?php 

$i = 0;
$rawsyst = '';
while($i < 5) {
  $i++;
  $rawsyst[] = array(
    'x'=>floor(rand(-100,100)),
    'y'=>floor(rand(-100,100)),
    'name'=>$i
  );
}

foreach ($rawsyst as $sys) {
  $distances = array();
  foreach ($rawsyst as $subsys) {
    echo $subsys['name'];
      $d = floor(sqrt((($subsys['x'] - $sys['x']) ** 2) + (($subsys['y'] - $sys['y']) ** 2)));
      $distances[$subsys['name']] = array(
        $d,
        $subsys['name']);
      var_dump($distances);
  }
  $systems[] = $sys;
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
    for (var i = systems.length-1; i >= 0; i--) {
      var circle = new createjs.Shape();
      circle.data = systems[i];
      var x = parseInt((systems[i].x * zoom) + (canvas.width/4));
      var y = parseInt(((systems[i].y * zoom) - (canvas.height/4)) * -1);
      circle.data.x = x;
      circle.data.y = y;
      systems[i] = circle.data;
      circle.graphics.beginFill('#000').drawCircle(x,y,4);
      stage.addChild(circle);
      circle.on("mouseover", hover);
      circle.on("mouseout", unhover);
      console.log(systems[i]);
      circle.on("click", sysclick);
    }

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