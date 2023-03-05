<?php
namespace App\Http\Controllers;

use App\User;
use App\Models\FavouritePlace;
use Illuminate\Http\Request;

class ProfileController extends UserController
{
    
    public function index()
    {
        $user = auth()->user();
        $places = FavouritePlace::select('id', 'place_id')->where('user_id', $user->id)->pluck('place_id', 'id')->toArray();
        return view('user.profile', ['user' => $user, 'places' => $places]);
    }
    
}