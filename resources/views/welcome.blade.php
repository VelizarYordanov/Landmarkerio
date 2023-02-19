@extends("layouts.header")

@vite(['resources/js/welcome.js'])
@section('content')
<div class="container bg-red-900">
    <div class="row justify-content-center">
        <div class="col-md-8">
        <form id="input-form">
          <input id="start" type="text" autocomplete="on" placeholder="Start location"><br>
          <input id="end" type="text" autocomplete="on"><br>
          <button id="submit">Submit</button>
        </form>
        <div id="map"></div>
        <component :is="'style'">
          #map{
            width: 400px;
            height: 400px; 
          }
        </component>
        <button id="open-map-button" style="display: none;">Open Route in Google Maps!</button>
        <div id="dashboard" style="display: none;">
        <table>
          <tr>
            <th>Name</th>
            <th>Address</th>
          </tr>
          <tbody id="waypoints-table"></tbody>
        </table>
    </div>
</div>
@endsection