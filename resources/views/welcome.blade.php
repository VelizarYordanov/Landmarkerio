@extends("layouts.header")

@vite(['resources/js/welcome.js'])
@section('content')
<section class="container mx-auto">
	<div class="flex justify-between gap-4 pt-8">
		<div>
			<h1 class="text-2xl">Build your perfect trip</h1>
			<p class="text-gray-500">Plan your trip with our easy to use trip planner</p>
		</div>
		<div class="flex gap-3">
			<input id="start" value="burgas" class="px-4 py-2 border rounded-lg" type="text" placeholder="Start Location">
			<input id="end" value="sofia" class="px-4 py-2 border rounded-lg"  type="text" placeholder="End Location">
			<button id="submit" class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-700">Plan Trip</button>
		</div>
	</div>
</section>
<section id="map-container" class="flex pt-12 gap-3">
	<div class="w-full h-full" id="map"></div>
	<div id="results-container" class=" hidden w-2/5 border mr-2 p-2 overflow-scroll">
		<div class="border-b-2 border-gray-800 mb-2 pb-3 flex flex-col gap-2">
			<h2 id="selected-waypoints" class="text-lg mb-2">Selected Waypoints</h2>
		</div>
		<div id="waypoints" class="flex flex-col gap-2">
			<h2 class="text-lg mb-2">Suggested Waypoints</h2>

		</div>
	</div>
</section>

@endsection