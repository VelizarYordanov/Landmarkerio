@extends('layouts.header')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
        <form>
            <label for="location-input">Enter a location:</label>
            <input id="location-input" type="text" placeholder="Enter a location">
        </form>
        <div id='map'></div>
        <style type="text/css">
            #map {
                height: 400px;
            }
        </style>

<script>
  /*var map;
  function initMap() {
      map = new google.maps.Map(document.getElementById('map'), {
      zoom: 7,
      center: {lat: 41.85, lng: -87.65}
    });
  }

  var autocomplete;
  function initMap() {
    autocomplete = new google.maps.places.Autocomplete(
    document.getElementById('location-input'), {types: ['geocode']});
    autocomplete.addListener('place_changed', geocodePlace);
  }
*/

var map;
var autocomplete;
function initMap() {
   map = new google.maps.Map(document.getElementById('map'), {
    center: {lat: -33.8688, lng: 151.2195},
    zoom: 13
  });
  autocomplete = new google.maps.places.Autocomplete(
    document.getElementById('location-input'), {types: ['geocode']});
  autocomplete.addListener('place_changed', geocodePlace);
}

  function geocodePlace() {
    var place = autocomplete.getPlace();
    if (!place.geometry) {
      window.alert("No details available for input: '" + place.name + "'");
      return;
    }
    if (place.geometry.viewport) {
      map.fitBounds(place.geometry.viewport);
    } else {
      map.setCenter(place.geometry.location);
      map.setZoom(17); 
    }
    var marker = new google.maps.Marker({
      map: map,
      position: place.geometry.location
    });
  }
</script>
<script async defer
src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&libraries=places&callback=initMap"
async defer>
</script>
        </div>
    </div>
</div>
