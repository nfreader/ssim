<script>$('body').addClass('bluespace');</script>
<div id="center" class="wide pull-left">
  <h1>Bluespace jump to  System <?php echo $pilot->systname; ?></h1>
  <div class="countdown">
    <div id="timer"></div>
  </div>
  <div id="ftlmsg" class="technical">
    ETA: <?php echo date(SSIM_DATE,strtotime($pilot->jumpeta));?>
    <span class="green right">
      Jump in progress <?php echo icon('sun-o','fa-spin'); ?>
    </span>
  </div>
</div>
<script src="assets/js/lib/jquery.plugin.min.js"></script>  
<script src="assets/js/lib/jquery.countdown.min.js"></script>   
<script>
  $(function() {
    $("#timer").countdown({
        until: <?php echo $pilot->remaining; ?>,
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
      $('#spinner').addClass('fa-spin');
      $('body').removeClass('bluespace');
      var msg = "msg=["+encodeURIComponent('<?php echo returnSuccess("Bluespace jump to System $pilot->systname complete"); ?>')+"]";
      loadView('home',msg);
    }
});

</script>