<?php

require_once('../inc/config.php');

?>


<div id="left">
  <ul class="options">
    <li><a href='about' class='page'>Back</a></li>
  </ul>
</div>

<div id="center" class="wide">
<h1>Galaxy Map</h1>
<canvas id="demoCanvas" width="513" height="513"></canvas>
</div>
<?php $syst = new syst();?>

<script src="//code.createjs.com/easeljs-0.8.1.min.js"></script>

<script>
    loadCreateJS();
    var canvas, stage, exportRoot;
    var canvas = document.getElementById("demoCanvas");
    var context = canvas.getContext("2d");

    var systems = <?php echo json_encode($syst->listSysts(),JSON_NUMERIC_CHECK); ?>;
    var jumps = <?php echo json_encode($syst->listConnections(),JSON_NUMERIC_CHECK); ?>;
    var stage = new createjs.Stage("demoCanvas");
    var w = 513;
    var h = 513;
    var gridColor = '#0E0E0E';
    var lineColor = '#444';
    var labelColor = '#FFF';
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


    var linksys = [];
    var newlinks = [];

    var gridline = new createjs.Shape();
    for (var x = 0.5; x < canvas.width; x += 8) {
      gridline.graphics.setStrokeStyle(1).beginStroke(gridColor).mt(x,0).lt(x,canvas.height);
      stage.addChild(gridline);
    }

    for (var y = 0.5; y < canvas.height ; y += 8) {
      gridline.graphics.setStrokeStyle(1).beginStroke(gridColor).mt(0,y).lt(canvas.width,y);
      stage.addChild(gridline);
    }
    stage.update();

    //This is the label that we use for hover()
    var output = new createjs.Text("", "14px Arial",labelColor);
    output.x = output.y = 10;
    stage.addChild(output);

    //This draws jump lines
    for (var j = jumps.length-1; j >= 0; j--){
      var line = new createjs.Shape();
      var ox = parseInt((jumps[j].originx * zoom) + (canvas.width/4));
      var oy = parseInt(((jumps[j].originy * zoom) - (canvas.height/4)) * -1);
      var dx = parseInt((jumps[j].destx * zoom) + (canvas.width/4));
      var dy = parseInt(((jumps[j].desty * zoom) - (canvas.height/4)) * -1);
      line.graphics.setStrokeStyle(1).beginStroke(lineColor).mt(ox,oy).lt(dx,dy);
      stage.addChild(line);
      var distance = new createjs.Text(jumps[j].distance,"10px Helvetica",labelColor);
      var mx = ((ox+dx)/2);
      var my = ((oy+dy)/2);
      distance.x = mx;
      distance.y = my;
      //stage.addChild(distance);
    }

    //This draws system dots
    for (var i = systems.length-1; i >= 0; i--) {
      var circle = new createjs.Shape();
      circle.data = systems[i];
      circle.id = systems[i].id;
      var x = parseInt((systems[i].coord_x * zoom) + (canvas.width/4));
      var y = parseInt(((systems[i].coord_y * zoom) - (canvas.height/4)) * -1);
      circle.data.coord_x = x;
      circle.data.coord_y = y;
      if (typeof(circle.data.jumps)=='string'){
      circle.data.jumps = circle.data.jumps.split(',');
      }
      systems[i] = circle.data;
      if (0 == circle.data.ports){
        circle.graphics.beginFill('black').drawCircle(x,y,5);
        circle.graphics.beginFill('white').drawCircle(x,y,4);
      } else {
        circle.graphics.beginFill(circle.data.color2).drawCircle(x,y,5);
        circle.graphics.beginFill(circle.data.color1).drawCircle(x,y,4);
        circle.graphics.beginFill(circle.data.color2).drawCircle(x,y,2);
      }
      var text = new createjs.Text(systems[i].name,"10px Helvetica",labelColor);
      text.x = x+7;
      text.y = y-10;
      stage.addChild(circle);
      //stage.addChild(text);
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
      output.x = event.target.data.coord_x + 7;
      output.y = event.target.data.coord_y - 10;
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
