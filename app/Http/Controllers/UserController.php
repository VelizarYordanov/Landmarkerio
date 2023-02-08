<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getCurrentUserId(Request $request){
        if(auth()->check()){
            $user = auth()->user();
            return response()->json(['id' => $user->id]);
        }else{
            return response()->json(['error' => 'Unauthenticated user', 401]);
        }
    }
}
