function initialize() {

    var myLatlng = new google.maps.LatLng(-23.2433662, -47.3205737);
    var mapOptions = {
        zoom: 14,
        center: myLatlng,
        panControl: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        mapTypeControlOptions: {
            mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
        }
    }

    var contentString = '<h5>TSA Do Brasil</h5>' +
        '<p>Av. Sete Quedas, 838 - Vila Padre Bento, Itu - SP, 13313-006</p>'
    var infowindow = new google.maps.InfoWindow({
        content: contentString,
        maxWidth: 700
    });


    var map = new google.maps.Map(document.getElementById("map"), mapOptions);


    var image = 'tsa/assets/site/images/pin-tsa.png';
    var marcadorPersonalizado = new google.maps.Marker({
        position: myLatlng,
        map: map,
        icon: image,
        title: 'TSA do Brasil',
        animation: google.maps.Animation.DROP
    });


    google.maps.event.addListener(marcadorPersonalizado, 'click', function () {
        infowindow.open(map, marcadorPersonalizado);
    });


    var styles = [{
            stylers: [{
                    hue: "#ccc"
                },
                {
                    saturation: -100
                },
                {
                    lightness: 10
                },
                {
                    gamma: 0
                }
            ]
        },
        {
            featureType: "road",
            elementType: "geometry",
            stylers: [{
                    lightness: 100
                },
                {
                    visibility: "simplified"
                }
            ]
        },
        {
            featureType: "road",
            elementType: "labels"
        }
    ];

    var styledMap = new google.maps.StyledMapType(styles, {
        name: "Mapa Style"
    });

    map.mapTypes.set('map_style', styledMap);
    map.setMapTypeId('map_style');

}

function loadScript() {
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.src =
        "https://maps.googleapis.com/maps/api/js?key=AIzaSyDmjkEri5pEh98mYRj8Jq9b4XqK3LoU_6w&sensor=true&callback=initialize";
    document.body.appendChild(script);
}

window.onload = loadScript;