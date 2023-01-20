<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <style type="text/css">
        #map {
          height: 400px;
        }
    </style>
</head>
    
<body>
    <div id="map"></div>
<script>
  function initMap() {
    var directionsService = new google.maps.DirectionsService;
    var directionsRenderer = new google.maps.DirectionsRenderer;
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 7,
      center: {lat: 41.85, lng: -87.65}
    });
    directionsRenderer.setMap(map);
    var origin = "Chicago";
    var destination = "Detroit";
    calculateAndDisplayRoute(directionsService, directionsRenderer, origin, destination);
  }

var originLongitude = 0, originLatitude = 0, destinationLongitude = 0, destinationLatitude = 0;
function calculateAndDisplayRoute(directionsService, directionsRenderer, originadd, destinationadd) {
    var geocoder;
    geocoder = new google.maps.Geocoder();
    geocoder.geocode({'address': originadd}, function(results, status) {
    if (status === 'OK') {
        originLatitude = results[0].geometry.location.lat();
        originLongitude = results[0].geometry.location.lng();
        console.log("origin latitude: " + originLatitude + ", origin longitude: " + originLongitude);
    } else {
        console.log("Geocode was not successful for the following reason: " + status);
    }
    });
    geocoder.geocode({'address': destinationadd}, function(results, status) {
    if (status === 'OK') {
        destinationLatitude = results[0].geometry.location.lat();
        destinationLongitude = results[0].geometry.location.lng();
        console.log("destination latitude: " + destinationLatitude + ",destination longitude: " + destinationLongitude);
    } else {
        console.log("Geocode was not successful for the following reason: " + status);
    }
    });
    console.log(originLatitude,originLongitude,destinationLatitude,destinationLongitude);
    directionsService.route({
      origin: {lat: 41.85, lng: -87.65}, // Chicago
      destination: {lat: 42.33, lng: -83.04},  // Detroit
      travelMode: 'DRIVING'
    }, function(response, status) {
      if (status === 'OK') {
        directionsRenderer.setDirections(response);
      } else {
        window.alert('Directions request failed due to ' + status);
      }
    });
}
</script>
<script async defer
src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&callback=initMap">
</script>
</body>
</html>