@extends('layouts.header')
@section('content')
<!--<div class="container mt-5">
  <div class="row">
    <div class="col-md-6 mx-auto">
      <div class="card bg-light border-dark">
        <div class="card-header bg-secondary text-white">
          <h3 class="text-center">User Profile</h3>
        </div>
        <div class="card-body">
          <form>
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
              @foreach ($places as $id => $place_id)
              @php
              $place_details = file_get_contents('https://maps.googleapis.com/maps/api/place/details/json?place_id=' . $place_id . '&key=' . env('GOOGLE_MAP_KEY'));
              $place_details = json_decode($place_details);
              $place_name = $place_details->result->name;
              $place_address = $place_details->result->formatted_address;
              $place_image = isset($place_details->result->photos) ? $place_details->result->photos[0]->photo_reference : null;
              @endphp
              <tr>
                  <td>{{ $place_name }}</td>
                  <td>{{ $place_address }}</td>
                  @if ($place_image)
                      <td><img src="https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference={{ $place_image }}&key={{ env('GOOGLE_MAP_KEY') }}" alt="{{ $place_name }}" class="img-fluid"></td>
                  @else
                      <td>No image available</td>
                  @endif
                  <td>
                    <form method="POST" action="{{ route('user.places.delete', $id) }}">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger">Delete</button>
                  </form>
                  </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div> 
    </div>
  </div>
</div>!-->



<section class="text-gray-600 body-font flex flex-items-stretch relative">
  <div class="container px-5 py-24 h-3/5 mx-auto flex sm:flex-nowrap flex-wrap gap-60">
    <div class="bg-white rounded-lg p-8 flex flex-col w-2/5 mt-10 md:mt-0 relative z-10 shadow-lg">
      <h2 class="text-gray-900 text-lg mb-1 font-medium title-font">Welcome to your profile!</h2>
      <div class="relative mb-4">
        <label for="name" class="leading-7 text-sm text-gray-600">Name</label>
        <input type="text" value="{{ $user->name }}" id="name" name="name" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700  py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
      </div>
      <div class="relative mb-4">
        <label for="email" class="leading-7 text-sm text-gray-600">Email</label>
        <input type="email" value="{{ $user->email }}" id="email" name="email" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
      </div>
    </div>
    <div id="favourites-container" class="w-2/5 rounded-lg p-8 shadow-lg border mr-2 p-2 overflow-scroll">
      <h2 id="favourite-destinations" class="text-gray-900 text-lg mb-1 font-medium title-font">View your favourite destinations!</h2>
    </div>
  </div>
</section>

@endsection
