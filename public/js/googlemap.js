

async function getGeoCode (address, key)
{
    address = encodeURI(address);
    let url = " https://maps.googleapis.com/maps/api/geocode/json?address="+address+"&key="+key
}

function getRoadData(){
    var consignee_locations = [];
    var shipper_locations = [];

    Array.from(document.getElementsByClassName("shippers_select2")).forEach(function (currentValue, index, arr) {
        new google.maps.places.Autocomplete(currentValue);
        if (currentValue.value == "")
            return;
        consignee_locations[index] = currentValue.value;
    });

    Array.from(document.getElementsByClassName("consignees_select2")).forEach(function (currentValue, index, arr) {
        new google.maps.places.Autocomplete(currentValue);
        if (currentValue.value == "")
            return;
        shipper_locations[index] = currentValue.value;
    });

    var current_location = document.getElementById('current_address').value;
    if (current_location == "")
        return;

    var current_location_lat = document.getElementById('current_address_lat').value;
    var current_location_lng = document.getElementById('current_address_lng')

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'POST',
        data: {
            shipper_locations: shipper_locations,
            consignee_locations: consignee_locations,
            current_location: current_location,
            current_location_lng:current_location_lng,
            current_location_lat:current_location_lat,
        },
        url: '{{route('road-data')}}',
        dataType: "Json",
        success: function (result) {
            console.log(result);
            if (result.status == "success") {
                
            }
        }, error: function (result) {
        }
    });
}

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
      zoom: 14,
      center: {
        lat: 28.6247,
        lng: 77.3731
      },
      disableDefaultUI: true,
    });
  
    var directionsService = new google.maps.DirectionsService;
    var directionsDisplay = new google.maps.DirectionsRenderer({
      suppressInfoWindows: true,
      suppressMarkers: true
    });
  
    var response = {
      "abcd": {
        "points": [
          [28.5435, 77.2051, "2016-07-17 08:02:18 - 28.543500 - 77.205100"],
          [28.5313, 77.249, "2016-07-17 12:32:38 - 28.531300 - 77.249000"],
          [28.5279, 77.2462, "2016-07-17 12:35:02 - 28.527900 - 77.246200"],
          [28.5328, 77.2391, "2016-07-17 12:36:42 - 28.532800 - 77.239100"],
          [28.5407, 77.24, "2016-07-17 12:38:23 - 28.540700 - 77.240000"],
          [28.543, 77.2139, "2016-07-17 12:42:36 - 28.543000 - 77.213900"],
          [28.5429, 77.2095, "2016-07-17 12:43:26 - 28.542900 - 77.209500"],
          [28.5332, 77.2071, "2016-07-17 12:48:30 - 28.533200 - 77.207100"],
          [28.531, 77.2113, "2016-07-17 12:52:43 - 28.531000 - 77.211300"],
          [28.5315, 77.2077, "2016-07-17 13:03:15 - 28.531500 - 77.207700"],
          [28.5433, 77.2117, "2016-07-17 13:08:27 - 28.543300 - 77.211700"],
          [28.5412, 77.2374, "2016-07-17 13:12:50 - 28.541200 - 77.237400"],
          [28.5311, 77.2482, "2016-07-17 17:49:02 - 28.531100 - 77.248200"]
        ]
      },
      "ret-abcd": {
        "points": [
          [28.5294, 77.2502, "2016-07-18 07:29:38 - 28.529400 - 77.250200"],
          [28.5541, 77.2637, "2016-07-18 07:43:59 - 28.554100 - 77.263700"],
          [28.5635, 77.2648, "2016-07-18 07:46:08 - 28.563500 - 77.264800"],
          [28.5721, 77.2579, "2016-07-18 07:48:07 - 28.572100 - 77.257900"],
          [28.5746, 77.2608, "2016-07-18 07:49:14 - 28.574600 - 77.260800"],
          [28.5787, 77.2813, "2016-07-18 07:51:16 - 28.578700 - 77.281300"],
          [28.5731, 77.3086, "2016-07-18 07:52:39 - 28.573100 - 77.308600"],
          [28.5902, 77.336, "2016-07-18 07:58:00 - 28.590200 - 77.336000"],
          [28.6252, 77.3735, "2016-07-18 08:49:47 - 28.625200 - 77.373500"]
        ]
      }
    };
  
    var timeout = 100;
    var m = 0;
    var cnt = 0;
    var markers = [];
    var combinedResults;
    var directionsResultsReturned = 0;
    var linecolors = ['red', 'blue', 'green', 'yellow'];
    var colorIdx = 0;
    var dd = [];
  
    for (key in response) {
      if (response[key].points.length > 0) {
        var blocks = [];
        var k = 0;
        for (var i = 0; i < response[key].points.length; i++) {
          if (i != 0 && i % 10 == 0) {
            k++;
          }
          //console.log(k);
          if (typeof blocks[k] == 'undefined') {
            blocks[k] = [];
          }
  
          blocks[k].push(response[key].points[i]);
        }
  
        ds = new google.maps.DirectionsService;
  
        for (i = 0; i < blocks.length; i++) {
  
          waypts = [];
          markers.push([blocks[i][0][0], blocks[i][0][1], blocks[i][0][2]]);
          for (var j = 1; j < blocks[i].length - 1; j++) {
            waypts.push({
              location: blocks[i][j][0] + ',' + blocks[i][j][1],
              stopover: true
            });
            //var myLatlng = new google.maps.LatLng(blocks[i][j][0],blocks[i][j][1]);
            markers.push([blocks[i][j][0], blocks[i][j][1], blocks[i][j][2]]);
  
          }
          markers.push([blocks[i][blocks[i].length - 1][0], blocks[i][blocks[i].length - 1][1], blocks[i][blocks[i].length - 1][2]]);
          //data.start[0]+','+data.start[1],
          //ds[m].route({   
          try {
            ds.route({
                'origin': blocks[i][0][0] + ',' + blocks[i][0][1],
                'destination': blocks[i][blocks[i].length - 1][0] + ',' + blocks[i][blocks[i].length - 1][1],
                'waypoints': waypts,
                'travelMode': 'DRIVING'
              },
              function(directions, status) {
                dd.push(new google.maps.DirectionsRenderer({
                  suppressInfoWindows: true,
                  suppressMarkers: true,
                  polylineOptions: {
                    strokeColor: linecolors[colorIdx++ % 3]
                  },
                  map: map
                }));
    
                if (status == google.maps.DirectionsStatus.OK) {
                  dd[dd.length - 1].setDirections(directions);
                }
              }
            );
          }catch(error)
          {
            console.log(error.message)
          }
          
  
        }
      }
  
      for (h = 0; h < markers.length; h++) {
        createMapMarker(map, new google.maps.LatLng(markers[h][0], markers[h][1]), markers[h][2], "", "");
      }
      cnt++;
  
    }
  }
  
  function createMapMarker(map, latlng, label, html, sign) {
    var marker = new google.maps.Marker({
      position: latlng,
      map: map,
      icon: "http://www.google.com/mapfiles/marker" + sign + ".png",
      title: label,
    });
  
    marker.myname = label;
  
  
    return marker;
  }
  google.maps.event.addDomListener(window, "load", initMap);