</div>
    <div class="footerbar">
      <div class="pull-right"></div>
      <div class="pull-left"></div>
    </div>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="assets/js/vendor/jquery-1.11.1.min.js"><\/script>')</script>
        <!-- <script src="js/plugins.js"></script> -->
    <!-- <script src="assets/js/vendor/jquery.playsound.js"></script>-->
    <script src="assets/js/main.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.17.7/js/jquery.tablesorter.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/jquery-editable/js/jquery-editable-poshytip.min.js"></script>
    <script>
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
    };

    </script>
  </body>
</html>
