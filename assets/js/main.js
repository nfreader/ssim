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

function stripHTML(dirtyString) {
    var container = document.createElement('div');
    container.innerHTML = dirtyString;
    return container.textContent || container.innerText;
}


function loadPage(page, qs) {
  qs = typeof qs !== 'undefined' ? qs : '';
  $('#game').empty().load("view/" + page + ".php?" + qs);
  console.log("view/" + page + ".php?" + stripHTML(qs));
}

function loadView(view,qs) {
  qs = typeof qs !== 'undefined' ? qs : '';
  $("#game").empty().load("view/"+ view +".php?"+qs);
  console.log("Loaded view: view/"+ view +".php?"+qs);
}

$('body').delegate('.async','submit',function(){
  event.preventDefault();
  console.log(this);
  var action = $(this).attr('action');
  var data = $(this).serialize();
  var dest = $(this).attr('data-dest');
  console.log(data);
  $.ajax({
    type: "POST",
    url: "view/action.php?action="+action,
    data: data,
    success: function(retval) {
      loadView(dest,"msg="+encodeURIComponent(retval));
      console.log(retval);
    },
  });
});

function notifyLevel(level){

  switch(level) {
    case 'normal':
    default:
    return 'green';

    case 'warn':
    return 'orange';

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
  console.log(data);
  if (isJSON(data)) {
    $.each($.parseJSON(data), function(n, m) {
      var color = notifyLevel(m.level);
      var html = "<li class='color " + color + " notify-unread'>" + m.message +"</li>";
      $('.headerbar .msglist').append(html);
      console.log(m.message + ' : ' + m.level);
    });
  } else {
    var html = "<li class='color green notify-unread'>" + data + "</li>";
    $('.headerbar .msglist').append(html); 
    console.log(data + ': normal');     
  }
}

$('body').delegate('.admin-form', 'submit', function() {
    event.preventDefault();
    var action = $(this).attr('action');
    var formdata = $(this).serialize();
    var page = $(this).attr('page');
    $.ajax({
        type: "POST",
        url: 'view/admin/action.php?action=' + action,
        data: formdata,
        success: function(data) {
            console.log('view/admin/action.php?action=' + action);
            $('#game').empty().load('view/' + page + '.php?msg=' + encodeURIComponent(data));
            console.log('view/' + page + '.php?msg=' + data);
        }
    })
})

 $('body').delegate('.local-action', 'click', function() {
    event.preventDefault();
    var action = $(this).attr('action');
    var href = $(this).attr('href');
    if (href === null) {
      //Default to home view if a destination isn't specified
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

  $('body').delegate('.local-view', 'click', function() {
     event.preventDefault();
     var view = $(this).attr('view');
     var href = $(this).attr('href');
     if (href === null) {
       //Default to home view if a destination isn't specified
       href = 'home';
     }
     $.ajax({
         type: 'GET',
         url: 'view/' + href + '.php?view=' + view,
         success: function(data) {
             //var msg = data;
             $('#game').empty().load('view/' + href + '.php?view=' + view);
             console.log(this.url);
             //console.log(data);
         }
     })
     return false;
 });

$('body').delegate('.admin-action', 'click', function() {
    event.preventDefault();
    var action = $(this).attr('action');
    var href = $(this).attr('href');
    if (href === null) { //Default to home view if a destination isn't specified
        href = 'home';
    }
    $.ajax({
        type: 'GET',
        url: 'view/admin/action.php?action=' + action,
        success: function(data) {
            //var msg = data;
            $('#game').empty().load('view/admin/' + href + '.php?msg=' + encodeURIComponent(data));
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
});

$('body').delegate('.headerbar .msglist .color','click', function(){
  $(this).removeClass('notify-unread');
  $(this).addClass('notify-read');
});


function loadContent(page, content, dest) {
    $(dest).empty().load("view/" + page + ".php " + content + "");
    console.log("view/" + page + ".php " + content + " " + dest);
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

function systemScan() {
    $.ajax({
        type: "GET",
        url: "inc/api/pilot.php?data=scan",
        dataType: 'json',
        success: function(data) {
            console.log(data);
            $('.contacts .scanresults').text(data);
            $('.contacts h1 .pull-right').text('CONTACT');
            //$.playSound('assets/sound/interface/powerUp2');
        },
        error: function(data) {
            console.log('No contact');
            $('.contacts .scanresults').html('<div class="pull-center">≪ No contact ≫</div>');
            $('.contacts h1 .pull-right').text('NO CONTACT');
        }
    });
}


 // function ping() {
 //     $('.footerbar').load('view/ping.php');
 // }
 // setInterval(ping, 10000);