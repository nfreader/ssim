<div class="center wide">
  <h1>Bluespace jump to  System <?php echo $pilot->systname; ?></h1>
  <div id="Countdown"></div>
  <div id="ftlmsg" class="technical">Status: <span class="green pull-right">JUMPING <?php echo icon('power-off','fa-spin'); ?></span></div>
</div>
<script src="assets/js/lib/jquery.plugin.min.js"></script>  
<script src="assets/js/lib/jquery.countdown.min.js"></script>   
<script>
  $(function() {
    var d = <?php echo $pilot->remaining; ?> +1;
    console.log(d);
    $("#Countdown").countdown({
        until: d,
        compact: true,
        format: 'H:M:S',
        onExpiry: expiry,
        onTick: ftlMsg
    });

    function ftlMsg(periods) {
      if ($.countdown.periodsToSeconds(periods) === 6) {
        $('#ftlmsg').html('Preparing to exit bluespace...');
      }
      if ($.countdown.periodsToSeconds(periods) === 3) {
        $('#ftlmsg').html(' Brace for termination shock...');
      }
      if ($.countdown.periodsToSeconds(periods) === 1) {
        $('#ftlmsg').html('<span class="pull-center">Exiting bluespace!</span>');
      }
    }

    function expiry() {
      $('#game').toggleClass('bluespace');
      var msg = "msg=["+encodeURIComponent('<?php echo returnSuccess("Bluespace jump to System $pilot->systname complete"); ?>')+"]";
      loadView('home',msg);
    }
});
$('#game').toggleClass('bluespace');
</script>