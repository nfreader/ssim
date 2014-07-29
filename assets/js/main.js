  WebFontConfig = {
      google: {
          families: ['Share+Tech+Mono::latin']
      },
      timeout: 1000,
      active: function() {
          $('.loading').slideUp(250);
      },
      inactive: function() {
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

  $('body').delegate('.login-form', "submit", function() {
      event.preventDefault();
      var formcontents = $(this).serialize();
      $.ajax({
          type: "POST",
          url: "index.php?action=login",
          data: formcontents,
          success: function(data) {
              //console.log(data);
              $('#game').html(data);
              console.log(data);
              //console.log(retval);
              //loadContent('footer', 'footerbar', 'footerbar');
          }
      })
      console.log(formcontents);
      return false;
  });

   //Long form parser
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
              $('#game').html(retval);
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

   //Form parser for local pages
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


  $('body').delegate('.local-action', 'click', function() {
      event.preventDefault();
      var action = $(this).attr('action');
      var href = $(this).attr('href');
      $.ajax({
          type: 'GET',
          url: 'view/action.php?action=' + action,
          success: function(data) {
              $('#game').empty().html(data);
              console.log(this.url);
          }
      })
  })

   $('body').delegate('.action', 'click', function() {
      event.preventDefault();
      var action = $(this).attr('action');
      $('#game').empty().load('index.php?action=' + action);
      console.log('index.php?action=' + action);
  })

  function loadContent(page, content, dest) {
      $(dest).empty().load("view/" + page + ".php " + content + "");
      console.log("view/" + page + ".php " + content + " " + dest);
  }

  function loadPage(page, qs) {
      $('#game').empty().load("view/" + page + ".php?" + qs);
      console.log("view/" + page + ".php?" + qs);
  }

  function footerInject(content) {
      $('.footerbar .pull-right').html(content);
  }

  function returnMsg(content) {
      $('#game .center').prepend(content);
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

  $('body').delegate("[disabled='true']", 'click', function() {
      event.preventDefault();
  })

   $('body').delegate('.dialog', "click", function() {
      $(this).fadeOut(250);
  });

   // function time() {
   //     //Mon, 21 Jul 2192 20:51:27
   //     var now = new Date();
   //     var day = new.getDate();
   //     var

   // }

   // $(document).ready(function() {
   //     $('#game').load('view/login.php');
   // });

  function loadGmapScript() {
      var script = document.createElement('script');
      script.type = 'text/javascript';
      script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&' +
          'callback=initialize';
      document.body.appendChild(script);
  }

   // $(document).ready(function() {
   //     loadPage('home');
   // })