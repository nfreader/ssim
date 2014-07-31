<?php

?>


<div class="leftbar">
</div>

<div class="center">
  <h1>Bluespace jump to  System <?php echo $pilot->pilot->system; ?></h1>
  <div id="Countdown"></div>
  <div id="ftlmsg" class="technical">Status: <span class="green pull-right">JUMPING <?php echo icon('circle-o-notch fa-spin'); ?></span></div>
</div>
<script src="assets/js/vendor/jquery.countdown.min.js"></script>   
<script>
  $(function() {
    var d = <?php echo $pilot->pilot->remaining; ?> +1;

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
        $.ajax({
            type: 'GET',
            url: 'view/action.php?action=jumpcomplete',
            success: function(data) {
                $('#game').empty().html(data);
                console.log(this.url);
            }
        })
    }
});
</script>