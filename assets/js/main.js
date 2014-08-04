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
      var formdata = $(this).serialize();
      var page = $(this).attr('page');
      $.ajax({
          type: "POST",
          url: action,
          data: formdata,
          success: function(retval) {
              $('#game').empty().load('view/' + page + '.php?msg=' + encodeURIComponent(retval));
              console.log('view/' + page + '.php?msg=' + retval);
          },
          error: function(retval) {}
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
      if (href === null) { //Default to home view if a destination isn't specified
          href = 'home';
      }
      $.ajax({
          type: 'GET',
          url: 'view/action.php?action=' + action,
          success: function(data) {
              //var msg = data;
              $('#game').empty().load('view/' + href + '.php?msg=' + encodeURIComponent(data));
              console.log(this.url);
              console.log(data);
          }
      })
      return false;
  });

  function jumpComplete(msg) {
      $('#game').empty().load('view/home.php?msg=' + encodeURIComponent(msg));
      console.log(this.url);
      console.log(msg);
  }

  $('body').delegate('.action', 'click', function() {
      event.preventDefault();
      var action = $(this).attr('action');
      $('#game').empty().load('route.php?action=' + action);
      console.log('route.php?action=' + action);
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

  $('body').delegate('.page', 'click', function() {
      event.preventDefault();
      var href = $(this).attr('href');
      var query = $(this).attr('data');
      $('#game').empty().load('view/' + href + '.php?' + query);
      console.log('view/' + href + '.php?' + query);
  })

   $('body').delegate('.notice', 'click', function(e) {
      var title = $(this).attr('title');
      var original = $(this).text();
      $('.helpText').show().text(title).css({
          position: "absolute",
          left: e.pageX,
          top: e.pageY
      });
  });

  $('body').delegate("[disabled='true']", 'click', function() {
      event.preventDefault();
      return false;
  })

   $('body').delegate('.dialog', "click", function() {
      $(this).fadeOut(250);
  });

  $('body').delegate('.rightbar #ship .right', 'click', function() {
      if ($(this).data('clicked')) {
          return;
      }
      var text = $(this).text();
      var form = "<input name='vesselName' id='newVessel' placeholder='" + text + "' />";
      $(this).html(form);
      $(this).data('clicked', true);
  });
  $('body').delegate('#newVessel', 'keypress', function(event) {
      if (event.which == 13) {
          event.preventDefault();
          var name = $(this).serialize();
          $.ajax({
              type: 'GET',
              url: 'view/action.php?action=renameVessel',
              data: name,
              success: function(data) {
                  $('#game').empty().load('view/home.php?msg=' + encodeURIComponent(data));
                  console.log(this.url);
                  console.log(data);
              }
          });
      }
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
      var len = $('script').filter(function() {
          return ($(this).attr('src') == 'https://maps.googleapis.com/maps/api/js?v=3.exp&callback=initialize');
      }).length;
      if (len === 0) {
          var script = document.createElement('script');
          script.type = 'text/javascript';
          script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&' +
              'callback=initialize';
          document.body.appendChild(script);
      }
  }

  $('.helpText').click(function() {
      $(this).hide();
  });
  $('.helpText').hide();

   // $(document).ready(function() {
   //     loadGmapScript();
   // })

  function showText(target, message, index, interval) {
      if (index < message.length) {
          $(target).append(message[index++]);
          setTimeout(function() {
              showText(target, message, index, interval);
          }, interval);
      }
  }