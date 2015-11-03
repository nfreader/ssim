<?php

require_once('../inc/config.php');

?>

<div class="center wide">
<h1>Galaxy Editor</h1>
<h3>Click on two systems to link them</h3>
<canvas id="demoCanvas" width="513" height="513"></canvas>
</div>
<?php $syst = new syst();?>

<div class="rightbar">
<h1>JSON output</h1>
<pre class='newlinkjson'>&nbsp;</pre>
<p id="sysname">&nbsp;</p>
</div>

<script src="//code.createjs.com/easeljs-0.8.1.min.js"></script>

<script>
    var canvas, stage, exportRoot;
    var canvas = document.getElementById("demoCanvas");
    var context = canvas.getContext("2d");

    var systems = <?php echo json_encode($syst->listSysts(),JSON_NUMERIC_CHECK); ?>;
    var jumps = <?php echo json_encode($syst->listConnections(),JSON_NUMERIC_CHECK); ?>;
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
    var zoom = 10;

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

    output = new createjs.Text("", "14px Arial");
    output.x = output.y = 10;
    stage.addChild(output);

    for (var j = jumps.length-1; j >= 0; j--){
      var line = new createjs.Shape();
      var ox = parseInt((jumps[j].originx * zoom) + (canvas.width/4));
      var oy = parseInt(((jumps[j].originy * zoom) - (canvas.height/4)) * -1);
      var dx = parseInt((jumps[j].destx * zoom) + (canvas.width/4));
      var dy = parseInt(((jumps[j].desty * zoom) - (canvas.height/4)) * -1);
      line.graphics.setStrokeStyle(1).beginStroke('#CCC').mt(ox,oy).lt(dx,dy);
      stage.addChild(line);
    }

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
      circle.graphics.beginFill(circle.data.color2).drawCircle(x,y,5);
      circle.graphics.beginFill(circle.data.color1).drawCircle(x,y,4);
      circle.graphics.beginFill(circle.data.color2).drawCircle(x,y,2);
      var text = new createjs.Text(systems[i].name,"10px Helvetica",'#000');
      text.x = x+7;
      text.y = y-10;
      stage.addChild(circle);
      //stage.addChild(text);
      circle.on("mouseover", hover);
      circle.on("mouseout", unhover);
      console.log(systems[i]);
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

</script>