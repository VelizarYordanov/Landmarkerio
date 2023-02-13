@extends('layouts.header')
@section('content')
<div class="container mt-5">
  <div class="row">
    <div class="col-md-6 mx-auto">
      <div class="card bg-light border-dark">
        <div class="card-header bg-secondary text-white">
          <h3 class="text-center">User Profile</h3>
        </div>
        <div class="card-body">
          <form>
            <div class="form-group">
              <label for="profile-picture">Profile Picture:</label>
              <img src="{{ $user->profile_picture }}" alt="{{ $user->name }}" class="img-fluid">
            </div>
            <div class="form-group">
              <label for="username">Username:</label>
              <input type="text" class="form-control" id="username" value="{{ $user->name }}" disabled>
            </div>
            <div class="form-group">
              <label for="email">Email:</label>
              <input type="email" class="form-control" id="email" value="{{ $user->email }}" disabled>
            </div>
          </form>
        </div>
      </div> 
    </div>
    <div class="col-md-8 mx-auto">
      <div class="card bg-light border-dark">
        <div class="card-header bg-secondary text-white">
          <h3 class="text-center">Favorite Places</h3>
        </div>
        <div class="card-body">
          <table class="table table-striped">
          <thead>
              <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Image</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($places as $place)
                @php
                $place_details = file_get_contents('https://maps.googleapis.com/maps/api/place/details/json?place_id=' . $place . '&key=' . env('GOOGLE_MAP_KEY'));
                $place_details = json_decode($place_details);
                $place_name = $place_details->result->name;
                $place_address = $place_details->result->formatted_address;
                $place_image = $place_details->result->photos[0]->photo_reference;
                @endphp
                <tr>
                    <td>{{ $place_name }}</td>
                    <td>{{ $place_address }}</td>
                    <td><img src="https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference={{ $place_image }}&key={{ env('GOOGLE_MAP_KEY') }}" alt="{{ $place_name }}" class="img-fluid"></td>
                </tr>
                @endforeach
            </tbody>
          </table>
        </div>
      </div> 
    </div>
  </div>
</div>
@endsection
