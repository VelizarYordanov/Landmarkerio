@extends('layouts.app')

@section('content')
<section class="text-gray-600 body-font">
    <div class="container px-5 py-24 mx-auto flex flex-wrap items-center justify-center">
      <div class="lg:w-2/6 md:w-1/2 bg-gray-100 rounded-lg p-8 flex flex-col w-full mt-10 md:mt-0">
        <h2 class="text-gray-900 text-lg font-medium title-font mb-5">{{ __('Log in') }}</h2>
        <form method="POST" action="{{ route('login') }}" class="w-full">
          @csrf
  
          <div class="relative mb-4">
            <label for="email" class="leading-7 text-sm text-gray-600">{{ __('Email Address') }}</label>
            <input id="email" type="email" name="email" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out @error('email') is-invalid @enderror" value="{{ old('email') }}" required autocomplete="email" autofocus>
  
            @error('email')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>
  
          <div class="relative mb-4">
            <label for="password" class="leading-7 text-sm text-gray-600">{{ __('Password') }}</label>
            <input id="password" type="password" name="password" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out @error('password') is-invalid @enderror" required autocomplete="current-password">
  
            @error('password')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>
  
          <div class="relative mb-4">
            <div class="flex items-center">
              <input class="form-check-input h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
              <label class="ml-2 block text-sm text-gray-900" for="remember">
                {{ __('Remember Me') }}
              </label>
            </div>
          </div>
  
          <button type="submit" class="text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">{{ __('Login') }}</button>
  
          <!--@if (Route::has('password.request'))
            <div class="mt-4">
              <a class="text-sm text-indigo-600 hover:text-indigo-800" href="{{ route('password.request') }}">
                {{ __('Forgot Your Password?') }}
              </a>
            </div>
          @endif!-->
        </form>
      </div>
    </div>
  </section>
  
  

@endsection
