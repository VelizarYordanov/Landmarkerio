<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Landmarkerio</title>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="py-4 px-2 border-b shadow-md">
            <div class="container flex mx-auto justify-between">
                <a class="text-2xl font-bold text-center items-center self-center" href="{{ url('/') }}">
                    Landmarkerio
                </a>
				</ul>
				<ul class="flex gap-4 items-center">
					@guest
						@if (Route::has('login'))
							<li class="">
								<a class="" href="{{ route('login') }}">{{ __('Login') }}</a>
							</li>
						@endif

						@if (Route::has('register'))
							<li class="">
								<a class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-700" href="{{ route('register') }}">{{ __('Register') }}</a>
							</li>
						@endif
					@else
						<li class="flex items-center gap-4">
							<a id="navbarDropdown" class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-700" href="{{ route('profile') }}" role="button">
								My Profile
							</a>

							<a class="" href="{{ route('logout') }}"
								onclick="event.preventDefault();
												document.getElementById('logout-form').submit();">
								{{ __('Logout') }}
							</a>
							
							<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
								@csrf
							</form>
						</li>
					@endguest
				</ul>
			</div>
		</div>
        </nav>
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>