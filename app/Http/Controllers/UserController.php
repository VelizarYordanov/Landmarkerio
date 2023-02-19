<?php

namespace App\Http\Controllers;

use App\Models\FavouritePlace;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function favourite_places()
    {
        return $this->hasMany(FavouritePlace::class);
    }


    public function getCurrentUserId(Request $request){
        if(auth()->check()){
            $user = auth()->user();
            return response()->json(['id' => $user->id]);
        }else{
            return response()->json(['error' => 'Unauthenticated user', 401]);
        }
    }

    public function deletePlace($place_id) {
        $user = auth()->user();
        $user->favourite_places()->where('id', $place_id)->delete();
        return redirect()->route('profile');
    }
}
