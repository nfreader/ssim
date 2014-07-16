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

  $('body').delegate('.async-form', "submit", function() {
      event.preventDefault();
      var action = $(this).attr('action');
      var pass = $(this).attr('pass');
      var fail = $(this).attr('fail');
      var data = $(this).serialize();
      $.ajax({
          type: "POST",
          url: "route.php?action=" + action,
          data: data,
          success: function(retval) {
              //console.log(data);
              $('#game').load("view/" + pass + ".php");
              //console.log(data);
              //console.log(retval);
              //loadContent('footer', 'footerbar', 'footerbar');
          },
          error: function(retval) {
              //console.log(data);
              $('#game').load("view/" + fail + ".php");
              //console.log(data);
              //console.log(retval);
          }
      })
      return false;
  });

  $('body').delegate('.local-form', "submit", function() {
      event.preventDefault();
      var data = $(this).serialize();
      var action = $(this).attr('action');
      var dest = $(this).attr('dest');
      console.log(data);
      $.ajax({
          type: "GET",
          data: data,
          success: function(returnval) {
              loadPage(dest, 'action=' + action + '&' + data);
              //console.log(returnval);
          }
      })
      return false;
  });

  $('body').delegate('.action', 'click', function() {
      var action = $(this).attr('action');
      $('#game').load('route.php?action=' + action);
  })

  function loadContent(page, content, dest) {
      $(dest).load("view/" + page + ".php " + content + "");
      console.log("view/" + page + ".php " + content + " " + dest);
  }

  function loadPage(page, qs) {
      $('#game').load("view/" + page + ".php?" + qs);
      console.log("view/" + page + ".php?" + qs);
  }

  function footerInject(content) {
      $('.footerbar .pull-right').html(content);
  }

  $('body').delegate('.load', 'click', function() {
      event.preventDefault();
      var page = $(this).attr('page');
      if (page == undefined) {
          var page = $(this).attr('href');
      }
      var content = $(this).attr('content');
      var dest = $(this).attr('dest');
      var qs = $(this).attr('query');
      //$('#game').load("view/" + content + ".php");
      if (content == undefined && dest == undefined) {
          loadPage(page, qs);
      } else {
          loadContent(page, content, dest);
      }
  });

  $('body').delegate('.dialog', "click", function() {
      $(this).slideUp(250);
  });


   // $(document).ready(function() {
   //     $('#game').load('view/login.php');
   // });