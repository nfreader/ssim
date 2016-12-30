</section>
    <footer>
      <div class="right"></div>
      <div class="left"></div>
    </footer>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="assets/js/vendor/jquery-1.11.1.min.js"><\/script>')</script>
        <!-- <script src="js/plugins.js"></script> -->
    <!-- <script src="assets/js/vendor/jquery.playsound.js"></script>-->
    <script src="assets/js/main.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.17.7/js/jquery.tablesorter.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/jquery-editable/js/jquery-editable-poshytip.min.js"></script>
    <script>
    setInterval(function() {
      var clock = document.querySelector('#clock');
      var date = new Date();
      var month = ('0'+(date.getUTCMonth()+1)).slice(-2);
      var days = ('0'+date.getUTCDate()+'').slice(-2);
      var seconds = ('0'+date.getUTCSeconds()+'').slice(-2);
      var minutes = ('0'+date.getUTCMinutes()+'').slice(-2);
      var hours = ('0'+date.getUTCHours()+'').slice(-2);
      var year = (date.getUTCFullYear()+<?php echo YEAR_OFFSET;?>);
      clock.textContent = hours+':'+minutes+':'+seconds+' '+days+'.'+month+'.'+year;
    }, 1000);
    $.fn.poshytip={defaults:null};
    $.fn.editable.defaults.mode = 'inline';
    $.fn.editableform.template = '<form class="form-inline editableform"><div class="control-group"><div class="editable-input"></div><div class="editable-buttons"></div><div class="editable-error-block"></div></div></form>';
    $.fn.editableform.buttons = '<button type="submit" class="editable-submit color green"><i class="fa fa-check"></i></button><button type="button" class="editable-cancel color red"><i class="fa fa-times"></i></button>';
    $.fn.editable.defaults.success = function(response, newValue) {
      notify(response);
      this.text = newValue;
      $(this).text(newValue);
      $(this).attr('style','');
    };
    $.fn.editable.defaults.error = function(response, newValue){
      notify(response);
      $(this).text(newValue);
      $(this).attr('style','');
    };

    </script>
  </body>
</html>
