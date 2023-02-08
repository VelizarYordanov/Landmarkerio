<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FavouritePlace;
class FavouritePlaceController extends Controller
{
    public function store(Request $request)
    {
        $user_id = $request->user_id;
        $place_id = $request->place_id;

        $favouritePlace = FavouritePlace::create([
            'user_id' => $user_id,
            'place_id' => $place_id,
        ]);

        return response()->json($favouritePlace, 201);
    }
}
