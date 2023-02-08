<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FavouriteVisit;
class FavouriteVisitController extends Controller
{
    public function store(Request $request)
    {
        $user_id = $request->user_id;
        $visit_id = $request->visit_id;

        $favouriteVisit = FavouriteVisit::create([
            'user_id' => $user_id,
            'visit_id' => $visit_id,
        ]);

        return response()->json($favouriteVisit, 201);
    }
}
