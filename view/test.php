<?php

include '../inc/config.php';
$user  = new user();
$pilot = new pilot();
$syst = new syst($pilot->pilot->syst);
$spob = new spob($pilot->pilot->spob);
$systs = $syst->getSyst(null, true);
$jumps = $syst->getMapLines();
?>

<div class="leftbar">
<h1>Test Page</h1>
<ul class="options">
<li><a href='home' class='page'>Back</a></li>
</ul>
</div>

<div class="center">
<canvas id="c" style="background: white; float: right; clear: left; background: black;" width="800" height="800">
</canvas>

<script>
var canvas = document.getElementById("c");
var context = canvas.getContext("2d");
context.beginPath();
//context.clearRect(0, 0, canvasW, canvasH);
for (var x = 0.5; x < canvas.width; x += 10) {
  context.moveTo(x, 0);
  context.lineTo(x, canvas.height);
}

for (var y = 0.5; y < canvas.height ; y += 10) {
  context.moveTo(0, y);
  context.lineTo(canvas.width, y);
}

context.strokeStyle = "#111";
context.stroke();

var halfw = canvas.width/2;
var halfh = canvas.height/2;
var zoom = 40;

function sysDot(x, y, color, title, id) {
    var newx = (x * zoom) + halfw;
    var newy = ((y * zoom) - halfh) * -1;
        if (color == undefined) {
          var color = '#FFF';
        }
        context.beginPath();
        context.arc(newx, newy, 2, 0, 2 * Math.PI, false);
        context.strokeStyle = color;
        context.fillStyle = color;
        context.fill();
        context.stroke();
    context.beginPath();
        context.font = 'normal 10pt Helvetica';
        context.fillStyle = color;
    context.fillText(title+' ('+newx+','+newy+') ('+ id +')',newx+5,newy+5);
    console.log('('+newx+','+newy+')'+title+'',newx+5,newy+5);
}

function jumpLink(dest_x, dest_y, origin_x, origin_y) {
  var dx = (dest_x * zoom) + halfw;
  var dy = ((dest_y * zoom) - halfh) * -1;
  var ox = (origin_x * zoom) + halfw;
  var oy = ((origin_y * zoom) - halfh) * -1;
  context.beginPath();
  context.moveTo(ox,oy);
  context.lineTo(dx,dy);
  context.lineWidth = 1;
  context.strokeStyle = '#428bca';
  context.stroke();
}

var systems = <?php echo $systs; ?>;
var jumps = <?php echo $jumps; ?>;

for (i in jumps) {
  jumpLink(
    jumps[i].dest_x/2,
    jumps[i].dest_y/2,
    jumps[i].origin_x/2,
    jumps[i].origin_y/2
  );
}

for (i in systems) {
  sysDot(
    systems[i].coord_x/2,
    systems[i].coord_y/2,
    systems[i].color,
    systems[i].name,
    systems[i].id
  );
}
</script>
</div>

<?php
include 'rightbar.php';