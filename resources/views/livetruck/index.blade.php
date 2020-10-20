@extends('app')


@section('content')

<div class="col-lg-10">
<div class="panel panel-info">
    <div class="panel-heading">
        <h1 class="panel-title">Trucks Live Tracking</h1>
    </div>
    <div class="panel-body">
      <div id="my-map" style="width: 100%; height:600px; background:#DDD;"></div>
    </div>
</div>
</div>


<script type="text/javascript">
//var directionsDisplay;
//var directionsService = new google.maps.DirectionsService();
/*

var map;
var defaultLoc = {lat: 42.113332, lng: -85.567900}//{lat: 42.113332, lng: -85.567900}
var markers = [];

function initialize(){
  map = new google.maps.Map(document.getElementById('my-map'), {
	center: defaultLoc,
	zoom: 11,
	mapTypeId: google.maps.MapTypeId.ROADMAP
  });
  //var trafficLayer = new google.maps.TrafficLayer();
  //trafficLayer.setMap(map);

  markers = [
    @foreach($drivers as $k => $v)
    {"driver_name":"{{$v['driver_name']}}", "lat":42.113332, "long":-85.567900,"driver_id":"{{$v['driver_id']}}"},
    @endforeach
  ];

  placeMarker(markers);

}


function placeMarker(location) {

  if (location == null) {
    for( i = 0; i < markers.length; i++){
      console.log(markers[i]);
      marker = markers[i];
      marker = new google.maps.Marker({
          position: position,
          map: {lat:markers[i]['lat'], lng:markers[i]['long']},
          animation: google.maps.Animation.DROP,
          title: markers[i]['driver_name'],
          icon: 'http://feedstest.carrierinsite.com/images/truck-icon-map.png'
      });
      marker.setMap(null)
    }
  } else {

      // Loop through our array of markers & place each one on the map
      for( i = 0; i < location.length; i++ ) {

          var position = new google.maps.LatLng(location[i]['lat'], location[i]['long']);
          //bounds.extend(position);
          marker = new google.maps.Marker({
              position: position,
              map: map,
              animation: google.maps.Animation.DROP,
              title: location[i]['driver_name'],
              icon: 'http://feedstest.carrierinsite.com/images/truck-icon-map.png'
          });



          window.setTimeout(function() {

          }, i * 1000);


      }

  }


  var position = new google.maps.LatLng(42.113332,-85.567900);

  var marker = new google.maps.Marker({
      position: position,
      map: map,
      title: "H and H Feed & Grain",
      icon: 'http://feedstest.carrierinsite.com/images/logo-map.png'
  });

}

// for marker set position
/*
function changeMarkerPosition(marker) {
    var latlng = new google.maps.LatLng(-24.397, 140.644);
    marker.setPosition(latlng);
}
*/

// Sets the map on all markers in the array.
//function clearMarkers(map) {
//  for (var i = 0; i < markers.length; i++) {
//    markers[i] = null;
//  }
//}

var map;
var markers = [];
var driver_markers = [];
var labels;
var markerCluster;

function initialize(){

  var defaultLoc = [
    @foreach($drivers as $k => $v)
    {lat:42.113332, lng:-85.567900},
    @endforeach
  ];

  driver_markers = [
    @foreach($drivers as $k => $v)
    {"driver_name":"{{$v['driver_name']}}", "lat":42.113332, "long":-85.567900,"driver_id":"{{$v['driver_id']}}"},
    @endforeach
  ];

  driver_markers = [];

  console.log(driver_markers);

  map = new google.maps.Map(document.getElementById('my-map'), {
    zoom: 12,
    center: {lat: 42.113332, lng: -85.567900},
    mapTypeId: 'terrain'
  });

  var trafficLayer = new google.maps.TrafficLayer();
  trafficLayer.setMap(map);

  // Create an array of alphabetical characters used to label the markers.
  labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

  // Add a marker clusterer to manage the markers.
  markerCluster = new MarkerClusterer(map, markers,{imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});

  // Adds a marker at the center of the map.
  addMarker(defaultLoc,driver_markers);
}

// Adds a marker to the map and push to the array.
function addMarker(location,driver_markers) {


  if(driver_markers.length != 0){
    for (var i = 0; i < location.length; i++) {

      deleteMarker(driver_markers[i]['driver_id'])

      markers.push(new google.maps.Marker({
              driver_id: driver_markers[i]['driver_id'],
              position: location[i],
              map: map,
              //animation: google.maps.Animation.DROP,
              //label: labels[i % labels.length],
              title: driver_markers[i]['driver_name'],
              icon: app_url+'/images/truck-icon-map.png'
            }));

      if(driver_markers[i]['status'] == 'completed' || driver_markers[i]['status'] == 'deleted' ){
        deleteMarker(driver_markers[i]['driver_id']);
      }

    }
  }

  var marker = new google.maps.Marker({
     position: {lat: 42.113332, lng: -85.567900},
     map: map,
     title: "H and H Feed & Grain",
     icon:  app_url+'/images/logo-map.png'
  });


}

// delete specific marker
function deleteMarker(id){

    for (var i = 0; i < markers.length; i++) {
      if(markers[i]['driver_id'] == id){
        markers[i].setMap(null);
      }
    }
    //if(marker != null){
      //marker.setMap(null);
    //}
}

// Sets the map on all markers in the array.
function setMapOnAll(map) {
   for (var i = 0; i < markers.length; i++) {
     markers[i].setMap(map);
   }
}

// Removes the markers from the map, but keeps them in the array.
function clearMarkers() {
   setMapOnAll(null);
}

// Shows any markers currently in the array.
function showMarkers() {
   setMapOnAll(map);
}

// Deletes all markers in the array by removing references to them.
function deleteMarkers() {
   clearMarkers();
   markers = [];
}


try {

  live_map_socket = new WebSocket(live_map_host);

  //Manages the open event within your client code
  live_map_socket.onopen = function () {
     // print('Click a person to message...');
    //$(".input-message").focus();
    console.log("live map connection open");
    return;
  };

  //Manages the message event within your client code
  live_map_socket.onmessage = function (msg) {

    //console.log(msg.data)
    var message = JSON.parse(msg.data);
    //clearMarkers();
    var locations = [];
    var location_adjuster = 1;
    driver_markers = [];
    driver_markers.push({driver_name: message['driver_name'], lat: message['lat'], long: message['long'], driver_id: message['driver_id'], status: message['status']});
    locations.push({lat:parseFloat(message['lat']),lng:parseFloat(message['long'])});
    console.log(driver_markers);
    addMarker(locations,driver_markers);

    return;
  };

  //Manages the close event within your client code
  live_map_socket.onclose = function () {
    console.log('Connection Closed');
    //return;
  };


} catch (e) {
  console.log(e);
  //alert("Chat feature not supported by this browser, please use Google Chrome...");
}



setTimeout(function(){
  //var message = {'driver_name':'Testing Driver','lat':41.87843560,'long':-85.51176300,'driver_id':30};
  //live_map_socket.send(JSON.stringify(message));
  //console.log(message)
  /*
  $.each(markers, function(key,val){
    if(message[0]['driver_id'] == val['driver_id']){
      //delete markers[key];
      //markers.push(message[0]);
      markers[key]['driver_name'] = message[0]['driver_name'];
      markers[key]['lat'] = message[0]['lat'];
      markers[key]['long'] = message[0]['long'];
      markers[key]['driver_id'] = message[0]['driver_id'];

    }
    if(val['driver_id'] == null){
      delete markers[key];
    }
  });
  */
//  console.log(markers);
  //placeMarker(markers);

},2000);

</script>
<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>

@stop
