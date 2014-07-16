  WebFontConfig = {
      google: {
          families: ['Share+Tech+Mono::latin']
      },
      active: function() {
          $('.loading').slideUp(250);
      }
  };
  (function() {
      var wf = document.createElement('script');
      wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
          '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
      wf.type = 'text/javascript';
      wf.async = 'false';
      var s = document.getElementsByTagName('script')[0];
      s.parentNode.insertBefore(wf, s);
  })();

  $('.async-form').submit(function() {
      var action = $(this).attr('action');
      var pass = $(this).attr('pass');
      var fail = $(this).attr('fail');
      $.ajax({
          type: "POST",
          url: "route.php?action=" + action,
          data: $(this).serialize(),
          success: function(data) {
              //console.log(data);
              $('body').load("view/" + pass + ".php");
          },
          error: function(data) {
              //console.log(data);
              $('body').load("view/" + fail + ".php");
          }
      })
      return false;
  })