<div class="form-group">
  <p>There is no validation on this form. Please be sure you know what you are doing</p>
    <h2 class='form-title'>Create a new ship</h2>
    <form class="vertical async" action='addShip' data-dest='admin/shipyard'>
      <div class="control-group">
        <label for='name'>Name</label>
        <input type='text' name='name' placeholder='Name' />
      </div>
      <div class="control-group">
        <label for='shipwright'>Shipwright</label>
        <input type='text' name='shipwright' placeholder='Shipwright' />
      </div>
      <div class="control-group">
        <label for='cost'>Cost</label>
        <input type='number' name='cost' min='1' placeholder='Cost' />
      </div>
      <?php echo $options; ?>
      <div class="control-group">
        <label for='starter' class="pull-right">Can be starter</label>
        <input type='checkbox' name='starter' value='1' />
      </div>
      <hr>
      <div class="control-group">
        <label for='mass'>Mass</label>
        <input type='number' name='mass' min='1' placeholder='Mass' />
      </div>
      <div class="control-group">
        <label for='accel'>Acceleration</label>
        <input type='number' name='accel' min='1' placeholder='Acceleration' />
      </div>
      <div class="control-group">
        <label for='turn'>Turn Speed</label>
        <input type='number' name='turn' min='1' placeholder='Turn Speed' />
      </div>
      <hr>
      <div class="control-group">
        <label for='fuel'>Fuel Tank (jumps)</label>
        <input type='number' name='fuel' min='1' placeholder='Fuel' />
      </div>
      <div class="control-group">
        <label for='cargo'>Cargo Space (tons)</label>
        <input type='number' name='cargo' min='1' placeholder='Cargo Space' />
      </div>
      <div class="control-group">
        <label for='expansion'>Expansion Space</label>
        <input type='number' name='expansion' min='1' placeholder='Expansion Space' />
      </div>
      <hr>
      <div class="control-group">
        <label for='armor'>Armor</label>
        <input type='number' name='armor' min='1' placeholder='Armor' />
      </div>
     <div class="control-group">
       <label for='shields'>Shields</label>
       <input type='number' name='shields' min='1' placeholder='Shields' />
     </div>
      <button>Add</button>
    </form>
  </div>
  <script>
  var radarChartData = {
    labels: ["Mass","Accel","Turn","Fuel","Cargo","Expand","Armor","Shields"],
    datasets: [
      {
        label: 'Under Construction Ship',
        fillColor: "rgba(180, 199, 255,.75)",
        strokeColor: "rgba(151,187,205,1)",
        data: [1,1,1,1,1,1,1,1]
      },
    ]
  };
  var ctx = document.getElementById("canvas").getContext('2d');
  var shipChart = new Chart(ctx).Radar(radarChartData, {
    responsive: true,
    showTooltips: false,
    pointDot: false,
    scaleFontColor: '#b4c7ff',
    angleLineColor : "rgba(255,255,255,.1)",
  });

  $("input[name='mass']").change(function(){
    shipChart.datasets[0].points[0].value = $(this).val();
    shipChart.update();
  });

  $("input[name='accel']").change(function(){
    shipChart.datasets[0].points[1].value = $(this).val();
    shipChart.update();
  });

  $("input[name='turn']").change(function(){
    shipChart.datasets[0].points[2].value = $(this).val();
    shipChart.update();
  });

  $("input[name='fuel']").change(function(){
    shipChart.datasets[0].points[3].value = $(this).val();
    shipChart.update();
  });

  $("input[name='cargo']").change(function(){
    shipChart.datasets[0].points[4].value = $(this).val();
    shipChart.update();
  });

  $("input[name='expansion']").change(function(){
    shipChart.datasets[0].points[5].value = $(this).val();
    shipChart.update();
  });

  $("input[name='armor']").change(function(){
    shipChart.datasets[0].points[6].value = $(this).val() * .1;
    shipChart.update();
  });

  $("input[name='shields']").change(function(){
    shipChart.datasets[0].points[7].value = $(this).val() * .1;
    shipChart.update();
  });
  </script>
