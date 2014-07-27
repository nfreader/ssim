function initialize() {

    var starsTypeOptions = {
        getTileUrl: function() {
            return 'assets/img/starfield.png';
        },
        tileSize: new google.maps.Size(256, 256),
        maxZoom: 5,
        minZoom: 0,
        name: 'stars'
    };

    var starsMapType = new google.maps.ImageMapType(starsTypeOptions);

    var myLatlng = new google.maps.LatLng(0, 0);
    var mapOptions = {
        center: myLatlng,
        zoom: 13,
        streetViewControl: false,
        mapTypeControlOptions: {
            mapTypeIds: ['stars']
        },
        mapTypeControl: false,
        scrollWheel: false,
    };

    var map = new google.maps.Map(document.getElementById('map-canvas'),
        mapOptions);
    map.mapTypes.set('stars', starsMapType);
    map.setMapTypeId('stars');

    var infowindow = new google.maps.InfoWindow({
        content: 'Holding...'
    });

    $.ajax({
        type: "GET",
        url: "inc/api/syst.php?data=systjson",
        dataType: 'json',
        success: function(data) {
            $.each(data, function(i, s) {

                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(s.coord_x / 5, s.coord_y / 5),
                    map: map,
                    title: s.name,
                    icon: 'assets/img/icons/green-square.png',
                    html: 'System ' + s.name
                })
                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.setContent(this.html);
                    infowindow.open(map, this);
                });
            })
        }
    });
};
loadGmapScript();