@extends("layouts.app")

@vite(['resources/js/welcome.js'])
@section('content')
<body data-auth="{{ auth()->check() ? 'true' : 'false' }}">
<body data-name="<?= isset($_GET["name"]) ? $_GET["name"] : 'null' ?>">
<section class="container mx-auto">
	<div class="flex justify-between gap-4 pt-8">
		<div>
			<h1 class="text-2xl">Build your perfect trip</h1>
			<p class="text-gray-500">Plan your trip with our easy to use trip planner</p>
		</div>
		<div class="flex gap-3">
			<input id="start" class="px-4 py-2 border rounded-lg" type="text" placeholder="Enter Location">
			<input id="end" class="px-4 py-2 border rounded-lg" type="text" placeholder="End Location">
			<div class="flex-col">
				<div>
					<span>Free time (optional):</span>
				</div>
				<div>
					<input id="hours" class="px-1 py-2 w-1/3 border rounded-lg" type="number" placeholder="Hrs" min="0">
					<input id="minutes" class="px-1 py-2 border rounded-lg" type="number" placeholder="Mins" min="0" max="59"></div>
				</div>
			<button id="current-location" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">Use Current Location</button>
			<button id="submit" class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-700">Plan Trip</button>
		</div>
	</div>
</section>
<section id="map-container" class="flex pt-12 gap-3">
	<div class="w-full h-full" id="map"></div>
	<div id="results-container" class=" hidden w-2/5 border mr-2 p-2 overflow-scroll">
		<div class="border-b-2 border-gray-800 mb-2 pb-3 flex flex-col gap-2">
			<h2 id="selected-waypoints" class="text-lg mb-2">Selected Waypoints</h2>
			<div class="flex gap-2">
				<h1 id ="display-freeTime-text" class="hidden">Free Time Left: </h1>
				<h1 id="display-freeTime"></h1>
			</div>
     		<button id="open-map-button" class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-700">Open in maps</button>
		</div>
		<div id="waypoints" class="flex flex-col gap-2">
			<h2 class="text-lg mb-2">Suggested Waypoints</h2>
		</div>
	</div>
</section>
<div class="bg-grey-100">
    <div class="container px-2 py-4 mx-auto flex items-center sm:flex-row flex-col">
      <a class="flex title-font font-medium items-center md:justify-start justify-center text-gray-900">
        <span class="ml-3 text-xl">Landmarkerio</span>
      </a>
      <p class="text-sm text-gray-500 sm:ml-6 sm:mt-0 mt-4">2023 Landmarkerio —
        <a href="https://github.com/VelizarYordanov" rel="noopener noreferrer" class="text-gray-600 ml-1" target="_blank">@VelizarYordanov</a>
      </p>
    </div>
  </div>
@endsection
