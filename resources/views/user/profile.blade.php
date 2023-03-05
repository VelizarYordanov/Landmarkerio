@extends('layouts.app')

@vite(['resources/js/profile.js']);

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="flex justify-between">
    <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
    <p class="text-gray-600">{{ $user->email }}</p>
  </div>
  <div class="mt-8">
    <h1 id="favourite-destinations" class="text-gray-900 text-lg mb-1 font-medium title-font">View your favourite destinations!</h1>
    <div class="">
      @foreach ($places as $id => $place_id)
    <div class="bg-white overflow-hidden shadow rounded-lg flex items-center mb-6" style="grid-column: 1 / span 2;">
      @php
        $place_details = file_get_contents('https://maps.googleapis.com/maps/api/place/details/json?place_id=' . $place_id . '&key=' . env('GOOGLE_MAP_KEY'));
        $place_details = json_decode($place_details);
        $place_name = $place_details->result->name;
        $place_address = $place_details->result->formatted_address;
        $place_image = isset($place_details->result->photos) ? $place_details->result->photos[0]->photo_reference : null;
      @endphp
      @if($place_image)
      <img id="image" class="h-48 w-48 object-cover" src="https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference={{ $place_image }}&key={{ env('GOOGLE_MAP_KEY') }}" alt="{{ $place_name }}">
      @else
      <img id="image" class="h-48 w-48 object-cover" src="https://via.placeholder.com/150" alt="">
      @endif
      <div class="flex-grow px-4 py-5 sm:p-6">
        <h3 id="name" class="text-lg font-medium leading-6 text-gray-900 mb-2">{{ $place_name }}</h3>
        <p id="address" class="text-sm text-gray-500 mb-2">{{ $place_address }}</p>
      </div>
      <div class="flex items-center gap-2">
        <a href="/?name=<?php echo $place_name; ?>" class="px-4 py-2 bg-indigo-500 text-white float-right rounded-lg hover:bg-indigo-700" onclick="sendData('<?php echo $place_name; ?>');">Visit</a>
        <form class="p-0 m-0" method="POST" action="{{ route('user.places.delete', $id) }}">
          @csrf
          @method('DELETE')
          <button type="submit">&#128465;</button>
        </form>
      </div>
    </div>
    @endforeach
    </div>
  </div>
</div>
@endsection