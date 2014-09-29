<?php 
include '../../inc/config.php';
?>

<div class='fiftyfifty'>

<script>

  function notifyLevel(level){

    switch(level) {
      case 'normal':
      default:
      return 'green';

      case 'emergency':
      return 'red';
    }

  }

  function isJSON(json) {
    try {
      JSON.parse(json);
    } catch(e) {
      return false;
    }
    return true;
  }

  function notify(data) {
    if (isJSON(data)) {
      $.each(JSON.parse(data), function(n, m) {
        var color = notifyLevel(m.level);
        var html = "<li class='color " + color + "'>" + m.message +"</li>";
        $('.headerbar .msglist').append(html);
        console.log(m.message + ' : ' + m.level);
      });
    } else {
      var html = "<li class='color green'>" + data + "</li>";
      $('.headerbar .msglist').append(html); 
      console.log(data + ': normal');     
    }
  }

  var msgs = 
'[{"message":"94 cr. have been added to your account.","level":"normal"},{"message":"Sold 2 tons of Food for 94 credits.","level":"emergency"}]';

  notify(msgs);

  //notify('OMGWFTBBQ');
  //notify("You lifted off");

$('.headerbar .msglist .color').click(function(){
  $(this).addClass('notify-read');
});

</script>

<ul class="options">

</ul>

</div>
